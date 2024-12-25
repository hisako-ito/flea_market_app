<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\PurchaseRequest;
use Stripe\Stripe;
use Stripe\Checkout\Session as StripeSession;
use Stripe\PaymentIntent;
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

        if (!empty($keyword)) {
            $items = Item::KeywordSearch($keyword)->get();
            return view('search_results', compact('items', 'keyword'));
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
                    'unit_amount' =>
                    (int)$item->price,
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => route('stripe.success', ['item_id' => $item->id]),
            'cancel_url' => route('stripe.cancel', ['item_id' => $item->id]),
            'payment_intent_data' => [
                'metadata' => [
                    'item_id' => strval($item->id),
                    'user_id' => strval($user->id),
                    'payment_method' => $paymentMethod,
                    'shipping_address' => $shippingAddress,
                ],
            ],
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
            $event = Webhook::constructEvent(
                $payload,
                $sigHeader,
                $endpointSecret
            );
            if ($event->type === 'payment_intent.succeeded') {
                $paymentIntent = $event->data->object;

                Log::info('Received PaymentIntent:', (array) $paymentIntent);
                Log::info('Received Metadata:', (array) $paymentIntent->metadata);

                if (!isset($paymentIntent->metadata) || !is_object($paymentIntent->metadata)) {
                    Log::error('Metadata is missing or invalid in PaymentIntent.');
                    return response('Invalid metadata', 400);
                }

                $item_id = $paymentIntent->metadata->item_id ?? null;

                if (!$item_id) {
                    Log::error('Item ID not found in PaymentIntent metadata');
                    return response('Item ID not found', 400);
                }

                $item = Item::find($item_id);
                if (!$item) {
                    Log::error("Item not found for ID: $item_id");
                    return response('Item not found', 404);
                }

                $paymentMethod = $paymentIntent->metadata->payment_method ?? null;
                $shippingAddress = $paymentIntent->metadata->shipping_address ?? null;

                if ($paymentMethod === 'コンビニ払い') {
                    $paymentMethod = 1;
                } elseif ($paymentMethod === 'カード払い') {
                    $paymentMethod = 2;
                }

                $form = [
                    'user_id' => $paymentIntent->metadata->user_id ?? null,
                    'item_id' => $item->id,
                    'price' => $item->price,
                    'payment_method' => $paymentMethod,
                    'shipping_address' => $shippingAddress,
                ];

                Order::create($form);
                $item->update(['is_sold' => true]);
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

        return redirect()->route('item.detail', [
            'item_id' => $item->id,
            'purchase_completed' => true
        ]);
    }
}
