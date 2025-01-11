@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/login.css') }}">
@endsection

@section('content')
@if(session('verified'))
<div class="alert alert-success">
    メールアドレスが確認されました。ログインをお願いします。
</div>
@endif
<div class="login__content">
    <div class="login__form">
        <div class="login-form__heading">
            <h2>ログイン</h2>
        </div>
        <form class="form" action="/login" method="post">
            @csrf
            <div class="form__group">
                <div class="form__group-title">
                    <label class="form__label--item" for="email">メールアドレス</label>
                </div>
                <div class="form__group-content">
                    <div class="form__input--text">
                        <input type="text" name="email" id="email" value="{{ old('email') }}">
                    </div>
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
            </div>
            <div class="form__button">
                <button class="form__button-submit btn" type="submit">ログインする</button>
            </div>
        </form>
        <div class="register__link">
            <a class="register__button-submit" href="/register">会員登録はこちら</a>
        </div>
    </div>
</div>
@endsection