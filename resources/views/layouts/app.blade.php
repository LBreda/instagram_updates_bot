<!doctype html>
<html class="no-js h-100" lang="en">
<head>
    @section('head')
        @include('layouts.head')
    @show
</head>
<body class="h-100">

<div class="container-fluid">
    <div class="row">
        @include('layouts.navigation')
        <main class="main-content col-lg-10 col-md-9 col-sm-12 p-0 offset-lg-2 offset-md-3">
            @include('layouts.headerbar')

            <div class="main-content-container container-fluid px-4">
                <!-- Page Header -->
                <div class="page-header row no-gutters py-4">
                    <div class="col-12 col-sm-4 text-center text-sm-left mb-0">
                        <h3 class="page-title">@yield('title')</h3>
                    </div>
                </div>
                <!-- End Page Header -->

                @yield('content')
            </div>
            <footer class="main-footer d-flex p-2 px-3 bg-white border-top">
                <ul class="nav">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('privacy') }}">Privacy Policy</a>
                    </li>
                </ul>
                <span class="copyright ml-auto my-auto mr-2">Copyright © 2018
              <a href="https://lbreda.com" rel="nofollow">Lorenzo Breda</a>
            </span>
            </footer>
        </main>
    </div>
</div>

@section('modals')
@show
@section('scripts')
    @include('layouts.scripts.main')
@show
@yield('core-scripts')
</body>
</html>