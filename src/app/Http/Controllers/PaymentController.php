<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Stripe\Stripe;
use Stripe\Checkout\Session as StripeSession;
use App\Http\Requests\PurchaseRequest;

class PaymentController extends Controller
{
    public function postPurchase($item_id, PurchaseRequest $request)
    {
        $user = Auth::user();
        $item = Item::find($item_id);

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
                return redirect()->back()->with('error', '無効な支払い方法が指定されました。');
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
                    'unit_amount' => (int) $item->price,
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => route('stripe.waiting_for_payment', ['item_id' => $item->id]),
            'cancel_url' => route('stripe.cancel', ['item_id' => $item->id]),
        ]);

        switch ($paymentMethod) {
            case 'コンビニ払い':
                $paymentMethod = 1;
                break;
            case 'カード払い':
                $paymentMethod = 2;
                break;
        }

        session(['stripe_session_id' => $session->id]);

        Order::create([
            'user_id' => $user->id,
            'item_id' => $item->id,
            'price' => $item->price,
            'payment_method' => $paymentMethod,
            'shipping_address' => $shippingAddress,
            'stripe_session_id' => $session->id,
            'status' => 'pending',
        ]);

        return redirect($session->url);
    }

    public function waitingForPayment(Request $request)
    {
        $session_id = $request->query('session_id') ?? session('stripe_session_id');

        Stripe::setApiKey(config('services.stripe.secret'));

        try {
            $session = StripeSession::retrieve($session_id);
        } catch (\Exception $e) {
            return redirect()->route('item.list')->with('error', '無効なセッションIDが指定されました。');
        }

        if ($session->payment_status === 'paid') {
            $order = Order::where('stripe_session_id', $session_id)->first();

            if ($order) {
                $order->status = 'paid';
                $order->save();
            }
            $item = Item::find($order->item_id);
            if ($item) {
                $item->is_sold = true;
                $item->save();
            }

            return redirect()->route('item.list')->with('message', '購入が完了しました。出品者の発送通知までお待ちください。');
        }

        return redirect()->route('item.list')->with('error', '支払いが完了していません。');
    }

    public function checkPaymentStatus(Request $request)
    {
        $session_id = $request->query('session_id') ?? session('stripe_session_id');

        Stripe::setApiKey(config('services.stripe.secret'));

        try {
            $session = StripeSession::retrieve($session_id);
        } catch (\Exception $e) {
            return redirect()->route('item.list')->with('error', '無効なセッションIDが指定されました。');
        }

        if ($session->payment_status === 'paid') {
            $order = Order::where('stripe_session_id', $session_id)->first();

            if ($order) {
                $order->status = 'paid';
                $order->save();
            }
            $item = Item::find($order->item_id);
            if ($item) {
                $item->is_sold = true;
                $item->save();
            }

            return response()->json(['status' => 'paid'], 200);
        }

        return response()->json(['status' => 'pending'], 200);
    }
}
