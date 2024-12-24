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

        $request->session()->put('payment_method', $paymentMethod);
        $request->session()->put('shipping_address', $shippingAddress);

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
                    'unit_amount' => $item->price,
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => route('stripe.success', ['item_id' => $item->id]),
            'cancel_url' => route('stripe.cancel', ['item_id' => $item->id]),
        ]);

        return redirect($session->url);
    }

    public function success(Request $request)
    {
        $item_id = $request->query('item_id');
        $item = Item::find($item_id);

        $paymentMethod = $request->session()->get('payment_method');

        if ($paymentMethod === 'コンビニ払い') {
            $paymentMethod = 1;
        } elseif ($paymentMethod === 'カード払い') {
            $paymentMethod = 2;
        }

        $shippingAddress = $request->session()->get('shipping_address');

        $form = [
            'user_id' => Auth::id(),
            'item_id' => $item->id,
            'price' => $item->price,
            'payment_method' => $paymentMethod,
            'shipping_address' => $shippingAddress,
        ];

        Order::create($form);

        $item->update(['is_sold' => true]);

        return redirect()->route('item.detail', ['item_id' => $item->id, 'purchase_completed' => true]);
    }
}
