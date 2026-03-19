@extends('admin.layout.layout')
@section('content')
    <div class="main-panel">
        <div class="content-wrapper">
            <div class="row">
                <div class="col-md-12 grid-margin">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">Book Listing Details</h4>
                            <a href="{{ route('admin.sell-book-requests.index') }}" class="btn btn-light mb-3">Back to List</a>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <h5 class="mb-4">Book Information</h5>
                                    <table class="table table-borderless">
                                        <tr>
                                            <th width="30%">Name:</th>
                                            <td>{{ $requestData->product->product_name ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <th>ISBN:</th>
                                            <td>{{ $requestData->product->product_isbn ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Author(s):</th>
                                            <td>
                                                @if($requestData->product && $requestData->product->authors)
                                                    {{ $requestData->product->authors->pluck('name')->implode(', ') }}
                                                @else
                                                    N/A
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Base Price:</th>
                                            <td>₹{{ $requestData->product->product_price ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Selling Price:</th>
                                            <td class="text-primary font-weight-bold">
                                                @php
                                                    $finalPrice = $requestData->price;
                                                    if (!$finalPrice && $requestData->product && $requestData->product->product_price > 0) {
                                                        if ($requestData->condition) {
                                                            $finalPrice = ($requestData->product->product_price * $requestData->condition->percentage) / 100;
                                                        } else {
                                                            $finalPrice = $requestData->product->product_price;
                                                        }
                                                    }
                                                @endphp
                                                ₹{{ $finalPrice ?? 'N/A' }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Condition:</th>
                                            <td>
                                                @if($requestData->condition)
                                                    <span class="badge badge-info">{{ $requestData->condition->name }}</span>
                                                @else
                                                    N/A
                                                @endif
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <h5 class="mb-4">Seller Information</h5>
                                    <table class="table table-borderless">
                                        <tr>
                                            <th width="30%">Type:</th>
                                            <td>
                                                @if($requestData->admin_type === 'vendor')
                                                    <span class="badge badge-warning">Vendor</span>
                                                @else
                                                    <span class="badge badge-info">User (Student)</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Name:</th>
                                            <td>
                                                @if($requestData->admin_type === 'vendor' && $requestData->vendor && $requestData->vendor->user)
                                                    {{ $requestData->vendor->user->name ?? 'Vendor #'.$requestData->vendor_id }}
                                                @else
                                                    {{ $requestData->user->name ?? 'N/A' }}
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Email:</th>
                                            <td>
                                                @if($requestData->admin_type === 'vendor' && $requestData->vendor && $requestData->vendor->user)
                                                    {{ $requestData->vendor->user->email ?? 'N/A' }}
                                                @else
                                                    {{ $requestData->user->email ?? 'N/A' }}
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Phone:</th>
                                            <td>
                                                @if($requestData->admin_type === 'vendor' && $requestData->vendor && $requestData->vendor->user)
                                                    {{ $requestData->vendor->user->mobile ?? 'N/A' }}
                                                @else
                                                    {{ $requestData->user->mobile ?? 'N/A' }}
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Requested On:</th>
                                            <td>{{ $requestData->created_at ? $requestData->created_at->format('d M Y, h:i A') : 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Status:</th>
                                            <td>
                                                @if($requestData->admin_approved == 1)
                                                    <span class="badge badge-success">Approved</span>
                                                @else
                                                    <span class="badge badge-warning">Pending Approval</span>
                                                @endif
                                            </td>
                                        </tr>
                                    </table>

                                    <div class="mt-4">
                                        @if($requestData->admin_approved == 0)
                                            <form action="{{ route('admin.sell-book-requests.approve', $requestData->id) }}" method="POST" style="display:inline;">
                                                @csrf
                                                <button type="submit" class="btn btn-success mr-2">Approve Listing</button>
                                            </form>
                                        @endif
                                        <form action="{{ route('admin.sell-book-requests.reject', $requestData->id) }}" method="POST" style="display:inline;">
                                            @csrf
                                            <button type="submit" class="btn btn-danger" onclick="return confirm('Reject and delete this request?')">Reject Request</button>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            @if($requestData->user_old_book_image && $requestData->user_old_book_image)
                                <div class="row mt-4">
                                    <div class="col-12">
                                        <h5>Book Image</h5>
                                        <img src="{{ asset('front/images/product_images/medium/' . $requestData->user_old_book_image) }}" class="img-thumbnail" style="max-width: 300px;">
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
