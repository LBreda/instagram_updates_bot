<!doctype html>
<html class="no-js h-100" lang="en">
<head>
    @section('head')
        @include('layouts.head')
    @show
</head>
<body class="h-100">

<div class="container">
    <div class="row">
        <main class="main-content col-lg-12 d-flex justify-content-center">
            @yield('content')
        </main>
    </div>
</div>

@section('modals')
    {{-- @include('layouts.modals') --}}
@show
@section('scripts')
    @include('layouts.scripts.main')
    {{-- @include('layouts.scripts.notifications') --}}
@show
@yield('core-scripts')
</body>
</html>