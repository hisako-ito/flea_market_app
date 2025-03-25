<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\Message;
use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    public function chatShow($item_id)
    {
        $user = Auth::user();
        $item = Item::with('user', 'buyer')->find($item_id);
        $transactions = Transaction::where('item_id', $item->id)
            ->where('seller_id', $user->id)
            ->get();

        return view('mypage_chat', compact('user', 'item', 'transactions'));
    }

    public function store($item_id, Request $request)
    {
        $user = Auth::user();
        $item = Item::find($item_id);

        $transaction = Transaction::firstOrCreate(
            [
                'item_id' => $item->id,
                'buyer_id' => $item->buyer_id,
            ],
            [
                'seller_id' => $item->user_id,
            ]
        );

        // $path = $request->file('image')?->store('message_images', 'public');

        Message::create([
            'transaction_id' => $transaction->id,
            'sender_id' => $user->id,
            'content' => $request->content,
            // 'image' => $path,
        ]);

        return redirect()->route('chat.show', ['transaction' => $transaction->id]);
    }
}
