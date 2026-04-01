@extends('admin.layout.layout')
@section('content')
    <div class="main-panel">
        <div class="content-wrapper">
            <div class="row">
                <div class="col-lg-12 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">Sell Old Book Requests</h4>
                            <p class="card-description">Review and approve old book listings submitted by students and vendors.</p>

                            @if (Session::has('success_message'))
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    <strong>Success:</strong> {{ Session::get('success_message') }}
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            @endif

                            <div class="table-responsive pt-3">
                                <table id="sell_requests" class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Type</th>
                                            <th>Seller Name</th>
                                            <th>Book Name</th>
                                            <th>ISBN</th>
                                            <th>Book Condition</th>
                                            <th>Selling Price</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($requests as $key => $request)
                                            <tr>
                                                <td>{{ $key + 1 }}</td>

                                                {{-- Type badge --}}
                                                <td>
                                                    @if($request->admin_type === 'vendor')
                                                        <span class="badge badge-warning">Vendor</span>
                                                    @else
                                                        <span class="badge badge-info">User</span>
                                                    @endif
                                                </td>

                                                {{-- Seller name --}}
                                                <td>
                                                    @if($request->admin_type === 'vendor')
                                                        {{-- Vendor listing --}}
                                                        @if($request->vendor && $request->vendor->user)
                                                            {{ $request->vendor->user->name ?? 'Vendor #'.$request->vendor_id }}
                                                            <br><small class="text-muted">{{ $request->vendor->user->email ?? '' }}</small>
                                                        @else
                                                            Vendor ID: {{ $request->vendor_id }}
                                                        @endif
                                                    @elseif($request->user)
                                                        {{-- User/student listing --}}
                                                        {{ $request->user->name }}
                                                        <br><small class="text-muted">{{ $request->user->email }}</small>
                                                    @else
                                                        N/A
                                                    @endif
                                                </td>

                                                <td>{{ $request->product->product_name ?? 'N/A' }}</td>
                                                <td>{{ $request->product->product_isbn ?? 'N/A' }}</td>
                                                <td>
                                                    @if($request->condition)
                                                        <span class="badge badge-info">{{ $request->condition->name }}</span>
                                                    @else
                                                        N/A
                                                    @endif
                                                </td>
                                                <td>
                                                    @php
                                                        $finalPrice = $request->price;
                                                        if (!$finalPrice && $request->product && $request->product->product_price > 0) {
                                                            if ($request->condition) {
                                                                $finalPrice = ($request->product->product_price * $request->condition->percentage) / 100;
                                                            } else {
                                                                $finalPrice = $request->product->product_price;
                                                            }
                                                        }
                                                    @endphp
                                                    &#8377;{{ $finalPrice ?? 'N/A' }}
                                                </td>
                                                <td>
                                                    @if($request->admin_approved == 1)
                                                        <span class="badge badge-success">Approved</span>
                                                    @else
                                                        <span class="badge badge-warning">Pending</span>
                                                    @endif
                                                </td>
                                                <td>
                                                     <a href="{{ route('admin.sell-book-requests.show', $request->id) }}" 
                                                      class="btn btn-sm btn-outline-primary">
                                                            View
                                                      </a>
                                                    {{-- @if($request->admin_approved == 0)
                                                        <form action="{{ route('admin.sell-book-requests.approve', $request->id) }}" method="POST" style="display:inline;">
                                                            @csrf
                                                            <button type="submit" class="btn btn-link p-0" title="Approve" onclick="return confirm('Approve this listing?')">
                                                                <i style="font-size: 25px" class="mdi mdi-check-circle text-success"></i>
                                                            </button>
                                                        </form>
                                                    @endif --}}
                                                    <form action="{{ route('admin.sell-book-requests.reject', $request->id) }}" method="POST" style="display:inline;">
                                                     @csrf
                                                    <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Reject and delete this request?')">
                                                     Reject
                                                    </button>
                                                     </form>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <footer class="footer">
            <div class="d-sm-flex justify-content-center justify-content-sm-between">
                <span class="text-muted text-center text-sm-left d-block d-sm-inline-block">Copyright &copy; 2024. All rights reserved.</span>
            </div>
        </footer>
    </div>
@endsection
