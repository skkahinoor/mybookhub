@extends('admin.layout.layout')

@section('content')
    <style>
        /* --- Page Specific Styles --- */
        :root {
            --primary-color: #4b6cb7;
            --secondary-color: #182848;
            --soft-bg: #f8fbff;
            --border-color: #eef2f7;
            --success-color: #00b09b;
            --danger-color: #ff5e62;
        }

        /* Card & Layout */
        .content-wrapper {
            background: #f4f6f9;
        }

        .report-card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.05);
            margin-bottom: 2rem;
        }

        .report-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid var(--border-color);
        }

        .report-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: #2c3e50;
            margin: 0;
        }

        .report-subtitle {
            color: #7f8c8d;
            font-size: 0.9rem;
            margin-top: 5px;
        }

        /* Filter Section */
        .filter-container {
            background-color: var(--soft-bg);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 2rem;
        }

        .filter-label {
            font-weight: 600;
            color: #34495e;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 0.5rem;
            display: block;
        }

        .btn-apply {
            background: linear-gradient(to right, #4b6cb7, #182848);
            border: none;
            color: white;
            font-weight: 600;
            padding: 10px 25px;
            border-radius: 8px;
            transition: transform 0.2s;
        }

        .btn-apply:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(75, 108, 183, 0.3);
            color: white;
        }

        .btn-reset {
            background: white;
            border: 1px solid #dcdfe3;
            color: #636e72;
            font-weight: 600;
            padding: 10px 20px;
            border-radius: 8px;
            margin-left: 10px;
        }

        /* Table Styles */
        #mainStockTable {
            border-collapse: separate;
            border-spacing: 0;
            width: 100% !important;
        }

        #mainStockTable thead th {
            background-color: #fff;
            color: #636e72;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.5px;
            padding: 15px;
            border-bottom: 2px solid #f1f2f6;
        }

        #mainStockTable tbody td {
            padding: 15px;
            vertical-align: middle;
            border-bottom: 1px solid #f8f9fa;
            color: #2d3436;
            font-size: 0.95rem;
        }

        .product-row {
            transition: background-color 0.2s;
        }

        .product-row:hover {
            background-color: #fbfcfe;
        }

        /* Icons & Badges */
        .book-icon {
            width: 40px;
            height: 40px;
            border-radius: 8px;
            background-color: rgba(75, 108, 183, 0.1);
            color: var(--primary-color);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
            margin-right: 15px;
        }

        .category-badge {
            background-color: white;
            border: 1px solid #dfe6e9;
            color: #636e72;
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .stock-circle {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 0.95rem;
            margin: 0 auto;
        }

        .stock-high {
            background-color: rgba(0, 176, 155, 0.1);
            color: var(--success-color);
            border: 1px solid rgba(0, 176, 155, 0.2);
        }

        .stock-low {
            background-color: rgba(255, 94, 98, 0.1);
            color: var(--danger-color);
            border: 1px solid rgba(255, 94, 98, 0.2);
        }

        /* Expansion Controls */
        .btn-view-sellers {
            padding: 8px 16px;
            border-radius: 8px;
            border: 1px solid var(--primary-color);
            color: var(--primary-color);
            background: transparent;
            font-size: 0.85rem;
            font-weight: 600;
            transition: all 0.2s;
            cursor: pointer;
        }

        .btn-view-sellers:hover {
            background-color: var(--primary-color);
            color: white;
        }

        .expand-icon {
            cursor: pointer;
            color: var(--primary-color);
            font-size: 1.2rem;
            transition: transform 0.3s;
            display: inline-block;
        }

        tr.shown .expand-icon {
            transform: rotate(45deg);
            color: var(--danger-color);
        }

        /* Child Row Styles */
        .child-row-content {
            background-color: #f8fbff;
            padding: 20px;
            border-radius: 0 0 12px 12px;
            box-shadow: inset 0 2px 10px rgba(0, 0, 0, 0.02);
            border: 1px solid #eef2f7;
            border-top: none;
            margin: -1px -1px 20px -1px;
            /* Align deeply */
        }

        .vendor-table {
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.03);
            width: 100%;
        }

        .vendor-table th {
            font-size: 0.75rem;
            text-transform: uppercase;
            color: #b2bec3;
            font-weight: 700;
            padding: 12px 15px;
            background: #fff;
            border-bottom: 2px solid #f1f2f6;
        }

        .vendor-table td {
            padding: 12px 15px;
            font-size: 0.9rem;
            color: #2d3436;
            border-bottom: 1px solid #f1f2f6;
        }

        /* Select2 Customization */
        .select2-container .select2-selection--single {
            height: 42px !important;
            border-radius: 8px !important;
            border: 1px solid #e0e0e0 !important;
            padding: 6px 0 !important;
        }
    </style>

    <div class="main-panel">
        <div class="content-wrapper">

            <div class="row">
                <div class="col-12">

                    <div class="card report-card">
                        <div class="card-body">

                            <!-- Header -->
                            <div class="report-header">
                                <div>
                                    <h3 class="report-title"><i class="fas fa-boxes mr-2 text-primary"></i>Stock Report</h3>
                                    <p class="report-subtitle">Detailed inventory tracking across all vendors and categories
                                    </p>
                                </div>
                                <div>
                                    <span class="badge badge-primary px-3 py-2"
                                        style="border-radius: 6px; font-size: 0.9rem;">
                                        Total Books: {{ count($stockReport) }}
                                    </span>
                                </div>
                            </div>

                            <!-- Filter Section -->
                            <div class="filter-container">
                                <form action="{{ url('admin/reports/stock_report') }}" method="GET">
                                    <div class="row align-items-end">
                                        <div class="col-md-4 mb-3 mb-md-0">
                                            <label class="filter-label">Category</label>
                                            <select name="category_id" class="form-control select2">
                                                <option value="">All Categories</option>
                                                @foreach ($categories as $category)
                                                    <option value="{{ $category->id }}"
                                                        {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                                        {{ $category->category_name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-4 mb-3 mb-md-0">
                                            <label class="filter-label">Section</label>
                                            <select name="section_id" class="form-control select2">
                                                <option value="">All Sections</option>
                                                @foreach ($sections as $section)
                                                    <option value="{{ $section->id }}"
                                                        {{ request('section_id') == $section->id ? 'selected' : '' }}>
                                                        {{ $section->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-4 text-right">
                                            <button type="submit" class="btn-apply">
                                                <i class="fas fa-filter mr-2"></i>Apply Filters
                                            </button>
                                            <a href="{{ url('admin/reports/stock_report') }}" class="btn-reset">
                                                Reset
                                            </a>
                                        </div>
                                    </div>
                                </form>
                            </div>

                            <!-- Data Table -->
                            <div class="table-responsive">
                                <table id="mainStockTable" class="table">
                                    <thead>
                                        <tr>
                                            <th style="width: 50px;"></th> <!-- Expand -->
                                            <th style="width: 80px;">ID</th>
                                            <th>Book Details</th>
                                            <th>Category / Section</th>
                                            <th class="text-center">Total Stock</th>
                                            <th class="text-center">Actions</th>
                                            <!-- Hidden Column for Expansion Data -->
                                            <th class="d-none">HiddenDetails</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($stockReport as $report)
                                            <tr data-id="{{ $report['id'] }}" class="product-row">
                                                <!-- 1. Expand Icon -->
                                                <td class="details-control text-center">
                                                    <i class="fas fa-plus-circle expand-icon"></i>
                                                </td>

                                                <!-- 2. ID -->
                                                <td class="font-weight-bold text-muted">#{{ $report['id'] }}</td>

                                                <!-- 3. Book Details -->
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="book-icon">
                                                            <i class="fas fa-book-open"></i>
                                                        </div>
                                                        <div>
                                                            <div class="font-weight-bold text-dark"
                                                                style="font-size: 1rem;">{{ $report['name'] }}</div>
                                                        </div>
                                                    </div>
                                                </td>

                                                <!-- 4. Category/Section -->
                                                <td>
                                                    <span
                                                        class="category-badge mb-1 d-inline-block">{{ $report['category'] }}</span>
                                                    <div class="small text-muted mt-1 pl-1">{{ $report['section'] }}</div>
                                                </td>

                                                <!-- 5. Total Stock -->
                                                <td class="text-center">
                                                    <div
                                                        class="stock-circle {{ $report['total_stock'] > 10 ? 'stock-high' : 'stock-low' }}">
                                                        {{ $report['total_stock'] }}
                                                    </div>
                                                </td>

                                                <!-- 6. Actions -->
                                                <td class="text-center">
                                                    <button type="button" class="btn-view-sellers view-details-btn">
                                                        View Sellers
                                                    </button>
                                                </td>

                                                <!-- 7. Hidden Detail Content -->
                                                <td class="d-none detail-content">
                                                    <div class="child-row-content">
                                                        <div class="d-flex align-items-center mb-3">
                                                            <div class="mr-3 text-primary"><i
                                                                    class="fas fa-store fa-lg"></i></div>
                                                            <h6 class="m-0 font-weight-bold text-dark">Vendor Stock
                                                                Distribution</h6>
                                                        </div>

                                                        <table class="table vendor-table table-sm">
                                                            <thead>
                                                                <tr>
                                                                    <th width="50%">Vendor Name</th>
                                                                    <th width="25%" class="text-center">Stock Available
                                                                    </th>
                                                                    <th width="25%" class="text-center">Status</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @foreach ($report['vendors'] as $vendor)
                                                                    <tr>
                                                                        <td class="font-weight-medium">
                                                                            {{ $vendor['name'] }}</td>
                                                                        <td class="text-center font-weight-bold">
                                                                            {{ $vendor['stock'] }}</td>
                                                                        <td class="text-center">
                                                                            @if ($vendor['stock'] > 0)
                                                                                <span class="badge badge-success">In
                                                                                    Stock</span>
                                                                            @else
                                                                                <span class="badge badge-danger">Out of
                                                                                    Stock</span>
                                                                            @endif
                                                                        </td>
                                                                    </tr>
                                                                @endforeach
                                                                @if (count($report['vendors']) == 0)
                                                                    <tr>
                                                                        <td colspan="3"
                                                                            class="text-center text-muted py-3">No vendors
                                                                            assigned</td>
                                                                    </tr>
                                                                @endif
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            @if (count($stockReport) == 0)
                                <div class="text-center py-5">
                                    <div class="bg-light d-inline-block p-4 rounded-circle mb-3">
                                        <i class="feather-box fa-3x text-muted"></i>
                                    </div>
                                    <h5>No Items Found</h5>
                                    <p class="text-muted">Try adjusting your category or section filters.</p>
                                </div>
                            @endif

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    {{-- Dependencies --}}
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        (function($) {
            "use strict";

            $(function() {
                // Initialize Select2
                if ($.fn.select2) {
                    $('.select2').select2({
                        placeholder: "Select an option",
                        width: '100%'
                    });
                }

                // Check and Initialize DataTable
                var initStockTable = function() {
                    // Check if both jQuery and DataTables are loaded
                    if (typeof $.fn.DataTable === 'undefined') {
                        // Retry shortly if not ready yet (admin templates sometimes lazy load)
                        setTimeout(initStockTable, 200);
                        return;
                    }

                    var tableId = '#mainStockTable';
                    // Re-initialize: Check existing instance
                    if ($.fn.DataTable.isDataTable(tableId)) {
                        $(tableId).DataTable().destroy();
                    }

                    var table = $(tableId).DataTable({
                        "pageLength": 10,
                        "order": [
                            [1, "asc"]
                        ], // Order by Book ID
                        "columnDefs": [{
                                "orderable": false,
                                "targets": [0, 5, 6]
                            }, // Expand(0), Actions(5), Hidden(6)
                            {
                                "className": "text-center",
                                "targets": [0, 4, 5]
                            },
                            {
                                "visible": false,
                                "targets": [6]
                            } // CRITICAL: Hide DetailData column
                        ],
                        "language": {
                            "search": "",
                            "searchPlaceholder": "Search by name or ID...",
                            "lengthMenu": "Show _MENU_ items",
                            "paginate": {
                                "next": "<i class='fas fa-chevron-right'></i>",
                                "previous": "<i class='fas fa-chevron-left'></i>"
                            }
                        },
                        "dom": '<"d-flex justify-content-between align-items-center mb-4"f>rt<"d-flex justify-content-between align-items-center mt-4"ip>'
                    });

                    // Row Expansion Logic
                    // We explicitly unbind click first to avoid duplicates if re-initialized
                    $(tableId + ' tbody').off('click', 'td.details-control, .view-details-btn');

                    $(tableId + ' tbody').on('click', 'td.details-control, .view-details-btn', function(e) {
                        e.preventDefault();
                        e.stopPropagation();

                        var tr = $(this).closest('tr');
                        var row = table.row(tr);

                        if (row.child.isShown()) {
                            // Close
                            row.child.hide();
                            tr.removeClass('shown');
                        } else {
                            // Open
                            // Get content from the hidden column index 6
                            // DataTables internal storage [0, 1, 2, 3, 4, 5, 6]
                            var rowData = row.data();
                            var details = rowData[6]; // Index 6 is our "HiddenDetails" column HTML

                            if (details) {
                                row.child(details).show();
                                tr.addClass('shown');
                                tr.next().addClass('fadeInRow'); // Add nice fade-in class
                            }
                        }
                    });
                };

                // Initialize with a slight safety delay to ensure resources are ready
                setTimeout(initStockTable, 100);
            });
        })(jQuery);
    </script>

    <style>
        .fadeInRow {
            animation: fadeRow 0.4s ease-out forwards;
        }

        @keyframes fadeRow {
            from {
                opacity: 0;
                transform: translateY(-5px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Fix DataTables Pagination Buttons to match theme */
        .page-item.active .page-link {
            background-color: #4b6cb7 !important;
            border-color: #4b6cb7 !important;
        }
    </style>
@endpush
