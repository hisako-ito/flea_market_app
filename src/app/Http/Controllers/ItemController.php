<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\Category;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\PurchaseRequest;
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

    public function postPurchase($item_id, PurchaseRequest $request)
    {
        $user = Auth::user();
        $item = Item::find($item_id);
        $keyword = $request->input('keyword', '');

        if (!empty($keyword)) {
            $items = Item::KeywordSearch($keyword)->get();
            return view('search_results', compact('items', 'keyword'));
        }

        $paymentMethod = 0;
        if ($request->payment_method === 'コンビニ払い') {
            $paymentMethod = 1;
        } elseif ($request->payment_method === 'カード払い') {
            $paymentMethod = 2;
        }

        $form = [
            'user_id' => Auth::id(),
            'item_id' => $item->id,
            'price' => $item->price,
            'payment_method' => $paymentMethod,
            'shipping_address' => $request->shipping_address,
        ];

        Order::create($form);

        $item->update(['is_sold' => true]);

        $message = "商品を購入しました。";

        return redirect()->route('purchase.success', ['item_id' => $item->id])->with('message', $message);
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

        $item = Item::create([
            'user_id' => auth()->id(),
            'condition' => $request->condition,
            'item_name' => $request->item_name,
            'brand' => $request->brand,
            'description' => $request->description,
            'price' => $request->price,
            'item_image' => '',
        ]);

        $item->categories()->attach($request->categories);

        if ($request->hasFile('item_image')) {
            $path = $request->file('item_image')->store('item_images', 'public');
            $item->update(['item_image' => 'storage/' . $path]);
        }

        return redirect('/sell')->with('message', '商品を出品しました！');
    }
}
