<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\CommentRequest;
use App\Models\Item;

class CommentController extends Controller
{
    public function storeComment($item_id, CommentRequest $request)
    {
        $item = Item::find($item_id);

        $item->comments()->create([
            'user_id' => auth()->id(),
            'content' => $request->input('content'),
        ]);

        return redirect()->route('item.detail', $item_id)->with('message', 'コメントを投稿しました！');
    }
}
