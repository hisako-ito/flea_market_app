<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\Category;

class ItemController extends Controller
{
    public function index(Request $request)
    {
        $items = Item::all();

        $keyword = $request->input('keyword', '');

        if(!empty($keyword)) {
            $items = Item::KeywordSearch($request->keyword)->get();

            return view('search_results', compact('items', 'keyword'));
        }
        return view('list', compact('items', 'keyword'));
    }

    public function getDetail($item_id)
    {
        $item = Item::find($item_id);
        $categories = Category::all();

        return view('item', compact('item','categories'));
    }
}
