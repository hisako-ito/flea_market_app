<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;

class UserController extends Controller
{
        public function show(Request $request)
    {
        $keyword = $request->input('keyword', '');
        $items = Item::query();

        if(!empty($keyword)) {
            $items = $items->KeywordSearch($keyword);
        }
        $items = $items->get();

        return view('mypage', compact('items', 'keyword'));
    }
}
