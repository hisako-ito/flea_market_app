<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\PurchaseRequest;
use Stripe\Stripe;
use Stripe\Checkout\Session as StripeSession;
use Exception;

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

        $paymentMethod = 0;
        if ($request->payment_method === 'コンビニ払い') {
            $paymentMethod = 1;
        } elseif ($request->payment_method === 'カード払い') {
            $paymentMethod = 2;
        }

        $form = [
            'user_id' => Auth::id(),
            'item_id' => $item->id,
            'price' => $item->price,
            'payment_method' => $paymentMethod,
            'shipping_address' => $request->shipping_address,
        ];

        Order::create($form);

        $item->update(['is_sold' => true]);

        return view('stripe.checkout', ['item_id' => $item->id]);
    }

    public function checkout($item_id, PurchaseRequest $request)
    {
        // $user = Auth::user();
        $item = Item::find($item_id);

        // Stripe APIキーの設定
        Stripe::setApiKey(config('services.stripe.secret'));

        // Checkoutセッションを作成
        $session = StripeSession::create([
            'payment_method_types' => ['card', 'konbini'],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'jpy',
                    'product_data' => [
                        'name' => $item->item_name,
                        'price' => $item->price,
                        'quantity' => 1,
                    ],
                ],
            ]],
            'mode' => 'payment',
            'success_url' => route('stripe.success'),
            'cancel_url' => route('stripe.cancel'),
        ]);

        // StripeのCheckoutページにリダイレクト
        return redirect($session->url);
    }

    public function success()
    {
        return view('stripe.success');
    }

    public function cancel()
    {
        return view('stripe.cancel');
    }
}
