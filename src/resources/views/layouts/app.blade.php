<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>coachtech</title>
    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}">
    <link rel="stylesheet" href="{{ asset('css/common.css')}}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <script src="{{ asset('js/app.js') }}" defer></script>
    @yield('css')
    @yield('jquery')
</head>

<body>
    <header class="header">
        <div class="header__inner">
            <div class="header__logo">
                <a href="/"><img src="{{ asset('/images/logo.svg') }}" alt="coachtech" class="img-logo-icon" /></a>
            </div>
            <div class="header-nav__search">
                @yield('nav_search')
            </div>
            <div class="header-nav__actions">
                @yield('nav_actions')
            </div>
        </div>
    </header>
    <main>
        <div class="content__alert">
            @if (session('message'))
            <div class="alert--success">
                {{ session('message') }}
            </div>
            @endif
            @if ($errors->any())
            <div class="alert--danger">
                <ul>
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif
        </div>
        <div class="content">
            @yield('content')
        </div>
    </main>
</body>

<script>
    // let paymentConfirmedAlertShown = false;

    const checkPaymentStatus = () => {
        fetch(`/stripe/check-payment-status?session_id={{ session('stripe_session_id') }}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
        // .then(data => {
        //     switch (data.status) {
        //         case 'success':
        //             if (!paymentConfirmedAlertShown) {
        //                 alert('支払いが確認されました！');
        //                 paymentConfirmedAlertShown = true;
        //             }
        //             break;
        //         case 'pending':
        //             document.querySelector('.content__alert').innerHTML = '<div class="alert--info">支払い確認中...</div>';
        //             break;
        //         default:
        //             document.querySelector('.content__alert').innerHTML = '<div class="alert--danger">' + data.message + '</div>';
        //             break;
        //     }
        // })
        // .catch(error => {
        //     console.error('エラー:', error);
        //     document.querySelector('.content__alert').innerHTML = '<div class="alert--danger">エラーが発生しました。</div>';
        // });
    };

    setInterval(checkPaymentStatus, 5000);
</script>

@yield('script')

</html>