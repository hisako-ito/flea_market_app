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
                <div class="form__error">
                    @error('item_image')
                    {{ $message }}
                    @enderror
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
                        <input type="checkbox" id="category-{{ $category->id }}" value="{{$category->id}}" name="category_item">
                        <label class="category-item" for="category-{{ $category->id }}">{{ $category->name }}</label>
                        @endforeach
                    </div>
                    <div class="form__error">
                        @error('category_item')
                        {{ $message }}
                        @enderror
                    </div>
                </div>
            </div>
            <div class="form__group">
                <div class="form__group-title">
                    <label class="form__label--item" for="condition">商品の状態</label>
                </div>
                <div class="form__group-content">
                    <select class="exhibition-form__select" id="exhibition-form__select">
                        <option value="1">良好</option>
                        <option value="2">目立った傷や汚れなし</option>
                        <option value="3">やや傷や汚れあり</option>
                        <option value="4">状態が悪い</option>
                    </select>
                    <div class="form__error">
                        @error('condition')
                        {{ $message }}
                        @enderror
                    </div>
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
                    <div class="form__error">
                        @error('item_name')
                        {{ $message }}
                        @enderror
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
                    <div class="form__error">
                        @error('description')
                        {{ $message }}
                        @enderror
                    </div>
                </div>
            </div>
            <div class="form__group">
                <div class="form__group-title">
                    <label class="form__label--item" for="price">販売価格</label>
                </div>
                <div class="form__group-content">
                    <div class="form__input--text">
                        <input type="text" name="price" id="price" value="￥{{ old('price') }}">
                    </div>
                    <div class="form__error">
                        @error('price')
                        {{ $message }}
                        @enderror
                    </div>
                </div>
            </div>
            <div class="form__button">
                <button class="form__button-submit btn" type="submit">出品する</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('script')
<script>
    // 要素を取得
    const imageInput = document.getElementById("image-input");
    const previewImage = document.getElementById("previewImage");
    const imagePreview = document.getElementById("imagePreview");
    const closeBtn = document.getElementById("close-btn");

    // 画像選択時の処理
    imageInput.addEventListener("change", function() {
        const file = this.files[0]; // 選択されたファイル
        if (file) {
            const reader = new FileReader();

            // ファイル読み込みが完了した時の処理
            reader.onload = function(e) {
                imagePreview.style.display = "block";
                previewImage.src = e.target.result; // 画像のプレビュー
                previewImage.style.display = "block"; // 画像を表示
                closeBtn.style.display = "block"; // バツ印を表示
            };

            reader.readAsDataURL(file); // ファイルをデータURLとして読み込む
        }
    });

    // 削除ボタン（バツ印）クリック時の処理
    closeBtn.addEventListener("click", function() {
        previewImage.src = "#"; // 画像をリセット
        previewImage.style.display = "none"; // 画像を非表示
        closeBtn.style.display = "none"; // バツ印を非表示
        imagePreview.style.display = "none";
        imageInput.value = ""; // ファイル入力をクリア
    });
</script>
@endsection