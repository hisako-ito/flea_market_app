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
            <h2 class="item-name">{{$item->item_name}}</h2>
            <p class="item-brand-name">{{$item->brand}}</p>
            <p class="item-price">{{ number_format($item->price) }}</p>
            <div class="count-content">
                <div class="favorite_count">
                    <form id="favorite-form-{{ $item->id }}" action="{{ route('favorite', $item->id) }}" data-item-id="{{ $item->id }}" method="post">
                        @csrf
                        <button type="submit" class="favorite_btn" aria-label="お気に入り切り替え">
                            @if (Auth::check() && $item->isFavoritedBy(Auth::user()))
                            <i class="fas fa-star filled"></i>
                            @else
                            <i class="far fa-star empty"></i>
                            @endif
                        </button>
                    </form>
                    <p class="favorite_count__number" id="favorite-count-{{ $item->id }}">{{ $item->favorites->count() }}</p>
                </div>
                <div class="comment_count">
                    <div class='comment_wrapper'>
                        <i class="fa-regular fa-comment"></i>
                        <p class="comment_count__number">{{ $commentsCount }}</p>
                    </div>
                </div>
            </div>
            <form action="/purchase/{{$item->id}}" method="get">
                @csrf
                <div class="purchase-procedure-btn">
                    @if ($item->is_sold)
                    <a class="button-sold btn">売り切れました</a>
                    @else
                    <a class="button-purchase btn" href="/purchase/{{$item->id}}">購入手続きへ</a>
                    @endif
                </div>
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
            <h3 class="comment__heading">コメント({{ $commentsCount }})</h3>
            @foreach ($item->comments as $comment)
            <div class="comment-user_info">
                <div class="comment-user_image">
                    <img src="{{ asset($comment->user->user_image) }}" alt="ユーザー画像">
                </div>
                <p class="comment-user_name">{{ $comment->user->user_name }}</p>
            </div>
            <div class="comment-content">{{ $comment->content }}</div>
            @endforeach
            <h4 class="item-comment">商品へのコメント</h4>
            <form class="comment__form" action="{{ route('items.comments.store', $item->id) }}" method="post">
                @csrf
                <textarea class="comment__form_textarea" name="content" cols="90" rows="7"></textarea>
                <button type="submit" class="button-comment btn">コメントを送信する</button>
            </form>
        </div>
    </div>
</div>
</div>
@endsection

@section('script')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const favoriteForms = document.querySelectorAll('form[id^="favorite-form-"]');

        favoriteForms.forEach(form => {
            form.addEventListener('submit', async function(e) {
                e.preventDefault();

                const button = form.querySelector('button');
                const icon = button.querySelector('i');

                try {
                    const response = await fetch(form.action, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Accept': 'application/json',
                        },
                    });

                    if (response.status === 401) {
                        window.location.href = '/login';
                        return;
                    }


                    if (response.ok) {

                        const data = await response.json();
                        console.log(data);
                        const favoriteCount = document.getElementById(`favorite-count-${form.dataset.itemId}`);

                        if (icon.classList.contains('fas')) {
                            icon.classList.remove('fas', 'filled');
                            icon.classList.add('far', 'empty');
                        } else {
                            icon.classList.remove('far', 'empty');
                            icon.classList.add('fas', 'filled');
                        }

                        if (favoriteCount) {
                            favoriteCount.textContent = data.favorite_count;
                        }
                    } else {
                        console.error('Failed to toggle favorite status:', response.status);
                    }
                } catch (error) {
                    console.error('An error occurred:', error);
                }
            });
        });
    });
</script>
@endsection