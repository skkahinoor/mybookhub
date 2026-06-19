@extends('admin.layout.layout')

@section('content')
    {{-- Fonts for Mazer Theme --}}
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <style>
        .low-stock-container {
            font-family: 'Nunito', sans-serif;
        }
        .page-header-title {
            color: #25396f;
            font-weight: 800;
            font-size: 1.6rem;
            margin-bottom: 0.25rem;
        }
        .page-header-subtitle {
            color: #7e8299;
            font-size: 1rem;
            font-weight: 600;
        }
        .card-custom {
            border-radius: 16px;
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.04);
            border: 1px solid rgba(0,0,0,0.05);
            background-color: #ffffff;
            margin-bottom: 1.8rem;
            overflow: hidden;
        }
        .card-custom-header {
            background-color: #ffffff;
            border-bottom: 1px solid #f1f3f7;
            padding: 1.25rem 1.5rem;
        }
        .card-custom-title {
            font-weight: 800;
            color: #25396f;
            font-size: 1.2rem;
            margin: 0;
        }
        .threshold-input-group {
            max-width: 400px;
        }
        .table-custom th {
            font-weight: 800;
            color: #6c757d;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            background-color: #fcfdff;
            border-bottom: 2px solid #edf2f9 !important;
        }
        .table-custom td {
            font-weight: 600;
            vertical-align: middle;
            color: #25396f;
            border-bottom: 1px solid #edf2f9;
        }
        .product-img {
            width: 70px;
            height: 70px;
            object-fit: cover;
            border-radius: 8px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.08);
        }
        .badge-stock-warning {
            background-color: #ffeef0;
            color: #ff3b30;
            font-weight: 800;
            font-size: 0.8rem;
            padding: 0.35rem 0.65rem;
            border-radius: 6px;
            border: 1px solid rgba(255, 59, 48, 0.15);
        }
        .stock-edit-input {
            width: 80px;
            border-radius: 8px;
            border: 1px solid #dcdcdc;
            padding: 0.4rem;
            font-weight: 700;
            text-align: center;
            outline: none;
            transition: all 0.2s;
        }
        .stock-edit-input:focus {
            border-color: #435ebe;
            box-shadow: 0 0 0 3px rgba(67, 94, 190, 0.15);
        }
        .btn-update-stock {
            border-radius: 8px;
            padding: 0.4rem 1rem;
            font-weight: 700;
            font-size: 0.85rem;
            transition: all 0.2s;
        }
        .toast-container-custom {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
        }
    </style>

    <div class="main-panel low-stock-container">
        <div class="content-wrapper">

            {{-- Page Header --}}
            <div class="row mb-4">
                <div class="col-md-12">
                    <h2 class="page-header-title">Low Stock Alert Panel</h2>
                    <p class="page-header-subtitle">Manage products with critically low stock levels and configure custom warning thresholds.</p>
                </div>
            </div>

            {{-- Success/Error Alerts --}}
            @if (Session::has('success_message'))
                <div class="alert alert-success alert-dismissible fade show" role="alert" style="border-radius: 12px; font-weight: 600;">
                    <strong><i class="fas fa-check-circle mr-1"></i> Success:</strong> {{ Session::get('success_message') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif
            @if (Session::has('error_message'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert" style="border-radius: 12px; font-weight: 600;">
                    <strong><i class="fas fa-exclamation-circle mr-1"></i> Error:</strong> {{ Session::get('error_message') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            {{-- Custom Warning Threshold Card --}}
            <div class="card card-custom">
                <div class="card-custom-header">
                    <h5 class="card-custom-title"><i class="fas fa-sliders-h text-primary mr-2"></i> Low Stock Threshold Settings</h5>
                </div>
                <div class="card-body p-4">
                    <p class="text-muted mb-3" style="font-weight: 600; font-size: 0.95rem;">
                        Specify the minimum stock quantity below which a product is considered "Low Stock". 
                        Currently, products with stock levels of <strong>{{ $threshold }}</strong> or less trigger a warning.
                    </p>
                    <form action="{{ route('vendor.products.updateThreshold') }}" method="POST">
                        @csrf
                        <div class="d-flex flex-wrap align-items-center" style="gap: 12px; max-width: 500px;">
                            <div class="input-group" style="flex: 1; min-width: 250px; box-shadow: 0 2px 8px rgba(0,0,0,0.03); border-radius: 10px; overflow: hidden;">
                                <span class="input-group-text border-right-0 bg-light" style="font-weight: 700; color: #6c757d; border: 1px solid #dcdcdc; border-radius: 10px 0 0 10px; padding: 0.6rem 1rem;">
                                    <i class="fas fa-boxes text-primary mr-2"></i> Alert Threshold
                                </span>
                                <input type="number" name="low_stock_threshold" class="form-control" value="{{ $threshold }}" min="0" step="1" required style="font-weight: 700; color: #25396f; border: 1px solid #dcdcdc; border-radius: 0 10px 10px 0; padding: 0.6rem 1rem; font-size: 1rem; height: auto;">
                            </div>
                            <button class="btn btn-primary" type="submit" style="border-radius: 10px; font-weight: 700; padding: 0.65rem 2rem; white-space: nowrap; font-size: 1rem; box-shadow: 0 4px 12px rgba(67, 94, 190, 0.25); height: auto; display: flex; align-items: center; justify-content: center; gap: 6px;">
                                <i class="fas fa-sync-alt"></i> Update
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Low Stock Products List Card --}}
            <div class="card card-custom">
                <div class="card-custom-header d-flex justify-content-between align-items-center">
                    <h5 class="card-custom-title">
                        <i class="fas fa-exclamation-triangle text-danger mr-2"></i> Low Stock Products 
                        <span class="badge badge-danger ml-2" style="font-size: 0.75rem; border-radius: 6px; font-weight: 800;">{{ count($lowStockProducts) }} Items</span>
                    </h5>
                    <a href="{{ url('vendor/products') }}" class="btn btn-sm btn-outline-primary fw-bold" style="border-radius: 8px; font-weight: 700;">
                        <i class="fas fa-list mr-1"></i> View All Products
                    </a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-custom mb-0">
                            <thead>
                                <tr>
                                    <th class="px-4 py-3">Book Cover</th>
                                    <th class="py-3">Book Name</th>
                                    <th class="py-3">ISBN</th>
                                    <th class="py-3">Price Details</th>
                                    <th class="py-3 text-center">Status</th>
                                    <th class="py-3 text-center">Current Stock</th>
                                    <th class="px-4 py-3 text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($lowStockProducts as $attribute)
                                    <tr id="row-attr-{{ $attribute->id }}" style="transition: all 0.3s;">
                                        <td class="px-4 py-3">
                                            @php
                                                $imgUrl = getBookCoverUrl($attribute->product->product_image ?? null);
                                            @endphp
                                            <img src="{{ $imgUrl }}" class="product-img" alt="Book Cover">
                                        </td>
                                        <td class="py-3" style="max-width: 250px;">
                                            <div class="d-flex flex-column">
                                                <span class="text-truncate font-weight-bold" style="font-size: 0.95rem; color: #25396f;">
                                                    {{ $attribute->product->product_name ?? 'N/A' }}
                                                </span>
                                                <small class="text-muted mt-1" style="font-weight: 700;">Sku: {{ $attribute->sku }}</small>
                                            </div>
                                        </td>
                                        <td class="py-3" style="font-family: monospace; font-weight: 800; font-size: 0.9rem;">
                                            {{ $attribute->product->product_isbn ?? 'N/A' }}
                                        </td>
                                        <td class="py-3">
                                            @php
                                                $discountDetails = \App\Models\Product::getDiscountPriceDetailsByAttribute($attribute->id, $attribute);
                                            @endphp
                                            @if ($discountDetails['discount'] > 0)
                                                <div class="d-flex flex-column">
                                                    <span class="text-muted" style="text-decoration: line-through; font-size: 0.8rem; font-weight: 700;">₹{{ $discountDetails['product_price'] }}</span>
                                                    <span class="text-danger" style="font-size: 0.95rem; font-weight: 800;">₹{{ $discountDetails['final_price'] }}</span>
                                                </div>
                                            @else
                                                <span style="font-size: 0.95rem; font-weight: 800; color: #28a745;">₹{{ $discountDetails['product_price'] }}</span>
                                            @endif
                                        </td>
                                        <td class="py-3 text-center">
                                            <span class="badge-stock-warning">
                                                <i class="fas fa-exclamation-circle mr-1"></i> Stock Low
                                            </span>
                                        </td>
                                        <td class="py-3 text-center">
                                            <div class="d-flex justify-content-center align-items-center" style="gap: 5px;">
                                                <input type="number" id="stock-input-{{ $attribute->id }}" class="stock-edit-input" value="{{ $attribute->stock }}" min="0" step="1">
                                            </div>
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            <button type="button" class="btn btn-primary btn-update-stock" data-attr-id="{{ $attribute->id }}">
                                                <i class="fas fa-save mr-1"></i> Update
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-5">
                                            <div class="d-flex flex-column align-items-center justify-content-center">
                                                <div style="width: 64px; height: 64px; background: rgba(40, 167, 69, 0.08); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-bottom: 15px; box-shadow: inset 0 0 0 1px rgba(40, 167, 69, 0.12);">
                                                    <i class="fas fa-check text-success" style="font-size: 1.4rem;"></i>
                                                </div>
                                                <h6 style="color: #25396f; font-weight: 800; font-size: 1.15rem; margin-bottom: 5px;">Perfect Stock Level!</h6>
                                                <p class="text-muted" style="font-size: 0.9rem; font-weight: 600; max-width: 400px; margin: 0 auto;">Great job! None of your active products have stock levels below your low-stock threshold.</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>

    {{-- Toast container for toast notifications --}}
    <div class="toast-container-custom" id="toastContainer"></div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            // Function to show toast notification
            function showToast(message, type = 'success') {
                var bgColor = type === 'success' ? '#28a745' : '#dc3545';
                var icon = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle';
                var toastId = 'toast-' + Date.now();

                var toastHtml = `
                    <div id="${toastId}" class="toast align-items-center text-white border-0 show mb-2" role="alert" aria-live="assertive" aria-atomic="true" style="background-color: ${bgColor}; border-radius: 10px; box-shadow: 0 4px 12px rgba(0,0,0,0.15); min-width: 250px;">
                        <div class="d-flex p-3">
                            <div class="me-2"><i class="fas ${icon}"></i></div>
                            <div class="toast-body fw-bold" style="font-family: 'Nunito', sans-serif;">
                                ${message}
                            </div>
                            <button type="button" class="btn-close btn-close-white m-auto" data-bs-dismiss="toast" aria-label="Close" style="background: none; border: none; color: white; font-weight: bold; cursor: pointer;">&times;</button>
                        </div>
                    </div>
                `;

                $('#toastContainer').append(toastHtml);

                // Auto remove after 3.5 seconds
                setTimeout(function() {
                    $('#' + toastId).fadeOut(400, function() {
                        $(this).remove();
                    });
                }, 3500);
            }

            // Close toast on click
            $(document).on('click', '.btn-close', function() {
                $(this).closest('.toast').remove();
            });

            // Inline update stock
            $('.btn-update-stock').on('click', function() {
                var btn = $(this);
                var attributeId = btn.data('attr-id');
                var stockVal = $('#stock-input-' + attributeId).val();

                if (stockVal === '') {
                    showToast('Stock value is required.', 'error');
                    return;
                }

                btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Saving...');

                $.ajax({
                    url: "{{ route('vendor.products.updateAttributeStock') }}",
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        attribute_id: attributeId,
                        stock: stockVal
                    },
                    success: function(response) {
                        btn.prop('disabled', false).html('<i class="fas fa-save mr-1"></i> Update');
                        if (response.success) {
                            showToast(response.message, 'success');
                            
                            // If stock has been updated above threshold, remove or fade out row
                            var thresholdVal = parseInt("{{ $threshold }}");
                            if (parseInt(stockVal) > thresholdVal) {
                                $('#row-attr-' + attributeId).css('background-color', '#e2f0d9').fadeOut(800, function() {
                                    $(this).remove();
                                    
                                    // Update low stock badge count on header
                                    var countBadge = $('.card-custom-title .badge-danger');
                                    var newCount = parseInt(countBadge.text()) - 1;
                                    countBadge.text(newCount + ' Items');
                                    
                                    // If no more items, reload page or show empty state
                                    if (newCount <= 0) {
                                        location.reload();
                                    }
                                });
                            }
                        } else {
                            showToast(response.message || 'Error occurred while updating stock.', 'error');
                        }
                    },
                    error: function(xhr) {
                        btn.prop('disabled', false).html('<i class="fas fa-save mr-1"></i> Update');
                        var errMsg = 'An error occurred. Please try again.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errMsg = xhr.responseJSON.message;
                        }
                        showToast(errMsg, 'error');
                    }
                });
            });
        });
    </script>
@endpush
