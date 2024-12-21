<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
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
        $user = Auth::user();

        if (!empty($keyword)) {
            $items = $items->KeywordSearch($keyword);
        }
        $items = $items->get();

        return view('mypage', compact('keyword', 'items', 'user'));
    }

    public function edit(Request $request)
    {
        $keyword = $request->input('keyword', '');
        $user = Auth::user();

        if (!empty($keyword)) {
            $items = Item::KeywordSearch($keyword)->get();
            return view('search_results', compact('items', 'keyword'));
        }

        return view('profile_edit', compact('keyword', 'user'));
    }

    public function getAddress($item_id, Request $request)
    {
        $item = Item::find($item_id);
        $keyword = $request->input('keyword', '');
        $user = Auth::user();

        if (!empty($keyword)) {
            $items = Item::KeywordSearch($keyword)->get();
            return view('search_results', compact('items', 'keyword'));
        }

        return view('profile_address', compact('item', 'keyword', 'user'));
    }
}
