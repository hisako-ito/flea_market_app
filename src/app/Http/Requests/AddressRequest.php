<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AddressRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     *
     */

    public function authorize()
    {
        $user = $this->user(); // 現在のユーザーを取得
        \Log::info('フォームリクエスト内のユーザー情報 (authorize): ', ['user' => $user]);
        return $user !== null; // ユーザーが取得できればtrueを返す
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function prepareForValidation()
    {
        \Log::info('prepareForValidation() でキャッシュされたユーザーID: ', ['userId' => $this->user()->id ?? null]);
    }

    public function rules()
    {
        $userId = $this->user() ? $this->user()->id : null;

        if ($userId === null) {
            \Log::error('ルール内でユーザーIDが取得できません');
        }

        \Log::info('バリデーションルール適用中: ', ['userId' => $userId]);


        return [
            'user_name' => 'required',
            // 'email' => [
            //     'nullable',
            //     'email',
            //     'max:255',
            //     Rule::unique('users', 'email')->ignore($userId),
            // ],
            'postal_code' => 'required|string|regex:/^\d{3}-\d{4}$/|max:8',
            'address' => 'required',
            'building' => 'required'
        ];
    }

    public function messages()
    {
        return [
            'user_name.required' => 'ユーザー名を入力してください',
            'postal_code.required' => '郵便番号を入力してください',
            'postal_code.string' => '郵便番号はハイフンありの8文字で入力してください',
            'postal_code.regex' => '郵便番号はハイフンありの8文字で入力してください',
            'postal_code.max' => '郵便番号はハイフンありの8文字で入力してください',
            'address.required' => '住所を入力してください',
            'building.required' => '建物名を入力してください',
        ];
    }
}
