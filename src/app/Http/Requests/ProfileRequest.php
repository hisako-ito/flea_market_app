<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProfileRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $isUpdate = $this->route()->getName() === 'user-profile-information.profile'; // ルート名で判断

        return [
            'user_image' => $isUpdate ? 'nullable|mimes:png,jpeg' : 'required|mimes:png,jpeg',
        ];
    }

    public function messages()
    {
        return [
            'user_image.required' => 'ユーザー画像を登録してください',
            'user_image.mimes' => '「.png」または「.jpeg」形式でアップロードしてください',
        ];
    }
}
