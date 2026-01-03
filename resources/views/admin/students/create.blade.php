@extends('admin.layout.layout')

@section('content')
<div class="container-fluid">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Custom CSS for enhanced styling -->
    <style>
        .form-container {
    background: #ffffff;
    border-radius: 12px;
    box-shadow: 0 4px 20px rgba(34, 51, 84, 0.10);
    padding: 30px;
    margin-bottom: 30px;
}

.page-header {
    background: #393e6e;           /* deep indigo */
    color: #f6f8fb;
    padding: 30px;
    border-radius: 12px;
    margin-bottom: 30px;
}

.page-title {
    font-size: 2rem;
    font-weight: 700;
    margin: 0;
}

.page-subtitle {
    font-size: 1rem;
    opacity: 0.9;
    margin: 10px 0 0 0;
}

.form-group {
    margin-bottom: 25px;
}

.form-label {
    font-weight: 600;
    color: #283046;                /* dark slate */
    margin-bottom: 8px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.form-control {
    border: 2px solid #e8eaf6;     /* pale indigo */
    border-radius: 8px;
    padding: 12px 16px;
    font-size: 0.95rem;
    transition: all 0.3s ease;
    background: #f5f6fa;           /* lightest background */
}

.form-control:focus {
    border-color: #0b0c0e;         /* strong blue */
    box-shadow: 0 0 0 0.2rem rgba(80, 143, 243, 0.20);
}

.btn-submit {
    background: #565381;           /* vibrant blue */
    border: none;
    color: #f4f8fa;
    padding: 12px 30px;
    border-radius: 8px;
    font-weight: 600;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    gap: 8px;
}

.btn-submit:hover {
    background: #2952a3;
    transform: translateY(-2px);
    box-shadow: 0 8px 24px rgba(80, 143, 243, 0.35);
}

.btn-cancel {
    background: #abb1c7;           /* muted lavender gray */
    border: none;
    color: #232425;
    padding: 12px 30px;
    border-radius: 8px;
    font-weight: 600;
    transition: all 0.3s ease;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    margin-right: 15px;
}

.btn-cancel:hover {
    background: #8287a6;
    color: #f6f8fb;
    text-decoration: none;
}

.required {
    color: #ee6c6c;                /* friendly red */
}

.form-row {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
}

.error-message {
    color: #ee6c6c;
    font-size: 0.875rem;
    margin-top: 5px;
}

.form-icon {
    color: #3b4877;
    width: 16px;
}

    </style>

    <div class="row">
        <div class="col-12">
            <div class="page-header">
                <h1 class="page-title">
                    <i class="fas fa-user-plus"></i>
                    Add New Student
                </h1>
                <p class="page-subtitle">Create a new student record in the system</p>
            </div>

            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <strong>Please fix the following errors:</strong>
                    <ul class="mb-0 mt-2">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            <div class="form-container">
                <form method="post" action="{{ url('admin/students') }}">
                    @csrf

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">
                                <i class="fas fa-school form-icon"></i>
                                Institution <span class="required">*</span>
                            </label>
                            <select name="institution_id" class="form-control">
                                <option value="">Select Institution</option>
                                @foreach($institutions as $institution)
                                    <option value="{{ $institution->id }}" {{ old('institution_id') == $institution->id ? 'selected' : '' }}>
                                        {{ $institution->name }} ({{ ucfirst($institution->type) }})
                                    </option>
                                @endforeach
                            </select>
                            @error('institution_id')
                                <div class="error-message">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label class="form-label">
                                <i class="fas fa-user form-icon"></i>
                                Student Name <span class="required">*</span>
                            </label>
                            <input type="text" name="name" class="form-control" value="{{ old('name') }}" placeholder="Enter student name" required>
                            @error('name')
                                <div class="error-message">{{ $message }}</div>
                            @enderror
                        </div>

                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">
                                <i class="fas fa-phone form-icon"></i>
                                Phone Number <span class="required">*</span>
                            </label>
                            <input type="tel" name="phone" class="form-control" value="{{ old('phone') }}" placeholder="Enter 10-digit phone number" required>
                            @error('phone')
                                <div class="error-message">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label class="form-label">
                                <i class="fas fa-envelope form-icon"></i>
                                Email Address
                            </label>
                            <input type="email" name="email" class="form-control" value="{{ old('email') }}" placeholder="Enter email address">
                            @error('email')
                                <div class="error-message">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">
                                <i class="fas fa-layer-group form-icon"></i>
                                Class <span class="required">*</span>
                            </label>
                            <input type="text" name="class" class="form-control" value="{{ old('class') }}" placeholder="Enter class/grade" required>
                            @error('class')
                                <div class="error-message">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label class="form-label">
                                <i class="fas fa-id-card form-icon"></i>
                                Roll Number
                            </label>
                            <input type="text" name="roll_number" class="form-control" value="{{ old('roll_number') }}" placeholder="Enter roll number">
                            @error('roll_number')
                                <div class="error-message">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">
                                <i class="fas fa-venus-mars form-icon"></i>
                                Gender <span class="required">*</span>
                            </label>
                            <select name="gender" class="form-control" required>
                                <option value="">Select Gender</option>
                                <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Male</option>
                                <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Female</option>
                                <option value="other" {{ old('gender') == 'other' ? 'selected' : '' }}>Other</option>
                            </select>
                            @error('gender')
                                <div class="error-message">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label class="form-label">
                                <i class="fas fa-birthday-cake form-icon"></i>
                                Date of Birth <span class="required">*</span>
                            </label>
                            <input type="date" name="dob" class="form-control" value="{{ old('dob') }}" required>
                            @error('dob')
                                <div class="error-message">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group text-center mt-4">
                        <a href="{{ url('admin/students') }}" class="btn-cancel">
                            <i class="fas fa-arrow-left"></i>
                            Cancel
                        </a>
                        <button type="submit" class="btn-submit">
                            <i class="fas fa-save"></i>
                            Create Student
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection
