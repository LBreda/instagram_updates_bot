<!-- Main Sidebar -->
<aside class="main-sidebar col-12 col-md-3 col-lg-2 px-0">
    <div class="main-navbar">
        <nav class="navbar align-items-stretch navbar-light bg-white flex-md-nowrap border-bottom p-0">
            <a class="navbar-brand w-100 mr-0" href="#" style="line-height: 25px;">
                <div class="d-table m-auto">
                    <span class="d-none d-md-inline ml-1">
                        <img src="{{ asset('images/logo.png') }}" style="width: 40px" alt="">
                        InstagramUpdates
                    </span>
                </div>
            </a>
            <a class="toggle-sidebar d-sm-inline d-md-none d-lg-none">
                <i class="material-icons">&#xE5C4;</i>
            </a>
        </nav>
    </div>

    <div class="nav-wrapper">
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link{{ Request::is('/') ? ' active' : '' }}" href="/">
                    <i class="fas fa-tachometer-alt"></i>
                    <span>Home</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link{{ Request::is('instagramProfiles*') ? ' active' : '' }}"
                   href="{{ route('instagramProfiles.index') }}">
                    <i class="fab fa-instagram"></i>
                    <span>Followed profiles</span>
                </a>
            </li>
        </ul>
    </div>
</aside><!-- End Main Sidebar -->