@extends('layouts.app')
@section('title')
    Request Withdrawal
@endsection
@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="page-title mb-0">Request Withdrawal</h2>
            <p class="text-muted mb-0">Submit a withdrawal request</p>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0">Withdrawal Details</h5>
                </div>
                <div class="card-body">
                    <!-- Available Balance Info -->
                    <div class="alert alert-info mb-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <strong>Available Balance:</strong> ₹{{ number_format($availableBalance, 2) }}
                            </div>
                            <i class="bi bi-wallet2 fs-4"></i>
                        </div>
                    </div>

                    <!-- Bank Details Info -->
                    @if($salesExecutive->bank_name && $salesExecutive->account_number)
                        <div class="alert alert-success mb-4">
                            <h6 class="mb-2"><i class="bi bi-bank me-2"></i>Bank Details</h6>
                            <p class="mb-1"><strong>Bank:</strong> {{ $salesExecutive->bank_name }}</p>
                            <p class="mb-1"><strong>Account Number:</strong> {{ substr($salesExecutive->account_number, 0, 4) }}****{{ substr($salesExecutive->account_number, -4) }}</p>
                            <p class="mb-1"><strong>IFSC:</strong> {{ $salesExecutive->ifsc_code }}</p>
                            @if($salesExecutive->upi_id)
                                <p class="mb-0"><strong>UPI ID:</strong> {{ $salesExecutive->upi_id }}</p>
                            @endif
                        </div>
                    @else
                        <div class="alert alert-warning mb-4">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            Please update your bank details in your profile before requesting withdrawal.
                            <a href="{{ route('sales.profile.edit') }}" class="alert-link">Update Profile</a>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('sales.withdrawals.store') }}">
                        @csrf

                        <div class="mb-3">
                            <label for="amount" class="form-label">Withdrawal Amount <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">₹</span>
                                <input type="number" 
                                       class="form-control @error('amount') is-invalid @enderror" 
                                       id="amount" 
                                       name="amount" 
                                       step="0.01"
                                       min="1"
                                       max="{{ $availableBalance }}"
                                       value="{{ old('amount') }}"
                                       placeholder="Enter withdrawal amount"
                                       required>
                                @error('amount')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <small class="form-text text-muted">Maximum withdrawal amount: ₹{{ number_format($availableBalance, 2) }}</small>
                        </div>

                        <div class="mb-3">
                            <label for="payment_method" class="form-label">Payment Method <span class="text-danger">*</span></label>
                            <select class="form-select @error('payment_method') is-invalid @enderror" 
                                    id="payment_method" 
                                    name="payment_method" 
                                    required>
                                <option value="">Select Payment Method</option>
                                <option value="bank_transfer" {{ old('payment_method') == 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                                <option value="upi" {{ old('payment_method') == 'upi' ? 'selected' : '' }}>UPI</option>
                            </select>
                            @error('payment_method')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="remarks" class="form-label">Remarks (Optional)</label>
                            <textarea class="form-control @error('remarks') is-invalid @enderror" 
                                      id="remarks" 
                                      name="remarks" 
                                      rows="3"
                                      maxlength="500"
                                      placeholder="Any additional information...">{{ old('remarks') }}</textarea>
                            @error('remarks')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Maximum 500 characters</small>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="{{ route('sales.withdrawals.index') }}" class="btn btn-secondary">
                                <i class="bi bi-x-circle me-1"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle me-1"></i> Submit Request
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const amountInput = document.getElementById('amount');
    const maxAmount = {{ $availableBalance }};
    
    amountInput.addEventListener('input', function() {
        const value = parseFloat(this.value);
        if (value > maxAmount) {
            this.setCustomValidity('Amount cannot exceed available balance');
        } else if (value <= 0) {
            this.setCustomValidity('Amount must be greater than 0');
        } else {
            this.setCustomValidity('');
        }
    });
});
</script>
@endsection

