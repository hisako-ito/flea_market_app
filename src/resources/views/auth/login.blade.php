@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/login.css') }}">
@endsection

@section('content')
<div class="content__alert">
    @if (session('message'))
    <div class="alert--success">
        {{ session('message') }}
    </div>
    @endif
    @if ($errors->any())
    <div class="alert--danger">
        <ul>
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif
</div>
<div class="login__content">
    <div class="login__form">
        <div class="login-form__heading">
            <h2>ログイン</h2>
        </div>
        <form class="form" action="/login" method="post">
            @csrf
            <div class="form__group">
                <div class="form__group-title">
                    <label class="form__label--item" for="login">ユーザー名 / メールアドレス</label>
                </div>
                <div class="form__group-content">
                    <div class="form__input--text">
                        <input type="text" name="login" id="login" value="{{ old('login') }}">
                    </div>
                    <div class="form__error">
                        @error('login')
                        {{ $message }}
                        @enderror
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
                    <div class="form__error">
                        @error('password')
                        {{ $message }}
                        @enderror
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