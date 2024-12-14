<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\AddressRequest;
use App\Http\Requests\ProfileRequest;

class UserController extends Controller
{
    public function showAddProfileForm()
    {
        return view('profile_add');
    }

    public function addProfile(AddressRequest $request)
    {
        $user = Auth::user();
        $user->update([
            'postal_code' => $request->postal_code,
            'address' => $request->address,
            'building' => $request->building,
        ]);

        // ログアウトしてログイン画面へ遷移
        Auth::logout();
        return redirect('/login')->with('status', 'プロフィールが更新されました。再度ログインしてください。');
    }

    public function show(Request $request)
    {
        $keyword = $request->input('keyword', '');
        $items = Item::query();
        $user = Auth::user();

        if(!empty($keyword)) {
            $items = $items->KeywordSearch($keyword);
        }
        $items = $items->get();

        return view('mypage', compact('keyword', 'items', 'user'));
    }

    public function edit(Request $request)
    {
        $keyword = $request->input('keyword', '');
        $user = Auth::user();

        if(!empty($keyword)) {
            $items = Item::KeywordSearch($keyword)->get();
            return view('search_results', compact('items', 'keyword'));
        }

        return view('profile_edit', compact('keyword', 'user'));
    }
}