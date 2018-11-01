@extends('layouts.app')

@section('title', 'Followed profiles')

@section('style')
    <style>
        .table td {
            vertical-align: middle;
        }

        .img-avatar {
            border: lightgray 1px solid;
            width: 32px;
            height: 32px;
        }
    </style>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card card-small mb-4">
                <div class="card-body p-0 pb-3 table-responsive">
                    <table class="table mb-0">
                        <thead class="bg-light">
                        <tr>
                            <th scope="col" style="width: 64px" class="text-center"><i
                                        class="fas fa-user-circle mr-2"></i></th>
                            <th scope="col" class="border-0">Username</th>
                            <th scope="col" class="border-0 d-none d-sm-table-cell" style="width: 100%">Name</th>
                            <th scope="col" class="border-0 d-none d-sm-table-cell">Status</th>
                            <th scope="col" class="border-0">
                                <button class="btn btn-primary add-ig" type="button" data-toggle="modal"
                                        data-target="#addIgModal"><i class="fas fa-plus"></i></button>
                            </th>
                        </tr>
                        </thead>
                        <tbody id="profiles_list">
                        @include('instagram_profiles.list')
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    @parent
    <script>
        $('#removeIgModal').on('show.bs.modal', function (event) {
            var $button = $(event.relatedTarget);
            $('#removeIgName').text($button.data('name'));
            $('#removeIgForm').attr('action', $button.data('action'))
        });

        function sendAndReload(e) {
            e.preventDefault();
            e.stopPropagation();

            var form = e.target;
            var r = new XMLHttpRequest();
            r.open(form.getAttribute('method'), form.getAttribute('action'));
            r.onload = function () {
                var response = JSON.parse(r.response);
                if (r.status >= 200 && r.status < 400) {
                    var profilesList = document.getElementById('profiles_list');
                    toastr.success(response.messages.join(', '))

                    var listReq = new XMLHttpRequest();
                    listReq.open('GET', '{!! route('instagramProfiles.index', array_merge($request->all(), ['list_only' => 1])) !!}', true);
                    listReq.onload = function () {
                        if (listReq.status >= 200 && listReq.status < 400) {
                            profilesList.innerHTML = listReq.responseText;
                        } else {
                            toastr.error("Error reloading the profiles list")
                        }
                    };
                    listReq.onerror = function () {
                        toastr.error("Error reloading the profiles list")
                    };
                    listReq.send();
                } else {
                    toastr.error(response.messages.join(', '))
                }
            };
            r.onerror = function () {
                toastr.error("Error adding instagram profile")
            };
            r.send(new FormData(form));
            form.reset();
            $('#' + form.getAttribute('data-ref-modal')).modal('hide');
        }

        document.getElementById('addIgForm').addEventListener('submit', sendAndReload);
        document.getElementById('removeIgForm').addEventListener('submit', sendAndReload);
    </script>
@endsection

@section('modals')
    <div class="modal fade" id="addIgModal">
        <div class="modal-dialog" role="document">
            <form method="POST" action="{{ route('instagramProfiles.store') }}" id="addIgForm" data-ref-modal="addIgModal">
                @csrf
                @method('POST')
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Add followed profile</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                    aria-hidden="true">&times;</span></button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="profile">Instagram profile URL</label>
                            <input required id="profile" type="url" class="form-control" name="profile">
                        </div>
                        <p>Note: You can also add new profiles pasting the URL in the bot's chat.</p>
                    </div>
                    <div class="modal-footer">
                        <button id="addIgCancel" type="button" class="btn btn-default" data-dismiss="modal">
                            Undo
                        </button>
                        <button id="addIgConfirm" type="submit" class="btn btn-primary">Save</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div class="modal fade" id="removeIgModal">
        <div class="modal-dialog" role="document">
            <form method="POST" action="" id="removeIgForm" data-ref-modal="removeIgModal">
                @csrf
                @method('DELETE')
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Remove followed profile</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                    aria-hidden="true">&times;</span></button>
                    </div>
                    <div class="modal-body">
                        <p>Are you sure you want to stop following the profile <strong>@<span id="removeIgName"></span></strong>?
                        </p>
                    </div>
                    <div class="modal-footer">
                        <button id="removeIgCancel" type="button" class="btn btn-default" data-dismiss="modal">
                            Undo
                        </button>
                        <button id="removeIgConfirm" type="submit" class="btn btn-danger">Delete</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection