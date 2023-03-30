<title>@yield('title', __('app.menu_title_unknownpage')) - {{ config('app.name') }}</title>

<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

<!-- Theming and icons. -->
<link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">
<link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
<link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}">
<link rel="manifest" href="{{ asset('site.webmanifest') }}">
<link rel="mask-icon" href="{{ asset('safari-pinned-tab.svg') }}" color="#2196f3">
<meta name="msapplication-TileColor" content="#2196f3">
<meta name="theme-color" content="#2196f3">

<!-- Vendor style. -->
<link rel="stylesheet" href="{{ asset('vendors/animate.css/animate.min.css') }}?v={{ Version::commit() }}">
<link rel="stylesheet" href="{{ asset('vendors/flatpickr/flatpickr.min.css') }}?v={{ Version::commit() }}">
<link rel="stylesheet" href="{{ asset('vendors/jquery-scrollbar/jquery.scrollbar.css') }}?v={{ Version::commit() }}">
<link rel="stylesheet" href="{{ asset('vendors/material-design-iconic-font/css/material-design-iconic-font.min.css') }}?v={{ Version::commit() }}">
<link rel="stylesheet" href="{{ asset('vendors/nouislider/nouislider.min.css') }}?v={{ Version::commit() }}">
<link rel="stylesheet" href="{{ asset('vendors/select2/css/select2.min.css') }}?v={{ Version::commit() }}">

<!-- App style. -->
<link rel="stylesheet" href="{{ asset('css/app.min.css') }}?v={{ Version::commit() }}">
<link rel="stylesheet" href="{{ asset('css/style.css') }}?v={{ Version::commit() }}">
