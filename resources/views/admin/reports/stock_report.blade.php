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
                                </div>
                            </div>

                            <!-- Filter Section -->
                            <div class="filter-container">
                                <form action="{{ url('admin/reports/stock_report') }}" method="GET">
                                    <div class="row align-items-end">
                                        <div class="col-md-3 mb-3 mb-md-0">
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
                                        <div class="col-md-3 mb-3 mb-md-0">
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
                                        <div class="col-md-3 mb-3 mb-md-0">
                                            <label class="filter-label">Stock Status</label>
                                            <select name="stock_status" class="form-control select2">
                                                <option value="">All Statuses</option>
                                                <option value="in_stock"
                                                    {{ request('stock_status') == 'in_stock' ? 'selected' : '' }}>In Stock
                                                </option>
                                                <option value="out_of_stock"
                                                    {{ request('stock_status') == 'out_of_stock' ? 'selected' : '' }}>Out of
                                                    Stock</option>
                                            </select>
                                        </div>
                                        <div class="col-md-3 text-right">
                                            <button type="submit" class="btn-apply w-100 mb-2">
                                                <i class="fas fa-filter mr-2"></i>Apply
                                            </button>
                                            <a href="{{ url('admin/reports/stock_report') }}"
                                                class="btn-reset w-100 d-inline-block text-center m-0">
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
                                            <th style="width: 50px;">No.</th>
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
                                    </tbody>
                                </table>
                            </div>



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
                        processing: true,
                        serverSide: true,
                        ajax: {
                            url: "{{ url()->current() }}",
                            data: function(d) {
                                d.category_id = $('select[name="category_id"]').val();
                                d.section_id = $('select[name="section_id"]').val();
                                d.stock_status = $('select[name="stock_status"]').val();
                            }
                        },
                        columns: [
                            {data: 'expand', name: 'expand', orderable: false, searchable: false},
                            {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                            {data: 'id_formatted', name: 'id'},
                            {data: 'book_details', name: 'name'},
                            {data: 'category_section', name: 'category', orderable: false},
                            {data: 'stock_badge', name: 'total_stock', orderable: false, searchable: false},
                            {data: 'actions', name: 'actions', orderable: false, searchable: false},
                            {data: 'hidden_details', name: 'hidden_details', orderable: false, searchable: false}
                        ],
                        "pageLength": 10,
                        "order": [
                            [2, "asc"]
                        ], // Order by Book ID
                        "columnDefs": [{
                                "orderable": false,
                                "targets": [0, 1, 6, 7]
                            }, // Expand(0), No(1), Actions(6), Hidden(7)
                            {
                                "className": "text-center",
                                "targets": [0, 1, 5, 6]
                            },
                            {
                                "visible": false,
                                "targets": [7]
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

                    // Dynamic Serial Numbering
                    table.on('order.dt search.dt', function() {
                        table.column(1, {
                            search: 'applied',
                            order: 'applied'
                        }).nodes().each(function(cell, i) {
                            cell.innerHTML = i + 1;
                        });
                    }).draw();

                    // Form submission using AJAX
                    $('form').on('submit', function(e) {
                        e.preventDefault();
                        table.draw();
                    });

                    // Row Expansion Logic
                    // We explicitly unbind click first to avoid duplicates if re-initialized
                    $(tableId + ' tbody').off('click', '.expand-icon, .view-details-btn');

                    $(tableId + ' tbody').on('click', '.expand-icon, .view-details-btn', function(e) {
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
                            // Get content from the hidden column name 'hidden_details'
                            var rowData = row.data();
                            var details = rowData.hidden_details || rowData[7]; // Support both object property and fallback

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
