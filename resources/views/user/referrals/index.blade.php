@include('user.layout.header')

<style>
    .input-group-append .btn {
        height: 55px;
        width: 60px;
        padding: 0;
        font-weight: 600;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
    }
    .btn-whatsapp {
        background-color: #25D366 !important;
        border-color: #25D366 !important;
        color: white !important;
    }
    .btn-whatsapp:hover {
        background-color: #128C7E !important;
        transform: scale(1.05);
    }
    .btn-primary:hover {
        transform: scale(1.05);
    }
    .share-buttons .btn {
        border-radius: 12px;
        margin: 5px;
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    }
</style>


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
                                
                                <div class="input-group mb-3 mx-auto" style="max-width: 550px;">
                                    <input type="text" class="form-control" id="referralLink" 
                                           value="{{ url('/') }}?ref={{ Auth::user()->referral_code }}" readonly 
                                           style="border-radius: 15px 0 0 15px; background: #fff; height: 55px; font-weight: 600; border: 2px solid #eee; border-right: none; box-shadow: inset 0 2px 4px rgba(0,0,0,0.02); color: #444;">
                                    <div class="input-group-append">
                                        <button class="btn btn-primary" type="button" onclick="copyReferralLink(this)" title="Copy Link" style="border-right: 1px solid rgba(255,255,255,0.2);">
                                            <i class="ti-clipboard" style="font-size: 1.3rem;"></i>
                                        </button>
                                        <a href="https://api.whatsapp.com/send?text={{ urlencode('Check out BookHub! Sign up using my link: ' . url('/') . '?ref=' . Auth::user()->referral_code) }}" 
                                           target="_blank" 
                                           class="btn btn-whatsapp" title="Share on WhatsApp" style="border-radius: 0 15px 15px 0;">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-whatsapp" viewBox="0 0 16 16">
                                                <path d="M13.601 2.326A7.854 7.854 0 0 0 7.994 0C3.627 0 .068 3.558.064 7.926c0 1.399.366 2.76 1.057 3.965L0 16l4.204-1.102a7.933 7.933 0 0 0 3.79.965h.004c4.368 0 7.926-3.558 7.93-7.93A7.898 7.898 0 0 0 13.601 2.326zM7.994 14.521a6.573 6.573 0 0 1-3.356-.92l-.24-.144-2.494.654.666-2.433-.156-.251a6.56 6.56 0 0 1-1.007-3.505c0-3.626 2.957-6.584 6.591-6.584a6.56 6.56 0 0 1 4.66 1.931 6.557 6.557 0 0 1 1.928 4.66c-.004 3.639-2.961 6.592-6.592 6.592zm3.615-4.934c-.197-.099-1.17-.578-1.353-.646-.182-.065-.315-.099-.445.099-.133.197-.513.646-.627.775-.114.133-.232.148-.43.05-.197-.1-.836-.308-1.592-.985-.59-.525-.985-1.175-1.103-1.372-.114-.198-.011-.304.088-.403.087-.088.197-.232.296-.346.1-.114.133-.198.198-.33.065-.134.034-.248-.015-.347-.05-.099-.445-1.076-.612-1.47-.16-.389-.323-.335-.445-.34-.114-.007-.247-.007-.38-.007a.729.729 0 0 0-.529.247c-.182.198-.691.677-.691 1.654 0 .977.71 1.916.81 2.049.098.133 1.394 2.132 3.383 2.992.47.205.84.326 1.129.418.475.152.904.129 1.246.08.38-.058 1.171-.48 1.338-.943.164-.464.164-.86.114-.943-.049-.084-.182-.133-.38-.232z"/>
                                            </svg>
                                        </a>
                                    </div>
                                </div>
                                
                                <p class="text-muted mt-3 small"><i class="ti-info-alt mr-1"></i> You can also share your link directly via the buttons above.</p>

                                <div class="mt-4">
                                    <span class="text-muted">Your Unique Referral Code: </span>
                                    <span class="badge badge-outline-primary p-2 ml-2" style="font-size: 1rem; letter-spacing: 1px; border-width: 2px;"><strong>{{ Auth::user()->referral_code }}</strong></span>
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
    function copyReferralLink(btn) {
        var copyText = document.getElementById("referralLink");
        copyText.select();
        copyText.setSelectionRange(0, 99999); /* For mobile devices */
        
        var originalHtml = btn.innerHTML;
        
        try {
            navigator.clipboard.writeText(copyText.value).then(function() {
                btn.innerHTML = '<i class="ti-check mr-2"></i> Copied!';
                btn.classList.replace('btn-primary', 'btn-success');
                
                setTimeout(function() {
                    btn.innerHTML = originalHtml;
                    btn.classList.replace('btn-success', 'btn-primary');
                }, 2000);
            });
        } catch (err) {
            // Fallback for older browsers
            document.execCommand('copy');
            btn.innerHTML = '<i class="ti-check mr-2"></i> Copied!';
            setTimeout(function() {
                btn.innerHTML = originalHtml;
            }, 2000);
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
