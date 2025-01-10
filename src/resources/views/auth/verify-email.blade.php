@extends('layouts.app')

@section('content')
<div class="container">
    <h1>メールアドレスの確認</h1>
    <p>登録を完了するには、送信されたメール内のリンクをクリックしてください。</p>

    @if (session('status') == 'verification-link-sent')
    <p class="text-success">確認リンクが再送信されました。</p>
    @endif

    <form method="POST" action="{{ route('verification.send') }}">
        @csrf
        <button type="submit" class="btn btn-primary">確認リンクを再送信</button>
    </form>
</div>
@endsection