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
        $user = Auth::user();
        $tab = $request->query('tab', 'recommend');
        $sort = "recommend";

        $query = Item::query();

        if (Auth::check() && $user) {
            $query->where('user_id', '!=', $user->id);
        }

        if (!empty($keyword)) {
            $query->where('item_name', 'like', '%' . $keyword . '%');
        }

        $items = collect();
        $favorites = collect();

        if ($tab == 'recommend') {
            $items = $query->withCount('favorites')
                ->orderBy('favorites_count', 'desc')
                ->get();
        } elseif ($tab === 'mylist' && $user) {
            $favorites = $user->favoriteEntries()
                ->whereHas('item', function ($query) use ($user, $keyword) {
                    if (!empty($keyword)) {
                        $query->where('item_name', 'like', '%' . $keyword . '%');
                    }
                    $query->where('user_id', '!=', $user->id);
                })
                ->get();
        }

        return view('list', compact('keyword', 'items', 'user', 'tab', 'sort', 'favorites'));
    }

    public function getDetail($item_id, Request $request)
    {
        $item = Item::with(['comments.user'])->findOrFail($item_id);
        $categories = Category::all();
        $keyword = $request->input('keyword', '');
        $query = Item::query();

        $commentsCount = $item->comments->count();

        if (!empty($keyword)) {
            $items = $query->where('item_name', 'like', '%' . $keyword . '%')->get();
            return view('search_results', compact('items', 'keyword', 'tab'));
        }

        return view('item', compact('item', 'categories', 'keyword', 'commentsCount'));
    }

    public function getPurchase($item_id, Request $request)
    {
        $item = Item::find($item_id);
        $keyword = $request->input('keyword', '');
        $user = Auth::user();

        $query = Item::query();

        if (!empty($keyword)) {
            $items = $query->where('item_name', 'like', '%' . $keyword . '%')->get();
            return view('search_results', compact('items', 'keyword', 'tab'));
        }
        return view('purchase', compact('item', 'keyword', 'user'));
    }

    public function getSell(Request $request)
    {
        $keyword = $request->input('keyword', '');
        $user = Auth::user();
        $categories = Category::all();

        $query = Item::query();

        if (!empty($keyword)) {
            $items = $query->where('item_name', 'like', '%' . $keyword . '%')->get();
            return view('search_results', compact('items', 'keyword', 'tab'));
        }

        return view('sell', compact('keyword', 'user', 'categories'));
    }

    public function postSell(ExhibitionRequest $request)
    {
        $keyword = $request->input('keyword', '');
        $user = Auth::user();
        $query = Item::query();

        if (!empty($keyword)) {
            $items = $query->where('item_name', 'like', '%' . $keyword . '%')->get();
            return view('search_results', compact('items', 'keyword', 'tab'));
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

        return redirect('/sell')->with('message', '商品を出品しました。');
    }
}
