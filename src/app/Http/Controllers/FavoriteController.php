<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\Item;

class FavoriteController extends Controller
{
    public function favorite($item_id)
    {
        if (!auth()->check()) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $user = Auth::user();
        $item = Item::find($item_id);

        if ($user->favorites()->where('item_id', $item->id)->exists()) {
            $user->favorites()->detach($item->id);
        } else {
            $user->favorites()->attach($item->id);
        }

        return response()->json([
            'favorite_count' => $item->favorites()->count(),
            'is_favorited' => $user->favorites()->where('item_id', $item->id)->exists(),
        ]);
    }
}
