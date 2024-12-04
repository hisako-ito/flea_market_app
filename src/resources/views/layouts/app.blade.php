<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>coachtech</title>
    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}">
    <link rel="stylesheet" href="{{ asset('css/common.css')}}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap" rel="stylesheet">
    @yield('css')
</head>

<body>
    <header class="header">
        <div class="header__inner">
            <div class="header__logo">
                <a href="/"><img src="{{ asset('/images/logo.svg') }}"  alt="coachtech" class="img-logo-icon"/></a>
            </div>
            <div class="header-utilities__search">
                <form class="header-utilities__search-form">
                @csrf
                    <input class="header-utilities__keyword-input" type="text" name="keyword" placeholder="なにをお探しですか？" value="{{request('keyword')}}">
                </form>
            </div>
            <div class="header-utilities__actions">
                <a href="/login" class="header-utilities__login-btn">ログイン</a>
                <a href="/mypage" class="header-utilities__mypage-btn">マイページ</a>
                <a href="/sell" class="header-utilities__sell-btn">出品</a>
            </div>
        </div>
    </header>
    <div class="content">
        @yield('content')
    </div>
</body>

</html>