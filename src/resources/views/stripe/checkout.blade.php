<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="utf-8" />
    <title>Accept a payment</title>
    <meta name="description" content="A demo of a payment on Stripe" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="stylesheet" href="stripe.css" />
    <script src="https://js.stripe.com/v3/"></script>
    <script src="stripe.js" defer></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
</head>

<body>
    <div class="container">
        <div class="card w-50 mt-5 m-auto">
            <div class="card-body">
                <form action="{{ route('stripe.checkout', ['item_id' => $item->id]) }}" method="POST" id="stripe-form">
                    @csrf
                    <div class="mb-3 row">
                        <label for="card-holder-name" class="col-sm-2 col-form-label">名前</label>
                        <div class="col-sm-10">
                            <input id="card-holder-name" type="text" class="form-control">
                        </div>
                    </div>

                    <!-- ストライプ要素のプレースホルダ -->
                    <div id="card-element" class="my-4"></div>

                    <button id="card-button" data-secret="{{ $intent->client_secret }}" class="btn btn-primary">
                        支払いをする
                    </button>
                </form>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz" crossorigin="anonymous"></script>
</body>

</html>