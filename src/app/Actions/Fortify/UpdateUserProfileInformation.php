<?php

namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Laravel\Fortify\Contracts\UpdatesUserProfileInformation;
use App\Http\Requests\AddressRequest;
use App\Http\Requests\ProfileRequest;
use Illuminate\Support\Facades\Storage;

class UpdateUserProfileInformation implements UpdatesUserProfileInformation
{
    public function update(User $user, array $input): void
    {
        \Log::info('更新処理開始: ', $input);

        try {
            Validator::make($input, [
                'user_name' => ['required', 'string', 'max:255'],

                'email' => [
                    'required',
                    'string',
                    'email',
                    'max:255',
                    Rule::unique('users')->ignore($user->id),
                ],
                'postal_code' => 'required|string|regex:/^\d{3}-\d{4}$/|max:8',
                'address' => 'required',
                'building' => 'required',
            ])->validate();
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('バリデーションエラー: ', $e->errors());
            throw $e;
        }

        if (isset($input['user_image'])) {
            \Log::info('画像処理開始');
            if ($user->user_image && Storage::exists(str_replace('storage', 'public/', $user->user_image))) {
                \Log::info('既存の画像を削除します: ' . $user->user_image);
                Storage::delete(str_replace('storage/', 'public/', $user->user_image));
            }
            $path = $input['user_image']->store('public/user_images');
            $user->user_image = str_replace('public/', 'storage/', $path);
            \Log::info('新しい画像を保存しました: ' . $user->user_image);
        }

        $email = $input['email'] ?? $user->email;
        \Log::info('保存前のデータ: ', $user->toArray());
        if (
            $email !== $user->email &&
            $user instanceof MustVerifyEmail
        ) {
            $this->updateVerifiedUser($user, $input);
        } else {
            $user->forceFill([
                'user_name' => $input['user_name'],
                'email' => $email,
                'postal_code' => $input['postal_code'] ?? $user->postal_code,
                'address' => $input['address'] ?? $user->address,
                'building' => $input['building'] ?? $user->building,
            ])->save();
            \Log::info('保存後のデータ: ', $user->toArray());
            \Log::info('更新処理完了: ', $user->toArray());
        }
    }
    /**
     * Update the given verified user's profile information.
     *
     * @param  array<string, string>  $input
     */
    protected function updateVerifiedUser(User $user, array $input): void
    {
        $user->forceFill([
            'name' => $input['name'],
            'email' => $input['email'],
            'email_verified_at' => null,
        ])->save();

        $user->sendEmailVerificationNotification();
    }
}
