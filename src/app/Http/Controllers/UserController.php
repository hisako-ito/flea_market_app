<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\AddressRequest;
use App\Http\Requests\ProfileRequest;

class UserController extends Controller
{
    public function store(Request $request)
    {
        $addressRequest = app(AddressRequest::class);
        $addressRequest->validate($request->all());

        $profileRequest = app(ProfileRequest::class);
        $profileRequest->validate($request->all());

        $user = Auth::user();
        $dir = 'user_images';

        $file_name = $request->file('user_image')->getClientOriginalName();
        $request->file('user_image')->storeAs('public/' . $dir, $file_name);

        $user_data = User::create([
            'image' => 'storage/' . $dir . '/' . $file_name,
            'name' => $request->input('user_name'),
            'postal_code' => $request->input('postal_code'),
            'address' => $request->input('address'),
            'building' => $request->input('building'),
        ]);

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }

    public function show(Request $request)
    {
        $keyword = $request->input('keyword', '');
        $items = Item::query();

        if(!empty($keyword)) {
            $items = $items->KeywordSearch($keyword);
        }
        $items = $items->get();

        return view('mypage', compact('keyword', 'items'));
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

    public function update(Request $request)
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
