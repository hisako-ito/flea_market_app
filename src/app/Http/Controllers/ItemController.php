<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\Category;

class ItemController extends Controller
{
    public function index()
    {
        $items = Item::all();
        return view('list',compact('items'));
    }

    public function getDetail($item_id)
    {
        $item = Item::find($item_id);
        $categories = Category::all();

        return view('item', compact('item','categories'));
    }
}
