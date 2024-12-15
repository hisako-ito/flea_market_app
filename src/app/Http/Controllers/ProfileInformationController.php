<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\AddressRequest;
use App\Http\Requests\ProfileRequest;
use App\Actions\Fortify\UpdateUserProfileInformation;
use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\Controller;
use Illuminate\Support\Str;
use Laravel\Fortify\Contracts\ProfileInformationUpdatedResponse;
use Laravel\Fortify\Contracts\UpdatesUserProfileInformation;
use Laravel\Fortify\Fortify;

class ProfileInformationController extends Controller
{
    public function update(
        AddressRequest $request,
        UpdatesUserProfileInformation $updater
    ) {
        \Log::info('コントローラ内の認証ユーザー: ', ['user' => $request->user()]);

        try {
            \Log::info('リクエストデータ: ', $request->all());
            \Log::info('フォームリクエスト内の認証ユーザー: ', ['user' => $request->user()]);

            if (config('fortify.lowercase_usernames')) {
                $request->merge([
                    Fortify::username() => Str::lower($request->{Fortify::username()}),
                ]);
            }

            $updater->update($request->user(), $request->all());

            \Log::info('更新処理完了');

            if ($request->route()->getName() === 'user-profile-information.register') {
                auth()->logout();
                return redirect('/login')->with('status', 'プロフィールを更新しました。再度ログインしてください。');
            }

            return redirect()->back()->with('status', 'プロフィールを更新しました！');
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('バリデーションエラー詳細: ', $e->errors());
            return redirect()->back()->withErrors($e->errors());
        } catch (\Exception $e) {
            \Log::error('更新処理中にエラーが発生しました: ' . $e->getMessage());
            return redirect()->back()->withErrors(['error' => 'プロフィールの更新中に問題が発生しました。もう一度お試しください。']);
        }
    }
}
