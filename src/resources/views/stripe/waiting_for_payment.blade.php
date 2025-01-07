<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>支払い確認中</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

<body>
    <h1>支払いを確認しています...</h1>
    <p>商品名: {{ $item->item_name }}</p>
    <p>金額: {{ $item->price }}円</p>


</body>

</html>