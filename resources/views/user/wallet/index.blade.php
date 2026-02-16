@include('user.layout.header')

<div class="container-fluid page-body-wrapper">
    @include('user.layout.sidebar')

    <div class="main-panel">
        <div class="content-wrapper">
            <!-- Wallet Balance Summary -->
            <div class="row">
                <div class="col-md-12 grid-margin">
                    <div class="row">
                        <div class="col-12 col-xl-8 mb-4 mb-xl-0">
                            <h3 class="font-weight-bold">My Wallet</h3>
                            <h6 class="font-weight-normal mb-0">Manage your wallet balance and view transaction history
                            </h6>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Wallet Stats Cards -->
            <div class="row">
                <div class="col-md-4 grid-margin stretch-card">
                    <div class="card tale-bg">
                        <div class="card-body">
                            <p class="mb-4 text-white">Current Balance</p>
                            <p class="fs-30 mb-2 text-white font-weight-bold">
                                ₹{{ number_format($user->wallet_balance, 2) }}</p>
                            <p class="text-white">Available to spend</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 grid-margin stretch-card">
                    <div class="card card-dark-blue">
                        <div class="card-body">
                            <p class="mb-4 text-white">Total Credits</p>
                            <p class="fs-30 mb-2 text-white font-weight-bold">₹{{ number_format($totalCredits, 2) }}</p>
                            <p class="text-white">Money added to wallet</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 grid-margin stretch-card">
                    <div class="card card-light-danger">
                        <div class="card-body">
                            <p class="mb-4 text-white">Total Spent</p>
                            <p class="fs-30 mb-2 text-white font-weight-bold">₹{{ number_format($totalDebits, 2) }}</p>
                            <p class="text-white">Money spent from wallet</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Transaction History -->
            <div class="row">
                <div class="col-md-12 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <p class="card-title mb-4">Transaction History</p>
                            <div class="table-responsive">
                                <table class="table table-striped table-borderless">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Transaction ID</th>
                                            <th>Description</th>
                                            <th>Type</th>
                                            <th>Amount</th>
                                            <th>Balance After</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($transactions as $transaction)
                                            <tr>
                                                <td>{{ $transaction->created_at->format('M d, Y h:i A') }}</td>
                                                <td>#{{ $transaction->id }}</td>
                                                <td>
                                                    {{ $transaction->description }}
                                                    @if ($transaction->order_id)
                                                        <br><small class="text-muted">Order
                                                            #{{ $transaction->order_id }}</small>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($transaction->type === 'credit')
                                                        <span class="badge badge-success">Credit</span>
                                                    @else
                                                        <span class="badge badge-danger">Debit</span>
                                                    @endif
                                                </td>
                                                <td
                                                    class="font-weight-bold {{ $transaction->type === 'credit' ? 'text-success' : 'text-danger' }}">
                                                    {{ $transaction->type === 'credit' ? '+' : '-' }}₹{{ number_format($transaction->amount, 2) }}
                                                </td>
                                                <td>₹{{ number_format($transaction->balance_after, 2) }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="text-center text-muted py-4">
                                                    <i class="icon-wallet" style="font-size: 48px; opacity: 0.3;"></i>
                                                    <p class="mt-3">No transactions yet</p>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            <!-- Pagination -->
                            @if ($transactions->hasPages())
                                <div class="d-flex justify-content-center mt-4">
                                    {{ $transactions->links() }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

@include('user.layout.footer')

</div>
<!-- plugins:js -->
<script src="{{ asset('user/vendors/js/vendor.bundle.base.js') }}"></script>
<!-- endinject -->
<!-- inject:js -->
<script src="{{ asset('user/js/off-canvas.js') }}"></script>
<script src="{{ asset('user/js/hoverable-collapse.js') }}"></script>
<script src="{{ asset('user/js/template.js') }}"></script>
<script src="{{ asset('user/js/settings.js') }}"></script>
<script src="{{ asset('user/js/todolist.js') }}"></script>
<!-- endinject -->
</body>

</html>
