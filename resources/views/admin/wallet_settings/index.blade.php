@extends('admin.layout.layout')

@section('content')
    <div class="main-panel">
        <div class="content-wrapper">
            <div class="row justify-content-center">
                <div class="col-lg-8 grid-margin stretch-card">
                    <div class="card shadow-sm border-0" style="border-radius: 16px; overflow: hidden;">
                        <div class="card-header bg-white border-0 pt-4 px-4">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h4 class="card-title text-primary mb-1 font-weight-bold" style="font-size: 1.4rem;">Wallet Settings</h4>
                                    <p class="text-muted mb-0 small">
                                        Configure the global signup bonus and wallet incentive settings for students.
                                    </p>
                                </div>
                                <div class="bg-light-primary p-2 rounded-circle">
                                    <i class="ti-wallet text-primary" style="font-size: 1.5rem;"></i>
                                </div>
                            </div>
                        </div>
                        <div class="card-body px-4 pb-4">
                            {{-- Success message --}}
                            @if (session('success_message'))
                                <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert" style="border-radius: 12px; background-color: #e8f5e9; color: #2e7d32;">
                                    <strong>Success:</strong> {{ session('success_message') }}
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true" style="color: #2e7d32;">&times;</span>
                                    </button>
                                </div>
                            @endif

                            {{-- Error message --}}
                            @if (session('error_message'))
                                <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm" role="alert" style="border-radius: 12px; background-color: #ffebee; color: #c62828;">
                                    <strong>Error:</strong> {{ session('error_message') }}
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true" style="color: #c62828;">&times;</span>
                                    </button>
                                </div>
                            @endif

                            {{-- Validation errors --}}
                            @if ($errors->any())
                                <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm" role="alert" style="border-radius: 12px; background-color: #ffebee; color: #c62828;">
                                    <ul class="mb-0 pl-3">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true" style="color: #c62828;">&times;</span>
                                    </button>
                                </div>
                            @endif

                            <form method="POST" action="{{ route('admin.wallet.settings.update') }}" class="mt-4">
                                @csrf

                                <div class="card bg-light border-0 mb-4" style="border-radius: 12px;">
                                    <div class="card-body p-4">
                                        <div class="row align-items-center">
                                            <div class="col-md-7">
                                                <div class="form-group mb-0">
                                                    <label for="signup_bonus" class="font-weight-bold text-dark mb-2" style="font-size: 1.1rem;">
                                                        Student Signup Bonus Amount (₹)
                                                    </label>
                                                    <input type="number" class="form-control form-control-lg border-0 shadow-sm"
                                                        id="signup_bonus" name="signup_bonus"
                                                        value="{{ $signupBonus }}" min="0" step="0.01"
                                                        required
                                                        style="border-radius: 10px; padding: 15px; height: 50px; font-size: 1.1rem; font-weight: 600; color: #435ebe; background: #fff;">
                                                    <small class="form-text text-muted mt-2">
                                                        This is the dynamic wallet incentive amount credited to a student when they register and verify their OTP successfully.
                                                    </small>
                                                </div>
                                            </div>
                                            <div class="col-md-5 text-center d-none d-md-block">
                                                <div class="p-3">
                                                    <h3 class="text-primary font-weight-bold mb-1">₹<span id="display_amount">{{ number_format($signupBonus, 0) }}</span></h3>
                                                    <p class="text-muted small mb-0">Current signup bonus credit</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="card bg-light border-0 mb-4" style="border-radius: 12px;">
                                    <div class="card-body p-4">
                                        <div class="row align-items-center">
                                            <div class="col-md-7">
                                                <div class="form-group mb-0">
                                                    <label for="wallet_min_cart_amount" class="font-weight-bold text-dark mb-2" style="font-size: 1.1rem;">
                                                        Minimum Cart Amount to Use Wallet (₹)
                                                    </label>
                                                    <input type="number" class="form-control form-control-lg border-0 shadow-sm"
                                                        id="wallet_min_cart_amount" name="wallet_min_cart_amount"
                                                        value="{{ $walletMinCartAmount ?? 0 }}" min="0" step="0.01"
                                                        required
                                                        style="border-radius: 10px; padding: 15px; height: 50px; font-size: 1.1rem; font-weight: 600; color: #435ebe; background: #fff;">
                                                    <small class="form-text text-muted mt-2">
                                                        Users can only apply their wallet balance if the cart subtotal is greater than or equal to this amount. Set 0 to allow for any amount.
                                                    </small>
                                                </div>
                                            </div>
                                            <div class="col-md-5 text-center d-none d-md-block">
                                                <div class="p-3">
                                                    <h3 class="text-primary font-weight-bold mb-1">₹<span id="display_min_cart">{{ number_format($walletMinCartAmount ?? 0, 0) }}</span></h3>
                                                    <p class="text-muted small mb-0">Current minimum threshold</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-end align-items-center">
                                    <a href="{{ url('admin/dashboard') }}" class="btn btn-light mr-2 font-weight-bold px-4 py-2" style="border-radius: 10px; font-size: 0.95rem;">
                                        Cancel
                                    </a>
                                    <button type="submit" class="btn btn-primary font-weight-bold px-4 py-2" style="border-radius: 10px; font-size: 0.95rem; background-color: #435ebe; border-color: #435ebe; box-shadow: 0 4px 10px rgba(67, 94, 190, 0.2);">
                                        Save Changes
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('signup_bonus').addEventListener('input', function(e) {
            var val = parseFloat(e.target.value);
            if (!isNaN(val) && val >= 0) {
                document.getElementById('display_amount').innerText = val.toLocaleString('en-IN', { maximumFractionDigits: 2 });
            } else {
                document.getElementById('display_amount').innerText = '0';
            }
        });

        document.getElementById('wallet_min_cart_amount').addEventListener('input', function(e) {
            var val = parseFloat(e.target.value);
            if (!isNaN(val) && val >= 0) {
                document.getElementById('display_min_cart').innerText = val.toLocaleString('en-IN', { maximumFractionDigits: 2 });
            } else {
                document.getElementById('display_min_cart').innerText = '0';
            }
        });
    </script>
@endsection
