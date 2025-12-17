@extends('admin.layout.layout')

@section('content')
    <div class="main-panel">
        <div class="content-wrapper">
            <div class="row">
                <div class="col-12 grid-margin">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">{{ isset($salesExecutive) ? 'Edit' : 'Add' }} Sales Executive</h4>

                            @if ($errors->any())
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            @endif

                            <form method="POST"
                                action="{{ url('admin/add-edit-sales-executive' . (isset($salesExecutive) ? '/' . $salesExecutive->id : '')) }}">
                                @csrf

                                <div class="form-group">
                                    <label>Name <span class="text-danger">*</span></label>
                                    <input type="text" name="name" class="form-control"
                                        value="{{ old('name', $salesExecutive->name ?? '') }}" required>
                                </div>

                                <div class="form-group">
                                    <label>Email <span class="text-danger">*</span></label>
                                    <input type="email" name="email" class="form-control"
                                        value="{{ old('email', $salesExecutive->email ?? '') }}" required>
                                </div>

                                <div class="form-group">
                                    <label>Phone <span class="text-danger">*</span></label>
                                    <input type="text" name="phone" class="form-control"
                                        value="{{ old('phone', $salesExecutive->phone ?? '') }}" required>
                                </div>

                                @if (!isset($salesExecutive))
                                    <div class="form-group">
                                        <label>Password <span class="text-danger">*</span></label>
                                        <input type="password" name="password" class="form-control" required>
                                    </div>
                                    <div class="form-group">
                                        <label>Confirm Password <span class="text-danger">*</span></label>
                                        <input type="password" name="password_confirmation" class="form-control" required>
                                    </div>
                                @else
                                    <div class="form-group">
                                        <label>New Password</label>
                                        <input type="password" name="password" class="form-control"
                                            placeholder="Leave blank to keep current">
                                    </div>
                                @endif

                                <button type="submit"
                                    class="btn btn-primary">{{ isset($salesExecutive) ? 'Update' : 'Create' }}</button>
                                <a href="{{ route('salesexecutives.index') }}" class="btn btn-light">Cancel</a>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
