@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/purchase.css')}}">
<link rel="stylesheet" type="text/css" href="{{ asset('css/jquery.minimalect.css') }}" media="screen" />
<!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous"> -->
@endsection

@section('jquery')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script type="text/javascript" src="{{ asset('js/jquery.minimalect.js') }}"></script>
<script type="text/javascript">
    $(function() {
        $("#payment-method").minimalect();

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
        <form class="purchase-form__form" action="{{ route('purchase.post', ['item_id' => $item->id]) }}" method="post" id="stripe-form">
            @csrf
            <div class="purchase-form__action">
                <div class="item__information">
                    <div class="item__card">
                        <img src="{{ asset($item->item_image) }}" alt="商品画像">
                    </div>
                    <div class="item__content">
                        <h2 class="item-name">{{ $item->item_name }}</h2>
                        <p class="item-price item-price--action">&nbsp;{{ number_format($item->price) }}</p>
                    </div>
                </div>
                <div class="item__pay-method">
                    <h3 class="pay-method__heading">支払い方法</h3>
                    <div class="select-wrapper">
                        <select class="pay-method__select" id="payment-method" name="payment_method">
                            <option value="" disabled selected>-- 支払い方法を選択 --</option>
                            <option value="コンビニ払い">コンビニ払い</option>
                            <option value="カード払い">カード支払い</option>
                        </select>
                    </div>
                </div>
                <div class="item__deliver-address">
                    <div class="deliver-address__wrapper">
                        <h3 class=" deliver-address__heading">配送先</h3>
                        <a class="address-update__button-submit" href="/purchase/address/{{$item->id}}" target="_blank">変更する</a>
                    </div>
                    <div class="deliver-address__content">
                        <input class="postal_code" value="〒{{ $user->postal_code }}" readonly>
                        <input class="postal_code" value="{{ $user->address }}{{ $user->building }}" readonly>
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
                <input type=hidden name="user_id" value="{{ $user->id }}">
                <input type=hidden name="item_id" value="{{ $item->id }}">
                <input type=hidden name="item_id" value="{{ $item->price }}">
                <input type=hidden name="shipping_address" value="{{ $user->postal_code }}{{ $user->address }}{{ $user->building }}">
                <button class="purchase-form__btn btn" type="submit">購入する</button>
            </div>
        </form>
    </div>
</div>
@endsection