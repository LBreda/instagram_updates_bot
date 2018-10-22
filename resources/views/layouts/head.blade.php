<meta charset="utf-8">
<meta http-equiv="x-ua-compatible" content="ie=edge">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
@yield('meta')

<!-- CSRF Token -->
<meta name="csrf-token" content="{{ csrf_token() }}">

<title>IGUD - @yield('title')</title>

<script defer src="{{ asset('js_modules/@fortawesome/fontawesome-free/js/all.min.js') }}"></script>
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
<link rel="stylesheet" id="main-stylesheet" data-version="1.1.0" href="{{ asset('css/shards-dashboards.1.1.0.min.css') }}">
<link rel="stylesheet" data-version="1.1.0" href="{{ asset('css/extras.1.1.0.min.css') }}">
<link rel="stylesheet" data-version="1.1.0" href="{{ asset('js_modules/toastr/build/toastr.min.css') }}">
<script async defer src="https://buttons.github.io/buttons.js"></script>

@yield('style')