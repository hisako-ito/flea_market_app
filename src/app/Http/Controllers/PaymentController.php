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
        $keyword = $request->input('keyword', '');
        $query = Item::query();

        if (!empty($keyword)) {
            $items = $query->where('item_name', 'like', '%' . $keyword . '%')->get();
            return view('search_results', compact('items', 'keyword', 'page'));
        }


        $paymentMethod = $request->input('payment_method');
        $shippingAddress = $request->input('shipping_address');

        if ($paymentMethod === 'コンビニ払い') {
            $payment_method_type = 'konbini';
        } elseif ($paymentMethod === 'カード払い') {
            $payment_method_type = 'card';
        } else {
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
            'cancel_url' => route('stripe.cancel', [
                'item_id' => $item->id,
                'session_id' => isset($session) ? $session->id : null,
            ]),
        ]);

        Log::info('Metadata being sent to Stripe:', [
            'item_id' => $item->id,
            'user_id' => $user->id,
            'payment_method' => $paymentMethod,
            'shipping_address' => $shippingAddress,
        ]);

        Log::info('Checkout Session Created:', (array)$session);

        return redirect($session->url);
    }

    public function handleWebhook(Request $request)
    {
        $endpointSecret = Config::get('services.stripe.webhook_secret');
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');

        try {
            $event = Webhook::constructEvent($payload, $sigHeader, $endpointSecret);
            Log::info('Webhook event received', [
                'type' => $event->type,
                'data' => $event->data->object,
            ]);

            if ($event->type === 'payment_intent.succeeded') {
                $paymentIntent = $event->data->object;

                Log::info('Processing payment_intent.succeeded event', [
                    'payment_intent_id' => $paymentIntent->id,
                    'metadata' => (array)$paymentIntent->metadata,
                ]);

                $item = Item::find($paymentIntent->metadata['item_id']);
                if (!$item) {
                    Log::error('Item not found', ['item_id' => $paymentIntent->metadata['item_id']]);
                    return response('Item not found', 404);
                }

                if (Order::where('item_id', $item->id)->exists()) {
                    Log::info('Order already exists for item_id: ' . $item->id);
                    return response('Order already processed', 200);
                }

                $paymentMethod = $paymentIntent->metadata['payment_method'] ?? null;
                $shippingAddress = $paymentIntent->metadata['shipping_address'] ?? null;

                Log::info('Creating order', [
                    'item_id' => $item->id,
                    'user_id' => $paymentIntent->metadata['user_id'] ?? null,
                    'payment_method' => $paymentMethod,
                    'shipping_address' => $shippingAddress,
                ]);

                $form = [
                    'user_id' => $paymentIntent->metadata['user_id'] ?? null,
                    'item_id' => $item->id,
                    'price' => $item->price,
                    'payment_method' => $paymentMethod === 'カード払い' ? 2 : 1,
                    'shipping_address' => $shippingAddress,
                ];

                $order = Order::create($form);
                $item->update(['is_sold' => true]);

                Log::info('Order created successfully', ['order_id' => $order->id]);
            }

            return response('Webhook handled', 200);
        } catch (\UnexpectedValueException $e) {
            Log::error('Invalid payload: ' . $e->getMessage());
            return response('Invalid payload', 400);
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            Log::error('Invalid signature: ' . $e->getMessage());
            return response('Invalid signature', 400);
        } catch (\Exception $e) {
            Log::error('General error: ' . $e->getMessage());
            return response('Webhook error', 500);
        }
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

        $session_id = $request->query('session_id'); // StripeのCheckout Session ID

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
