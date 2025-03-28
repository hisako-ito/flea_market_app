@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/register.css') }}">
@endsection

@section('content')
<div class="register__content">
    <div class="register__form">
        <div class="register-form__heading">
            <h2>会員登録</h2>
        </div>
        <form class="form" action="/register" method="post">
            @csrf
            <div class="form__group">
                <div class="form__group-title">
                    <label class="form__label--item" for="user_name">ユーザー名</label>
                </div>
                <div class="form__group-content">
                    <div class="form__input--text">
                        <input type="text" name="user_name" id="user_name" value="{{ old('user_name') }}">
                    </div>
                </div>
                <div class="form__error">
                    @error('user_name')
                    {{ $message }}
                    @enderror
                </div>
            </div>
            <div class="form__group">
                <div class="form__group-title">
                    <label class="form__label--item" for="email">メールアドレス</label>
                </div>
                <div class="form__group-content">
                    <div class="form__input--text">
                        <input type="email" name="email" id="email" value="{{ old('email') }}">
                    </div>
                </div>
                <div class="form__error">
                    @error('email')
                    {{ $message }}
                    @enderror
                </div>
            </div>
            <div class="form__group">
                <div class="form__group-title">
                    <label class="form__label--item" for="password">パスワード</label>
                </div>
                <div class="form__group-content">
                    <div class="form__input--text">
                        <input type="password" name="password" id="password">
                    </div>
                </div>
                <div class="form__error">
                    @error('password')
                    {{ $message }}
                    @enderror
                </div>
            </div>
            <div class="form__group">
                <div class="form__group-title">
                    <label class="form__label--item" for="password_confirmation">確認用パスワード</label>
                </div>
                <div class="form__group-content">
                    <div class="form__input--text">
                        <input type="password" name="password_confirmation" id="password_confirmation">
                    </div>
                </div>
                <div class="form__error">
                    @error('password_confirmation')
                    {{ $message }}
                    @enderror
                </div>
            </div>
            <div class="form__button">
                <button class="form__button-submit btn" type="submit">登録する</button>
            </div>
        </form>
        <div class="login__link">
            <a class="login__button-submit" href="/login">ログインはこちら</a>
        </div>
    </div>
</div>
@endsection