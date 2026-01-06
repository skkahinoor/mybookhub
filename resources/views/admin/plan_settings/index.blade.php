@extends('admin.layout.layout')

@section('content')
<div class="main-panel">
    <div class="content-wrapper">
        <div class="row justify-content-center">
            <div class="col-lg-10 grid-margin stretch-card">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <h4 class="card-title mb-1">Vendor Plan Settings</h4>
                                <p class="text-muted mb-0 small">
                                    Configure pricing, limits and trial settings for vendor plans.
                                </p>
                            </div>
                        </div>

                        {{-- Success message --}}
                        @if (session('success_message'))
                            <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
                                <strong>Success:</strong> {{ session('success_message') }}
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        @endif

                        {{-- Error message --}}
                        @if (session('error_message'))
                            <div class="alert alert-danger alert-dismissible fade show mt-3" role="alert">
                                <strong>Error:</strong> {{ session('error_message') }}
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        @endif

                        {{-- Validation errors --}}
                        @if ($errors->any())
                            <div class="alert alert-danger alert-dismissible fade show mt-3" role="alert">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        @endif

                        <form method="POST"
                              action="{{ route('admin.plan.settings.update') }}"
                              class="mt-4">
                            @csrf

                            {{-- Plan configuration --}}
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="pro_plan_price">Pro Plan Price (₹)</label>
                                        <input type="number"
                                               class="form-control form-control-lg"
                                               id="pro_plan_price"
                                               name="pro_plan_price"
                                               value="{{ $proPlanPrice }}"
                                               min="1"
                                               step="0.01"
                                               required>
                                        <small class="form-text text-muted">
                                            Monthly Pro plan price in rupees.
                                        </small>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="free_plan_book_limit">Free Plan Book Upload Limit</label>
                                        <input type="number"
                                               class="form-control form-control-lg"
                                               id="free_plan_book_limit"
                                               name="free_plan_book_limit"
                                               value="{{ $freePlanBookLimit }}"
                                               min="1"
                                               required>
                                        <small class="form-text text-muted">
                                            Maximum books a vendor can upload per month on Free plan.
                                        </small>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="pro_plan_trial_duration_days">Pro Plan Trial Duration (Days)</label>
                                        <input type="number"
                                               class="form-control form-control-lg"
                                               id="pro_plan_trial_duration_days"
                                               name="pro_plan_trial_duration_days"
                                               value="{{ $proPlanTrialDurationDays }}"
                                               min="1"
                                               max="365"
                                               required>
                                        <small class="form-text text-muted">
                                            Number of days new vendors get Pro access before downgrade.
                                        </small>
                                    </div>
                                </div>
                            </div>

                            {{-- New users Pro toggle --}}
                            <div class="form-group mt-2">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox"
                                           class="custom-control-input"
                                           id="give_new_users_pro_plan"
                                           name="give_new_users_pro_plan"
                                           value="1"
                                           {{ $giveNewUsersProPlan ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="give_new_users_pro_plan">
                                        Give new users Pro plan initially
                                    </label>
                                </div>
                                <small class="form-text text-muted">
                                    If enabled, all new vendors start on Pro plan for the trial period
                                    and then automatically move to Free plan.
                                </small>
                            </div>

                            <hr class="my-4">

                            {{-- Invite Pro link section --}}
                            <div class="row">
                                <div class="col-md-8">
                                    <h5 class="mb-2">Invite Pro Link</h5>
                                    <p class="text-muted small mb-3">
                                        Share this link so vendors can sign up directly to Pro without payment.
                                    </p>

                                    <div class="form-group mb-2">
                                        <label for="invite_pro_link">Invite Link</label>
                                        <div class="input-group">
                                            <input type="text"
                                                   class="form-control"
                                                   id="invite_pro_link"
                                                   value="{{ $inviteProLink }}"
                                                   readonly>
                                            <div class="input-group-append">
                                                <button type="button"
                                                        class="btn btn-outline-primary"
                                                        id="copy_invite_link">
                                                    Copy link
                                                </button>
                                            </div>
                                        </div>
                                        <small id="copy_status"
                                               class="form-text text-success d-none">
                                            Link copied to clipboard
                                        </small>
                                    </div>

                                    @php
                                        $encodedInviteLink = urlencode($inviteProLink);
                                    @endphp

                                    <div class="d-flex flex-wrap align-items-center mt-2">
                                        <a class="btn btn-success btn-sm mr-2 mb-2"
                                           target="_blank"
                                           rel="noopener"
                                           href="https://wa.me/?text={{ $encodedInviteLink }}">
                                            <i class="mdi mdi-whatsapp"></i> WhatsApp
                                        </a>
                                        <a class="btn btn-info btn-sm text-white mr-2 mb-2"
                                           target="_blank"
                                           rel="noopener"
                                           href="mailto:?subject=Pro%20Plan%20Invite&body={{ $encodedInviteLink }}">
                                            <i class="mdi mdi-email"></i> Email
                                        </a>
                                        <a class="btn btn-primary btn-sm mb-2"
                                           target="_blank"
                                           rel="noopener"
                                           href="sms:?body={{ $encodedInviteLink }}">
                                            <i class="mdi mdi-message-text"></i> Text
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-4 d-flex justify-content-end">
                                <a href="{{ url('admin/dashboard') }}"
                                   class="btn btn-light mr-2">
                                    Cancel
                                </a>
                                <button type="submit"
                                        class="btn btn-primary">
                                    Update Settings
                                </button>
                            </div>
                        </form>

                    </div> {{-- card-body --}}
                </div> {{-- card --}}
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const copyBtn = document.getElementById('copy_invite_link');
        const input   = document.getElementById('invite_pro_link');
        const status  = document.getElementById('copy_status');

        copyBtn?.addEventListener('click', function() {
            if (!input) return;
            input.select();
            input.setSelectionRange(0, 99999);
            navigator.clipboard.writeText(input.value).then(() => {
                if (status) {
                    status.textContent = 'Link copied to clipboard';
                    status.classList.remove('d-none');
                    setTimeout(() => status.classList.add('d-none'), 2000);
                }
            }).catch(() => {
                if (status) {
                    status.textContent = 'Copy failed. Please copy manually.';
                    status.classList.remove('d-none');
                }
            });
        });
    });
</script>
@endpush
