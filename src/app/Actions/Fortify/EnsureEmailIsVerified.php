<?php

namespace App\Actions\Fortify;

use Closure;
use Illuminate\Http\Request;

class EnsureEmailIsVerified
{
    public function handle(Request $request, Closure $next)
    {
        if (!$request->user()->hasVerifiedEmail()) {
            return redirect('/email/verify')->withErrors([
                'email' => 'メール認証を完了してください。',
            ]);
        }

        return $next($request);
    }
}
