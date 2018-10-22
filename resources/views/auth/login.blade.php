@extends('layouts.app_nonav')

@section('title', 'Login')

@section('content')
    <div class="col-8">
        <div class="card">
            <div class="card-body">
                <script async src="https://telegram.org/js/telegram-widget.js?4"
                        data-telegram-login="InstagramUpdatesBot" data-size="large"
                        data-auth-url="http://igud.local/auth/telegram/callback" data-request-access="write"></script>
            </div>
        </div>
    </div>

@endsection