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
        <a class="header-nav__mypage-btn" href="/mypage" >マイページ</a>
        <a class="header-nav__sell-btn" href="/sell">出品</a>
@endsection

@section('content')
    <div class="user-info">
        <div class="user-info__inner">
            <div class="user-info__image">
                <div class="user-info__image__inner">
                    <img src=""  alt="ユーザー画像">
                </div>
            </div>
            <div class="user-info__content">
                <h2 class="user-name">ユーザー名</h2>
            </div>
            <div class="user-info__edit-form">
                <a class="user-info__edit-btn" href="/mypage/profile">プロフィールを編集</a>
            </div>
        </div>
    </div>
    <div class="group-list">
        <span class="group-list_item group-list__item--Exhibited" tabindex="-1">出品した商品</span>
        <span class="group-list_item group-list__item--purchased" tabindex="-1">購入した商品</span>
    </div>
@endsection


