@include('user.layout.header')

<div class="container-fluid page-body-wrapper">
    @include('user.layout.sidebar')

    <div class="main-panel">
    <div class="content-wrapper">
      <div class="row">
        <div class="col-md-12 grid-margin">
          <div class="row">
            <div class="col-12 col-xl-8 mb-4 mb-xl-0">
              <h3 class="font-weight-bold">Welcome {{ Auth::user()->name ?? 'null'}}</h3>
              <h6 class="font-weight-normal mb-0">All systems are running smoothly! You have <span class="text-primary">3 unread alerts!</span></h6>
            </div>
            <div class="col-12 col-xl-4">
             <div class="justify-content-end d-flex">
              <div class="dropdown flex-md-grow-1 flex-xl-grow-0">
                <button class="btn btn-sm btn-light bg-white dropdown-toggle" type="button" id="dropdownMenuDate2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                 <i class="mdi mdi-calendar"></i> Today (10 Jan 2021)
                </button>
                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuDate2">
                  <a class="dropdown-item" href="#">January - March</a>
                  <a class="dropdown-item" href="#">March - June</a>
                  <a class="dropdown-item" href="#">June - August</a>
                  <a class="dropdown-item" href="#">August - November</a>
                </div>
              </div>
             </div>
            </div>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-md-6 grid-margin stretch-card">
          <div class="card tale-bg">
            <div class="card-people mt-auto">
              <img src="images/dashboard/people.svg" alt="people">
              <div class="weather-info">
                <div class="d-flex">
                  <div>
                    <h2 class="mb-0 font-weight-normal"><i class="icon-sun mr-2"></i>31<sup>C</sup></h2>
                  </div>
                  <div class="ml-2">
                    <h4 class="location font-weight-normal">Bangalore</h4>
                    <h6 class="font-weight-normal">India</h6>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-md-6 grid-margin transparent">
          <div class="row">
            <div class="col-md-6 mb-4 stretch-card transparent">
              <div class="card card-tale">
                <div class="card-body">
                  <p class="mb-4">Today’s Order</p>
                  <p class="fs-30 mb-2">{{ $todayOrders }}</p>
                  <p>₹{{ number_format($todayOrdersWorth, 2) }} worth of orders</p>
                </div>
              </div>
            </div>
            <div class="col-md-6 mb-4 stretch-card transparent">
              <div class="card card-dark-blue">
                <div class="card-body">
                  <p class="mb-4">Weekly Order</p>
                  <p class="fs-30 mb-2">{{ $weeklyOrders }}</p>
                  <p>₹{{ number_format($weeklyOrdersWorth, 2) }} worth of orders</p>
                </div>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-6 mb-4 mb-lg-0 stretch-card transparent">
              <div class="card card-light-blue">
                <div class="card-body">
                  <p class="mb-4">Monthly Order</p>
                  <p class="fs-30 mb-2">{{ $monthlyOrders }}</p>
                  <p>₹{{ number_format($monthlyOrdersWorth, 2) }} worth of orders</p>
                </div>
              </div>
            </div>
            <div class="col-md-6 stretch-card transparent">
              <div class="card card-light-danger">
                <div class="card-body">
                  <p class="mb-4">Total Order</p>
                  <p class="fs-30 mb-2">{{ $totalOrders }}</p>
                  <p>₹{{ number_format($totalSpent, 2) }} worth of orders</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-md-6 grid-margin stretch-card">
          <div class="card">
            <!-- Spending Chart Card -->
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title mb-4">Monthly Spending</h5>
                    <div style="position: relative; height: 250px;">
                        <canvas id="spendingChart"></canvas>
                    </div>
                </div>
            </div>
          </div>
        </div>
        <div class="col-md-6 grid-margin stretch-card">
          <div class="card">

           <!-- Order Status Pie Chart -->
           <div class="card">
            <div class="card-body">
                <h5 class="card-title mb-4">Order Statistics</h5>
                <div style="position: relative; height: 250px;">
                    <canvas id="orderStatusChart"></canvas>
                </div>
            </div>
        </div>
          </div>
        </div>
      </div>

      {{-- <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
          <div class="card position-relative">
            <div class="card-body">
              <div id="detailedReports" class="carousel slide detailed-report-carousel position-static pt-2" data-ride="carousel">
                <div class="carousel-inner">
                  <div class="carousel-item active">
                    <div class="row">
                      <div class="col-md-12 col-xl-3 d-flex flex-column justify-content-start">
                        <div class="ml-xl-4 mt-3">
                        <p class="card-title">Detailed Reports</p>
                          <h1 class="text-primary">$34040</h1>
                          <h3 class="font-weight-500 mb-xl-4 text-primary">North America</h3>
                          <p class="mb-2 mb-xl-0">The total number of sessions within the date range. It is the period time a user is actively engaged with your website, page or app, etc</p>
                        </div>
                        </div>
                      <div class="col-md-12 col-xl-9">
                        <div class="row">
                          <div class="col-md-6 border-right">
                            <div class="table-responsive mb-3 mb-md-0 mt-3">
                              <table class="table table-borderless report-table">
                                <tr>
                                  <td class="text-muted">Illinois</td>
                                  <td class="w-100 px-0">
                                    <div class="progress progress-md mx-4">
                                      <div class="progress-bar bg-primary" role="progressbar" style="width: 70%" aria-valuenow="70" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                  </td>
                                  <td><h5 class="font-weight-bold mb-0">713</h5></td>
                                </tr>
                                <tr>
                                  <td class="text-muted">Washington</td>
                                  <td class="w-100 px-0">
                                    <div class="progress progress-md mx-4">
                                      <div class="progress-bar bg-warning" role="progressbar" style="width: 30%" aria-valuenow="30" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                  </td>
                                  <td><h5 class="font-weight-bold mb-0">583</h5></td>
                                </tr>
                                <tr>
                                  <td class="text-muted">Mississippi</td>
                                  <td class="w-100 px-0">
                                    <div class="progress progress-md mx-4">
                                      <div class="progress-bar bg-danger" role="progressbar" style="width: 95%" aria-valuenow="95" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                  </td>
                                  <td><h5 class="font-weight-bold mb-0">924</h5></td>
                                </tr>
                                <tr>
                                  <td class="text-muted">California</td>
                                  <td class="w-100 px-0">
                                    <div class="progress progress-md mx-4">
                                      <div class="progress-bar bg-info" role="progressbar" style="width: 60%" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                  </td>
                                  <td><h5 class="font-weight-bold mb-0">664</h5></td>
                                </tr>
                                <tr>
                                  <td class="text-muted">Maryland</td>
                                  <td class="w-100 px-0">
                                    <div class="progress progress-md mx-4">
                                      <div class="progress-bar bg-primary" role="progressbar" style="width: 40%" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                  </td>
                                  <td><h5 class="font-weight-bold mb-0">560</h5></td>
                                </tr>
                                <tr>
                                  <td class="text-muted">Alaska</td>
                                  <td class="w-100 px-0">
                                    <div class="progress progress-md mx-4">
                                      <div class="progress-bar bg-danger" role="progressbar" style="width: 75%" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                  </td>
                                  <td><h5 class="font-weight-bold mb-0">793</h5></td>
                                </tr>
                              </table>
                            </div>
                          </div>
                          <div class="col-md-6 mt-3">
                            <canvas id="north-america-chart"></canvas>
                            <div id="north-america-legend"></div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="carousel-item">
                    <div class="row">
                      <div class="col-md-12 col-xl-3 d-flex flex-column justify-content-start">
                        <div class="ml-xl-4 mt-3">
                        <p class="card-title">Detailed Reports</p>
                          <h1 class="text-primary">$34040</h1>
                          <h3 class="font-weight-500 mb-xl-4 text-primary">North America</h3>
                          <p class="mb-2 mb-xl-0">The total number of sessions within the date range. It is the period time a user is actively engaged with your website, page or app, etc</p>
                        </div>
                        </div>
                      <div class="col-md-12 col-xl-9">
                        <div class="row">
                          <div class="col-md-6 border-right">
                            <div class="table-responsive mb-3 mb-md-0 mt-3">
                              <table class="table table-borderless report-table">
                                <tr>
                                  <td class="text-muted">Illinois</td>
                                  <td class="w-100 px-0">
                                    <div class="progress progress-md mx-4">
                                      <div class="progress-bar bg-primary" role="progressbar" style="width: 70%" aria-valuenow="70" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                  </td>
                                  <td><h5 class="font-weight-bold mb-0">713</h5></td>
                                </tr>
                                <tr>
                                  <td class="text-muted">Washington</td>
                                  <td class="w-100 px-0">
                                    <div class="progress progress-md mx-4">
                                      <div class="progress-bar bg-warning" role="progressbar" style="width: 30%" aria-valuenow="30" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                  </td>
                                  <td><h5 class="font-weight-bold mb-0">583</h5></td>
                                </tr>
                                <tr>
                                  <td class="text-muted">Mississippi</td>
                                  <td class="w-100 px-0">
                                    <div class="progress progress-md mx-4">
                                      <div class="progress-bar bg-danger" role="progressbar" style="width: 95%" aria-valuenow="95" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                  </td>
                                  <td><h5 class="font-weight-bold mb-0">924</h5></td>
                                </tr>
                                <tr>
                                  <td class="text-muted">California</td>
                                  <td class="w-100 px-0">
                                    <div class="progress progress-md mx-4">
                                      <div class="progress-bar bg-info" role="progressbar" style="width: 60%" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                  </td>
                                  <td><h5 class="font-weight-bold mb-0">664</h5></td>
                                </tr>
                                <tr>
                                  <td class="text-muted">Maryland</td>
                                  <td class="w-100 px-0">
                                    <div class="progress progress-md mx-4">
                                      <div class="progress-bar bg-primary" role="progressbar" style="width: 40%" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                  </td>
                                  <td><h5 class="font-weight-bold mb-0">560</h5></td>
                                </tr>
                                <tr>
                                  <td class="text-muted">Alaska</td>
                                  <td class="w-100 px-0">
                                    <div class="progress progress-md mx-4">
                                      <div class="progress-bar bg-danger" role="progressbar" style="width: 75%" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                  </td>
                                  <td><h5 class="font-weight-bold mb-0">793</h5></td>
                                </tr>
                              </table>
                            </div>
                          </div>
                          <div class="col-md-6 mt-3">
                            <canvas id="south-america-chart"></canvas>
                            <div id="south-america-legend"></div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <a class="carousel-control-prev" href="#detailedReports" role="button" data-slide="prev">
                  <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                  <span class="sr-only">Previous</span>
                </a>
                <a class="carousel-control-next" href="#detailedReports" role="button" data-slide="next">
                  <span class="carousel-control-next-icon" aria-hidden="true"></span>
                  <span class="sr-only">Next</span>
                </a>
              </div>
            </div>
          </div>
        </div>
      </div> --}}

      <div class="row">
        <div class="col-md-7 grid-margin stretch-card">
          <div class="card">
            <div class="card-body">
              <p class="card-title mb-0">Recent Orders</p>
              <div class="table-responsive">
                <table class="table table-striped table-borderless">
                  <thead>
                    <tr>
                      <th>Order ID</th>
                      <th>Products</th>
                      <th>Total Amount</th>
                      <th>Order Date</th>
                      <th>Order Status</th>
                    </tr>
                  </thead>
                  <tbody>
                    @forelse($recentOrders as $order)
                      @php
                          $status = strtolower($order->order_status ?? '');
                          $badgeClass = 'badge-secondary';
                          if (str_contains($status, 'pending')) {
                              $badgeClass = 'badge-warning';
                          } elseif (str_contains($status, 'shipped') || str_contains($status, 'delivered')) {
                              $badgeClass = 'badge-success';
                          } elseif (str_contains($status, 'cancel')) {
                              $badgeClass = 'badge-danger';
                          } elseif (str_contains($status, 'progress') || str_contains($status, 'processing')) {
                              $badgeClass = 'badge-info';
                          }
                      @endphp
                      <tr>
                        <td>#{{ $order->id }}</td>
                        <td>
                          @foreach($order->orders_products as $product)
                            {{ $product->product_name }}
                            @if(!$loop->last)
                              ,
                            @endif
                          @endforeach
                        </td>
                        <td class="font-weight-bold">₹{{ number_format($order->grand_total, 2) }}</td>
                        <td>{{ $order->created_at?->format('M d, Y') }}</td>
                        <td class="font-weight-medium">
                          <div class="badge {{ $badgeClass }}">{{ $order->order_status ?? 'N/A' }}</div>
                        </td>
                      </tr>
                    @empty
                      <tr>
                        <td colspan="4" class="text-center text-muted">No recent orders found.</td>
                      </tr>
                    @endforelse
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
        <div class="col-md-5 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title d-flex justify-content-between align-items-center">
                        <span>Wishlist</span>
                        <a href="{{ route('wishlist') }}" class="btn btn-sm btn-outline-primary">View All</a>
                    </h4>
                    <div class="list-wrapper pt-2">
                        @if($wishlistItems->isEmpty())
                            <p class="text-muted mb-0">You have not added any products to your wishlist yet.</p>
                        @else
                            <ul class="wishlist-list list-unstyled mb-0">
                                @foreach($wishlistItems as $item)
                                    @php
                                        $product = $item->product;
                                        $productName = $product->product_name ?? 'Product #' . $item->product_id;
                                    @endphp
                                    <li class="mb-3 pb-3 border-bottom">
                                        <div class="d-flex align-items-center">
                                            <div class="flex-grow-1">
                                                <div class="font-weight-bold">{{ $productName }}</div>
                                                <small class="text-muted">
                                                    Added {{ optional($item->created_at)->diffForHumans() ?? 'recently' }}
                                                </small>
                                            </div>
                                            <div class="text-right">
                                                <span class="badge badge-info">Qty: {{ $item->quantity ?? 1 }}</span>
                                            </div>
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
        <div class="col-md-4 stretch-card grid-margin">
          <div class="card">
            <div class="card-body">
              <p class="card-title mb-0">Projects</p>
              <div class="table-responsive">
                <table class="table table-borderless">
                  <thead>
                    <tr>
                      <th class="pl-0  pb-2 border-bottom">Places</th>
                      <th class="border-bottom pb-2">Orders</th>
                      <th class="border-bottom pb-2">Users</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <td class="pl-0">Kentucky</td>
                      <td><p class="mb-0"><span class="font-weight-bold mr-2">65</span>(2.15%)</p></td>
                      <td class="text-muted">65</td>
                    </tr>
                    <tr>
                      <td class="pl-0">Ohio</td>
                      <td><p class="mb-0"><span class="font-weight-bold mr-2">54</span>(3.25%)</p></td>
                      <td class="text-muted">51</td>
                    </tr>
                    <tr>
                      <td class="pl-0">Nevada</td>
                      <td><p class="mb-0"><span class="font-weight-bold mr-2">22</span>(2.22%)</p></td>
                      <td class="text-muted">32</td>
                    </tr>
                    <tr>
                      <td class="pl-0">North Carolina</td>
                      <td><p class="mb-0"><span class="font-weight-bold mr-2">46</span>(3.27%)</p></td>
                      <td class="text-muted">15</td>
                    </tr>
                    <tr>
                      <td class="pl-0">Montana</td>
                      <td><p class="mb-0"><span class="font-weight-bold mr-2">17</span>(1.25%)</p></td>
                      <td class="text-muted">25</td>
                    </tr>
                    <tr>
                      <td class="pl-0">Nevada</td>
                      <td><p class="mb-0"><span class="font-weight-bold mr-2">52</span>(3.11%)</p></td>
                      <td class="text-muted">71</td>
                    </tr>
                    <tr>
                      <td class="pl-0 pb-0">Louisiana</td>
                      <td class="pb-0"><p class="mb-0"><span class="font-weight-bold mr-2">25</span>(1.32%)</p></td>
                      <td class="pb-0">14</td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
        <div class="col-md-4 stretch-card grid-margin">
          <div class="row">
            <div class="col-md-12 grid-margin stretch-card">
              <div class="card">
                <div class="card-body">
                  <p class="card-title">Charts</p>
                  <div class="charts-data">
                    <div class="mt-3">
                      <p class="mb-0">Data 1</p>
                      <div class="d-flex justify-content-between align-items-center">
                        <div class="progress progress-md flex-grow-1 mr-4">
                          <div class="progress-bar bg-inf0" role="progressbar" style="width: 95%" aria-valuenow="95" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        <p class="mb-0">5k</p>
                      </div>
                    </div>
                    <div class="mt-3">
                      <p class="mb-0">Data 2</p>
                      <div class="d-flex justify-content-between align-items-center">
                        <div class="progress progress-md flex-grow-1 mr-4">
                          <div class="progress-bar bg-info" role="progressbar" style="width: 35%" aria-valuenow="35" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        <p class="mb-0">1k</p>
                      </div>
                    </div>
                    <div class="mt-3">
                      <p class="mb-0">Data 3</p>
                      <div class="d-flex justify-content-between align-items-center">
                        <div class="progress progress-md flex-grow-1 mr-4">
                          <div class="progress-bar bg-info" role="progressbar" style="width: 48%" aria-valuenow="48" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        <p class="mb-0">992</p>
                      </div>
                    </div>
                    <div class="mt-3">
                      <p class="mb-0">Data 4</p>
                      <div class="d-flex justify-content-between align-items-center">
                        <div class="progress progress-md flex-grow-1 mr-4">
                          <div class="progress-bar bg-info" role="progressbar" style="width: 25%" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        <p class="mb-0">687</p>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-md-12 stretch-card grid-margin grid-margin-md-0">
              <div class="card data-icon-card-primary">
                <div class="card-body">
                  <p class="card-title text-white">Number of Meetings</p>
                  <div class="row">
                    <div class="col-8 text-white">
                      <h3>34040</h3>
                      <p class="text-white font-weight-500 mb-0">The total number of sessions within the date range.It is calculated as the sum . </p>
                    </div>
                    <div class="col-4 background-icon">
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-md-4 stretch-card grid-margin">
          <div class="card">
            <div class="card-body">
              <p class="card-title">Notifications</p>
              <ul class="icon-data-list">
                <li>
                  <div class="d-flex">
                    <img src="images/faces/face1.jpg" alt="user">
                    <div>
                      <p class="text-info mb-1">Isabella Becker</p>
                      <p class="mb-0">Sales dashboard have been created</p>
                      <small>9:30 am</small>
                    </div>
                  </div>
                </li>
                <li>
                  <div class="d-flex">
                    <img src="images/faces/face2.jpg" alt="user">
                    <div>
                      <p class="text-info mb-1">Adam Warren</p>
                      <p class="mb-0">You have done a great job #TW111</p>
                      <small>10:30 am</small>
                    </div>
                  </div>
                </li>
                <li>
                  <div class="d-flex">
                  <img src="images/faces/face3.jpg" alt="user">
                 <div>
                  <p class="text-info mb-1">Leonard Thornton</p>
                  <p class="mb-0">Sales dashboard have been created</p>
                  <small>11:30 am</small>
                 </div>
                  </div>
                </li>
                <li>
                  <div class="d-flex">
                    <img src="images/faces/face4.jpg" alt="user">
                    <div>
                      <p class="text-info mb-1">George Morrison</p>
                      <p class="mb-0">Sales dashboard have been created</p>
                      <small>8:50 am</small>
                    </div>
                  </div>
                </li>
                <li>
                  <div class="d-flex">
                    <img src="images/faces/face5.jpg" alt="user">
                    <div>
                    <p class="text-info mb-1">Ryan Cortez</p>
                    <p class="mb-0">Herbs are fun and easy to grow.</p>
                    <small>9:00 am</small>
                    </div>
                  </div>
                </li>
              </ul>
            </div>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
          <div class="card">
            <div class="card-body">
              <p class="card-title">Advanced Table</p>
              <div class="row">
                <div class="col-12">
                  <div class="table-responsive">
                    <table id="example" class="display expandable-table" style="width:100%">
                      <thead>
                        <tr>
                          <th>Quote#</th>
                          <th>Product</th>
                          <th>Business type</th>
                          <th>Policy holder</th>
                          <th>Premium</th>
                          <th>Status</th>
                          <th>Updated at</th>
                          <th></th>
                        </tr>
                      </thead>
                  </table>
                  </div>
                </div>
              </div>
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
    <script src="{{ asset('user/vendors/datatables.net/jquery.dataTables.js') }}"></script>
    <script src="{{ asset('user/vendors/datatables.net-bs4/dataTables.bootstrap4.js') }}"></script>
    <script src="{{ asset('user/js/dataTables.select.min.js') }}"></script>

    <!-- End plugin js for this page -->
    <!-- inject:js -->
    <script src="{{ asset('user/js/off-canvas.js') }}"></script>
    <script src="{{ asset('user/js/hoverable-collapse.js') }}"></script>
    <script src="{{ asset('user/js/template.js') }}"></script>
    <script src="{{ asset('user/js/settings.js') }}"></script>
    <script src="{{ asset('user/js/todolist.js') }}"></script>
    <!-- endinject -->
    <!-- Custom js for this page-->
    <script src="{{ asset('user/js/dashboard.js') }}"></script>
    <script src="{{ asset('user/js/Chart.roundedBarCharts.js') }}"></script>
    <!-- End custom js for this page-->

    <script>
        // Monthly Spending Line Chart
        const spendingCtx = document.getElementById('spendingChart').getContext('2d');
        const spendingChart = new Chart(spendingCtx, {
            type: 'line',
            data: {
                labels: {!! json_encode(array_column($monthlyData, 'month')) !!},
                datasets: [{
                    label: 'Spending (₹)',
                    data: {!! json_encode(array_column($monthlyData, 'amount')) !!},
                    borderColor: 'rgb(75, 192, 192)',
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return '₹' + value.toLocaleString();
                            }
                        }
                    }
                }
            }
        });

        // Order Status Pie Chart
        const statusCtx = document.getElementById('orderStatusChart').getContext('2d');
        const statusChart = new Chart(statusCtx, {
            type: 'doughnut',
            data: {
                labels: ['Pending', 'Delivered', 'Other'],
                datasets: [{
                    data: [{{ $pendingOrders }}, {{ $deliveredOrders }}, {{ $totalOrders - $pendingOrders - $deliveredOrders }}],
                    backgroundColor: [
                        'rgba(255, 206, 86, 0.8)',
                        'rgba(75, 192, 192, 0.8)',
                        'rgba(153, 102, 255, 0.8)'
                    ],
                    borderColor: [
                        'rgba(255, 206, 86, 1)',
                        'rgba(75, 192, 192, 1)',
                        'rgba(153, 102, 255, 1)'
                    ],
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    </script>
</body>

</html>



