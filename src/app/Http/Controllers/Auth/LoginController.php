<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function login(LoginRequest $request)
    {
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $user = Auth::user();

            // メール認証が完了していない場合、ログアウトしてエラーを返す
            if (!$user->hasVerifiedEmail()) {
                Auth::logout(); // 即時ログアウト
                return back()->withErrors([
                    'email' => 'メールアドレスが確認されていません。確認メールをご確認ください。',
                ])->withInput();
            }
            // セッションを再生成してログイン成功
            $request->session()->regenerate();
            return redirect()->intended('/')->with('message', 'ログインができました！');
        }

        // 認証失敗時の処理
        return back()->withErrors([
            'email' => 'ログイン情報が登録されていません。',
        ])->withInput();
    }
}
