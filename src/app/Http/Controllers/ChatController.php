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

        $transactions = Transaction::with('item')
            ->where('status', 'pending')
            ->where(function ($query) use ($user) {
                $query->where('seller_id', $user->id)
                    ->orWhere('buyer_id', $user->id);
            })
            ->orderBy('created_at', 'desc')
            ->get();
        $messages = Message::where('item_id', $item_id)
            ->with('sender')
            ->orderBy('created_at', 'asc')
            ->get();

        return view('mypage_chat', compact('user', 'item', 'transactions', 'messages'));
    }

    public function messageStore($item_id, Request $request)
    {
        $user = Auth::user();
        $item = Item::find($item_id);

        $transaction = Transaction::firstOrCreate(
            [
                'item_id' => $item_id,
                'buyer_id' => $item->buyer_id,
            ],
            [
                'seller_id' => $item->user_id,
            ]
        );

        $message = new Message();
        $message->transaction_id = $transaction->id;
        $message->item_id = $item_id;
        $message->sender_id = $user->id;
        $message->content = $request->content;

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('chat_images', 'public');
            $message->image = 'storage/' . $path;
        }
        $message->save();

        return redirect()->route('chat.show', ['item_id' => $item_id])->with('message', 'メッセージを送信しました。');
    }
}
