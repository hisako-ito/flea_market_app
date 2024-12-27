<?php

namespace App\Http\Controllers;

use App\Models\Favorite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FavoriteController extends Controller
{
    public function favorite($itemId)
    {
        $user = Auth::user();

        $favorite = Favorite::where('user_id', $user->id)->where('item_id', $itemId)->first();

        if ($favorite) {
            $favorite->delete();
            $status = 'removed';
        } else {
            Favorite::create([
                'user_id' => $user->id,
                'item_id' => $itemId,
            ]);
            $status = 'added';
        }

        $favoriteCount = Favorite::where('item_id', $itemId)->count();

        return response()->json([
            'status' => $status,
            'favoriteCount' => $favoriteCount,
        ]);
    }
}
