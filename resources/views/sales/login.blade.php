@extends('layouts.guest')

@section('content')
    <style>
        body {
            background: linear-gradient(135deg, #1f3c88 0%, #2a5298 100%);
            font-family: 'Poppins', sans-serif;
        }

        .login-wrapper {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .card {
            border: none;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.15);
        }

        .card-body {
            padding: 2.5rem;
        }

        .brand-logo {
            width: 70px;
            height: 70px;
            object-fit: contain;
            margin-bottom: 1rem;
        }

        .form-control {
            border-radius: 10px;
            padding: 0.75rem 1rem;
        }

        .input-group-text {
            border-radius: 10px 0 0 10px;
        }

        .btn-primary {
            background: #1f3c88;
            border: none;
            border-radius: 10px;
            font-weight: 600;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background: #2a5298;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(42, 82, 152, 0.3);
        }

        .text-primary {
            color: #1f3c88 !important;
        }

        .form-check-input:checked {
            background-color: #1f3c88;
            border-color: #1f3c88;
        }

        .card-footer {
            background: #f8f9fa;
            border-top: none;
            text-align: center;
        }

        .card-footer a {
            text-decoration: none;
            color: #1f3c88;
            font-weight: 600;
            transition: 0.3s;
        }

        .card-footer a:hover {
            color: #2a5298;
        }
    </style>

    <div class="login-wrapper">
        <div class="col-md-6 col-lg-5 col-xl-4">
            <div class="card">
                <div class="card-body text-center">
                    <img src="{{ asset('uploads/logos/' . $logos->first()->logo) }}" style="height:60px; width:220px;"
                        alt="BookHub Logo" class="brand-logo mb-3">
                    <h3 class="fw-bold text-primary mb-2">Sales Executive Login</h3>
                    <p class="text-muted mb-4">Access your BookHub sales dashboard</p>
                    
                    @if (session('success'))
                        <div id="autoHideAlert" class="alert alert-success text-start">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger text-start">
                            {{ session('error') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('sales.login.submit') }}">
                        @csrf
                        <div class="mb-3 text-start">
                            <label for="login" class="form-label fw-semibold">Email or Mobile</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="bi bi-envelope"></i></span>
                                <input type="text" name="login" id="login" class="form-control"
                                    placeholder="Enter your email or mobile" value="{{ old('login') }}" required autofocus>
                            </div>
                            @error('login')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3 text-start">
                            <label for="password" class="form-label fw-semibold">Password</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="bi bi-lock"></i></span>
                                <input type="password" name="password" id="password" class="form-control"
                                    placeholder="Enter your password" required>
                            </div>
                            @error('password')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-check mb-3 text-start">
                            <input class="form-check-input" type="checkbox" name="remember" id="remember">
                            <label class="form-check-label" for="remember">Remember me</label>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 py-2">
                            <i class="bi bi-box-arrow-in-right me-2"></i>Login
                        </button>
                    </form>
                </div>
                <div class="card-footer">
                    <p class="mb-0">Don’t have an account?
                        <a href="{{ route('sales.register') }}">Register here</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
    <script>
    setTimeout(function() {
        let alertBox = document.getElementById('autoHideAlert');
        if (alertBox) {
            alertBox.style.transition = "opacity 0.8s ease";
            alertBox.style.opacity = "0";
            setTimeout(() => alertBox.remove(), 800);
        }
    }, 3000); // 3000ms = 3 seconds
</script>

@endsection
