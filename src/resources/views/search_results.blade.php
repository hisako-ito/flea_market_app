@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/list.css')}}">
@endsection

@section('search')
    <form class="header-nav__search-form" action="/" method="get">
    @csrf
        <input class="header-nav__keyword-input" type="search" name="keyword" placeholder="なにをお探しですか？" value="{{ $keyword ?? '' }}">
    </form>
@endsection

@section('actions')
        @if (Auth::check())
            <form class="logout-form" action="/logout" method="post">
            @csrf
                <button class="header-nav__logout-btn" value="ログアウト">
            </form>
        @else
            <a class="header-nav__login-btn" href="/login">ログイン</a>
        @endif
            <a class="header-nav__mypage-btn" href="/mypage" >マイページ</a>
            <a class="header-nav__sell-btn" href="/sell">出品</a>
@endsection

@section('content')
    <div class="group-list">
        <span class="group-list_item group-list__item--recommend" tabindex="-1">おすすめ</span>
        <span class="group-list_item group-list__item--favorite" tabindex="-1">マイリスト</span>
    </div>

    <div class="items-list">
        @if(isset($items) && $items->isNotEmpty())
            @foreach ($items as $item)
                <div class="item__card">
                    <div class="card__img">
                        <a href="/item/{{$item->id}}" class="product-link"></a>
                        <img src="{{ asset($item->image) }}"  alt="商品画像">
                    </div>
                    <div class="card__detail">
                        <p>{{$item->name}}</p>
                    </div>
                </div>
            @endforeach
        @else
            <p>表示する商品がありません。</p>
        @endif
    </div>
@endsection