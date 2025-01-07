<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>決済キャンセル</title>
</head>

<body>
    <h1>決済がキャンセルされました。</h1>
    <p>再度決済をお試しください。</p>

    <!-- 支払い待ちページへのリンク -->
    <a href="{{ route('stripe.waiting_for_payment', ['item_id' => $item->id]) }}">支払い待ちページへ</a>
</body>

</html>