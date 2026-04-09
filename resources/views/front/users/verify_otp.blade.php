@extends('front.layout.layout3')

@section('content')
    @php
        $phone = (string) (Session::get('registration_phone') ?? '');
        $maskedPhone = $phone !== '' ? (substr($phone, 0, 2) . str_repeat('•', max(0, strlen($phone) - 4)) . substr($phone, -2)) : '';
    @endphp

    <div class="otp-page py-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-5 col-md-8">
                    <div class="otp-card bg-white shadow-sm overflow-hidden">
                        <div class="otp-hero text-center">
                            <div class="otp-hero__icon">
                                <i class="fas fa-shield-alt"></i>
                            </div>
                            <h2 class="otp-hero__title mb-1">Verify your phone</h2>
                            <p class="otp-hero__subtitle mb-0">Enter the 6‑digit code we sent to your mobile number.</p>
                        </div>

                        <div class="p-4 p-sm-5">
                            <div class="d-flex align-items-center justify-content-between otp-meta mb-4">
                                <div class="d-flex align-items-center">
                                    <div class="otp-meta__badge mr-3">
                                        <i class="fas fa-mobile-alt"></i>
                                    </div>
                                    <div>
                                        <div class="otp-meta__label">Phone</div>
                                        <div class="otp-meta__value">
                                            @if($maskedPhone !== '')
                                                {{ $maskedPhone }}
                                            @else
                                                <span class="text-muted">Not found in session</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <a href="{{ url('student/register') }}" class="otp-link">Change</a>
                            </div>

                            @if (Session::has('success_message'))
                                <div class="alert alert-success border-0 otp-alert" role="alert">
                                    <i class="fas fa-check-circle mr-2"></i>{{ Session::get('success_message') }}
                                </div>
                            @endif
                            @if (Session::has('error_message'))
                                <div class="alert alert-danger border-0 otp-alert" role="alert">
                                    <i class="fas fa-exclamation-triangle mr-2"></i>{{ Session::get('error_message') }}
                                </div>
                            @endif

                            <form id="otpVerifyForm" action="{{ url('user/verify-otp') }}" method="post" novalidate>
                                @csrf
                                <input type="hidden" name="otp" id="fullOtp">

                                <div class="otp-inputs mb-4" aria-label="One time password">
                                    @for($i = 1; $i <= 6; $i++)
                                        <input
                                            id="otp{{ $i }}"
                                            class="otp-input"
                                            type="text"
                                            inputmode="numeric"
                                            autocomplete="{{ $i === 1 ? 'one-time-code' : 'off' }}"
                                            maxlength="1"
                                            aria-label="OTP digit {{ $i }}"
                                        >
                                    @endfor
                                </div>

                                <button type="submit" class="btn btn-primary btn-block otp-btn">
                                    Verify & Continue
                                </button>

                                <div class="d-flex align-items-center justify-content-between mt-4">
                                    <div class="otp-timer text-muted">
                                        <i class="far fa-clock mr-1"></i>
                                        <span id="resendTimerContainer">Resend in <strong><span id="timer">60</span>s</strong></span>
                                    </div>
                                    <a href="{{ route('user.resend-otp') }}" id="resendBtn" class="otp-link d-none">
                                        Resend code
                                    </a>
                                </div>

                                <div class="otp-help text-center mt-4 pt-4 border-top">
                                    <div class="text-muted small mb-2">Having trouble?</div>
                                    <div class="d-flex flex-wrap justify-content-center" style="gap: 14px;">
                                        <span class="small text-muted"><i class="fas fa-lock mr-1"></i> Secure verification</span>
                                        <span class="small text-muted"><i class="fas fa-bolt mr-1"></i> Usually arrives within 10 seconds</span>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .otp-page{
            min-height: calc(100vh - 200px);
            display:flex;
            align-items:center;
        }
        .otp-card{
            border-radius: 18px;
            border: 1px solid rgba(17,24,39,.08);
        }
        .otp-hero{
            padding: 28px 28px 22px;
            background: radial-gradient(1200px 400px at 50% -40%, rgba(255,107,0,.30), transparent 60%),
                        linear-gradient(135deg, rgba(255,107,0,.95), rgba(207,137,56,.95));
            color:#fff;
        }
        .otp-hero__icon{
            width: 54px; height:54px;
            display:flex; align-items:center; justify-content:center;
            border-radius: 16px;
            margin: 0 auto 14px;
            background: rgba(255,255,255,.16);
            border: 1px solid rgba(255,255,255,.22);
            box-shadow: 0 10px 25px rgba(0,0,0,.12);
            backdrop-filter: blur(10px);
        }
        .otp-hero__icon i{ font-size: 22px; }
        .otp-hero__title{ font-weight: 800; letter-spacing: .2px; }
        .otp-hero__subtitle{ opacity: .9; }

        .otp-meta__badge{
            width: 42px; height:42px;
            border-radius: 14px;
            background: rgba(255,107,0,.10);
            color: #ff6b00;
            display:flex; align-items:center; justify-content:center;
            border: 1px solid rgba(255,107,0,.16);
        }
        .otp-meta__label{ font-size: 12px; color:#6b7280; }
        .otp-meta__value{ font-weight: 700; color:#111827; letter-spacing: .5px; }
        .otp-link{
            color:#ff6b00;
            font-weight: 700;
            text-decoration: none !important;
        }
        .otp-link:hover{ color:#cf8938; }

        .otp-alert{
            border-radius: 14px;
            box-shadow: 0 10px 20px rgba(17,24,39,.06);
        }

        .otp-inputs{
            display: grid;
            grid-template-columns: repeat(6, minmax(0, 1fr));
            gap: 10px;
            max-width: 360px;
            margin-left: auto;
            margin-right: auto;
        }
        .otp-input{
            width: clamp(42px, 4.2vw, 54px);
            height: clamp(48px, 5.2vw, 56px);
            border-radius: 14px;
            border: 1px solid rgba(17,24,39,.12);
            background: #fff;
            text-align:center;
            font-size: 22px;
            font-weight: 800;
            color:#111827;
            outline: none;
            transition: .15s ease;
        }
        .otp-input:focus{
            border-color: rgba(255,107,0,.55);
            box-shadow: 0 0 0 4px rgba(255,107,0,.15);
            transform: translateY(-1px);
        }
        .otp-btn{
            height: 48px;
            border-radius: 14px;
            font-weight: 800;
            background: #ff6b00;
            border-color: #ff6b00;
        }
        .otp-btn:hover{ background:#cf8938; border-color:#cf8938; }

        @media (max-width: 576px){
            .otp-inputs{ gap: 8px; max-width: 320px; }
            .otp-input{ font-size: 20px; border-radius: 12px; }
            .otp-hero{ padding: 24px 18px 18px; }
        }
    </style>

    <script>
        (function () {
            var inputs = [];
            for (var i = 1; i <= 6; i++) inputs.push(document.getElementById('otp' + i));
            var full = document.getElementById('fullOtp');
            var form = document.getElementById('otpVerifyForm');

            function sanitizeDigit(v){ return (v || '').replace(/[^0-9]/g, '').slice(0, 1); }

            function combine() {
                var otp = '';
                inputs.forEach(function (el) { otp += (el && el.value) ? el.value : ''; });
                if (full) full.value = otp;
                return otp;
            }

            function focusNext(idx) {
                var next = inputs[idx + 1];
                if (next) next.focus();
            }
            function focusPrev(idx) {
                var prev = inputs[idx - 1];
                if (prev) prev.focus();
            }

            inputs.forEach(function (el, idx) {
                if (!el) return;

                el.addEventListener('input', function () {
                    el.value = sanitizeDigit(el.value);
                    if (el.value) focusNext(idx);
                    var otp = combine();
                    if (otp.length === 6 && form) {
                        // Auto submit when complete (feels premium)
                        form.requestSubmit ? form.requestSubmit() : form.submit();
                    }
                });

                el.addEventListener('keydown', function (e) {
                    if (e.key === 'Backspace' && !el.value) focusPrev(idx);
                    if (e.key === 'ArrowLeft') focusPrev(idx);
                    if (e.key === 'ArrowRight') focusNext(idx);
                });

                el.addEventListener('paste', function (e) {
                    var text = (e.clipboardData || window.clipboardData).getData('text') || '';
                    var digits = text.replace(/\D+/g, '').slice(0, 6).split('');
                    if (!digits.length) return;
                    e.preventDefault();
                    digits.forEach(function (d, i) { if (inputs[i]) inputs[i].value = d; });
                    combine();
                    var last = inputs[Math.min(digits.length, 6) - 1];
                    if (last) last.focus();
                });
            });

            // Timer Logic
            var timeLeft = 60;
            var timerElement = document.getElementById('timer');
            var timerContainer = document.getElementById('resendTimerContainer');
            var resendBtn = document.getElementById('resendBtn');

            var countdown = setInterval(function () {
                timeLeft--;
                if (timerElement) timerElement.innerText = String(timeLeft);
                if (timeLeft <= 0) {
                    clearInterval(countdown);
                    if (timerContainer) timerContainer.classList.add('d-none');
                    if (resendBtn) resendBtn.classList.remove('d-none');
                }
            }, 1000);

            // Final validation on submit (with Swal if present)
            if (form) {
                form.addEventListener('submit', function (e) {
                    var otp = combine();
                    if (otp.length < 6) {
                        e.preventDefault();
                        if (window.Swal && Swal.fire) {
                            Swal.fire({
                                icon: 'warning',
                                title: 'Incomplete code',
                                text: 'Please enter all 6 digits.',
                                confirmButtonColor: '#ff6b00'
                            });
                        } else {
                            alert('Please enter all 6 digits.');
                        }
                    }
                });
            }

            // Focus first input on load
            if (inputs[0]) inputs[0].focus();
        })();
    </script>
@endsection
