@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/mypage_chat.css')}}">
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
<div class="chat-container">
    <aside class="chat-sidebar">

    </aside>
    <div class="chat-header">
        <h2> さんとの取引画面</h2>
        <form method="POST" action="">
            @csrf
            <button type="submit" class="btn btn-danger">取引を完了する</button>
        </form>
    </div>

    <div class="item-info">
        <img src="{{ asset($item->item_image) }}" alt="商品画像" class="item-image">
        <div class="item-details">
            <h3>{{ $item->item_name }}</h3>
            <p>¥{{ number_format($item->price) }}</p>
        </div>
    </div>

    <div class="chat-area">
        <div class="">
            <div class="message-header">
                <form method="POST" action="">
                    @csrf
                    @method('DELETE')
                    <button type="submit">削除</button>
                </form>
                <a href="">編集</a>
            </div>
            <div class="message-body">
                <img src="" alt="画像" class="message-image">
            </div>
        </div>
    </div>

    <div class="message-form">
        <form method="POST" action="" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="sender_id" value="{{ $user->id }}">
            <textarea name="content" placeholder="取引メッセージを記入してください"></textarea>
            <input type="file" name="image">
            <button type="submit">送信</button>
        </form>
    </div>
</div>
@endsection