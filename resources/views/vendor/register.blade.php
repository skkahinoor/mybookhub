@extends('layouts.guest')

@section('content')
    <style>
        body {
            background: linear-gradient(135deg, #1f3c88 0%, #2a5298 100%);
            font-family: 'Poppins', sans-serif;
        }

        .register-wrapper {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .card {
            border: none;
            border-radius: 18px;
            overflow: hidden;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
        }

        .card-body {
            padding: 2.8rem;
        }

        .brand-logo {
            width: 80px;
            height: 80px;
            object-fit: contain;
            margin-bottom: 1rem;
        }

        .form-label {
            font-weight: 600;
            color: #333;
        }

        .form-control {
            border-radius: 10px;
            padding: 0.8rem 1rem;
            font-size: 0.95rem;
        }

        .input-group-text {
            border-radius: 10px 0 0 10px;
            background-color: #f8f9fa;
        }

        .btn-primary {
            background: #1f3c88;
            border: none;
            border-radius: 10px;
            font-weight: 600;
            padding: 0.75rem;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background: #2a5298;
            transform: translateY(-2px);
            box-shadow: 0 6px 18px rgba(42, 82, 152, 0.35);
        }

        .text-primary {
            color: #1f3c88 !important;
        }

        .card-footer {
            background: #f8f9fa;
            border-top: none;
            text-align: center;
            padding: 1.25rem;
        }

        .card-footer a {
            color: #1f3c88;
            font-weight: 600;
            text-decoration: none;
            transition: 0.3s;
        }

        .card-footer a:hover {
            color: #2a5298;
            text-decoration: underline;
        }

        .small-text {
            font-size: 0.85rem;
            color: #6c757d;
        }

        @media (max-width: 768px) {
            .card-body {
                padding: 2rem;
            }
        }
    </style>

    <div class="register-wrapper">
        <div class="col-md-8 col-lg-6 col-xl-5">
            <div class="card">
                <div class="card-body text-center">
                    <img src="{{ asset('uploads/logos/' . $logos->first()->logo) }}" style="height:60px; width:220px;"
                        alt="BookHub Logo" class="brand-logo mb-3">
                    <h3 class="fw-bold text-primary mb-2">Vendor Registration</h3>
                    <p class="text-muted mb-4">Create your BookHub Vendor account to access your dashboard</p>

                    <form method="POST" action="{{ route('vendor.register.submit') }}" id="vendorRegisterForm" enctype="multipart/form-data">
                        @csrf

                        {{-- Name --}}
                        <div class="mb-3 text-start">
                            <label class="form-label">Full Name</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-person"></i></span>
                                <input type="text" name="name" class="form-control" required>
                            </div>
                        </div>

                        {{-- Email --}}
                        <div class="mb-3 text-start">
                            <label class="form-label">Email Address</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                                <input type="email" name="email" class="form-control" required
                                    value="{{ old('email') }}">
                            </div>
                            @error('email')
                                <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Phone --}}
                        <div class="mb-3 text-start">
                            <label class="form-label">Phone Number</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-telephone"></i></span>
                                <input type="text" name="mobile" class="form-control" required
                                    value="{{ old('mobile') }}" maxlength="10" pattern="[0-9]{10}">
                            </div>
                            @error('mobile')
                                <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Photo --}}
                        <div class="mb-3 text-start">
                            <label class="form-label">Photo</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-image"></i></span>
                                <input type="file" name="vendor_image" class="form-control" accept="image/*">
                            </div>
                            <small class="form-text text-muted">Supported formats: JPG, PNG, GIF</small>
                            @error('vendor_image')
                                <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Password --}}
                        <div class="mb-3 text-start">
                            <label class="form-label">Password</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-lock"></i></span>
                                <input type="password" name="password" class="form-control" required minlength="6">
                            </div>
                            @error('password')
                                <span class="text-danger small">{{ $message }}</span>
                            @enderror
                            <small class="form-text text-muted">Minimum 6 characters</small>
                        </div>

                        {{-- Confirm Password --}}
                        <div class="mb-4 text-start">
                            <label class="form-label">Confirm Password</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                                <input type="password" name="password_confirmation" class="form-control" required minlength="6">
                            </div>
                            @error('password_confirmation')
                                <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-primary w-100 mb-2">
                            Register
                        </button>
                    </form>

                    <p class="small-text mb-0">
                        By registering, you agree to BookHub's
                    </p>
                </div>

                <div class="card-footer">
                    <p class="mb-0">Already have an account?
                        <a href="{{ route('vendor.register') }}">Login here</a>
                    </p>
                </div>
            </div>
        </div>
    </div>

@endsection

