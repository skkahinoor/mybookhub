@extends('admin.layout.layout')

@section('content')
<div class="main-panel">
    <div class="content-wrapper">
        <div class="row">
            <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Edit Role</h4>
                        <p class="card-description">Update role name and permissions.</p>
                        
                        <!-- Alert Container for AJAX responses -->
                        <div id="alert-container"></div>
                        
                        <form class="forms-sample" id="role-edit-form" data-role-id="{{ $role->id }}">
                            @csrf
                            @method('PUT')
                            <div class="form-group">
                                <label for="name">Role Name</label>
                                <input type="text" class="form-control" id="name" name="name" value="{{ $role->name }}" placeholder="Enter Role Name" required>
                                <span class="text-danger" id="name-error"></span>
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
                                                        <input type="checkbox" class="form-check-input permission-checkbox" name="permissions[]" value="{{ $permission->id }}" @if(in_array($permission->id, $rolePermissions)) checked @endif>
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

                            <button type="submit" class="btn btn-primary mr-2" id="update-btn">
                                <span class="btn-text">Update</span>
                                <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                            </button>
                            <a href="{{ route('admin.roles.index') }}" class="btn btn-light">Cancel</a>
                        </form>
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
    // Handle form submission with AJAX
    $('#role-edit-form').on('submit', function(e) {
        e.preventDefault();
        
        const form = $(this);
        const roleId = form.data('role-id');
        const submitBtn = $('#update-btn');
        const btnText = submitBtn.find('.btn-text');
        const spinner = submitBtn.find('.spinner-border');
        
        // Clear previous errors
        $('#name-error').text('');
        $('#alert-container').html('');
        
        // Show loading state
        submitBtn.prop('disabled', true);
        btnText.text('Updating...');
        spinner.removeClass('d-none');
        
        // Collect form data
        const formData = {
            _token: $('input[name="_token"]').val(),
            _method: 'PUT',
            name: $('#name').val(),
            permissions: []
        };
        
        // Collect checked permissions
        $('.permission-checkbox:checked').each(function() {
            formData.permissions.push($(this).val());
        });
        
        // Send AJAX request
        $.ajax({
            url: '{{ route("admin.roles.update", $role->id) }}',
            type: 'POST',
            data: formData,
            success: function(response) {
                // Show success message
                $('#alert-container').html(
                    '<div class="alert alert-success alert-dismissible fade show" role="alert">' +
                    '<strong>Success!</strong> Role updated successfully. Redirecting...' +
                    '<button type="button" class="close" data-dismiss="alert" aria-label="Close">' +
                    '<span aria-hidden="true">&times;</span>' +
                    '</button>' +
                    '</div>'
                );
                
                // Reset button state
                submitBtn.prop('disabled', false);
                btnText.text('Update');
                spinner.addClass('d-none');
                
                // Redirect after 1.5 seconds
                setTimeout(function() {
                    window.location.href = '{{ route("admin.roles.index") }}';
                }, 1500);
            },
            error: function(xhr) {
                // Reset button state
                submitBtn.prop('disabled', false);
                btnText.text('Update');
                spinner.addClass('d-none');
                
                if (xhr.status === 422) {
                    // Validation errors
                    const errors = xhr.responseJSON.errors;
                    if (errors.name) {
                        $('#name-error').text(errors.name[0]);
                    }
                    
                    $('#alert-container').html(
                        '<div class="alert alert-danger alert-dismissible fade show" role="alert">' +
                        '<strong>Error!</strong> Please fix the validation errors.' +
                        '<button type="button" class="close" data-dismiss="alert" aria-label="Close">' +
                        '<span aria-hidden="true">&times;</span>' +
                        '</button>' +
                        '</div>'
                    );
                } else {
                    // Other errors
                    $('#alert-container').html(
                        '<div class="alert alert-danger alert-dismissible fade show" role="alert">' +
                        '<strong>Error!</strong> Something went wrong. Please try again.' +
                        '<button type="button" class="close" data-dismiss="alert" aria-label="Close">' +
                        '<span aria-hidden="true">&times;</span>' +
                        '</button>' +
                        '</div>'
                    );
                }
            }
        });
    });
    
    // Add visual feedback when checkboxes are changed
    $('.permission-checkbox').on('change', function() {
        const label = $(this).closest('.form-check-label');
        if ($(this).is(':checked')) {
            label.addClass('text-primary font-weight-bold');
        } else {
            label.removeClass('text-primary font-weight-bold');
        }
    });
    
    // Initialize visual state for checked permissions
    $('.permission-checkbox:checked').each(function() {
        $(this).closest('.form-check-label').addClass('text-primary font-weight-bold');
    });
});
</script>
@endpush
