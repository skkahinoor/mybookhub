@include('user.layout.header')

<div class="container-fluid page-body-wrapper">
    @include('user.layout.sidebar')

    <div class="main-panel">
        <div class="content-wrapper">
            <div class="row">
                <div class="col-md-12 grid-margin">
                    <div class="row align-items-center">
                        <div class="col-12 col-xl-8 mb-3 mb-xl-0">
                            <h3 class="font-weight-bold mb-1">My Referrals</h3>
                            <div class="text-muted">Invite friends and earn rewards for every purchase they make!</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Referral Stats -->
                <div class="col-md-6 grid-margin stretch-card">
                    <div class="card bg-primary text-white">
                        <div class="card-body text-center">
                            <h4 class="mb-3">Referral Earnings</h4>
                            <h1 class="display-3 mb-0">₹{{ number_format((float) $totalReferralEarnings, 2) }}</h1>
                            <p class="mt-2 text-white-50">Total commission earned so far</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body text-center">
                            <h4 class="mb-3">Total Referrals</h4>
                            <h1 class="display-3 mb-0 text-primary">{{ $referralCount }}</h1>
                            <p class="mt-2 text-muted">Friends who joined using your code</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Share Section -->
            <div class="row">
                <div class="col-md-12 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">Share Your Referral Link</h4>
                            <p class="card-description">Copy the link below and share it with your friends via WhatsApp, Email, or Social Media.</p>
                            
                            <div class="bg-light p-4 rounded text-center border">
                                <h3 class="text-primary mb-3">Earn ₹50 per Referral</h3>
                                <p class="mb-4">When your friend joins using your link and makes their first purchase, you'll get ₹50 in your wallet instantly!</p>
                                
                                <div class="input-group mb-3 mx-auto" style="max-width: 600px;">
                                    <input type="text" class="form-control" id="referralLink" 
                                           value="{{ url('/') }}?ref={{ Auth::user()->referral_code }}" readonly 
                                           style="background: #fff; height: 50px; font-weight: 500;">
                                    <div class="input-group-append">
                                        <button class="btn btn-primary" type="button" onclick="copyReferralLink()" style="height: 50px; padding: 0 30px;">
                                            <i class="ti-clipboard mr-2"></i> Copy Link
                                        </button>
                                    </div>
                                </div>
                                <div class="mt-2">
                                    <span class="text-muted">Your Referral Code: </span>
                                    <span class="badge badge-outline-primary p-2"><strong>{{ Auth::user()->referral_code }}</strong></span>
                                </div>
                            </div>
                            
                            <div class="row mt-4">
                                <div class="col-md-4 text-center p-3">
                                    <i class="ti-share text-primary mb-2" style="font-size: 2rem;"></i>
                                    <h5>Step 1: Share</h5>
                                    <small class="text-muted">Send your referral link to friends</small>
                                </div>
                                <div class="col-md-4 text-center p-3">
                                    <i class="ti-user text-primary mb-2" style="font-size: 2rem;"></i>
                                    <h5>Step 2: Join</h5>
                                    <small class="text-muted">They sign up using your link</small>
                                </div>
                                <div class="col-md-4 text-center p-3">
                                    <i class="ti-gift text-primary mb-2" style="font-size: 2rem;"></i>
                                    <h5>Step 3: Earn</h5>
                                    <small class="text-muted">You get ₹50 on their first purchase</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Referral History -->
            <div class="row">
                <div class="col-md-12 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">Referral History</h4>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Friend's Name</th>
                                            <th>Joined On</th>
                                            <th>Purchase Status</th>
                                            <th>Earnings</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($referrals as $referred)
                                            @php
                                                // Check if we earned commission for this friend
                                                $commission = \App\Models\WalletTransaction::where('user_id', Auth::id())
                                                    ->where('type', 'credit')
                                                    ->where('description', 'LIKE', 'Referral commission for user ID: ' . $referred->id . '%')
                                                    ->first();
                                            @endphp
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <img src="{{ asset($referred->profile_image ?: 'asset/user/avatar.png') }}" class="mr-2" style="width: 32px; height: 32px; border-radius: 50%;">
                                                        <span>{{ $referred->name }}</span>
                                                    </div>
                                                </td>
                                                <td>{{ $referred->created_at->format('M d, Y') }}</td>
                                                <td>
                                                    @if($commission)
                                                        <span class="badge badge-success">Completed</span>
                                                    @else
                                                        <span class="badge badge-warning">Pending Purchase</span>
                                                    @endif
                                                </td>
                                                <td class="font-weight-bold">
                                                    @if($commission)
                                                        <span class="text-success">+₹{{ number_format($commission->amount, 2) }}</span>
                                                    @else
                                                        ₹0.00
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="text-center p-5 text-muted">
                                                    <i class="ti-announcement mb-3 d-block" style="font-size: 3rem;"></i>
                                                    <p>You haven't referred any friends yet. Start sharing to earn!</p>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            <div class="mt-3">
                                {{ $referrals->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        @include('user.layout.footer')
    </div>
</div>

{{-- JavaScript moved here to ensure it only runs on this page --}}
<script>
    function copyReferralLink() {
        var copyText = document.getElementById("referralLink");
        copyText.select();
        copyText.setSelectionRange(0, 99999); /* For mobile devices */
        
        try {
            navigator.clipboard.writeText(copyText.value);
            alert("Referral link copied to clipboard!");
        } catch (err) {
            // Fallback for older browsers
            document.execCommand('copy');
            alert("Referral link copied!");
        }
    }
</script>

<!-- plugins:js -->
<script src="{{ asset('user/vendors/js/vendor.bundle.base.js') }}"></script>
<!-- endinject -->
<!-- inject:js -->
<script src="{{ asset('user/js/off-canvas.js') }}"></script>
<script src="{{ asset('user/js/hoverable-collapse.js') }}"></script>
<script src="{{ asset('user/js/template.js') }}"></script>
<!-- endinject -->
