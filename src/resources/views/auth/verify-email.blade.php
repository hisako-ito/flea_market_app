@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/verify-email.css') }}">
@endsection

@section('content')
<div class="verify-email-page">
    <div class="verify-email-page__inner">
        <h2 class="verify-email-page__heading">メールアドレスの確認</h2>
        <p class="verify-email-page__message">ご登録いただき、ありがとうございます。<br />登録を完了するには、送信されたメール内のボタンをクリックして認証を完了させてください。</p>
    </div>
</div>
@endsection