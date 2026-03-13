@include('user.layout.header')

<div class="container-fluid page-body-wrapper">
    @include('user.layout.sidebar')

    <div class="main-panel">
        <div class="content-wrapper">
            <div class="row">
                <div class="col-md-12 grid-margin">
                    <div class="row align-items-center">
                        <div class="col-12 col-xl-8 mb-3 mb-xl-0">
                            <h3 class="font-weight-bold mb-1">Welcome {{ Auth::user()->name ?? 'Student' }}</h3>
                            <div class="text-muted">
                                All systems are running smoothly. You have
                                <span class="text-primary font-weight-bold">{{ $unreadAlertsCount ?? 0 }}</span>
                                unread alert{{ ($unreadAlertsCount ?? 0) === 1 ? '' : 's' }}.
                            </div>
                        </div>
                        <div class="col-12 col-xl-4">
                            <div class="justify-content-xl-end d-flex">
                                <div class="btn btn-sm bg-white" style="border-radius:999px; border:1px solid rgba(17,24,39,.08);">
                                    <i class="mdi mdi-calendar"></i> Today ({{ now()->format('d M Y') }})
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @if(Auth::user()->can('view_orders'))
                <div class="row">
                    <div class="col-md-3 grid-margin stretch-card">
                        <div class="card">
                            <div class="card-body">
                                <p class="text-muted mb-1">Today’s Orders</p>
                                <p class="h3 mb-1">{{ (int) ($todayOrders ?? 0) }}</p>
                                <small class="text-muted">₹{{ number_format((float) ($todayOrdersWorth ?? 0), 2) }}</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 grid-margin stretch-card">
                        <div class="card">
                            <div class="card-body">
                                <p class="text-muted mb-1">Weekly Orders</p>
                                <p class="h3 mb-1">{{ (int) ($weeklyOrders ?? 0) }}</p>
                                <small class="text-muted">₹{{ number_format((float) ($weeklyOrdersWorth ?? 0), 2) }}</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 grid-margin stretch-card">
                        <div class="card">
                            <div class="card-body">
                                <p class="text-muted mb-1">Monthly Orders</p>
                                <p class="h3 mb-1">{{ (int) ($monthlyOrders ?? 0) }}</p>
                                <small class="text-muted">₹{{ number_format((float) ($monthlyOrdersWorth ?? 0), 2) }}</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 grid-margin stretch-card">
                        <div class="card">
                            <div class="card-body">
                                <p class="text-muted mb-1">Total Orders</p>
                                <p class="h3 mb-1">{{ (int) ($totalOrders ?? 0) }}</p>
                                <small class="text-muted">₹{{ number_format((float) ($totalSpent ?? 0), 2) }}</small>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <div class="row">
                @if(Auth::user()->can('view_orders'))
                    <div class="col-md-6 grid-margin stretch-card">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5 class="mb-0">Monthly Spending</h5>
                                    <span class="badge badge-info">Last 6 months</span>
                                </div>
                                <div style="position: relative; height: 260px;">
                                    <canvas id="spendingChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 grid-margin stretch-card">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5 class="mb-0">Order Statistics</h5>
                                </div>
                                <div style="position: relative; height: 260px;">
                                    <canvas id="orderStatusChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <div class="row">
                @if(Auth::user()->can('view_orders'))
                    <div class="col-md-7 grid-margin stretch-card">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <h5 class="mb-0">Recent Orders</h5>
                                    <span class="badge badge-success">Latest 5</span>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-borderless table-striped mb-0">
                                        <thead>
                                        <tr>
                                            <th>Order ID</th>
                                            <th>Products</th>
                                            <th>Total</th>
                                            <th>Date</th>
                                            <th>Status</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @forelse($recentOrders ?? [] as $order)
                                            @php
                                                $status = strtolower($order->order_status ?? '');
                                                $badgeClass = 'badge-secondary';
                                                if (str_contains($status, 'pending')) $badgeClass = 'badge-warning';
                                                elseif (str_contains($status, 'shipped') || str_contains($status, 'delivered')) $badgeClass = 'badge-success';
                                                elseif (str_contains($status, 'cancel')) $badgeClass = 'badge-danger';
                                                elseif (str_contains($status, 'progress') || str_contains($status, 'processing')) $badgeClass = 'badge-info';
                                            @endphp
                                            <tr>
                                                <td>#{{ $order->id }}</td>
                                                <td>
                                                    @foreach(($order->orders_products ?? []) as $p)
                                                        {{ $p->product_name }}@if(!$loop->last), @endif
                                                    @endforeach
                                                </td>
                                                <td class="font-weight-bold">₹{{ number_format((float) $order->grand_total, 2) }}</td>
                                                <td>{{ $order->created_at?->format('M d, Y') }}</td>
                                                <td><span class="badge {{ $badgeClass }}">{{ $order->order_status ?? 'N/A' }}</span></td>
                                            </tr>
                                        @empty
                                            <tr><td colspan="5" class="text-center text-muted">No recent orders found.</td></tr>
                                        @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <div class="col-md-5 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h5 class="mb-0">Wishlist</h5>
                                <a href="{{ route('wishlist') }}" class="btn btn-sm btn-outline-primary" style="border-radius:999px;">View All</a>
                            </div>
                            <div class="pt-2">
                                @if(($wishlistItems ?? collect())->isEmpty())
                                    <p class="text-muted mb-0">You have not added any products to your wishlist yet.</p>
                                @else
                                    <ul class="list-unstyled mb-0">
                                        @foreach($wishlistItems as $item)
                                            @php
                                                $product = $item->product;
                                                $productName = $product->product_name ?? ('Product #' . $item->product_id);
                                            @endphp
                                            <li class="py-2" style="border-bottom:1px solid rgba(17,24,39,.06);">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <div>
                                                        <div class="font-weight-bold">{{ $productName }}</div>
                                                        <small class="text-muted">Added {{ optional($item->created_at)->diffForHumans() ?? 'recently' }}</small>
                                                    </div>
                                                    <span class="badge badge-info">Qty: {{ $item->quantity ?? 1 }}</span>
                                                </div>
                                            </li>
                                        @endforeach
                                    </ul>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="mb-3">Quick Stats</h5>
                            @php
                                $totalOrdersSafe = (int) ($totalOrders ?? 0);
                                $deliveredOrdersSafe = (int) ($deliveredOrders ?? 0);
                                $pendingOrdersSafe = (int) ($pendingOrders ?? 0);
                                $deliveryRate = $totalOrdersSafe > 0 ? (int) round(($deliveredOrdersSafe / $totalOrdersSafe) * 100) : 0;
                                $pendingRate = $totalOrdersSafe > 0 ? (int) round(($pendingOrdersSafe / $totalOrdersSafe) * 100) : 0;
                            @endphp
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Wishlist items</span>
                                <span class="font-weight-bold">{{ (int) ($wishlistCount ?? 0) }}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-3">
                                <span class="text-muted">Book requests</span>
                                <span class="font-weight-bold">
                                    {{ (int) ($bookRequestsCount ?? 0) }}
                                    @if(($pendingBookRequestsCount ?? 0) > 0)
                                        <span class="badge badge-warning ml-1">Pending: {{ (int) $pendingBookRequestsCount }}</span>
                                    @endif
                                </span>
                            </div>
                            <div class="mb-3">
                                <div class="d-flex justify-content-between"><small class="text-muted">Delivered rate</small><small class="font-weight-bold">{{ $deliveryRate }}%</small></div>
                                <div class="progress progress-sm mt-1"><div class="progress-bar bg-success" style="width: {{ $deliveryRate }}%"></div></div>
                            </div>
                            <div>
                                <div class="d-flex justify-content-between"><small class="text-muted">Pending rate</small><small class="font-weight-bold">{{ $pendingRate }}%</small></div>
                                <div class="progress progress-sm mt-1"><div class="progress-bar bg-warning" style="width: {{ $pendingRate }}%"></div></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="mb-3">This Month</h5>
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="text-muted">Orders</div>
                                    <div class="h4 mb-0">{{ (int) ($monthlyOrders ?? 0) }}</div>
                                </div>
                                <div class="text-right">
                                    <div class="text-muted">Spend</div>
                                    <div class="h4 mb-0">₹{{ number_format((float) ($monthlyOrdersWorth ?? 0), 2) }}</div>
                                </div>
                            </div>
                            <div class="mt-3">
                                <div class="d-flex justify-content-between"><small class="text-muted">Today</small><small class="font-weight-bold">{{ (int) ($todayOrders ?? 0) }}</small></div>
                                <div class="progress progress-sm mt-1"><div class="progress-bar bg-primary" style="width: {{ min(100, ((int) ($todayOrders ?? 0)) * 20) }}%"></div></div>
                            </div>
                            <div class="mt-3">
                                <div class="d-flex justify-content-between"><small class="text-muted">Last 7 days</small><small class="font-weight-bold">{{ (int) ($weeklyOrders ?? 0) }}</small></div>
                                <div class="progress progress-sm mt-1"><div class="progress-bar bg-info" style="width: {{ min(100, ((int) ($weeklyOrders ?? 0)) * 10) }}%"></div></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4 grid-margin stretch-card">
                    <div class="card data-icon-card-primary">
                        <div class="card-body text-white">
                            <h5 class="mb-2">Lifetime Summary</h5>
                            <div class="h2 mb-1">{{ (int) ($totalOrders ?? 0) }}</div>
                            <div class="opacity-90">Total orders • ₹{{ number_format((float) ($totalSpent ?? 0), 2) }} spent</div>
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
<!-- Plugin js for this page -->
<script src="{{ asset('user/vendors/chart.js/Chart.min.js') }}"></script>

<!-- inject:js -->
<script src="{{ asset('user/js/off-canvas.js') }}"></script>
<script src="{{ asset('user/js/hoverable-collapse.js') }}"></script>
<script src="{{ asset('user/js/template.js') }}"></script>
<script src="{{ asset('user/js/settings.js') }}"></script>
<script src="{{ asset('user/js/todolist.js') }}"></script>
<!-- endinject -->

<script>
    (function () {
        var spendingEl = document.getElementById('spendingChart');
        if (spendingEl) {
            var spendingCtx = spendingEl.getContext('2d');
            new Chart(spendingCtx, {
                type: 'line',
                data: {
                    labels: {!! json_encode(array_column($monthlyData ?? [], 'month')) !!},
                    datasets: [{
                        label: 'Spending (₹)',
                        data: {!! json_encode(array_column($monthlyData ?? [], 'amount')) !!},
                        borderColor: 'rgb(75, 192, 192)',
                        backgroundColor: 'rgba(75, 192, 192, 0.18)',
                        tension: 0.4,
                        fill: true,
                        pointRadius: 3
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: { callback: function (value) { return '₹' + value.toLocaleString(); } }
                        }
                    }
                }
            });
        }

        var statusEl = document.getElementById('orderStatusChart');
        if (statusEl) {
            var statusCtx = statusEl.getContext('2d');
            new Chart(statusCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Pending', 'Delivered', 'Other'],
                    datasets: [{
                        data: [{{ (int) ($pendingOrders ?? 0) }}, {{ (int) ($deliveredOrders ?? 0) }}, {{ (int) (($totalOrders ?? 0) - ($pendingOrders ?? 0) - ($deliveredOrders ?? 0)) }}],
                        backgroundColor: [
                            'rgba(255, 206, 86, 0.85)',
                            'rgba(75, 192, 192, 0.85)',
                            'rgba(153, 102, 255, 0.85)'
                        ],
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { position: 'bottom' } }
                }
            });
        }
    })();
</script>

</body>
</html>


