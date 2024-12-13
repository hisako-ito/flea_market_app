<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddressRequest extends FormRequest
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
        return [
            'user_name' => 'required',
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
