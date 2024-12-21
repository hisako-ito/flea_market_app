<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function login(LoginRequest $request)
    {
        $field = filter_var($request->login, FILTER_VALIDATE_EMAIL) ? 'email' : 'user_name';

        if (Auth::attempt([$field => $request->login, 'password' => $request->password])) {
            $request->session()->regenerate();
            return redirect()->intended('/')->with('message', 'ログインに成功しました');
        }

        return back()->withErrors([
            'login' => 'ログイン情報が登録されていません。',
        ])->withInput();
    }
}
