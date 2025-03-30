<h2>{{ $user->user_name }} さん、いつもご利用いただきありがとうございます。</h2>

<p>{{ $buyer->user_name }} さんが商品「{{ $item->item_name }}」の取引を完了しました。</p>
<p>取引チャット画面で{{ $buyer->user_name }} さんのへの評価をお願いいたします。</p>

<p>
    <a href="{{ url('/mypage/items/' . $item->id . '/chat') }}" style="display: inline-block; background-color: #3490dc; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;">
        取引チャット画面を開く
    </a>
</p>

<p style="margin-top: 20px;">※ ログインが必要です。ログイン後、取引画面が表示されます。</p>