@extends('admin.layout.layout')

@section('content')
<div class="main-panel">
    <div class="content-wrapper">
        <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h4 class="card-title mb-0">Roles Management</h4>
                            <div>
                                <a href="{{ route('admin.roles.assign_permissions') }}" class="btn btn-info mr-2">
                                    <i class="mdi mdi-account-key"></i> Assign Permissions
                                </a>
                                <a href="{{ route('admin.roles.create') }}" class="btn btn-primary">
                                    <i class="mdi mdi-plus"></i> Add Role
                                </a>
                            </div>
                        </div>

                        @if (Session::has('success_message'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <strong>Success:</strong> {{ Session::get('success_message') }}
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        @endif

                        @if (Session::has('error_message'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <strong>Error:</strong> {{ Session::get('error_message') }}
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>  
                            </div>
                        @endif

                        <div class="table-responsive pt-3">
                            <table class="table table-hover" id="roles-table">
                                <thead class="bg-light">
                                    <tr>
                                        <th style="width: 5%;">ID</th>
                                        <th style="width: 15%;">Name</th>
                                        <th style="width: 65%;">Permissions</th>
                                        <th style="width: 15%;">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('#roles-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ url()->current() }}",
            columns: [
                {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                {data: 'name', name: 'name'},
                {data: 'permissions', name: 'permissions', orderable: false, searchable: false},
                {data: 'actions', name: 'actions', orderable: false, searchable: false},
            ]
        });
    });
</script>
@endpush