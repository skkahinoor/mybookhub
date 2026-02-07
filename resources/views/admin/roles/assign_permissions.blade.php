@extends('admin.layout.layout')

@section('content')
<div class="main-panel">
    <div class="content-wrapper">
        <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Assign Permissions to Roles</h4>
                        <p class="card-description">Select a role tab and update its permissions.</p>

                        @if (Session::has('success_message'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <strong>Success:</strong> {{ Session::get('success_message') }}
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        @endif

                        <ul class="nav nav-tabs" id="roleTabs" role="tablist">
                            @foreach($roles as $role)
                                <li class="nav-item">
                                    <a class="nav-link {{ $loop->first ? 'active' : '' }}" id="role-{{ $role->id }}-tab" data-toggle="tab" href="#role-{{ $role->id }}" role="tab" aria-controls="role-{{ $role->id }}" aria-selected="{{ $loop->first ? 'true' : 'false' }}">
                                        {{ ucfirst($role->name) }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                        
                        <div class="tab-content border border-top-0 p-3" id="roleTabsContent">
                            @foreach($roles as $role)
                                <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}" id="role-{{ $role->id }}" role="tabpanel" aria-labelledby="role-{{ $role->id }}-tab">
                                    <form action="{{ route('admin.roles.update_permissions') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="role_id" value="{{ $role->id }}">
                                        
                                        <div class="row">
                                            @foreach($permissions->groupBy('module') as $module => $modulePermissions)
                                                <div class="col-md-12 mt-3">
                                                    <h5 class="text-primary">{{ ucfirst($module ?? 'General') }}</h5>
                                                    <div class="row">
                                                        @foreach($modulePermissions as $permission)
                                                            <div class="col-md-3">
                                                                <div class="form-check">
                                                                    <label class="form-check-label">
                                                                        <input type="checkbox" class="form-check-input" name="permissions[]" value="{{ $permission->id }}"
                                                                            {{ $role->hasPermissionTo($permission->name) ? 'checked' : '' }}>
                                                                        {{ $permission->name }}
                                                                        <i class="input-helper"></i>
                                                                    </label>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                    <hr>
                                                </div>
                                            @endforeach
                                        </div>

                                        <div class="mt-3">
                                            <button type="submit" class="btn btn-primary mr-2">Update Permissions for {{ ucfirst($role->name) }}</button>
                                        </div>
                                    </form>
                                </div>
                            @endforeach
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
