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
                        @if($success)
                            <p>Your account is successfully deleted</p>
                        @else
                            <p>ERROR: We couldn't delete your account. Try later or contact us.</p>
                        @endif
                        <a href="{{ route('home') }}" class="btn btn-primary">Home</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection