@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/profile_edit.css') }}">
@endsection

@section('nav_search')
    <form class="header-nav__search-form" action="/" method="get">
    @csrf
        <input class="header-nav__keyword-input" type="search" name="keyword" placeholder="なにをお探しですか？" value="{{ $keyword ?? '' }}">
    </form>
@endsection

@section('nav_actions')
    @if (Auth::check())
        <form class="logout-form" action="/logout" method="post">
        @csrf
            <button class="header-nav__logout-btn" type="submit">ログアウト</button>
        </form>
    @else
        <a class="header-nav__login-btn" href="/login">ログイン</a>
    @endif
        <a class="header-nav__mypage-btn" href="/mypage" >マイページ</a>
        <a class="header-nav__sell-btn" href="/sell">出品</a>
@endsection

@section('content')
<div class="profile-edit__content">
    <div class="profile-edit__form">
        <div class="profile-edit-form__heading">
            <h2>プロフィール設定</h2>
        </div>
        <form class="form" action="{{ route('user-profile-information.update') }}" method="post" enctype="multipart/form-data">
        @csrf
        @method('PATCH')
            <div class="form__group">
                <div class="form__input--image">
                    <input type="file" name="user_image">
                </div>
                <div class="form__error">
                    @error('user_image')
                    {{ $message }}
                    @enderror
                </div>
            </div>
            <div class="form__group">
                <div class="form__group-title">
                    <label class="form__label--item" for="user_name">ユーザー名</label>
                </div>
                <div class="form__group-content">
                    <div class="form__input--text">
                        <input type="text" name="user_name" id="name" value="{{ old('user_name', auth()->user()->user_name) }}">
                    </div>
                    <div class="form__error">
                        @error('user_name')
                        {{ $message }}
                        @enderror
                    </div>
                </div>
            </div>
            <div class="form__group">
                <div class="form__group-title">
                    <label class="form__label--item" for="postal_code">郵便番号</label>
                </div>
                <div class="form__group-content">
                    <div class="form__input--text">
                        <input type="text" name="postal_code" id="postal_code" value="{{ old('postal_code', auth()->user()->postal_code) }}">
                    </div>
                    <div class="form__error">
                        @error('postal_code')
                        {{ $message }}
                        @enderror
                    </div>
                </div>
            </div>
            <div class="form__group">
                <div class="form__group-title">
                    <label class="form__label--item" for="address">住所</label>
                </div>
                <div class="form__group-content">
                    <div class="form__input--text">
                        <input type="text" name="address" id="address" value="{{ old('address', auth()->user()->address) }}">
                    </div>
                    <div class="form__error">
                        @error('address')
                        {{ $message }}
                        @enderror
                    </div>
                </div>
            </div>
            <div class="form__group">
                <div class="form__group-title">
                    <label class="form__label--item" for="building">建物名</label>
                </div>
                <div class="form__group-content">
                    <div class="form__input--text">
                        <input type="text" name="building" id="building" value="{{ old('building', auth()->user()->building) }}">
                    </div>
                    <div class="form__error">
                        @error('building')
                        {{ $message }}
                        @enderror
                    </div>
                </div>
            </div>
            <div class="form__button">
                <button class="form__button-submit btn" type="submit">更新する</button>
            </div>
        </form>
    </div>
</div>
@endsection
