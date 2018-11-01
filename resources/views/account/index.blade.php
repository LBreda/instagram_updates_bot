@extends('layouts.app')

@section('title', 'Profile')

@section('content')
    <div class="row">
        <div class="col-lg-4">
            <div class="card card-small mb-4 pt-3">
                <div class="card-header border-bottom text-center">
                    <div class="mb-3 mx-auto">
                        <img class="rounded-circle" src="{{ asset('svg/empty_avatar.svg') }}" alt="User Avatar"
                             width="110">
                    </div>
                    <h4>{{ $user->name }}</h4>
                </div>
                <ul class="list-group list-group-flush text-center">
                    <li class="list-group-item p-4">
                        {{ $user->followedProfiles->count() }} followed profiles
                    </li>
                    <li class="list-group-item p-4">
                        <a href="{{ route('account.download') }}" class="btn btn-primary"><i
                                    class="fas fa-download"></i> Download my data</a>
                        <a href="#deleteProfile" data-toggle="modal" class="btn btn-danger"><i class="fas fa-trash"></i>
                            Delete my account</a>
                    </li>
                </ul>
            </div>
        </div>
        <div class="col-lg-8">
            <div class="card card-small mb4">
                <div class="card-header border-bottom">
                    <h6 class="m-0">Account details from Telegram</h6>
                </div>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item p-3">
                        <div class="row">
                            <div class="col">
                                <form>
                                    <div class="form-row">
                                        <div class="form-group col-md-6">
                                            <label for="firstName">First name</label>
                                            <p id="firstName"
                                               class="data-display-box">{{ $user->first_name ?: 'Not available' }}</p>
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label for="lastName">Last name</label>
                                            <p id="lastName"
                                               class="data-display-box">{{ $user->last_name ?: 'Not available' }}</p>
                                        </div>
                                    </div>
                                    <div class="form-row">
                                        <div class="form-group col-md-6">
                                            <label for="telegramID">Telegram ID</label>
                                            <p id="telegramID" class="data-display-box">{{ $user->telegram_id }}</p>
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label for="telegramUser">Telegram username</label>
                                            <p id="telegramUser"
                                               class="data-display-box">{!! $user->username ? "<a href='https://telegram.me/{$user->username}'>@{$user->username}</a>" : 'Not available' !!}</p>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
@endsection

@section('modals')
    <div class="modal fade" id="deleteProfile">
        <div class="modal-dialog" role="document">
            <form method="POST" action="{{ route('account.delete') }}" id="removeIgForm" data-ref-modal="removeIgModal">
                @csrf
                @method('DELETE')
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Delete profile</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                    aria-hidden="true">&times;</span></button>
                    </div>
                    <div class="modal-body">
                        <p>Are you sure you want to delete your profile?</p>
                        <p class="text-danger">WARNING: this operation is not reversible. </p>
                    </div>
                    <div class="modal-footer">
                        <button id="deleteCancel" type="button" class="btn btn-default" data-dismiss="modal">
                            No
                        </button>
                        <button id="deleteConfirm" type="submit" class="btn btn-danger">Delete</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection