<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">

<head>
    @include('partials.head')
    <style type="text/css">
        body {
            width: 100%;
            height: 100%;

        }

        .login {
            background-size: cover;
            background-repeat: no-repeat;
            background-position: bottom center;
            background-image: url("https://picsum.photos/2560/1440");
            padding-bottom: 28rem;
        }
    </style>
</head>

<body data-ma-theme="indigo">
    <div class="login">
        @yield('content')
    </div>

    @include('partials.scripts')
</body>

</html>
