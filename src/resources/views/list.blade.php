@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/list.css')}}">
@endsection

@section('nav_search')
<form class="header-nav__search-form" action="/" method="get">
    @csrf
    <input class="header-nav__keyword-input" type="search" name="keyword" placeholder="なにをお探しですか？" value="{{ $keyword ?? '' }}">
    <input type="hidden" name="page" value="{{ $page }}">
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
<div class="tabs">
    <a href="{{ route('item.list', ['page' => 'recommend', 'keyword' => $keyword]) }}"
        class="{{ $page === 'recommend' ? 'active-tab' : '' }}">おすすめ</a>
    <a href="{{ route('item.list', ['page' => 'mylist', 'keyword' => $keyword]) }}"
        class="{{ $page === 'mylist' ? 'active-tab' : '' }}">マイリスト</a>
</div>

<div class="items-list">
    @if($page === 'recommend' && $items->isNotEmpty())
    @foreach ($items as $item)
    <div class="item__card">
        <div class="card__img">
            @if ($item->is_sold)
            <div class="sold-label">
                <span class="sold-font">SOLD</span>
            </div>
            @endif
            <a href="/item/{{$item->id}}" class="item-link"></a>
            <img src="{{ asset($item->item_image) }}" alt="商品画像">
        </div>
        <div class="card__detail">
            <p>{{$item->item_name}}</p>
        </div>
    </div>
    @endforeach
    @elseif ($page === 'mylist' && $favorites->isNotEmpty())
    @foreach ($favorites as $favorite)
    @if ($favorite->item) {{-- itemが存在する場合のみ表示 --}}
    <div class="item__card">
        <div class="card__img">
            @if ($favorite->item->is_sold)
            <div class="sold-label">
                <span class="sold-font">SOLD</span>
            </div>
            @endif
            <a href="/item/{{$favorite->item->id}}" class="item-link"></a>
            <img src="{{ asset($favorite->item->item_image) }}" alt="商品画像">
        </div>
        <div class="card__detail">
            <p>{{$favorite->item->item_name}}</p>
        </div>
    </div>
    @endif
    @endforeach
    @else
    <p class="no-results"></p>
    @endif
</div>
@endsection