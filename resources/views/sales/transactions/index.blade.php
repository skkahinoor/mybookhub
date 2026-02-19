@extends('layouts.app')

@section('title')
    Earnings History
@endsection

@section('content')
    <div class="container-fluid py-4">

        {{-- Header --}}
        <div class="row mb-4">
            <div class="col-12">
                <h2 class="page-title mb-0">Earnings History</h2>
                <p class="text-muted mb-0">Track all your commissions and income sources</p>
            </div>
        </div>

        {{-- ── Row 1: Count cards (Wallet, Students, Vendors) ── --}}
        <div class="row g-3 mb-4">
            {{-- Wallet Balance --}}
            <div class="col-6 col-md-3">
                <div class="card h-100 border-0 shadow-sm text-white"
                    style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <p class="mb-1 small opacity-75">Wallet Balance</p>
                            <h3 class="fw-bold mb-0">₹{{ number_format(Auth::guard('sales')->user()->wallet_balance, 2) }}
                            </h3>
                        </div>
                        <div class="rounded-circle p-2 opacity-75" style="background: rgba(255,255,255,0.2);">
                            <i class="bi bi-wallet2 fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Student Enrollments --}}
            <div class="col-6 col-md-3">
                <div class="card h-100 border-0 shadow-sm text-white"
                    style="background: linear-gradient(135deg, #0dcaf0 0%, #0a7abf 100%);">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <p class="mb-1 small opacity-75">Student Enrollments</p>
                            <h3 class="fw-bold mb-0">{{ $totalStudentsCount }}</h3>
                        </div>
                        <div class="rounded-circle p-2 opacity-75" style="background: rgba(255,255,255,0.2);">
                            <i class="bi bi-person-check fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Free Vendors Enroll --}}
            <div class="col-6 col-md-3">
                <div class="card h-100 border-0 shadow-sm text-white"
                    style="background: linear-gradient(135deg, #28a745 0%, #1a7a30 100%);">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <p class="mb-1 small opacity-75">Free Vendors Enroll</p>
                            <h3 class="fw-bold mb-0">{{ $freeVendorCount }}</h3>
                        </div>
                        <div class="rounded-circle p-2 opacity-75" style="background: rgba(255,255,255,0.2);">
                            <i class="bi bi-shop fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Pro Vendors Enroll --}}
            <div class="col-6 col-md-3">
                <div class="card h-100 border-0 shadow-sm text-white"
                    style="background: linear-gradient(135deg,#667eea,#764ba2);">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <p class="mb-1 small opacity-75">Pro Vendors Enroll</p>
                            <h3 class="fw-bold mb-0">{{ $proVendorCount }}</h3>
                        </div>
                        <div class="rounded-circle p-2 opacity-75" style="background: rgba(255,255,255,0.2);">
                            <i class="bi bi-shop fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── Row 2: Earning stat cards ── --}}
        <div class="row g-3 mb-4">
            <div class="col-6 col-md-3">
                <div class="card border-0 shadow-sm h-100 text-white"
                    style="background: linear-gradient(135deg,#28a745,#1a7a30);">
                    <div class="card-body">
                        <i class="bi bi-cash-stack fs-3 mb-2 d-block opacity-75"></i>
                        <p class="mb-1 small opacity-75">Today's Earning</p>
                        <h3 class="fw-bold mb-0">₹{{ number_format($todayEarning, 2) }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card border-0 shadow-sm h-100 text-white"
                    style="background: linear-gradient(135deg,#0dcaf0,#0a7abf);">
                    <div class="card-body">
                        <i class="bi bi-calendar-week fs-3 mb-2 d-block opacity-75"></i>
                        <p class="mb-1 small opacity-75">Weekly Earning</p>
                        <h3 class="fw-bold mb-0">₹{{ number_format($weeklyEarning, 2) }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card border-0 shadow-sm h-100 text-white"
                    style="background: linear-gradient(135deg,#fd7e14,#c9510c);">
                    <div class="card-body">
                        <i class="bi bi-calendar fs-3 mb-2 d-block opacity-75"></i>
                        <p class="mb-1 small opacity-75">Monthly Earning</p>
                        <h3 class="fw-bold mb-0">₹{{ number_format($monthlyEarning, 2) }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card border-0 shadow-sm h-100 text-white"
                    style="background: linear-gradient(135deg,#667eea,#764ba2);">
                    <div class="card-body">
                        <i class="bi bi-graph-up-arrow fs-3 mb-2 d-block opacity-75"></i>
                        <p class="mb-1 small opacity-75">Total Earning</p>
                        <h3 class="fw-bold mb-0">₹{{ number_format($totalEarning, 2) }}</h3>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── Commission rate legend ── --}}
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-body py-2 px-4">
                <div class="d-flex gap-3 flex-wrap align-items-center small">
                    <span class="fw-semibold text-muted me-1">Commission rates:</span>
                    <span>
                        <span class="badge" style="background:#0dcaf0;">Student Enrollment</span>
                        — ₹{{ \App\Models\Setting::getValue('default_income_per_target', 10) }} per approval
                    </span>
                    <span>
                        <span class="badge bg-success">Free Vendor</span>
                        — ₹{{ \App\Models\Setting::getValue('default_income_per_vendor', 50) }} when free vendor activates
                    </span>
                    <span>
                        <span class="badge" style="background:#ffc107;color:#000;">Pro Vendor</span>
                        — ₹{{ \App\Models\Setting::getValue('default_income_per_pro_vendor', 100) }} when vendor upgrades
                        to Pro
                    </span>
                </div>
            </div>
        </div>

        {{-- ── Transactions Table ── --}}
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0">All Transactions</h5>
                <span class="badge bg-secondary">{{ $transactions->total() }} total</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light text-muted small text-uppercase">
                            <tr>
                                <th class="ps-4" style="width:55px;">#</th>
                                <th style="width:190px;">Type</th>
                                <th>Description</th>
                                <th style="width:120px;">Amount</th>
                                <th style="width:130px;">Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($transactions as $transaction)
                                @php
                                    $desc = $transaction->description ?? '';

                                    if (stripos($desc, 'Commission for Student') !== false) {
                                        $typeLabel = 'Student Enrollment';
                                        $typeBg = '#0dcaf0';
                                        $typeColor = '#fff';
                                    } elseif (
                                        stripos($desc, 'Commission for Vendor') !== false &&
                                        stripos($desc, 'Pro') !== false
                                    ) {
                                        $typeLabel = 'Pro Vendor Referral';
                                        $typeBg = '#ffc107';
                                        $typeColor = '#000';
                                    } elseif (stripos($desc, 'Commission for Vendor') !== false) {
                                        $typeLabel = 'Free Vendor Referral';
                                        $typeBg = '#28a745';
                                        $typeColor = '#fff';
                                    } else {
                                        $typeLabel = 'Bonus';
                                        $typeBg = '#6f42c1';
                                        $typeColor = '#fff';
                                    }
                                @endphp
                                <tr>
                                    <td class="ps-4 text-muted small">
                                        {{ ($transactions->currentPage() - 1) * $transactions->perPage() + $loop->iteration }}
                                    </td>
                                    <td>
                                        <span class="badge fw-semibold"
                                            style="background:{{ $typeBg }};color:{{ $typeColor }};padding:.45em .8em;font-size:.73rem;">
                                            {{ $typeLabel }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-column">
                                            <span style="font-size:.88rem;">{{ $desc }}</span>
                                            @if ($transaction->order_id)
                                                <small class="text-muted">
                                                    <i class="bi bi-bag me-1"></i>Order #{{ $transaction->order_id }}
                                                </small>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="fw-bold text-success" style="font-size:1rem;">
                                        +₹{{ number_format($transaction->amount, 2) }}
                                    </td>
                                    <td class="text-muted small">
                                        {{ $transaction->created_at->format('M d, Y') }}<br>
                                        <span
                                            style="font-size:.75rem;">{{ $transaction->created_at->format('h:i A') }}</span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5 text-muted">
                                        <i class="bi bi-wallet2 fs-1 mb-3 d-block opacity-25"></i>
                                        <p class="mb-0">No transactions found yet.</p>
                                        <small>Your earnings from student enrollments and vendor referrals will appear
                                            here.</small>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if ($transactions->hasPages())
                <div class="card-footer bg-white py-3">
                    {{ $transactions->links() }}
                </div>
            @endif
        </div>
    </div>

    <style>
        .page-title {
            color: #1a202c;
            font-weight: 700;
            letter-spacing: -.02em;
        }

        .table thead th {
            font-weight: 600;
            border-top: none;
            font-size: .72rem;
            letter-spacing: .05em;
        }

        .card {
            border-radius: .75rem;
        }

        .card:hover {
            box-shadow: 0 4px 14px rgba(0, 0, 0, .1) !important;
        }
    </style>
@endsection
