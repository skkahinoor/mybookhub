@extends('admin.layout.layout')

@section('content')
<div class="main-panel">
    <div class="content-wrapper">
        <div class="row">
            <div class="col-md-12 grid-margin">
                <div class="row">
                    <div class="col-12 col-xl-8 mb-4 mb-xl-0">
                        <h3 class="font-weight-bold">Edit Staff Member</h3>
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
                        <h4 class="card-title">Edit Staff Information</h4>

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

                        <form method="POST" action="{{ route('admin.staff.update', $staff->id) }}" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            {{-- Name --}}
                            <div class="form-group">
                                <label>Full Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                       value="{{ old('name', $staff->name) }}" placeholder="Enter staff name" required>
                                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            {{-- Email --}}
                            <div class="form-group">
                                <label>Email Address <span class="text-danger">*</span></label>
                                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                                       value="{{ old('email', $staff->email) }}" placeholder="Enter email address" required>
                                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            {{-- Mobile --}}
                            <div class="form-group">
                                <label>Mobile Number <span class="text-danger">*</span></label>
                                <input type="number" name="mobile" class="form-control @error('mobile') is-invalid @enderror"
                                       value="{{ old('mobile', $staff->phone) }}" placeholder="Enter mobile number" required>
                                @error('mobile')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            {{-- Assign Role --}}
                            <div class="form-group">
                                <label>Assign Role <span class="text-danger">*</span></label>
                                <select name="role_id" class="form-control @error('role_id') is-invalid @enderror" required id="role-select">
                                    <option value="">-- Select Role --</option>
                                    @forelse ($roles as $role)
                                        <option value="{{ $role->id }}"
                                            {{ (old('role_id', $currentRoleId) == $role->id) ? 'selected' : '' }}>
                                            {{ ucfirst($role->name) }}
                                        </option>
                                    @empty
                                        <option value="" disabled>No custom roles found.</option>
                                    @endforelse
                                </select>
                                @error('role_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                <small class="text-muted">
                                    Permissions are inherited from the selected role.
                                    <a href="{{ route('admin.roles.index') }}" target="_blank">Manage Roles →</a>
                                </small>
                            </div>

                            {{-- New Password (optional on edit) --}}
                            <div class="form-group">
                                <label>New Password <small class="text-muted">(leave blank to keep current)</small></label>
                                <input type="password" name="password" class="form-control @error('password') is-invalid @enderror"
                                       placeholder="Enter new password" minlength="6">
                                @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            {{-- Confirm Password --}}
                            <div class="form-group">
                                <label>Confirm New Password</label>
                                <input type="password" name="password_confirmation" class="form-control"
                                       placeholder="Confirm new password" minlength="6">
                            </div>

                            {{-- Profile Photo --}}
                            <div class="form-group">
                                <label>Profile Photo</label>
                                @if (!empty($staff->profile_image))
                                    <div class="mb-2">
                                        <img src="{{ asset('admin/images/photos/' . $staff->profile_image) }}"
                                             alt="Current Photo" style="width:80px;height:80px;object-fit:cover;border-radius:50%;border:2px solid #dee2e6;">
                                        <small class="text-muted d-block">Current photo</small>
                                    </div>
                                @endif
                                <input type="file" name="admin_image" class="form-control" accept="image/*">
                                <small class="text-muted">Upload new photo to replace existing one.</small>
                            </div>

                            <button type="submit" class="btn btn-primary mr-2">
                                <i class="mdi mdi-content-save"></i> Update Staff
                            </button>
                            <a href="{{ route('admin.staff.index') }}" class="btn btn-secondary">Cancel</a>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Info Card --}}
            <div class="col-lg-4">
                <div class="card border-warning">
                    <div class="card-header bg-warning text-white">
                        <h6 class="mb-0"><i class="mdi mdi-account-circle"></i> Staff Details</h6>
                    </div>
                    <div class="card-body">
                        <p><strong>Name:</strong> {{ $staff->name }}</p>
                        <p><strong>Email:</strong> {{ $staff->email }}</p>
                        <p><strong>Current Role:</strong>
                            @if ($staff->roles->first())
                                <span class="badge badge-primary">{{ ucfirst($staff->roles->first()->name) }}</span>
                            @else
                                <span class="badge badge-secondary">No Role</span>
                            @endif
                        </p>
                        <p><strong>Status:</strong>
                            @if ($staff->status == 1)
                                <span class="badge badge-success">Active</span>
                            @else
                                <span class="badge badge-danger">Inactive</span>
                            @endif
                        </p>
                        <p><strong>Joined:</strong> {{ $staff->created_at ? $staff->created_at->format('d M Y') : 'N/A' }}</p>
                        <hr>
                        <p class="text-muted small">
                            Staff uses <strong>Admin login URL</strong> to sign in and sees the dashboard filtered by their role permissions.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('admin.layout.footer')
</div>
@endsection
