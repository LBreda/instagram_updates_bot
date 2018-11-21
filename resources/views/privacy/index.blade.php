@extends('layouts.app')

@section('title', 'Privacy Policy')

@section('content')
    <div class="row">
        <div class="card mb-4">
            <div class="card-body">
                <div class="col-12">@markdown($privacy_data)</div>
            </div>
        </div>
    </div>
@endsection