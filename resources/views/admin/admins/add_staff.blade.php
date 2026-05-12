@extends('admin.layout.layout')

@section('content')
<div class="main-panel">
    <div class="content-wrapper">
        <div class="row">
            <div class="col-md-12 grid-margin">
                <div class="row">
                    <div class="col-12 col-xl-8 mb-4 mb-xl-0">
                        <h3 class="font-weight-bold">Add New Staff Member</h3>
                        <h6 class="font-weight-normal mb-0">
                            <a href="{{ route('admin.staff.index') }}">← Back to Staff List</a>
                        </h6>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-8 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Staff Information</h4>

                        @if ($errors->any())
                            <div class="alert alert-danger alert-dismissible fade show">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                                <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                            </div>
                        @endif

                        @if (Session::has('error_message'))
                            <div class="alert alert-danger alert-dismissible fade show">
                                <strong>Error:</strong> {{ Session::get('error_message') }}
                                <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('admin.staff.store') }}" enctype="multipart/form-data">
                            @csrf

                            {{-- Name --}}
                            <div class="form-group">
                                <label>Full Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                       value="{{ old('name') }}" placeholder="Enter staff name" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Email --}}
                            <div class="form-group">
                                <label>Email Address <span class="text-danger">*</span></label>
                                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                                       value="{{ old('email') }}" placeholder="Enter email address" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Mobile --}}
                            <div class="form-group">
                                <label>Mobile Number <span class="text-danger">*</span></label>
                                <input type="number" name="mobile" class="form-control @error('mobile') is-invalid @enderror"
                                       value="{{ old('mobile') }}" placeholder="Enter mobile number" required>
                                @error('mobile')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Assign Role --}}
                            <div class="form-group">
                                <label>Assign Role <span class="text-danger">*</span></label>
                                <select name="role_id" class="form-control @error('role_id') is-invalid @enderror" required id="role-select">
                                    <option value="">-- Select Role --</option>
                                    @forelse ($roles as $role)
                                        <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>
                                            {{ ucfirst($role->name) }}
                                        </option>
                                    @empty
                                        <option value="" disabled>No custom roles found. Please create a role first.</option>
                                    @endforelse
                                </select>
                                @error('role_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">
                                    Permissions are inherited from the selected role.
                                    <a href="{{ route('admin.roles.index') }}" target="_blank">Manage Roles →</a>
                                </small>
                            </div>

                            {{-- Role Permissions Preview --}}
                            <div id="role-permissions-preview" class="mb-3" style="display:none;">
                                <label class="font-weight-bold">Permissions granted by selected role:</label>
                                <div id="permissions-list" class="d-flex flex-wrap gap-1"></div>
                            </div>

                            {{-- Password --}}
                            <div class="form-group">
                                <label>Password <span class="text-danger">*</span></label>
                                <input type="password" name="password" class="form-control @error('password') is-invalid @enderror"
                                       placeholder="Enter password" required minlength="6">
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Minimum 6 characters</small>
                            </div>

                            {{-- Confirm Password --}}
                            <div class="form-group">
                                <label>Confirm Password <span class="text-danger">*</span></label>
                                <input type="password" name="password_confirmation" class="form-control"
                                       placeholder="Confirm password" required minlength="6">
                            </div>

                            {{-- Photo --}}
                            <div class="form-group">
                                <label>Profile Photo</label>
                                <input type="file" name="admin_image" class="form-control" accept="image/*">
                                <small class="text-muted">Supported: JPG, PNG, GIF</small>
                            </div>

                            {{-- Status --}}
                            <div class="form-group">
                                <div class="form-check form-check-flat form-check-primary">
                                    <label class="form-check-label">
                                        <input type="checkbox" name="status" class="form-check-input" value="1" checked>
                                        Active
                                    </label>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-success mr-2">
                                <i class="mdi mdi-account-plus"></i> Add Staff Member
                            </button>
                            <a href="{{ route('admin.staff.index') }}" class="btn btn-secondary">Cancel</a>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Login Info Card --}}
            <div class="col-lg-4">
                <div class="card border-info">
                    <div class="card-header bg-info text-white">
                        <h6 class="mb-0"><i class="mdi mdi-information-outline"></i> Staff Login Info</h6>
                    </div>
                    <div class="card-body">
                        <p class="text-muted mb-2">Staff members will login using:</p>
                        <ul class="list-unstyled">
                            <li><i class="mdi mdi-link text-info"></i> <strong>URL:</strong>
                                <code>{{ url('admin/login') }}</code>
                            </li>
                            <li class="mt-2"><i class="mdi mdi-eye text-info"></i> <strong>Dashboard:</strong>
                                Same admin dashboard, but menu items are filtered by their role permissions.
                            </li>
                        </ul>
                        <hr>
                        <p class="text-muted small mb-0">
                            <i class="mdi mdi-shield-key text-warning"></i>
                            To assign specific permissions, go to
                            <a href="{{ route('admin.roles.index') }}">Role & Permission</a> and configure the role.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('admin.layout.footer')
</div>

<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script>
$(document).ready(function () {
    // Role permission preview
    $('#role-select').on('change', function () {
        var roleId = $(this).val();
        if (!roleId) {
            $('#role-permissions-preview').hide();
            return;
        }
        $.ajax({
            url: '{{ url("admin/roles") }}/' + roleId + '/edit',
            type: 'GET',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            success: function (html) {
                // Parse checked permissions from the edit page
                var parser   = new DOMParser();
                var doc      = parser.parseFromString(html, 'text/html');
                var checkboxes = doc.querySelectorAll('input[name="permissions[]"]:checked');
                var list = '';
                checkboxes.forEach(function (cb) {
                    var label = cb.closest('label') ? cb.closest('label').textContent.trim() : cb.value;
                    list += '<span class="badge badge-info m-1">' + label + '</span>';
                });
                if (list) {
                    $('#permissions-list').html(list);
                    $('#role-permissions-preview').show();
                } else {
                    $('#permissions-list').html('<span class="text-muted">No permissions assigned to this role yet.</span>');
                    $('#role-permissions-preview').show();
                }
            },
            error: function () {
                $('#permissions-list').html('<span class="text-muted">Could not load permissions preview.</span>');
                $('#role-permissions-preview').show();
            }
        });
    });
});
</script>
@endsection
