<?php

namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Laravel\Fortify\Contracts\UpdatesUserProfileInformation;
use Illuminate\Support\Facades\Storage;

class UpdateUserProfileInformation implements UpdatesUserProfileInformation
{
    public function update(User $user, array $input): void
    {
        \Log::info('更新処理開始: ', $input);

        if (isset($input['user_image'])) {
            $this->handleUserImage($user, $input['user_image']);
        }

        $email = $input['email'] ?? $user->email;

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

    protected function handleUserImage(User $user, $image): void
    {
        try {
            if ($user->user_image && Storage::exists(str_replace('storage', 'public/', $user->user_image))) {
                Storage::delete(str_replace('storage', 'public/', $user->user_image));
            }

            $path = $image->store('public/user_images');
            $user->user_image = str_replace('public/', 'storage/', $path);
            $user->save();
        } catch (\Exception $e) {
            \Log::error('画像処理エラー: ' . $e->getMessage());
            throw new \RuntimeException('画像の保存に失敗しました。');
        }
    }

    protected function updateVerifiedUser(User $user, array $input): void
    {
        $user->forceFill([
            'name' => $input['user_name'],
            'email' => $input['email'],
            'email_verified_at' => null,
        ])->save();

        $user->sendEmailVerificationNotification();
    }
}
