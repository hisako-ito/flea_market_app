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
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Stripe\Event;
use Stripe\Webhook;


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
        $endpointSecret = config('services.stripe.webhook_secret');
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');

        try {
            $event = Webhook::constructEvent($payload, $sigHeader, $endpointSecret);
        } catch (\UnexpectedValueException $e) {
            Log::error("Invalid payload");
            return response()->json(['error' => 'Invalid payload'], 400);
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            Log::error("Invalid signature");
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        $this->handleEvent($event);

        return response()->json(['status' => 'success']);
    }

    protected function handleEvent($event)
    {
        $eventType = $event['type'] ?? null;

        switch ($eventType) {
            case 'checkout.session.completed':
                $this->handleCheckoutSessionCompleted($event['data']['object']);
                break;

            case 'payment_intent.succeeded':
                $this->handlePaymentIntentSucceeded($event['data']['object']);
                break;

            default:
                Log::info("Unhandled event type", ['type' => $eventType]);
        }
    }

    protected function handleCheckoutSessionCompleted($session)
    {
        $this->processEventData($session['metadata'], $session['amount_total'] ?? 0);
    }

    protected function handlePaymentIntentSucceeded($paymentIntent)
    {
        $this->processEventData($paymentIntent['metadata'], $paymentIntent['amount_received'] ?? 0);
    }

    protected function processEventData($metadata, $amount)
    {
        if (!$metadata) {
            Log::error("Missing metadata");
            return;
        }

        $userId = $metadata['user_id'] ?? null;
        $itemId = $metadata['item_id'] ?? null;
        $shippingAddress = $metadata['shipping_address'] ?? null;
        $paymentMethod = $metadata['payment_method'] ?? null;

        if (!$userId || !$itemId || !$shippingAddress || !$paymentMethod) {
            Log::error("Incomplete metadata", ['metadata' => $metadata]);
            return;
        }

        $paymentMethodType = $paymentMethod === 'カード払い' ? 2 : 1;

        try {
            DB::transaction(function () use ($userId, $itemId, $shippingAddress, $paymentMethodType, $amount) {
                $orderData = [
                    'user_id' => $userId,
                    'item_id' => $itemId,
                    'price' => $amount,
                    'payment_method' => $paymentMethodType,
                    'shipping_address' => $shippingAddress,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
                Log::info("Inserting order data", $orderData);
                DB::table('orders')->insert($orderData);

                DB::table('items')->where('id', $itemId)->update(['is_sold' => true]);
                Log::info("Item marked as sold", ['item_id' => $itemId]);
            });
        } catch (\Exception $e) {
            Log::error("Database operation failed", [
                'error' => $e->getMessage(),
                'user_id' => $userId,
                'item_id' => $itemId,
            ]);
        }
    }

    public function success(Request $request)
    {
        $item_id = $request->query('item_id');
        $item = Item::find($item_id);

        $query = Item::query();

        $keyword = $request->input('keyword', '');
        if (!empty($keyword)) {
            $items = $query->where('item_name', 'like', '%' . $keyword . '%')->get();
            return view('search_results', compact('items', 'keyword'));
        }

        return redirect()->route('item.detail', ['item_id' => $item->id])
            ->with('message', '商品の購入が完了しました！');
    }


    public function cancel(Request $request)
    {
        $item_id = $request->query('item_id');
        $item = Item::find($item_id);

        $query = Item::query();

        $keyword = $request->input('keyword', '');
        if (!empty($keyword)) {
            $items = $query->where('item_name', 'like', '%' . $keyword . '%')->get();
            return view('search_results', compact('items', 'keyword'));
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
