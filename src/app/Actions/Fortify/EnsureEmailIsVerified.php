<?php

namespace App\Actions\Fortify;

use Closure;
use Illuminate\Http\Request;

class EnsureEmailIsVerified
{
    public function handle(Request $request, Closure $next)
    {
        // ユーザーが未認証（ログインしていない）場合
        if (!$request->user()) {
            return redirect('/login')->withErrors([
                'email' => 'メール認証を完了してください。',
            ]);
        }

        // ユーザーがログイン済みだがメール認証が未完了の場合
        if (!$request->user()->hasVerifiedEmail()) {
            return redirect('/email/verify')->withErrors([
                'email' => 'メール認証を完了してください。',
            ]);
        }

        // 次の処理に進む
        return $next($request);
    }
}
