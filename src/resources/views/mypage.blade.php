@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/list.css')}}">
<link rel="stylesheet" href="{{ asset('css/mypage.css')}}">
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
<div class="user-info">
    <div class="user-info__inner">
        <div class="user-info__image">
            <div class="user-info__image__inner">
                <img src="{{ $user->user_image }}" alt="ユーザー画像">
            </div>
        </div>
        <div class="user-info__content">
            <h2 class="user-name">{{ $user->user_name }}</h2>
        </div>
        <div class="user-info__edit-form">
            <a class="user-info__edit-btn" href="/mypage/profile">プロフィールを編集</a>
        </div>
    </div>
</div>
<div class="tabs">
    <a href="{{ route('mypage', ['page' => 'sell']) }}"
        class="{{ $page === 'sell' ? 'active-tab' : '' }}">出品した商品</a>
    <a href="{{ route('mypage', ['page' => 'buy']) }}"
        class="{{ $page === 'buy' ? 'active-tab' : '' }}">購入した商品</a>
</div>

<div class="items-list">
    @if($page === 'buy' && isset($orders) && $orders->isNotEmpty())
    @foreach ($orders as $order)
    <div class="item__card">
        <div class="card__img">
            @if ($order->item->is_sold)
            <div class="sold-label">
                <span class="sold-font">SOLD</span>
            </div>
            @endif
            <a href="/item/{{$order->item->id}}" class="product-link"></a>
            <img src="{{ asset($order->item->item_image) }}" alt="商品画像">
        </div>
        <div class="card__detail">
            <p>{{$order->item->item_name}}</p>
        </div>
    </div>
    @endforeach
    @elseif ($page === 'sell' && isset($items) && $items->isNotEmpty())
    @foreach ($items as $item)
    <div class="item__card">
        <div class="card__img">
            @if ($item->is_sold)
            <div class="sold-label">
                <span class="sold-font">SOLD</span>
            </div>
            @endif
            <a href="/item/{{$item->id}}" class="product-link"></a>
            <img src="{{ asset($item->item_image) }}" alt="商品画像">
        </div>
        <div class="card__detail">
            <p>{{$item->item_name}}</p>
        </div>
    </div>
    @endforeach
    @else
    <p class="no-results">該当する商品がありません</p>
    @endif
</div>
@endsection