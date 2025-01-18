@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/cancel.css')}}">
@endsection

@section('nav_search')
<form class="header-nav__search-form" action="/" method="get">
    @csrf
    <input class="header-nav__keyword-input" type="search" name="keyword" placeholder="なにをお探しですか？" value="{{ $keyword ?? '' }}">
    <input type="hidden" name="tab" value="{{ $tab }}">
</form>
@endsection

@section('nav_actions')
<div class="purchase-cancel-page">
    <div class="purchase-cancel-page__inner">
        <h2 class="purchase-cancel-page__heading">購入がキャンセルされました。</h2>
        <p class="purchase-cancel-page__message">もしご購入ご希望の場合は、再度購入手続きをお願いいたします。</p>
    </div>
</div>
@endsection