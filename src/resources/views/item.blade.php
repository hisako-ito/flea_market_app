@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/item.css')}}">
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
<div class="detail-content">
    <div class="detail-content__inner">
        <div class="content__img">
            <div class="img__card">
                @if ($item->is_sold)
                <div class="sold-label">
                    <span class="sold-font">SOLD</span>
                </div>
                @endif
                <img src="{{ asset($item->item_image) }}" alt="商品画像">
            </div>
        </div>
        <div class="content__form">
            <div class="form__inner">
                <form action="/purchase/{{$item->id}}" method="get">
                    @csrf
                    <h2 class="item-name">{{$item->item_name}}</h2>
                    <p class="item-brand-name">{{$item->brand}}</p>
                    <p class="item-price">{{ number_format($item->price) }}</p>
                    <div class="count-content">
                        <div class="like_count">
                            <button type="submit" class="like_btn"><img src="{{ asset('/images/星アイコン8.png') }}" class="img-like_icon" />
                                <p class="like_count__number">カウント</p>
                            </button>
                        </div>
                        <div class="comment_count">
                            <img src="{{ asset('/images/ふきだしのアイコン.png') }}" class="img-comment_icon" />
                            <p class="comment_count__number">カウント</p>
                        </div>
                    </div>
                    <button class="button-purchase btn" type="submit">購入手続きへ</button>
                </form>
                <div class="item-description">
                    <h3 class="description__heading">商品説明</h3>
                    <p class="description__description">{{$item->description}}</p>
                </div>
                <div class="item-information">
                    <h3 class="information__heading">商品の情報</h3>
                    <table class="item-information__table">
                        <tr class="table__group">
                            <th class="information__category">カテゴリー</th>
                            <td class="category__list">
                                @foreach($categories as $category)
                                <input type="checkbox" {{ $item->categories->contains('id', $category->id) ? 'checked' : '' }}>
                                <label>{{ $category->name }}</label>
                                @endforeach
                            </td>
                        </tr>
                        <tr class="table__group">
                            <th class="information__condition">商品の状態</th>
                            <td class="condition__content">{{$item->checkCondition()}}</td>
                        </tr>
                    </table>
                </div>
                <h3 class="comment__heading">コメント(カウント)</h3>
                <div class="comment-user">comment-user</div>
                <div class="comment-content">comment-content</div>
                <h4 class="item-comment">商品へのコメント</h4>
                <form class="comment__form" action="/item/{item_id}" method="post">
                    @csrf
                    <textarea class="comment__form_textarea" cols="90" rows="7"></textarea>
                    <button type="submit" class="button-comment btn">コメントを送信する</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection