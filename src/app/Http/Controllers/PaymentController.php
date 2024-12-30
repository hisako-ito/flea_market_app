<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\PurchaseRequest;
use Stripe\Stripe;
use Stripe\Checkout\Session as StripeSession;
use Illuminate\Support\Facades\Log;
use Stripe\Webhook;
use Illuminate\Support\Facades\Config;


class PaymentController extends Controller
{
    public function postPurchase($item_id, PurchaseRequest $request)
    {
        $user = Auth::user();
        $item = Item::find($item_id);

        if (!$item) {
            throw new \Exception('Item not found.');
        }

        $paymentMethod = $request->input('payment_method');
        $shippingAddress = $request->input('shipping_address');

        switch ($paymentMethod) {
            case 'コンビニ払い':
                $payment_method_type = 'konbini';
                break;
            case 'カード払い':
                $payment_method_type = 'card';
                break;
            default:
                throw new \InvalidArgumentException('Invalid payment method selected.');
        }

        Stripe::setApiKey(config('services.stripe.secret'));

        $session = StripeSession::create([
            'payment_method_types' => [$payment_method_type],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'jpy',
                    'product_data' => [
                        'name' => $item->item_name,
                    ],
                    'unit_amount' => (int)$item->price,
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'payment_intent_data' => [
                'metadata' => [
                    'item_id' => strval($item->id),
                    'user_id' => strval($user->id),
                    'payment_method' => $paymentMethod,
                    'shipping_address' => $shippingAddress,
                ],
            ],
            'success_url' => route('stripe.success', ['item_id' => $item->id]),
            'cancel_url' => route('stripe.cancel', ['item_id' => $item->id]),
        ]);

        Log::info('Metadata being set in Checkout Session', [
            'item_id' => $item->id,
            'user_id' => $user->id,
            'payment_method' => $paymentMethod,
            'shipping_address' => $shippingAddress,
        ]);

        return redirect($session->url);
    }

    public function handleWebhook(Request $request)
    {
        $payload = $request->getContent();
        $signature = $request->header('Stripe-Signature');
        $secret = env('STRIPE_WEBHOOK_SECRET');

        try {
            $event = \Stripe\Webhook::constructEvent($payload, $signature, $secret);
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            Log::error('Invalid signature', ['message' => $e->getMessage()]);
            return response('Invalid signature', 400);
        } catch (\Exception $e) {
            Log::error('Webhook error', ['message' => $e->getMessage()]);
            return response('Webhook error', 500);
        }

        $eventType = $event->type;

        Log::info('Webhook event received', ['type' => $eventType]);

        switch ($eventType) {
            case 'payment_intent.succeeded':
                $this->handlePaymentIntentSucceeded($event->data->object);
                break;

            case 'charge.succeeded':
                $this->handleChargeSucceeded($event->data->object);
                break;

            case 'payment_intent.created':
                Log::info('Processing payment_intent.created event');
                break;

            case 'charge.updated':
                Log::info('Processing charge.updated event');
                break;

            default:
                Log::warning('Unhandled webhook event type', ['type' => $eventType]);
                return response('Unhandled event type', 400);
        }

        return response('Webhook handled', 200);
    }

    protected function handleCheckoutSessionCompleted($session)
    {
        Log::info('Processing checkout.session.completed event', ['session_id' => $session->id]);
    }

    protected function handlePaymentIntentSucceeded($paymentIntent)
    {
        Log::info('Received metadata for payment_intent.succeeded', ['metadata' => $paymentIntent->metadata]);

        $metadata = $paymentIntent->metadata ?? [];
        if (empty($metadata)) {
            Log::error('Missing metadata in payment_intent.succeeded', ['payment_intent_id' => $paymentIntent->id]);
            return;
        }

        $itemId = $metadata['item_id'] ?? null;
        if (!$itemId) {
            Log::error('Item ID not found in metadata', ['metadata' => $metadata, 'payment_intent_id' => $paymentIntent->id]);
            return;
        }

        $item = Item::find($itemId);
        if (!$item) {
            Log::error('Item not found for payment_intent', ['item_id' => $itemId, 'payment_intent_id' => $paymentIntent->id]);
            return;
        }

        $paymentMethod = $metadata['payment_method'] ?? null;
        $shippingAddress = $metadata['shipping_address'] ?? null;

        $form = [
            'user_id' => $metadata['user_id'] ?? null,
            'item_id' => $item->id,
            'price' => $item->price,
            'payment_method' => $paymentMethod === 'カード払い' ? 2 : 1,
            'shipping_address' => $shippingAddress,
        ];

        $order = Order::create($form);
        $item->update(['is_sold' => true]);

        Log::info('Order created successfully', ['order_id' => $order->id, 'item_id' => $item->id]);
    }


    protected function handleChargeSucceeded($charge)
    {
        Log::info('Processing charge.succeeded event', ['charge_id' => $charge->id]);
    }


    public function success(Request $request)
    {
        $item_id = $request->query('item_id');
        $item = Item::find($item_id);

        $query = Item::query();

        if (!empty($keyword)) {
            $items = $query->where('item_name', 'like', '%' . $keyword . '%')->get();
            return view('search_results', compact('items', 'keyword', 'page'));
        }

        return redirect()->route('item.detail', ['item_id' => $item->id])
            ->with('message', '商品の購入が完了しました！');
    }


    public function cancel(Request $request)
    {
        $item_id = $request->query('item_id');
        $item = Item::find($item_id);

        $query = Item::query();

        if (!empty($keyword)) {
            $items = $query->where('item_name', 'like', '%' . $keyword . '%')->get();
            return view('search_results', compact('items', 'keyword', 'page'));
        }

        $session_id = $request->query('session_id');

        if ($session_id) {
            Stripe::setApiKey(config('services.stripe.secret'));

            try {
                $session = StripeSession::retrieve($session_id);
                $paymentMethod = $session->payment_method_types[0] ?? null;

                if ($paymentMethod === 'konbini') {

                    return redirect()->route('item.detail', ['item_id' => $item->id])
                        ->with('message', 'コンビニ払いの場合、支払いが確認されるまで注文が確定されません。');
                }
            } catch (\Exception $e) {
                Log::error('Error retrieving Stripe session in cancel action:', [
                    'error' => $e->getMessage(),
                    'session_id' => $session_id,
                ]);
            }
        }
        return redirect()->route('item.detail', ['item_id' => $item->id])
            ->with('message', '決済がキャンセルされました。再度お試しください。');
    }
}
