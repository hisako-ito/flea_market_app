@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/mypage_chat.css')}}">
@endsection

@section('content')
<div class="chat-container" id="app" data-should-show-modal="{{ $shouldShowModal ? 'true' : 'false' }}">
    <aside class="chat-sidebar">
        <h4 class="chat-sidebar__heading">その他の取引</h4>
        <div class="chat-sidebar__btn-container">
            @foreach ($transactions as $transaction)
            @if ($transaction->item && $transaction->item->id !== $item->id)
            <a class="chat-sidebar__link-btn" href="/mypage/items/{{$transaction->item->id}}/chat">{{ $transaction->item->item_name }}</a>
            @endif
            @endforeach
        </div>
    </aside>

    <div class="chat-header">
        <div class="chat-header__receiver-info">
            <div class="receiver-info__image">
                @if ($item->buyer && $item->buyer->id == $user->id)
                <img src="{{ asset($item->user->user_image) }}" alt="ユーザー画像">
                @elseif ($item->buyer)
                <img src="{{ asset($item->buyer->user_image) }}" alt="ユーザー画像">
                @else
                <img src="{{ asset('/images/icon.png') }}" alt="デフォルト画像">
                @endif
            </div>
            <h2>
                @if ($item->buyer && $item->buyer->id == $user->id)
                「{{ $item->user->user_name }}」さんとの取引画面
                @elseif ($item->buyer)
                「{{ $item->buyer->user_name }}」さんとの取引画面
                @else
                まだ購入者がいません
                @endif
            </h2>
        </div>
        @if ($item->buyer && $item->buyer->id == $user->id && $mainTransaction && $mainTransaction->status === "pending")
        <button type="button" class="btn complete-btn" id="completeTransactionBtn">取引を完了する</button>
        @elseif ($item->buyer && $item->buyer->id == $user->id && $mainTransaction->status === "completed")
        <button type="button" class="btn completed-btn">取引は完了しました</button>
        @else
        @endif
    </div>

    <div class="item-info">
        <img src="{{ asset($item->item_image) }}" alt="商品画像" class="item-image">
        <div class="item-details">
            <div class="item-details__inner">
                <h3 class="item-name">{{ $item->item_name }}</h3>
                <p class="item-price">{{ number_format($item->price) }}</p>
            </div>
        </div>
    </div>

    <div class="message-area">
        @foreach ($messages as $message)
        @php
        $isOwnMessage = $message->sender_id === $user->id;
        @endphp
        <div class="message-container {{ $isOwnMessage ? 'my-message' : 'other-message' }}">
            <div class="message-sender-info {{ $isOwnMessage ? 'my-info' : 'receiver-info' }}">
                <div class="sender-info__image">
                    <img src="{{ asset($message->sender->user_image) }}" alt="ユーザー画像">
                </div>
                <p class="sender-info__name">{{ $message->sender->user_name }}</p>
            </div>
            <div class="message-content">
                @if ($message->sender_id === $user->id)
                <form class="message-update-form" method="POST" action="/messages/{{$message->id}}">
                    @csrf
                    @method('PATCH')
                    <textarea name="content[{{ $message->id }}]" class="message-update-form__textarea">{{ old("content.{$message->id}", $message->content) }}</textarea>
                    @if ($errors->getBag('edit_' . $message->id)->has("content.{$message->id}"))
                    <p class="form__error" style="color: red;">
                        {{ $errors->getBag('edit_' . $message->id)->first("content.{$message->id}") }}
                    </p>
                    @endif
                    <div class="message-buttons">
                        <button class="message-update-form__btn" type="submit">編集</button>
                </form>
                <form class="message-delete-form" method="POST" action="/messages/{{$message->id}}">
                    @csrf
                    @method('DELETE')
                    <input type="hidden" name="id" value="{{ $message->id }}">
                    <button class="message-delete-form__btn" type="submit">削除</button>
            </div>
            </form>
            @else
            <textarea class="message-update-form__textarea" value="" readonly>{{ $message->content }}</textarea>
            @endif
            @if ($message->msg_image)
            <div class="message-image">
                <img src="{{ asset($message->msg_image) }}" alt="画像" class="message-image">
            </div>
            @endif
        </div>
        @endforeach
    </div>
</div>

<div class="message-form-area">
    <div class="message-form-area__inner">
        <div class="form__error">
            @error('content')
            {{ $message }}
            @enderror
        </div>
        <div class="form__error">
            @error('msg_image')
            {{ $message }}
            @enderror
        </div>
        @if ($item->buyer && $mainTransaction->status === "pending")
        <div class="message-form">
            <form id="chat-form" data-user-id="{{ $user->id }}" data-item-id="{{ $item->id }}" class="message-form__form" method="POST" action="/mypage/items/{{$item->id}}/chat" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="sender_id" value="{{ $user->id }}">
                <textarea id="chat-input" class="message-form__textarea" name="content" placeholder="取引メッセージを記入してください">{{ old('content', '') }}</textarea>
                <div class="message-form__btn-container">
                    <input type="file" name="msg_image" id="fileInput" accept="image/png, image/jpeg" hidden>
                    <label for="fileInput" class="file-input-label">画像を追加</label>
                    <span id="fileNameDisplay" class="file-name-display"></span>
                    <button class="message-form__btn" type="submit"><i class="fa-regular fa-paper-plane" style="color: #5F5F5F; font-size: 32px;"></i></button>
                </div>
            </form>
        </div>
        @endif
    </div>
</div>

<div id="ratingModal" class="modal-overlay" style="display: none;">
    <div class="modal">
        <div class="modal__heading">
            <h2>取引が完了しました。</h2>
        </div>
        <div class="rating-form">
            <p class="rating-form__message">今回の取引相手はどうでしたか？</p>
            <form class="rating-form__form" method="POST" action="/ratings/{{$item->id}}">
                @csrf
                <input type="hidden" name="rating" value="0">
                <div class="star-rating">
                    @for ($i = 5; $i >= 1; $i--)
                    <input type="radio" name="rating" value="{{ $i }}" id="star{{ $i }}" hidden>
                    <label class="star" for="star{{ $i }}"><i class="fas fa-star"></i></label>
                    @endfor
                </div>
                <div class="form__button">
                    <button type="submit" class="send-btn">送信する</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
@section('script')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const fileInput = document.getElementById('fileInput');
        const fileNameDisplay = document.getElementById('fileNameDisplay');
        if (fileInput && fileNameDisplay) {
            fileInput.addEventListener('change', function(event) {
                const fileName = event.target.files[0]?.name || '選択されていません';
                fileNameDisplay.textContent = fileName;
            });
        }

        const completeBtn = document.getElementById('completeTransactionBtn');
        const modal = document.getElementById('ratingModal');
        const shouldShowModalAttr = document.getElementById('app')?.dataset.shouldShowModal;
        const shouldShowModal = shouldShowModalAttr === 'true';
        if (shouldShowModal && modal) {
            modal.style.display = 'flex';
        }
        if (completeBtn) {
            completeBtn.addEventListener('click', function() {
                modal.style.display = 'flex';
            });
        }

        const form = document.getElementById('chat-form');
        const textarea = document.getElementById('chat-input');

        if (form && textarea) {
            const itemId = form.dataset.itemId;
            const userId = form.dataset.userId;
            const storageKey = `chat_draft_${userId}_${itemId}`;

            const saved = sessionStorage.getItem(storageKey);
            if (saved) {
                textarea.value = saved;
            }

            textarea.addEventListener('input', () => {
                sessionStorage.setItem(storageKey, textarea.value);
            });

            form.addEventListener('submit', () => {
                sessionStorage.removeItem(storageKey);
            });
        }
    });
</script>
@endsection