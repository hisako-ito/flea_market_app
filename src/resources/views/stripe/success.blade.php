@extends('layouts.app')

@section('content')
<div class="container">
    <h2>決済が完了しました</h2>
    <p>ご購入ありがとうございました。</p>
    <a href="{{ url('/') }}" class="btn btn-primary">トップに戻る</a>
</div>
@endsection