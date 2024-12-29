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
        $items = Item::query();
        $orders = collect();
        $user = Auth::user();
        $page = $request->query('page', 'sell');

        $query = Item::query();

        if (!empty($keyword)) {
            $items = $query->where('item_name', 'like', '%' . $keyword . '%')->get();
            return view('search_results', compact('items', 'keyword', 'page'));
        }

        if ($page == 'buy') {
            $orders = Order::where('user_id', $user->id)->with('item')->get();
            $items = collect();
        } else {
            $items = Item::where('user_id', $user->id)->get();
        }

        return view('mypage', compact('keyword', 'items', 'user', 'page', 'orders'));
    }

    public function edit(Request $request)
    {
        $keyword = $request->input('keyword', '');
        $user = Auth::user();

        $query = Item::query();

        if (!empty($keyword)) {
            $items = $query->where('item_name', 'like', '%' . $keyword . '%')->get();
            return view('search_results', compact('items', 'keyword', 'page'));
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
            return view('search_results', compact('items', 'keyword', 'page'));
        }

        return view('profile_address', compact('item', 'keyword', 'user'));
    }
}
