<h2>{{ $user->user_name }} さん、いつもご利用いただきありがとうございます。</h2>

<p>{{ $buyer->user_name }} さんが商品「{{ $item->item_name }}」の取引を完了しました。</p>
<p>トップページ → マイページ → 出品した商品 → 該当商品をクリック → 取引チャット画面に遷移すると、評価画面が表示されるので、{{ $buyer->user_name }} さんのへの評価をお願いいたします。</p>

<p>
    <a href="{{ url('/login') }}" style="display: inline-block; background-color: #3490dc; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;">
        ログイン画面
    </a>
</p>