<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\Message;
use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\MessageRequest;
use App\Models\Review;
use Illuminate\Support\Facades\Validator;

class ChatController extends Controller
{
    public function chatShow($item_id)
    {
        $user = Auth::user();
        $item = Item::with('user', 'buyer')->find($item_id);
        $transaction = Transaction::where('item_id', $item_id)->first();

        $reviewerId = auth()->id();
        $reviewedId = ($transaction->buyer_id === $reviewerId)
            ? $transaction->seller_id : $transaction->buyer_id;

        $alreadyReviewed = Review::where('item_id', $item_id)
            ->where('reviewer_id', $user->id)
            ->exists();

        $shouldShowModal = false;

        if (
            $transaction &&
            $transaction->status === 'completed' && !$alreadyReviewed &&
            $item->user_id === $user->id
        ) {
            $shouldShowModal = true;
        }

        $transactions = Transaction::with('item')
            ->where('status', 'pending')
            ->where(function ($query) use ($user) {
                $query->where('seller_id', $user->id)
                    ->orWhere('buyer_id', $user->id);
            })
            ->orderBy('created_at', 'asc')
            ->get();

        $messages = Message::where('item_id', $item_id)
            ->with('sender')
            ->orderBy('created_at', 'asc')
            ->get();

        return view('mypage_chat', compact('user', 'item', 'transactions', 'messages', 'shouldShowModal'));
    }

    public function messageStore($item_id, MessageRequest $request)
    {
        $user = Auth::user();
        $item = Item::find($item_id);
        $transaction = Transaction::where('item_id', $item_id)->first();

        if (!$transaction) {
            $transaction = Transaction::Create([
                'item_id' => $item_id,
                'buyer_id' => $item->buyer_id,
                'seller_id' => $item->user_id,
            ]);
        } elseif ($transaction->status === 'completed') {
            $transaction->update(['status' => 'pending',]);
        }

        $message = new Message();
        $message->transaction_id = $transaction->id;
        $message->item_id = $item_id;
        $message->sender_id = $user->id;
        $message->content = $request->content;

        if ($item->buyer_id !== $user->id) {
            $message->receiver_id = $item->buyer_id;
        } else {
            $message->receiver_id = $item->user_id;
        }

        if ($request->hasFile('msg_image')) {
            $file_name = $request->file('msg_image')->getClientOriginalName();
            $request->file('msg_image')->storeAs('public/chat_images', $file_name);
            $message->msg_image = 'storage/chat_images/' . $file_name;
        }
        $message->save();

        return redirect()->route('chat.show', ['item_id' => $item_id])->with('message', 'メッセージを送信しました');
    }

    public function messageUpdate($message_id, Request $request)
    {
        $message = Message::with('item')->find($message_id);
        $validator = Validator::make($request->all(), [
            'content' => 'required|max:400',
        ], [
            'content.required' => '本文を入力してください',
            'content.max' => '本文は400文字以内で入力してください',
        ]);

        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator, 'edit_' . $message->id)
                ->withInput();
        }

        $message->update([
            'content' => $request->input('content'),
        ]);

        return redirect()->route('chat.show', ['item_id' => $message->item_id])
            ->with('message', 'メッセージを編集しました');
    }

    public function messageDestroy($message_id)
    {
        $message = Message::with('item')->find($message_id);

        if (!$message) {
            return redirect()->back()->with('message', 'メッセージが見つかりませんでした');
        }

        $message->delete();

        return redirect()->route('chat.show', ['item_id' => $message->item_id])
            ->with('message', 'メッセージを削除しました');
    }

    public function reviewStore($item_id, $transaction_id, Request $request)
    {
        $transaction = Transaction::where('item_id', $item_id)
            ->orderBy('created_at', 'desc')
            ->first();

        if (!$transaction || $transaction->item_id != $item_id) {
            return redirect()->back()->with('message', '指定された取引が見つかりません');
        }

        $reviewerId = auth()->id();
        $reviewedId = ($transaction->buyer_id === $reviewerId)
            ? $transaction->seller_id : $transaction->buyer_id;

        $alreadyReviewed = Review::where('transaction_id', $transaction->id)
            ->where('reviewer_id', $reviewerId)
            ->where('reviewed_id', $reviewedId)
            ->exists();

        if ($alreadyReviewed) {
            return redirect()->route('item.list')->with('message', 'すでに評価済みです');
        }

        if ($transaction->status !== 'completed') {
            $transaction->status = 'completed';
            $transaction->save();
        }

        Review::create([
            'transaction_id' => $transaction->id,
            'item_id' => $item_id,
            'reviewer_id' => $reviewerId,
            'reviewed_id' => $reviewedId,
            'rating' => $request->rating,
        ]);

        return redirect()->route('item.list')->with('message', '評価を送信しました');
    }
}
