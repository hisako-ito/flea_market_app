@extends('layouts.app')

@section('css')
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
                <img src="{{ asset($user->user_image) }}" alt="ユーザー画像">
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
    <a href="{{ route('mypage', ['tab' => 'sell']) }}"
        class="{{ $tab === 'sell' ? 'active-tab' : '' }}">出品した商品</a>
    <a href="{{ route('mypage', ['tab' => 'buy']) }}"
        class="{{ $tab === 'buy' ? 'active-tab' : '' }}">購入した商品</a>
    <a href="{{ route('mypage', ['tab' => 'trade']) }}"
        class="{{ $tab === 'trade' ? 'active-tab' : '' }}">取引中の商品<span class="total-unread-msg">{{ $unreadMessages->sum('unread_count') }}</span></a>
</div>

<div class="items-list">
    @if($tab === 'buy' && isset($items) && $items->isNotEmpty())
    @foreach ($items as $item)
    <div class="item__card">
        <div class="card__img">
            @if ($item->is_sold)
            <div class="sold-label">
                <span class="sold-font">SOLD</span>
            </div>
            @endif
            <a href="/mypage/items/{{$item->id}}/chat" class="product-link"></a>
            <img src="{{ asset($item->item_image) }}" alt="商品画像">
        </div>
        <div class="card__detail">
            <p>{{$item->item_name}}</p>
        </div>
    </div>
    @endforeach
    @elseif ($tab === 'sell' && isset($items) && $items->isNotEmpty())
    @foreach ($items as $item)
    <div class="item__card">
        <div class="card__img">
            @if ($item->is_sold)
            <div class="sold-label">
                <span class="sold-font">SOLD</span>
            </div>
            @endif
            <a href="/mypage/items/{{$item->id}}/chat" class="product-link"></a>
            <img src="{{ asset($item->item_image) }}" alt="商品画像">
        </div>
        <div class="card__detail">
            <p>{{$item->item_name}}</p>
        </div>
    </div>
    @endforeach
    @elseif ($tab === 'trade' && isset($items) && $items->isNotEmpty())
    @foreach ($items as $item)
    <div class="item__card">
        <div class="card__img">
            @if ($item->is_sold)
            <div class="sold-label">
                <span class="sold-font">SOLD</span>
            </div>
            @endif
            @if (isset($unreadMessages[$item->id]))
            <div class="notification-badge">
                {{ $unreadMessages[$item->id]->unread_count }}
            </div>
            @endif
            <a href="/mypage/items/{{$item->id}}/chat" class="product-link"></a>
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