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
<a class="header-nav__mypage-btn" href="/mypage">マイページ</a>
<a class="header-nav__sell-btn" href="/sell">出品</a>
@endsection

@section('content')
<div class="profile-edit__content">
    <div class="profile-edit__form">
        <div class="profile-edit-form__heading">
            <h2>プロフィール設定</h2>
        </div>
        <form class="form" action="{{ route('user-profile-information.register') }}" method="post" enctype="multipart/form-data">
            @csrf
            @method('PATCH')
            <div class="form__group">
                <div class="image-upload-container">
                    <div class="user-info__image" id="imagePreview">
                        <img src="{{ $user ?? '' }}" alt="ユーザー画像" id="previewImage">
                    </div>
                    <div class="form__input--image">
                        <input type="file" name="user_image" id="fileInput" accept="image/*" hidden>
                        <label for="fileInput" class="file-input-label">画像を選択する</label>
                    </div>
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
                        <input type="text" name="user_name" id="user_name" value="{{ old('user_name', auth()->check() ? (auth()->user()->user_name ?? '') : '') }}">
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
                        <input type="text" name="postal_code" id="postal_code" value="{{ old('postal_code', auth()->check() ? (auth()->user()->postal_code ?? '') : '') }}">
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
                        <input type="text" name="address" id="address" value="{{ old('address', auth()->check() ? (auth()->user()->address ?? '') : '') }}">
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
                        <input type="text" name="building" id="building" value="{{ old('building', auth()->check() ? (auth()->user()->building ?? '') : '') }}">
                    </div>
                    <div class="form__error">
                        @error('building')
                        {{ $message }}
                        @enderror
                    </div>
                </div>
                <input type="hidden" id="email" name="email" value="{{ old('email', auth()->user()->email) }}">
            </div>
            <div class="form__button">
                <button class="form__button-submit btn" type="submit">更新する</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('script')
<script>
    const fileInput = document.getElementById('fileInput');
    const previewImage = document.getElementById('previewImage');
    const imagePreview = document.getElementById('imagePreview');

    fileInput.addEventListener('change', function() {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                previewImage.src = e.target.result;
                imagePreview.style.backgroundColor = 'transparent';
            };
            reader.readAsDataURL(file);
        }
    });
</script>
@endsection