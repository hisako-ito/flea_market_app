@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/verify-email.css') }}">
@endsection

@section('content')
<div class="verify-email__content">
    <div class="verify-email__heading">
        <h2>メールアドレスの確認</h2>
    </div>
    <div>
        <p>ご登録いただき、ありがとうございます。</p>
        <p>登録を完了するには、送信されたメール内のボタンをクリックして認証を完了させてください。</p>
    </div>
</div>
@endsection