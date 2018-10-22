@extends('layouts.app')

@section('title', 'Home')

@section('content')
    <div class="row">
        <div class="col-3">
            <div class="stats-small stats-small--1 card card-small">
                <div class="card-body p-0 d-flex">
                    <div class="d-flex flex-column m-auto">
                        <div class="stats-small__data text-center">
                            <span class="stats-small__label text-uppercase">Followed profiles</span>
                            <h6 class="stats-small__value count my-3">{{ Auth::user()->followedProfiles->count() }}</h6>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection