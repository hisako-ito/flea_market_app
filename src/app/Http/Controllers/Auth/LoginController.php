<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    public function login(LoginRequest $request)
    {
        $user = User::where('email', $request->email)->first();

        if ($user) {
            if (Hash::check($request->password, $user->password)) {
                if (!$user->hasVerifiedEmail()) {
                    return back()->withErrors([
                        'email' => 'メールアドレスが確認されていません。確認メールをご確認ください',
                    ])->withInput();
                }

                Auth::login($user);
                $request->session()->regenerate();

                return redirect('/')->with('message', 'ログインしました');
            }

            return back()->withErrors([
                'email' => 'ログイン情報が登録されていません',
            ])->withInput();
        }
        return back()->withErrors([
            'email' => 'ログイン情報が登録されていません',
        ])->withInput();
    }

    // if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
    //     $user = Auth::user();

    //     if (!$user->hasVerifiedEmail()) {
    //         Auth::logout();
    //         return back()->withErrors([
    //             'email' => 'メールアドレスが確認されていません。確認メールをご確認ください。',
    //         ])->withInput();
    //     }

    //     $request->session()->regenerate();
    //     return redirect('/')->with('message', 'ログインしました');
    // }

    // return back()->withErrors([
    //     'email' => 'ログイン情報が登録されていません',
    // ])->withInput();
}
