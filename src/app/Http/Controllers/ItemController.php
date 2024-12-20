<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\Category;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\ExhibitionRequest;

class ItemController extends Controller
{
    public function index(Request $request)
    {

        $keyword = $request->input('keyword', '');
        $items = Item::query();

        if (!empty($keyword)) {
            $items = $items->KeywordSearch($keyword);
        }
        $items = $items->get();

        return view('list', compact('keyword', 'items'));
    }

    public function getDetail($item_id, Request $request)
    {
        $item = Item::find($item_id);
        $categories = Category::all();
        $keyword = $request->input('keyword', '');

        if (!empty($keyword)) {
            $items = Item::KeywordSearch($keyword)->get();
            return view('search_results', compact('items', 'keyword'));
        }

        return view('item', compact('item', 'categories', 'keyword'));
    }

    public function getPurchase($item_id, Request $request)
    {
        $item = Item::find($item_id);
        $keyword = $request->input('keyword', '');
        $user = Auth::user();

        if (!empty($keyword)) {
            $items = Item::KeywordSearch($keyword)->get();
            return view('search_results', compact('items', 'keyword'));
        }
        return view('purchase', compact('item', 'keyword', 'user'));
    }

    public function postPurchase($item_id, Request $request)
    {
        $user = Auth::user();
        $item = Item::find($item_id);
        $keyword = $request->input('keyword', '');
        $item->delete();

        if (!empty($keyword)) {
            $items = Item::KeywordSearch($keyword)->get();
            return view('search_results', compact('items', 'keyword'));
        }
        $message = "商品を購入しました。";

        return redirect('purchase/{item_id}')->with('message', $message);
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

    public function getSell(Request $request)
    {
        $keyword = $request->input('keyword', '');
        $user = Auth::user();
        $categories = Category::all();

        if (!empty($keyword)) {
            $items = Item::KeywordSearch($keyword)->get();
            return view('search_results', compact('items', 'keyword'));
        }
        return view('sell', compact('keyword', 'user', 'categories'));
    }

    public function postSell(ExhibitionRequest $request)
    {
        $keyword = $request->input('keyword', '');
        $user = Auth::user();

        if (!empty($keyword)) {
            $items = Item::KeywordSearch($keyword)->get();
            return view('search_results', compact('items', 'keyword'));
        }

        $item = Item::create(
            array_merge(
                $request->only([
                    'item_image',
                    'condition',
                    'item_name',
                    'brand',
                    'description',
                    'price',
                ]),
                ['user_id' => $user->id]
            )
        );

        $item->categories()->attach($request->categories);

        $message = "商品を出品しました。";

        return redirect('/sell')->with('message', $message);
    }
}
