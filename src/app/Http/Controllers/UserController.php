<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function add()
    {
        return view('profile_add');
    }

    public function show(Request $request)
    {
        $keyword = $request->input('keyword', '');
        $user = Auth::user();
        $tab = $request->query('tab', 'sell');

        $query = Item::query();

        if (!empty($keyword)) {
            $items = $query->where('item_name', 'like', '%' . $keyword . '%')->get();
            return view('search_results', compact('items', 'keyword', 'tab'));
        }

        if ($tab == 'buy') {
            $orders = Order::where('user_id', $user->id)->with('item')->get();
            $items =
                $orders->map(function ($order) {
                    return $order->item;
                });
        } elseif ($tab === 'sell') {
            $items = Item::where('user_id', $user->id)->get();
        } else {
            $items = collect();
        }

        return view('mypage', compact('keyword', 'items', 'user', 'tab'));
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
