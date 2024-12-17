<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Actions\Fortify\UpdateUserProfileInformation;
use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\Controller;
use Illuminate\Support\Str;
use Laravel\Fortify\Contracts\ProfileInformationUpdatedResponse;
use Laravel\Fortify\Contracts\UpdatesUserProfileInformation;
use App\Http\Requests\ProfileRequest;
use App\Http\Requests\AddressRequest;


class ProfileInformationController extends Controller
{
    protected $updateUserProfile;

    public function __construct(UpdateUserProfileInformation $updateUserProfile)
    {
        $this->updateUserProfile = $updateUserProfile;
    }

    public function update(ProfileRequest $profileRequest, AddressRequest $addressRequest)
    {
        $profileData = $profileRequest->validated();
        $addressData = $addressRequest->validated();

        $mergedData = array_merge($profileData, $addressData);

        $user = $addressRequest->user();
        $this->updateUserProfile->update($user, $mergedData);

        if ($addressRequest->route()->getName() === 'user-profile-information.register') {
            auth()->logout();
            return redirect('/login')->with('message', 'プロフィールを更新しました。再度ログインしてください。');
        }

        return redirect()->back()->with('message', 'プロフィールを更新しました！');
    }

    public function postAddress(AddressRequest $request)
    {
        $data = $request->validated();

        $user = $request->user();
        $this->updateUserProfile->update($user, $data);

        return redirect()->back()->with('message', '住所を更新しました！');
    }
}
