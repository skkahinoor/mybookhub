@extends('front.layout.layout3')

@section('content')
    <!-- Professional Verification Section -->
    <div class="registration-verify-section py-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-5 col-md-8">
                    <!-- Glassmorphism Main Card -->
                    <div class="verify-card shadow-2xl rounded-3xl bg-white overflow-hidden animate__animated animate__fadeInUp">
                        <!-- Top Header with Brand Pattern -->
                        <div class="card-header-pattern p-5 text-center position-relative">
                            <div class="brand-logo-glow mb-3">
                                <div class="icon-pulse">
                                    <i class="fas fa-user-shield text-white" style="font-size: 32px;"></i>
                                </div>
                            </div>
                            <h2 class="text-white font-weight-bold mb-1" style="letter-spacing: 0.5px;">Verification Required</h2>
                            <p class="text-white-50 mb-0 small">Enter the 6-digit code sent to your device</p>
                            
                            <!-- Floating Decorative Elements -->
                            <div class="floating-shape shape-1"></div>
                            <div class="floating-shape shape-2"></div>
                        </div>

                        <div class="card-body p-5">
                            <div class="text-center mb-5">
                                <div class="phone-display d-inline-flex align-items-center bg-light px-4 py-2 rounded-pill border shadow-inner">
                                    <i class="fas fa-mobile-alt mr-2 text-primary" style="color: #cf8938 !important;"></i>
                                    <span class="font-weight-bold text-dark" style="font-size: 1.1rem; letter-spacing: 1px;">
                                        {{ substr(Session::get('registration_phone'), 0, 3) }}-{{ substr(Session::get('registration_phone'), 3, 3) }}-{{ substr(Session::get('registration_phone'), 6) }}
                                    </span>
                                </div>
                            </div>

                            <div class="alert-container mb-4">
                                @if (Session::has('success_message'))
                                    <div class="alert alert-success border-0 shadow-sm rounded-xl animate__animated animate__shakeX" role="alert">
                                        <i class="fas fa-check-circle mr-2"></i>{{ Session::get('success_message') }}
                                    </div>
                                @endif
                                @if (Session::has('error_message'))
                                    <div class="alert alert-danger border-0 shadow-sm rounded-xl animate__animated animate__headShake" role="alert">
                                        <i class="fas fa-exclamation-triangle mr-2"></i>{{ Session::get('error_message') }}
                                    </div>
                                @endif
                            </div>

                            <form id="otpVerifyForm" action="{{ url('user/verify-otp') }}" method="post" class="text-center">
                                @csrf
                                <input type="hidden" name="otp" id="fullOtp">
                                
                                <div class="otp-input-group d-flex justify-content-between mb-5">
                                    <input type="text" class="otp-box-modern" maxlength="1" oninput="moveToNext(this, 'otp2')" id="otp1" autofocus autocomplete="one-time-code" pattern="\d*">
                                    <input type="text" class="otp-box-modern" maxlength="1" oninput="moveToNext(this, 'otp3')" onkeydown="moveToPrev(event, 'otp1')" id="otp2" pattern="\d*">
                                    <input type="text" class="otp-box-modern" maxlength="1" oninput="moveToNext(this, 'otp4')" onkeydown="moveToPrev(event, 'otp2')" id="otp3" pattern="\d*">
                                    <input type="text" class="otp-box-modern" maxlength="1" oninput="moveToNext(this, 'otp5')" onkeydown="moveToPrev(event, 'otp3')" id="otp4" pattern="\d*">
                                    <input type="text" class="otp-box-modern" maxlength="1" oninput="moveToNext(this, 'otp6')" onkeydown="moveToPrev(event, 'otp4')" id="otp5" pattern="\d*">
                                    <input type="text" class="otp-box-modern" maxlength="1" oninput="combineOtp()" onkeydown="moveToPrev(event, 'otp5')" id="otp6" pattern="\d*">
                                </div>

                                <div class="resend-section mb-5">
                                    <div id="resendTimerContainer" class="timer-badge">
                                        <i class="far fa-clock mr-2"></i>Resend OTP in <span id="timer" class="font-weight-bold">60</span>s
                                    </div>
                                    <a href="{{ route('user.resend-otp') }}" id="resendBtn" class="resend-link d-none">
                                        <i class="fas fa-sync-alt mr-1"></i>Didn't get the code? <strong>Resend Now</strong>
                                    </a>
                                </div>

                                <button type="submit" class="btn btn-verify-modern w-100 py-3 shadow-lg">
                                    <span class="btn-text">VERIFY & REGISTER</span>
                                    <i class="fas fa-arrow-right ml-2 group-hover:translate-x-1 transition-transform"></i>
                                </button>
                            </form>

                            <div class="mt-5 pt-4 border-top text-center">
                                <a href="{{ url('user/login-register') }}" class="text-muted text-decoration-none hover-primary back-link">
                                    <i class="fas fa-chevron-left mr-2"></i>Changed your number?
                                </a>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Trust Badges -->
                    <div class="mt-4 text-center animate__animated animate__fadeIn animate__delay-1s">
                        <div class="d-flex justify-content-center align-items-center gap-4 py-2 px-4 rounded-pill bg-white shadow-sm border d-inline-flex">
                            <span class="text-muted small"><i class="fas fa-shield-check mr-1 text-success"></i> Secure SSL</span>
                            <div class="vr mx-2 bg-secondary opacity-25" style="height: 15px;"></div>
                            <span class="text-muted small"><i class="fas fa-lock mr-1 text-primary"></i> Data Encrypted</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        @import url('https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css');

        .registration-verify-section {
            background-color: #f0f2f5;
            min-height: calc(100vh - 200px);
            display: flex;
            align-items: center;
        }

        .verify-card {
            border: none;
            background: #ffffff;
            position: relative;
            z-index: 10;
        }

        .card-header-pattern {
            background: linear-gradient(135deg, #cf8938 0%, #b57731 100%);
            overflow: hidden;
        }

        .brand-logo-glow {
            width: 70px;
            height: 70px;
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(5px);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto;
            border: 1px solid rgba(255, 255, 255, 0.3);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }

        .icon-pulse {
            animation: pulse-white 2s infinite;
        }

        @keyframes pulse-white {
            0% { transform: scale(0.95); }
            70% { transform: scale(1); box-shadow: 0 0 0 10px rgba(255, 255, 255, 0); }
            100% { transform: scale(0.95); }
        }

        .floating-shape {
            position: absolute;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            z-index: -1;
        }

        .shape-1 { width: 100px; height: 100px; top: -20px; left: -20px; }
        .shape-2 { width: 150px; height: 150px; bottom: -50px; right: -50px; }

        .phone-display {
            border: 1px dashed rgba(207, 137, 56, 0.3) !important;
            background: #fff8f0 !important;
        }

        .otp-box-modern {
            width: 52px;
            height: 64px;
            border: 2px solid #edeff2;
            border-radius: 12px;
            text-align: center;
            font-size: 24px;
            font-weight: 800;
            color: #2d3436;
            transition: all 0.25s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            background: #fbfbfc;
        }

        .otp-box-modern:focus {
            outline: none;
            border-color: #cf8938;
            background: #fff;
            box-shadow: 0 8px 20px rgba(207, 137, 56, 0.15);
            transform: scale(1.08);
            z-index: 10;
        }

        .timer-badge {
            display: inline-flex;
            align-items: center;
            background: #f8f9fa;
            color: #636e72;
            padding: 8px 20px;
            border-radius: 50px;
            font-size: 0.9rem;
            border: 1px solid #edeff2;
        }

        .resend-link {
            color: #cf8938;
            text-decoration: none !important;
            font-size: 0.95rem;
            transition: all 0.3s ease;
        }

        .resend-link:hover {
            color: #b57731;
            transform: translateY(-1px);
        }

        .btn-verify-modern {
            background: #cf8938;
            border: none;
            color: #fff;
            border-radius: 14px;
            font-size: 1rem;
            font-weight: 700;
            letter-spacing: 1px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }

        .btn-verify-modern::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: 0.5s;
        }

        .btn-verify-modern:hover::before {
            left: 100%;
        }

        .btn-verify-modern:hover {
            background: #b57731;
            transform: translateY(-3px);
            box-shadow: 0 15px 30px rgba(207, 137, 56, 0.35);
            color: #fff;
        }

        .back-link {
            font-size: 0.9rem;
            transition: all 0.3s ease;
        }

        .back-link:hover {
            color: #cf8938 !important;
        }

        .rounded-xl { border-radius: 12px !important; }
        .rounded-3xl { border-radius: 30px !important; }
        .shadow-2xl { box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.15) !important; }

        @media (max-width: 576px) {
            .otp-box-modern { width: 44px; height: 56px; font-size: 20px; }
            .card-body { padding: 30px 20px !important; }
        }
    </style>

    <script>
        function moveToNext(current, nextFieldID) {
            // Allow only numbers
            current.value = current.value.replace(/[^0-9]/g, '');
            
            if (current.value.length >= 1) {
                const next = document.getElementById(nextFieldID);
                if (next) next.focus();
            }
            combineOtp();
        }

        function moveToPrev(event, prevFieldID) {
            if (event.key === "Backspace" && event.target.value.length === 0) {
                const prev = document.getElementById(prevFieldID);
                if (prev) prev.focus();
            }
        }

        function combineOtp() {
            let otp = '';
            for (let i = 1; i <= 6; i++) {
                otp += document.getElementById('otp' + i).value;
            }
            document.getElementById('fullOtp').value = otp;
        }

        // Timer Logic
        let timeLeft = 60;
        const timerElement = document.getElementById('timer');
        const timerContainer = document.getElementById('resendTimerContainer');
        const resendBtn = document.getElementById('resendBtn');

        const countdown = setInterval(() => {
            timeLeft--;
            if (timerElement) timerElement.innerText = timeLeft;
            if (timeLeft <= 0) {
                clearInterval(countdown);
                if (timerContainer) timerContainer.classList.add('d-none');
                if (resendBtn) resendBtn.classList.remove('d-none');
            }
        }, 1000);

        // Form submission check
        document.getElementById('otpVerifyForm').onsubmit = function() {
            combineOtp();
            const otp = document.getElementById('fullOtp').value;
            if (otp.length < 6) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Incomplete Code',
                    text: 'Please enter all 6 digits of the OTP version.',
                    confirmButtonColor: '#cf8938'
                });
                return false;
            }
            return true;
        };
    </script>
@endsection
