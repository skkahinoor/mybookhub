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
            padding: 1.5rem;
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

        /* Premium Play Store Button */
        .playstore-btn {
            display: inline-flex;
            align-items: center;
            background: #0d1117;
            color: #ffffff !important;
            border: 1px solid #30363d;
            padding: 0.6rem 1.5rem;
            border-radius: 12px;
            text-decoration: none !important;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.15);
        }

        .playstore-btn:hover {
            background: #161b22;
            border-color: #8b949e;
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(31, 60, 136, 0.3);
        }

        .playstore-content {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .playstore-icon {
            width: 24px;
            height: 24px;
            fill: #ffffff;
            transition: transform 0.3s ease;
        }

        .playstore-btn:hover .playstore-icon {
            transform: scale(1.15) rotate(5deg);
        }

        .playstore-text {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            line-height: 1.1;
        }

        .playstore-sub {
            font-size: 0.6rem;
            font-weight: 600;
            color: #8b949e;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        .playstore-main {
            font-size: 1.05rem;
            font-weight: 700;
            color: #ffffff;
            font-family: 'Poppins', sans-serif;
            letter-spacing: -0.2px;
            margin-top: 2px;
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

                        <button type="submit" class="btn btn-primary w-100 py-2 mb-3">
                            <i class="bi bi-box-arrow-in-right me-2"></i>Login
                        </button>
                    </form>

                    <div class="mt-4 pt-3 border-top text-center">
                        <p class="text-muted small mb-2 fw-semibold text-uppercase" style="letter-spacing: 0.8px; font-size: 0.7rem;">Download Sales App</p>
                        <a href="https://play.google.com/store/apps/details?id=com.mybookhub" target="_blank" class="playstore-btn">
                            <div class="playstore-content">
                                <svg class="playstore-icon" viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M325.3 234.3L104.6 13l280.8 161.2-60.1 60.1zM47 0C34 0 24 10 24 23v466c0 13 10 23 23 23h3l251-251L47 0zM325.3 277.7l60.1 60.1L104.6 499l220.7-221.3zM413 311.5L488 268c16-9 16-25 0-34l-75-43.5-60.2 60.1 60.2 60.9z"/>
                                </svg>
                                <div class="playstore-text">
                                    <span class="playstore-sub">GET IT ON</span>
                                    <span class="playstore-main">Google Play</span>
                                </div>
                            </div>
                        </a>
                    </div>
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
