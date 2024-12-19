@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/purchase.css')}}">
<link rel="stylesheet" type="text/css" href="{{ asset('css/jquery.minimalect.css') }}" media="screen" />
@endsection

@section('jquery')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script type="text/javascript" src="{{ asset('js/jquery.minimalect.js') }}"></script>
<script type="text/javascript">
    $(function() {
        // Minimalectを有効化
        $("#payment-method").minimalect();

        // Minimalectのイベントをリッスンして値を更新
        $("#payment-method").on("change", function() {
            const selectedValue = $(this).val();
            $("#selected-payment").text(selectedValue);
        });
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
<div class="purchase-form">
    <div class="purchase-form__inner">
        <form class="purchase-form__form" action="/purchase/{{$item->id}}" method="post">
            @csrf
            <div class="purchase-form__action">
                <div class="item__information">
                    <div class="item__card">
                        <img src="{{ asset($item->image) }}" alt="商品画像">
                    </div>
                    <div class="item__content">
                        <h2 class="item-name">{{$item->name}}</h2>
                        <p class="item-price item-price--action">&nbsp;{{ number_format($item->price) }}</p>
                    </div>
                </div>
                <div class="item__pay-method">
                    <h3 class="pay-method__heading">支払い方法</h3>
                    <div class="select-wrapper">
                        <select class="pay-method__select" id="payment-method">
                            <option value="コンビニ払い">コンビニ払い</option>
                            <option value="カード支払い">カード支払い</option>
                        </select>
                    </div>
                </div>
                <div class="item__deliver-address">
                    <div class="deliver-address__wrapper">
                        <h3 class=" deliver-address__heading">配送先</h3>
                        <a class="address-update__button-submit" href="/purchase/address/{{$item->id}}" target="_blank">変更する</a>
                    </div>
                    <div class="deliver-address__content">
                        <p class="postal_code">{{ $user->postal_code }}</p>
                        <p class="address">{{ $user->address }}{{ $user->building }}</p>
                    </div>
                </div>
            </div>
            <div class="purchase-form__confirm">
                <table class="confirm-table" border="1" rules="rows">
                    <tr class="confirm-table__row">
                        <td class="confirm-table__label">商品代金</td>
                        <td class="confirm-table__data item-price">{{ number_format($item->price) }}</td>
                    </tr>
                    <tr class="confirm-table__row">
                        <td class="confirm-table__label">支払い方法</td>
                        <td class="confirm-table__data" id="selected-payment">コンビニ払い</td>
                    </tr>
                </table>
                <button class="purchase-form__btn btn" type="submit">購入する</button>
            </div>
        </form>
    </div>
</div>
@endsection