@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/sell.css') }}">
@endsection

@section('jquery')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script type="text/javascript" src="{{ asset('js/jquery.minimalect.js') }}"></script>
<link rel="stylesheet" type="text/css" href="{{ asset('css/jquery.minimalect.css') }}" media="screen" />
<script type="text/javascript">
    $(function() {
        $("#exhibition-form__select").minimalect();
    });
</script>
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
<div class="exhibition-form">
    <div class="exhibition-form__inner">
        <div class="exhibition-form__heading">
            <h2>商品の出品</h2>
        </div>
        <form class="form" action="/sell" method="post" enctype="multipart/form-data">
            @csrf
            <div class="form__group">
                <div class="form__group-title">
                    <label class="form__label--item">商品画像</label>
                </div>
                <div class="item-image" id="imagePreview">
                    <span class="close-btn" id="close-btn">×</span>
                    <img src="#" alt="商品画像" id="previewImage" style="display: none;">
                </div>
                <div class="form__input--image">
                    <label for="image-input" class="file-input-label">画像を選択する</label>
                    <input type="file" name="item_image" id="image-input" accept="image/*">
                </div>
            </div>
            <div class="exhibition-item-info__heading">
                <h3>商品の詳細</h3>
            </div>
            <div class="form__group">
                <div class="form__group-title">
                    <label class="form__label--item">カテゴリー</label>
                </div>
                <div class="form__group-content">
                    <div class="category-container">
                        @foreach ($categories as $category)
                        <input type="checkbox" id="category-{{ $category->id }}" name="categories[]" value="{{ $category->id }}" {{ is_array(old('category_item')) && in_array($category->id, old('category_item')) ? 'checked' : '' }}>
                        <label class="category-item" for="category-{{ $category->id }}">{{ $category->name }}</label>
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="form__group">
                <div class="form__group-title">
                    <label class="form__label--item" for="condition">商品の状態</label>
                </div>
                <div class="form__group-content">
                    <select class="exhibition-form__select" id="exhibition-form__select" name="condition">
                        <option value="" disabled selected>-- 支払い方法を選択 --</option>
                        <option value="1" {{
                old('condition') == 1 ? 'selected' : '' }}>良好</option>
                        <option value="2" {{
                old('condition') == 2 ? 'selected' : '' }}>目立った傷や汚れなし</option>
                        <option value="3" {{
                old('condition') == 3 ? 'selected' : '' }}>やや傷や汚れあり</option>
                        <option value="4" {{
                old('condition') == 4 ? 'selected' : '' }}>状態が悪い</option>
                    </select>
                </div>
            </div>
            <div class="exhibition-item-info__heading">
                <h3>商品名と説明</h3>
            </div>
            <div class="form__group">
                <div class="form__group-title">
                    <label class="form__label--item" for="item_name">商品名</label>
                </div>
                <div class="form__group-content">
                    <div class="form__input--text">
                        <input type="text" name="item_name" id="item_name" value="{{ old('item_name') }}">
                    </div>
                </div>
            </div>
            <div class="form__group">
                <div class="form__group-title">
                    <label class="form__label--item" for="brand">ブランド名</label>
                </div>
                <div class="form__group-content">
                    <div class="form__input--text">
                        <input type="text" name="brand" id="brand" value="{{ old('brand') }}">
                    </div>
                </div>
            </div>
            <div class="form__group">
                <div class="form__group-title">
                    <label class="form__label--item" for="description">商品の説明</label>
                </div>
                <div class="form__group-content">
                    <div class="form__input--textarea">
                        <textarea type="text" name="description" id="description" cols="30" rows="10">{{ old('description') }}</textarea>
                    </div>
                </div>
            </div>
            <div class="form__group">
                <div class="form__group-title">
                    <label class="form__label--item" for="price">販売価格</label>
                </div>
                <div class="form__group-content">
                    <div class="form__input--text">
                        <input type="text" class="price-input" name="price" id="price" value="{{ old('price') }}" placeholder="￥">
                    </div>
                </div>
            </div>
            <input type="hidden" name="user_id" value="{{ $user->id }}">
            <div class="form__button">
                <button class="form__button-submit btn" type="submit">出品する</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('script')
<script>
    const imageInput = document.getElementById("image-input");
    const previewImage = document.getElementById("previewImage");
    const imagePreview = document.getElementById("imagePreview");
    const closeBtn = document.getElementById("close-btn");

    imageInput.addEventListener("change", function() {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();

            reader.onload = function(e) {
                imagePreview.style.display = "block";
                previewImage.src = e.target.result;
                previewImage.style.display = "block";
                closeBtn.style.display = "block";
            };

            reader.readAsDataURL(file);
        }
    });

    closeBtn.addEventListener("click", function() {
        previewImage.src = "#";
        previewImage.style.display = "none";
        closeBtn.style.display = "none";
        imagePreview.style.display = "none";
        imageInput.value = "";
    });
</script>
@endsection