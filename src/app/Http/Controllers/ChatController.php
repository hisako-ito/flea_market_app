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
use Illuminate\Support\Facades\Mail;
use App\Mail\TransactionCompletedMail;

class ChatController extends Controller
{
    public function chatShow($item_id)
    {
        $user = Auth::user();
        $item = Item::with('buyer')->find($item_id);
        $mainTransaction = Transaction::where('item_id', $item_id)->first();

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

        $shouldShowModal = $mainTransaction &&
            $mainTransaction->status === 'completed' &&
            $item->user_id === $user->id &&
            !Review::where('transaction_id', $mainTransaction->id)
                ->where('reviewer_id', $user->id)
                ->exists();

        Message::where('item_id', $item_id)
            ->where('sender_id', '!=', $user->id)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return view('mypage_chat', compact('user', 'item', 'mainTransaction', 'transactions', 'messages', 'shouldShowModal'));
    }

    public function messageStore($item_id, MessageRequest $request)
    {
        $user = Auth::user();
        $item = Item::with('buyer')->find($item_id);

        $transaction = Transaction::where('item_id', $item_id)
            ->where('buyer_id', $item->buyer_id)
            ->where('seller_id', $item->user_id)
            ->where('status', 'pending')
            ->first();

        if ($transaction) {
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
        return redirect()->route('chat.show', ['item_id' => $item_id])->with('message', 'すでに取引は完了済みのため、メッセージを送信できません');
    }

    public function messageUpdate($message_id, Request $request)
    {
        $message = Message::find($message_id);
        $validator = Validator::make($request->all(), [
            "content.{$message->id}" => 'required|max:400',
        ], [
            "content.{$message->id}.required" => '本文を入力してください',
            "content.{$message->id}.max" => '本文は400文字以内で入力してください',
        ]);

        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator, 'edit_' . $message->id)
                ->withInput();
        }

        $content = $request->input("content.{$message->id}");

        $message->update([
            'content' => $content,
        ]);

        return redirect()->route('chat.show', ['item_id' => $message->item_id])
            ->with('message', 'メッセージを編集しました');
    }

    public function messageDestroy($message_id)
    {
        $message = Message::find($message_id);

        if (!$message) {
            return redirect()->back()->with('message', 'メッセージが見つかりませんでした');
        }

        $message->delete();

        return redirect()->route('chat.show', ['item_id' => $message->item_id])
            ->with('message', 'メッセージを削除しました');
    }

    public function reviewStore($item_id, Request $request)
    {
        $user = Auth::user();
        $item = Item::find($item_id);

        $transaction = Transaction::where('item_id', $item_id)
            ->where('buyer_id', $item->buyer_id)
            ->where('seller_id', $item->user_id)
            ->first();

        if (!$transaction) {
            return redirect()->back()->with('message', '指定された取引が見つかりません');
        }

        $reviewedId = ($transaction->buyer_id === $user->id)
            ? $transaction->seller_id : $transaction->buyer_id;

        $alreadyReviewed = Review::where('transaction_id', $transaction->id)
            ->where('reviewer_id', $user->id)
            ->exists();

        if ($alreadyReviewed) {
            return redirect()->route('item.list')->with('message', 'すでに評価済みです');
        }

        Review::create([
            'transaction_id' => $transaction->id,
            'item_id' => $item_id,
            'reviewer_id' => $user->id,
            'reviewed_id' => $reviewedId,
            'rating' => $request->rating,
        ]);

        if ($transaction->status !== 'completed' && $user->id === $transaction->buyer_id) {
            $transaction->status = 'completed';
            $transaction->save();

            Mail::to($transaction->seller->email)
                ->send(new TransactionCompletedMail($item, $transaction->seller, $user));
        }

        return redirect()->route('item.list')->with('message', '評価を送信しました');
    }
}
