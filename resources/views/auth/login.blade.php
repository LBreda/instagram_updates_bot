@extends('layouts.app_nonav')

@section('title', 'Login')

@section('content')
    <div class="col-12">
        <div class="row">
            <div class="col-12 col-lg-8 ml-auto mr-auto mt-auto text-center mb-3">
                <h1>InstagramUpdates</h1>
            </div>
        </div>
        <div class="row">
            <div class="col-12 col-lg-8 m-auto">
                <div class="card">
                    <div class="card-body text-center">
                        <p>InstagramUpdates is a Telegram bot to get updates from your favorite public Instagram
                            profiles</p>
                        <script async src="https://telegram.org/js/telegram-widget.js?4"
                                data-telegram-login="{{ env('TELEGRAM_BOT_NAME') }}" data-size="large"
                                data-auth-url="{{ env('APP_URL') }}/auth/telegram/callback"
                                data-request-access="write"></script>
                        <p> By logging in you accept the <a data-toggle="modal" href="#privacyPolicy">privacy policy</a>.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('modals')
    <div class="modal fade" id="privacyPolicy">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Privacy Policy</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    @markdown($privacy_data)
                </div>
            </div>
        </div>
@endsection