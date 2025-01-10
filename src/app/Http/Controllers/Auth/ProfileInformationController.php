<?php

namespace App\Http\Controllers\Auth;

use App\Actions\Fortify\UpdateUserProfileInformation;
use Illuminate\Routing\Controller;
use App\Http\Requests\ProfileRequest;
use App\Http\Requests\AddressRequest;
use App\Models\Item;

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
            return redirect('/login')->with('message', 'メールアドレスの確認メールをお送りしました。メールアドレスを確認後、ログインしてください。');
        }

        return redirect()->back()->with('message', 'プロフィールを更新しました。');
    }

    public function postAddress($item_id, AddressRequest $request)
    {
        $item = Item::find($item_id);
        $data = $request->validated();
        $user = $request->user();
        $this->updateUserProfile->update($user, $data);

        return redirect()->route('purchase.get', ['item_id' => $item->id])->with('message', '住所を更新しました。');
    }
}
