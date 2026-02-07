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
                                    @foreach ($roles as $role)
                                        <tr>
                                            <td class="align-middle">{{ $loop->iteration }}</td>
                                            <td class="align-middle font-weight-bold">{{ $role->name }}</td>
                                            <td class="text-wrap">
                                                <div class="d-flex flex-wrap">
                                                    @foreach ($role->permissions as $permission)
                                                        <span class="badge badge-info m-1">{{ $permission->name }}</span>
                                                    @endforeach
                                                </div>
                                            </td>
                                            <td class="align-middle">
                                                <a href="{{ route('admin.roles.edit', $role->id) }}" title="Edit Role" class="btn btn-sm btn-outline-primary border-0">
                                                    <i style="font-size: 20px" class="mdi mdi-pencil-box"></i>
                                                </a>
                                                <form action="{{ route('admin.roles.destroy', $role->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this role?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger border-0" title="Delete Role">
                                                        <i style="font-size: 20px" class="mdi mdi-delete"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
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
        $('#roles-table').DataTable();
    });
</script>
@endpush