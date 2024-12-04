@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/list.css')}}">
@endsection

@section('content')
    <div class="group-list">
        <span class="group-list_item group-list__item--recommend" tabindex="-1">おすすめ</span>
        <span class="group-list_item group-list__item--favorite" tabindex="-1">マイリスト</span>
    </div>

    <div class="items-list">
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
    </div>
@endsection