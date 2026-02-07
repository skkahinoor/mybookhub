@extends('admin.layout.layout')

@section('content')
<div class="main-panel">
    <div class="content-wrapper">
        <div class="row">
            <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Add Role</h4>
                        <p class="card-description">Enter role name and select permissions.</p>
                        
                        <form class="forms-sample" action="{{ route('admin.roles.store') }}" method="POST">
                            @csrf
                            <div class="form-group">
                                <label for="name">Role Name</label>
                                <input type="text" class="form-control" id="name" name="name" placeholder="Enter Role Name" required>
                                @error('name')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label>Permissions</label>
                                <div class="row">
                                    @foreach ($permissions->groupBy('module') as $module => $modulePermissions)
                                        <div class="col-md-4 mb-3">
                                            <h6 class="font-weight-bold">{{ ucfirst($module ?: 'General') }}</h6>
                                            @foreach ($modulePermissions as $permission)
                                                <div class="form-check">
                                                    <label class="form-check-label">
                                                        <input type="checkbox" class="form-check-input" name="permissions[]" value="{{ $permission->id }}">
                                                        {{ $permission->name }}
                                                    </label>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endforeach
                                    
                                    @if($permissions->isEmpty())
                                        <div class="col-12">
                                            <p class="text-muted">No permissions found. Please add permissions first.</p>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary mr-2">Submit</button>
                            <a href="{{ route('admin.roles.index') }}" class="btn btn-light">Cancel</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
