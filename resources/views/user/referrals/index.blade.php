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

    /* Referral share modal */
    .ref-share-card {
        background: linear-gradient(135deg, #0d6efd 0%, #4c8dff 60%, #ffffff 60%);
        border-radius: 18px;
        overflow: hidden;
        color: #fff;
        position: relative;
        box-shadow: 0 14px 40px rgba(13, 110, 253, 0.25);
    }
    .ref-share-card .ref-card-inner {
        display: grid;
        grid-template-columns: 1.2fr 1fr;
        gap: 18px;
        padding: 18px;
        align-items: center;
    }
    .ref-share-card .ref-brand {
        font-weight: 800;
        letter-spacing: .5px;
        font-size: 18px;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .ref-share-card .ref-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: rgba(255,255,255,0.15);
        border: 1px solid rgba(255,255,255,0.25);
        padding: 6px 10px;
        border-radius: 999px;
        font-weight: 700;
        font-size: 12px;
    }
    .ref-share-card .ref-title {
        font-size: 22px;
        font-weight: 800;
        line-height: 1.2;
        margin: 10px 0 6px;
    }
    .ref-share-card .ref-sub {
        opacity: .95;
        margin: 0 0 10px;
        font-size: 13px;
        line-height: 1.4;
    }
    .ref-share-card .ref-code {
        background: rgba(255,255,255,0.18);
        border: 1px dashed rgba(255,255,255,0.55);
        border-radius: 14px;
        padding: 12px 14px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
    }
    .ref-share-card .ref-code strong {
        font-size: 18px;
        letter-spacing: 2px;
    }
    .ref-share-card .ref-mini {
        font-size: 11px;
        opacity: .9;
        margin-top: 8px;
    }
    .ref-share-card .ref-right {
        background: #fff;
        border-radius: 14px;
        padding: 14px;
        color: #1f2a37;
        border: 1px solid rgba(0,0,0,0.06);
        box-shadow: 0 10px 28px rgba(0,0,0,0.08);
    }
    .ref-share-card .ref-qr-wrap {
        width: 100%;
        display: grid;
        place-items: center;
        padding: 6px 0 10px;
    }
    .ref-share-card .ref-qr {
        width: 160px;
        height: 160px;
        display: grid;
        place-items: center;
    }
    .ref-share-card .ref-link {
        font-size: 11px;
        word-break: break-all;
        color: #475569;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 8px 10px;
    }

    /* Mobile responsiveness for referral share modal */
    @media (max-width: 575.98px) {
        #referralShareModal .modal-dialog {
            margin: 0.75rem;
        }
        #referralShareModal .modal-content {
            border-radius: 14px !important;
        }
        #referralShareModal .modal-header {
            padding: 12px 14px;
        }
        #referralShareModal .modal-body {
            padding: 14px;
        }

        .ref-share-card {
            border-radius: 14px;
            background: linear-gradient(180deg, #0d6efd 0%, #4c8dff 55%, #ffffff 55%);
        }
        .ref-share-card .ref-card-inner {
            grid-template-columns: 1fr;
            gap: 12px;
            padding: 14px;
        }
        .ref-share-card .ref-title {
            font-size: 18px;
        }
        .ref-share-card .ref-code strong {
            font-size: 16px;
            letter-spacing: 1px;
        }
        .ref-share-card .ref-right {
            padding: 12px;
        }
        .ref-share-card .ref-qr {
            width: 124px;
            height: 124px;
        }
        .ref-share-card .ref-link {
            font-size: 10px;
            padding: 8px 9px;
        }

        .ref-modal-actions .btn {
            width: 100%;
        }
        #refWhatsappMessage {
            min-height: 140px;
        }
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
                                        <button
                                            type="button"
                                            class="btn btn-whatsapp"
                                            title="Share on WhatsApp"
                                            style="border-radius: 0 15px 15px 0;"
                                            onclick="openReferralShareModal()">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-whatsapp" viewBox="0 0 16 16">
                                                <path d="M13.601 2.326A7.854 7.854 0 0 0 7.994 0C3.627 0 .068 3.558.064 7.926c0 1.399.366 2.76 1.057 3.965L0 16l4.204-1.102a7.933 7.933 0 0 0 3.79.965h.004c4.368 0 7.926-3.558 7.93-7.93A7.898 7.898 0 0 0 13.601 2.326zM7.994 14.521a6.573 6.573 0 0 1-3.356-.92l-.24-.144-2.494.654.666-2.433-.156-.251a6.56 6.56 0 0 1-1.007-3.505c0-3.626 2.957-6.584 6.591-6.584a6.56 6.56 0 0 1 4.66 1.931 6.557 6.557 0 0 1 1.928 4.66c-.004 3.639-2.961 6.592-6.592 6.592zm3.615-4.934c-.197-.099-1.17-.578-1.353-.646-.182-.065-.315-.099-.445.099-.133.197-.513.646-.627.775-.114.133-.232.148-.43.05-.197-.1-.836-.308-1.592-.985-.59-.525-.985-1.175-1.103-1.372-.114-.198-.011-.304.088-.403.087-.088.197-.232.296-.346.1-.114.133-.198.198-.33.065-.134.034-.248-.015-.347-.05-.099-.445-1.076-.612-1.47-.16-.389-.323-.335-.445-.34-.114-.007-.247-.007-.38-.007a.729.729 0 0 0-.529.247c-.182.198-.691.677-.691 1.654 0 .977.71 1.916.81 2.049.098.133 1.394 2.132 3.383 2.992.47.205.84.326 1.129.418.475.152.904.129 1.246.08.38-.058 1.171-.48 1.338-.943.164-.464.164-.86.114-.943-.049-.084-.182-.133-.38-.232z"/>
                                            </svg>
                                        </button>
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

{{-- Referral Share Modal (WhatsApp) --}}
<div class="modal fade" id="referralShareModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content" style="border-radius: 18px; overflow: hidden;">
            <div class="modal-header" style="background:#0d6efd; color:#fff;">
                <h5 class="modal-title" style="font-weight:800;">
                    <i class="ti-share mr-2"></i> Share Referral
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-lg-7 mb-3 mb-lg-0">
                        <div id="refShareCard" class="ref-share-card">
                            <div class="ref-card-inner">
                                <div>
                                    <div class="ref-brand">
                                        <span style="display:inline-flex;width:34px;height:34px;border-radius:10px;background:rgba(255,255,255,0.2);align-items:center;justify-content:center;font-weight:900;">BH</span>
                                        BookHub
                                    </div>
                                    <div class="ref-badge mt-2">
                                        <i class="ti-gift"></i> Earn ₹50 per referral
                                    </div>
                                    <div class="ref-title">Invite friends. Earn rewards.</div>
                                    <p class="ref-sub">
                                        Your friend signs up using your code/link and makes their first purchase — you get ₹50 in your wallet.
                                    </p>

                                    <div class="ref-code">
                                        <div>
                                            <div style="font-size:11px;opacity:.9;">Referral Code</div>
                                            <strong id="refModalCodeText">{{ Auth::user()->referral_code }}</strong>
                                        </div>
                                        <div style="text-align:right;">
                                            <div style="font-size:11px;opacity:.9;">Link</div>
                                            <div style="font-weight:700;">Scan QR</div>
                                        </div>
                                    </div>
                                    <div class="ref-mini">
                                        Share this card or send the link directly.
                                    </div>
                                </div>

                                <div class="ref-right">
                                    <div class="ref-qr-wrap">
                                        <div id="refModalQr" class="ref-qr"></div>
                                    </div>
                                    <div class="ref-link" id="refModalLinkText">
                                        {{ url('/') }}?ref={{ Auth::user()->referral_code }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-3 d-flex flex-wrap ref-modal-actions" style="gap:10px;">
                            <button type="button" class="btn btn-outline-primary" onclick="copyTextFromEl('refModalCodeText', this)">
                                <i class="ti-clipboard mr-1"></i> Copy Code
                            </button>
                            <button type="button" class="btn btn-outline-primary" onclick="copyTextFromEl('refModalLinkText', this)">
                                <i class="ti-clipboard mr-1"></i> Copy Link
                            </button>
                            <button type="button" class="btn btn-primary" onclick="downloadReferralCardImage(this)">
                                <i class="ti-download mr-1"></i> Download Image
                            </button>
                        </div>
                        <small class="text-muted d-block mt-2">
                            WhatsApp sharing supports text links; you can download the image and share it in chat if needed.
                        </small>
                    </div>

                    <div class="col-lg-5">
                        <div class="p-3 bg-light rounded border">
                            <h6 style="font-weight:800;">Send on WhatsApp</h6>
                            <div class="form-group mb-2">
                                <label class="small text-muted mb-1">Message Preview</label>
                                <textarea class="form-control" id="refWhatsappMessage" rows="5" style="border-radius:12px;"></textarea>
                            </div>
                            <button type="button" class="btn btn-whatsapp btn-block" onclick="shareReferralOnWhatsapp()">
                                <span style="display:inline-flex;align-items:center;gap:10px;justify-content:center;">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="bi bi-whatsapp" viewBox="0 0 16 16">
                                        <path d="M13.601 2.326A7.854 7.854 0 0 0 7.994 0C3.627 0 .068 3.558.064 7.926c0 1.399.366 2.76 1.057 3.965L0 16l4.204-1.102a7.933 7.933 0 0 0 3.79.965h.004c4.368 0 7.926-3.558 7.93-7.93A7.898 7.898 0 0 0 13.601 2.326zM7.994 14.521a6.573 6.573 0 0 1-3.356-.92l-.24-.144-2.494.654.666-2.433-.156-.251a6.56 6.56 0 0 1-1.007-3.505c0-3.626 2.957-6.584 6.591-6.584a6.56 6.56 0 0 1 4.66 1.931 6.557 6.557 0 0 1 1.928 4.66c-.004 3.639-2.961 6.592-6.592 6.592zm3.615-4.934c-.197-.099-1.17-.578-1.353-.646-.182-.065-.315-.099-.445.099-.133.197-.513.646-.627.775-.114.133-.232.148-.43.05-.197-.1-.836-.308-1.592-.985-.59-.525-.985-1.175-1.103-1.372-.114-.198-.011-.304.088-.403.087-.088.197-.232.296-.346.1-.114.133-.198.198-.33.065-.134.034-.248-.015-.347-.05-.099-.445-1.076-.612-1.47-.16-.389-.323-.335-.445-.34-.114-.007-.247-.007-.38-.007a.729.729 0 0 0-.529.247c-.182.198-.691.677-.691 1.654 0 .977.71 1.916.81 2.049.098.133 1.394 2.132 3.383 2.992.47.205.84.326 1.129.418.475.152.904.129 1.246.08.38-.058 1.171-.48 1.338-.943.164-.464.164-.86.114-.943-.049-.084-.182-.133-.38-.232z"/>
                                    </svg>
                                    Share on WhatsApp
                                </span>
                            </button>
                            <div class="mt-2 small text-muted">
                                Tip: Add your friend’s name or a short note before sharing.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
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

    function openReferralShareModal() {
        var link = document.getElementById('referralLink')?.value || '';
        var code = @json(Auth::user()->referral_code);

        // Fill WhatsApp message
        var msg =
            "*BookHub Referral*\n\n" +
            "Use my referral code: *" + code + "*\n" +
            "Sign up using this link:\n" + link + "\n\n" +
            "After your first purchase, you’ll unlock great deals and I’ll receive referral rewards.";
        var ta = document.getElementById('refWhatsappMessage');
        if (ta) ta.value = msg;

        // Render QR (safe re-render)
        var qrEl = document.getElementById('refModalQr');
        if (qrEl) qrEl.innerHTML = '';
        var qrSize = (window.matchMedia && window.matchMedia('(max-width: 575.98px)').matches) ? 124 : 160;
        if (window.QRCode && qrEl) {
            new QRCode(qrEl, {
                text: link,
                width: qrSize,
                height: qrSize,
                colorDark: "#111827",
                colorLight: "#ffffff",
                correctLevel: QRCode.CorrectLevel.M
            });
        } else if (qrEl) {
            qrEl.innerHTML = '<div class="text-muted small">QR loading…</div>';
        }

        $('#referralShareModal').modal('show');
    }

    function shareReferralOnWhatsapp() {
        var ta = document.getElementById('refWhatsappMessage');
        var text = ta ? ta.value : '';
        var url = 'https://api.whatsapp.com/send?text=' + encodeURIComponent(text);
        window.open(url, '_blank');
    }

    function copyTextFromEl(elId, btn) {
        var el = document.getElementById(elId);
        if (!el) return;
        var text = (el.innerText || el.value || '').trim();
        if (!text) return;

        var original = btn ? btn.innerHTML : null;
        var done = function () {
            if (!btn) return;
            btn.innerHTML = '<i class="ti-check mr-1"></i> Copied';
            btn.classList.remove('btn-outline-primary');
            btn.classList.add('btn-success');
            setTimeout(function () {
                btn.innerHTML = original;
                btn.classList.remove('btn-success');
                btn.classList.add('btn-outline-primary');
            }, 1600);
        };

        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(text).then(done).catch(function () {
                done();
            });
        } else {
            // Fallback
            var tmp = document.createElement('textarea');
            tmp.value = text;
            document.body.appendChild(tmp);
            tmp.select();
            document.execCommand('copy');
            document.body.removeChild(tmp);
            done();
        }
    }

    async function downloadReferralCardImage(btn) {
        var card = document.getElementById('refShareCard');
        if (!card || !window.html2canvas) return;

        var original = btn ? btn.innerHTML : null;
        if (btn) {
            btn.disabled = true;
            btn.innerHTML = '<i class="ti-time mr-1"></i> Preparing...';
        }

        try {
            var canvas = await html2canvas(card, {
                backgroundColor: null,
                scale: 2,
                useCORS: true
            });
            var dataUrl = canvas.toDataURL('image/png');
            var a = document.createElement('a');
            a.href = dataUrl;
            a.download = 'bookhub-referral-' + @json(Auth::user()->referral_code) + '.png';
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
        } finally {
            if (btn) {
                btn.disabled = false;
                btn.innerHTML = original;
            }
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

<!-- Referral share helpers -->
<script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/html2canvas@1.4.1/dist/html2canvas.min.js"></script>
