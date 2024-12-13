<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\Category;

class ItemController extends Controller
{
    public function index(Request $request)
    {

        $keyword = $request->input('keyword', '');
        $items = Item::query();

        if(!empty($keyword)) {
            $items = $items->KeywordSearch($keyword);
        }
        $items = $items->get();

        return view('list', compact('keyword', 'items'));
    }

    public function getDetail($item_id,Request $request)
    {
        $item = Item::find($item_id);
        $categories = Category::all();
        $keyword = $request->input('keyword', '');

        if(!empty($keyword)) {
            $items = Item::KeywordSearch($keyword)->get();
            return view('search_results', compact('items', 'keyword'));
        }

        return view('item', compact('item','categories','keyword'));
    }

    public function getPurchase($item_id,Request $request)
    {
        $item = Item::find($item_id);
        $keyword = $request->input('keyword', '');

        if(!empty($keyword)) {
            $items = Item::KeywordSearch($keyword)->get();
            return view('search_results', compact('items', 'keyword'));
        }
        return view('purchase', compact('item','keyword'));
    }

    public function purchase($item_id,Request $request)
    {
        $item = Item::find($item_id);
        if (!$item) {
        return redirect('/')->with('error', '指定された商品が見つかりません。');
        }
        $keyword = $request->input('keyword', '');
        $item->delete();

        if(!empty($keyword)) {
            $items = Item::KeywordSearch($keyword)->get();
            return view('search_results', compact('items', 'keyword'));
        }
        $message = "商品を購入しました。";

        return redirect('/')->with(compact('item','keyword','message'));
    }
}
