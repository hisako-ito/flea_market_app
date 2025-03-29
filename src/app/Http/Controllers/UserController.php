<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\Order;
use App\Models\Message;
use App\Models\Review;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public function add()
    {
        return view('profile_add');
    }

    public function myPageShow(Request $request)
    {
        $keyword = $request->input('keyword', '');
        $user = Auth::user();
        $tab = $request->query('tab', 'sell');
        $query = Item::query();

        $unreadMessages = Message::with('transaction.item')
            ->whereHas('transaction', function ($query) use ($user) {
                $query->where('status', 'pending')
                    ->where(function ($q) use ($user) {
                        $q->where('seller_id', $user->id)
                            ->orWhere('buyer_id', $user->id);
                    });
            })
            ->where('receiver_id', $user->id)
            ->where('is_read', false)
            ->select('item_id', DB::raw('count(*) as unread_count'))
            ->groupBy('item_id')
            ->get()
            ->map(function ($message) {
                $message->item_id = (int) $message->item_id;
                return $message;
            })
            ->keyBy('item_id');

        $averageRating = Review::where('reviewed_id', $user->id)->avg('rating');

        if (!empty($keyword)) {
            $items = $query->where('item_name', 'like', '%' . $keyword . '%')->get();
            return view('search_results', compact('items', 'keyword', 'tab'));
        }

        if ($tab == 'buy') {
            $orders = Order::where('buyer_id', $user->id)->with('item')->get();
            $items = $orders->map(function ($order) {
                return $order->item;
            });
        } elseif ($tab === 'sell') {
            $items = Item::where('user_id', $user->id)->get();
        } elseif ($tab === 'trade') {
            $messages = Message::with('transaction.item')
                ->whereHas('transaction', function ($query) use ($user) {
                    $query->where('status', 'pending')
                        ->where(function ($q) use ($user) {
                            $q->where('seller_id', $user->id)
                                ->orWhere('buyer_id', $user->id);
                        });
                })
                ->latest('updated_at')
                ->get();
            $latestMessages = $messages->unique('transaction_id');
            $sortedMessages = $latestMessages->sortByDesc(function ($message) use ($user) {
                return $message->receiver_id === $user->id ? 1 : 0;
            });
            $items = $sortedMessages->map(function ($message) {
                return $message->item;
            });
        } else {
            $items = collect();
        }

        return view('mypage', compact('keyword', 'items', 'user', 'tab', 'unreadMessages', 'averageRating'));
    }

    public function edit(Request $request)
    {
        $keyword = $request->input('keyword', '');
        $user = Auth::user();

        $query = Item::query();

        if (!empty($keyword)) {
            $items = $query->where('item_name', 'like', '%' . $keyword . '%')->get();
            return view('search_results', compact('items', 'keyword', 'tab'));
        }

        return view('profile_edit', compact('keyword', 'user'));
    }

    public function getAddress($item_id, Request $request)
    {
        $item = Item::find($item_id);
        $keyword = $request->input('keyword', '');
        $user = Auth::user();

        $query = Item::query();

        if (!empty($keyword)) {
            $items = $query->where('item_name', 'like', '%' . $keyword . '%')->get();
            return view('search_results', compact('items', 'keyword', 'tab'));
        }

        return view('profile_address', compact('item', 'keyword', 'user'));
    }
}
