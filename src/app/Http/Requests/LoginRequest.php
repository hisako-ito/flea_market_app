<?php

namespace App\Http\Requests;

use Laravel\Fortify\Http\Requests\LoginRequest as FortifyLoginRequest;

class LoginRequest extends FortifyLoginRequest
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
            'login' => [
                'required',
                function ($attribute, $value, $fail) {
                    if (!filter_var($value, FILTER_VALIDATE_EMAIL) && !preg_match('/^[a-zA-Z0-9_]+$/', $value)) {
                        $fail('ユーザー名またはメールアドレスを正しく入力してください。');
                    }
                },
            ],
            'password' => 'required|min:8',
        ];
    }

    public function messages()
    {
        return [
            'login.required' => 'ユーザー名またはメールアドレスを入力してください',
            'login.email' => 'メールアドレスはメール形式で入力してください',
            'password.required' => 'パスワードを入力してください',
            'password.min:8' => 'パスワードは8文字以上で入力してください',
        ];
    }
}
