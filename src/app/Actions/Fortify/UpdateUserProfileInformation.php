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
        $addressRules = (new AddressRequest())->rules();
        $addressMessages = (new AddressRequest())->messages();

        $profileRules = (new ProfileRequest())->rules();
        $profileMessages = (new ProfileRequest())->messages();

        $rules = array_merge($addressRules, $profileRules);
        $messages = array_merge($addressMessages, $profileMessages);

        Validator::make($input, $rules, $messages)->validate();

        if (isset($input['user_image'])) {
            if($user->user_image && Storage::exists(str_replace('storage', 'public/', $user->user_image))) {
                Storage::delete(str_replace('storage/', 'public/', $user->user_image));
            }
            $path = $input['user_image']->store('public/user_images');
            $user->user_image = str_replace('public/', 'storage/', $path);
        }

        if ($input['email'] !== $user->email &&
            $user instanceof MustVerifyEmail) {
            $this->updateVerifiedUser($user, $input);
        } else {
            $user->forceFill([
                'user_name' => $input['user_name'],
                'email' => $input['email'] ?? $user->email,
                'postal_code' => $input['postal_code'] ?? $user->postal_code,
                'address' => $input['address'] ?? $user->address,
                'building' => $input['building'] ?? $user->building,
            ])->save();
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