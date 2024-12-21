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
        }
    }

    protected function handleUserImage(User $user, $image): void
    {
        if ($user->user_image) {
            Storage::disk('public')->delete(str_replace('storage/', '', $user->user_image));
        }

        $user->update([
            'user_image' => 'storage/' . $image->store('user_images', 'public'),
        ]);
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
