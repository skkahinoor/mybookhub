@extends('admin.layout.layout')

@section('content')
    <div class="container-fluid">
        <!-- Font Awesome -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

        <!-- DataTables CSS -->
        <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap4.min.css">

        <!-- Custom CSS for refined styling -->
        <style>
            :root {
                --primary-color: #253858;
                --success-color: #25836e;
                --warning-color: #f59e0b;
                --danger-color: #dc2626;
                --info-color: #3b82f6;
                --light-bg: #f8fafc;
                --card-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
                --card-shadow-hover: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
                --border-radius: 12px;
                --transition: all 0.2s ease-in-out;
            }

            .page-header {
                position: relative;
                background: linear-gradient(135deg, var(--primary-color) 0%, #1e3a5f 100%);
                color: white;
                padding: 2rem;
                margin-bottom: 2rem;
                border-radius: var(--border-radius);
                box-shadow: var(--card-shadow);
                overflow: hidden;
            }

            .page-header::after {
                content: '';
                position: absolute;
                width: 220px;
                height: 220px;
                top: -60px;
                right: -60px;
                background: rgba(255, 255, 255, 0.08);
                border-radius: 50%;
                filter: blur(0.5px);
            }

            .page-header-content {
                position: relative;
                z-index: 1;
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 2rem;
                flex-wrap: wrap;
            }

            .page-header-text {
                max-width: 720px;
            }

            .page-title {
                font-size: 1.75rem;
                font-weight: 700;
                margin: 0;
                color: white;
            }

            .page-subtitle {
                font-size: 0.95rem;
                margin: 0;
                opacity: 0.9;
                color: rgba(255, 255, 255, 0.85);
            }

            .page-header-meta {
                display: flex;
                gap: 1rem;
                flex-wrap: wrap;
                margin-top: 1.5rem;
            }

            .meta-card {
                padding: 0.85rem 1.25rem;
                border-radius: 10px;
                background: rgba(255, 255, 255, 0.12);
                backdrop-filter: blur(6px);
                min-width: 140px;
                box-shadow: inset 0 0 0 1px rgba(255, 255, 255, 0.08);
            }

            .meta-label {
                font-size: 0.75rem;
                letter-spacing: 0.08em;
                text-transform: uppercase;
                opacity: 0.8;
                display: block;
                margin-bottom: 0.2rem;
            }

            .meta-value {
                font-size: 1.4rem;
                font-weight: 700;
            }

            .meta-value.total {
                color: #fde68a;
            }

            .meta-value.active {
                color: #86efac;
            }

            .meta-value.inactive {
                color: #fecaca;
            }

            .page-header-actions {
                display: flex;
                align-items: center;
                gap: 0.75rem;
            }

            .add-institution-btn {
                background: linear-gradient(135deg, var(--success-color) 0%, #166534 100%);
                border: none;
                color: white;
                padding: 0.75rem 1.5rem;
                border-radius: 8px;
                text-decoration: none;
                font-weight: 600;
                font-size: 0.9rem;
                transition: var(--transition);
                display: inline-flex;
                align-items: center;
                gap: 0.5rem;
                box-shadow: var(--card-shadow);
            }

            .add-institution-btn:hover {
                transform: translateY(-2px);
                box-shadow: var(--card-shadow-hover);
                color: white;
                text-decoration: none;
                background: linear-gradient(135deg, #166534 0%, #14532d 100%);
            }

            .stats-grid {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
                gap: 1rem;
                margin-bottom: 1.5rem;
            }

            .stats-card {
                background: #ffffff;
                border-radius: 10px;
                border: 1px solid #e5e7eb;
                padding: 0.9rem 1rem;
                display: flex;
                align-items: center;
                justify-content: space-between;
                box-shadow: 0 2px 4px rgba(15, 23, 42, 0.04);
                transition: transform 0.15s ease, box-shadow 0.15s ease;
            }

            .stats-card:hover {
                transform: translateY(-2px);
                box-shadow: 0 6px 14px rgba(15, 23, 42, 0.08);
            }

            .stats-card .meta {
                display: flex;
                flex-direction: column;
                gap: 0.15rem;
            }

            .stats-label {
                font-size: 0.8rem;
                font-weight: 500;
                text-transform: uppercase;
                letter-spacing: 0.06em;
                color: #6b7280;
            }

            .stats-count {
                font-size: 1.4rem;
                font-weight: 600;
                color: #111827;
            }

            .stats-icon {
                width: 36px;
                height: 36px;
                border-radius: 999px;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 0.95rem;
            }

            .stats-card.school .stats-icon {
                background: rgba(59, 130, 246, 0.1);
                color: #1d4ed8;
            }

            .stats-card.college .stats-icon {
                background: rgba(34, 197, 94, 0.1);
                color: #15803d;
            }

            .stats-card.university .stats-icon {
                background: rgba(139, 92, 246, 0.1);
                color: #7c3aed;
            }

            .stats-card.total .stats-icon {
                background: rgba(234, 179, 8, 0.1);
                color: #b45309;
            }

            .stats-card .count {
                font-size: 2rem;
                font-weight: 700;
                color: var(--primary-color);
                margin: 0;
                line-height: 1.2;
            }

            .stats-card .label {
                font-size: 0.875rem;
                color: #64748b;
                font-weight: 500;
                margin: 0;
                text-transform: uppercase;
                letter-spacing: 0.05em;
            }

            .main-table {
                background: white;
                border-radius: var(--border-radius);
                box-shadow: var(--card-shadow);
                overflow: hidden;
                border: 1px solid #e2e8f0;
            }

            .table thead th {
                background: linear-gradient(135deg, var(--primary-color) 0%, #1e3a5f 100%);
                color: white;
                font-weight: 600;
                font-size: 0.85rem;
                text-transform: uppercase;
                letter-spacing: 0.05em;
                border: none;
                padding: 1rem 0.75rem;
                white-space: nowrap;
            }

            .table tbody td {
                padding: 0.875rem 0.75rem;
                vertical-align: middle;
                border-color: #f1f5f9;
                font-size: 0.875rem;
            }

            .table tbody tr {
                transition: var(--transition);
                border-bottom: 1px solid #f1f5f9;
            }

            .table tbody tr:hover {
                background-color: #f8fafc;
                transform: scale(1.01);
            }

            .type-badge {
                padding: 0.375rem 0.75rem;
                border-radius: 20px;
                font-size: 0.75rem;
                font-weight: 600;
                display: inline-flex;
                align-items: center;
                gap: 0.25rem;
                text-transform: capitalize;
            }

            .type-badge.school {
                background: #dbeafe;
                color: #1d4ed8;
            }

            .type-badge.college {
                background: #dcfce7;
                color: #166534;
            }

            .type-badge.university {
                background: #f3e8ff;
                color: #7c3aed;
            }

            .location-info {
                font-size: 0.85rem;
            }

            .location-info .main-location {
                font-weight: 600;
                color: var(--primary-color);
                margin-bottom: 0.25rem;
            }

            .location-info .sub-location {
                color: #64748b;
                font-size: 0.8rem;
            }

            .status-toggle-container {
                display: flex;
                flex-direction: column;
                align-items: center;
                gap: 0.5rem;
                min-width: 80px;
            }

            .toggle-switch {
                position: relative;
                display: inline-block;
                width: 48px;
                height: 24px;
            }

            .toggle-switch input {
                opacity: 0;
                width: 0;
                height: 0;
            }

            .toggle-slider {
                position: absolute;
                cursor: pointer;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: #797b7e;
                transition: var(--transition);
                border-radius: 12px;
            }

            .toggle-slider:before {
                position: absolute;
                content: "";
                height: 18px;
                width: 18px;
                left: 3px;
                bottom: 3px;
                background-color: white;
                transition: var(--transition);
                border-radius: 50%;
                box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            }

            .toggle-switch input:checked+.toggle-slider {
                background: linear-gradient(135deg, var(--success-color), #166534);
            }

            .toggle-switch input:checked+.toggle-slider:before {
                transform: translateX(24px);
            }

            .status-label {
                font-size: 0.75rem;
                font-weight: 600;
                text-transform: uppercase;
                letter-spacing: 0.05em;
            }

            .status-label.active {
                color: var(--success-color);
            }

            .status-label.inactive {
                color: #dc2626;
            }

            .action-buttons {
                display: flex;
                gap: 0.25rem;
            }

            .action-btn {
                width: 36px;
                height: 36px;
                border-radius: 6px;
                border: none;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 0.875rem;
                transition: var(--transition);
                color: white;
            }

            .action-btn.edit {
                background: var(--info-color);
            }

            .action-btn.view {
                background: #0fa070;
            }

            .action-btn.delete {
                background: var(--danger-color);
            }

            .action-btn:hover {
                transform: translateY(-1px);
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
                color: white;
            }

            .empty-state {
                text-align: center;
                padding: 3rem 1rem;
                background: white;
                border-radius: var(--border-radius);
                box-shadow: var(--card-shadow);
                margin: 2rem 0;
            }

            .empty-state .icon {
                color: #cbd5e1;
                margin-bottom: 1rem;
            }

            .empty-state h4 {
                color: var(--primary-color);
                margin-bottom: 0.5rem;
                font-weight: 600;
            }

            .empty-state p {
                color: #52565c;
                margin-bottom: 2rem;
                font-size: 0.95rem;
            }

            .alert {
                border: none;
                border-radius: var(--border-radius);
                box-shadow: var(--card-shadow);
                font-size: 0.9rem;
                position: relative;
                padding-right: 3rem;
            }

            .alert-close {
                position: absolute;
                top: 50%;
                right: 1rem;
                transform: translateY(-50%);
                width: 34px;
                height: 34px;
                border-radius: 50%;
                border: 1px solid rgba(255, 255, 255, 0.7);
                background: rgba(255, 255, 255, 0.15);
                color: #fff;
                font-size: 1.25rem;
                font-weight: 600;
                line-height: 1;
                display: flex;
                align-items: center;
                justify-content: center;
                cursor: pointer;
                transition: var(--transition);
                padding: 0;
            }

            .alert-close:hover {
                background: rgba(255, 255, 255, 0.35);
                border-color: white;
            }

            .table-responsive {
                border-radius: var(--border-radius);
                overflow: hidden;
                box-shadow: var(--card-shadow);
            }

            @media (max-width: 768px) {
                .stats-grid {
                    grid-template-columns: 1fr;
                    gap: 1rem;
                }

                .page-header-content {
                    flex-direction: column;
                    align-items: flex-start;
                }

                .page-header-actions {
                    width: 100%;
                    justify-content: flex-start;
                }

                .page-title {
                    font-size: 1.5rem;
                }

                .action-buttons {
                    flex-direction: column;
                    gap: 0.125rem;
                }

                .action-btn {
                    width: 32px;
                    height: 32px;
                }
            }
        </style>

        @php
            $totalInstitutions = $institutions->count();
            $activeInstitutions = $institutions->where('status', 1)->count();
            $inactiveInstitutions = max($totalInstitutions - $activeInstitutions, 0);
        @endphp

        <div class="row">
            <div class="col-12">
                <!-- Page Header -->
                <div class="page-header">
                    <div class="page-header-content">
                        <div class="page-header-text">
                            <h1 class="page-title">Institution Management</h1>
                            <p class="page-subtitle">Manage schools, colleges, and universities efficiently</p>

                        </div>
                        <div class="page-header-actions">
                            <a href="{{ url('admin/institution-managements/create') }}" class="add-institution-btn">
                                <i class="fas fa-plus"></i>
                                Add New Institution
                            </a>
                        </div>
                    </div>
                </div>

                @if (Session::has('success_message'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle me-2"></i>
                        <strong>Success!</strong> {{ Session::get('success_message') }}
                        <button type="button" class="alert-close" data-bs-dismiss="alert" aria-label="Close">&times;</button>
                    </div>
                @endif

                <!-- Statistics Cards -->
                @if ($institutions->count() > 0)
                    <div class="stats-grid">
                        <div class="stats-card school">
                            <div class="meta">
                                <span class="stats-label">Schools</span>
                                <span class="stats-count">{{ $institutions->where('type', 'school')->count() }}</span>
                            </div>
                            <div class="stats-icon">
                                <i class="fas fa-school"></i>
                            </div>
                        </div>

                        <div class="stats-card college">
                            <div class="meta">
                                <span class="stats-label">Colleges</span>
                                <span class="stats-count">{{ $institutions->where('type', 'college')->count() }}</span>
                            </div>
                            <div class="stats-icon">
                                <i class="fas fa-graduation-cap"></i>
                            </div>
                        </div>

                        <div class="stats-card university">
                            <div class="meta">
                                <span class="stats-label">Universities</span>
                                <span class="stats-count">{{ $institutions->where('type', 'university')->count() }}</span>
                            </div>
                            <div class="stats-icon">
                                <i class="fas fa-university"></i>
                            </div>
                        </div>

                        <div class="stats-card total">
                            <div class="meta">
                                <span class="stats-label">Total Institutions</span>
                                <span class="stats-count">{{ $institutions->count() }}</span>
                            </div>
                            <div class="stats-icon">
                                <i class="fas fa-chart-bar"></i>
                            </div>
                        </div>
                    </div>
                @endif


                @if ($institutions->count() > 0)
                    <!-- Institutions Table -->
                    <div class="table-responsive">
                        <table id="institutionsTable" class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th class="text-center" style="width: 50px">#</th>
                                    <th>Institution Name</th>
                                    <th>Type</th>
                                    <th>Board</th>
                                    <th>Principal Name</th>
                                    <th>Contact</th>
                                    <th>Location</th>
                                    <th class="text-center" style="width: 100px">Status</th>
                                    <th class="text-center" style="width: 120px">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($institutions as $index => $institution)
                                    <tr>
                                        <td class="text-center">{{ $index + 1 }}</td>
                                        <td>
                                            <strong class="text-dark">{{ $institution->name }}</strong>
                                        </td>
                                        <td>
                                            <span class="type-badge {{ $institution->type }}">
                                                <i
                                                    class="fas fa-{{ $institution->type == 'school' ? 'school' : ($institution->type == 'college' ? 'graduation-cap' : 'university') }}"></i>
                                                {{ ucfirst($institution->type) }}
                                            </span>
                                        </td>
                                        <td>{{ $institution->board ?? 'N/A' }}</td>
                                        <td>{{ $institution->principal_name ?? 'N/A' }}</td>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <i class="fas fa-phone text-success"></i>
                                                <span>{{ $institution->contact_number ?? 'N/A' }}</span>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="location-info">
                                                <div class="main-location">
                                                    <i class="fas fa-map-marker-alt text-primary me-1"></i>
                                                    {{ $institution->state->name ?? 'N/A' }}
                                                </div>
                                                <div class="sub-location">
                                                    {{ $institution->district->name ?? '' }}
                                                    @if ($institution->block)
                                                        , {{ $institution->block->name }}
                                                    @endif
                                                    , {{ $institution->country->name ?? '' }}
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <div class="status-toggle-container">
                                                <label class="toggle-switch">
                                                    <input type="checkbox" class="institution-status-toggle"
                                                        data-institution-id="{{ $institution->id }}"
                                                        {{ $institution->status == 1 ? 'checked' : '' }}>
                                                    <span class="toggle-slider"></span>
                                                </label>
                                                <span
                                                    class="status-label {{ $institution->status == 1 ? 'active' : 'inactive' }}">
                                                    {{ $institution->status == 1 ? 'Active' : 'Inactive' }}
                                                </span>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <div class="action-buttons">
                                                <a href="{{ url('admin/institution-managements/' . $institution->id . '/edit') }}"
                                                    class="action-btn edit" title="Edit Institution">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <button type="button" class="action-btn view"
                                                    onclick="viewInstitution({{ $institution->id }})" title="View Details">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <button type="button" class="action-btn delete"
                                                    onclick="confirmDelete({{ $institution->id }})"
                                                    title="Delete Institution">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <!-- Empty State -->
                    <div class="empty-state">
                        <div class="icon">
                            <i class="fas fa-schools fa-3x"></i>
                        </div>
                        <h4>No Institutions Found</h4>
                        <p>Get started by adding your first educational institution to the system.</p>
                        <a href="{{ url('admin/institution-managements/create') }}" class="add-institution-btn">
                            <i class="fas fa-plus"></i>
                            Add First Institution
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>

    <!-- Bootstrap JS (if not already included in layout) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>

    <script>
        $(document).ready(function() {
            // Initialize DataTable with refined configuration
            $('#institutionsTable').DataTable({
                "pageLength": 10,
                "lengthMenu": [
                    [10, 25, 50, -1],
                    [10, 25, 50, "All"]
                ],
                "ordering": true,
                "order": [
                    [0, "asc"]
                ],
                "language": {
                    "search": "Search institutions:",
                    "lengthMenu": "Show _MENU_ entries",
                    "info": "Showing _START_ to _END_ of _TOTAL_ institutions",
                    "paginate": {
                        "first": "First",
                        "last": "Last",
                        "next": "Next",
                        "previous": "Previous"
                    }
                },
                "columnDefs": [{
                        "orderable": false,
                        "targets": [0, 6, 7]
                    }, // Disable sorting for #, Status, and Actions
                    {
                        "width": "10%",
                        "targets": 1
                    }, // Institution Name
                    {
                        "width": "15%",
                        "targets": 2
                    }, // Type
                    {
                        "width": "12%",
                        "targets": 3
                    }, // Board
                    {
                        "width": "12%",
                        "targets": 4
                    }, // Principal Name
                    {
                        "width": "12%",
                        "targets": 5
                    }, // Contact
                    {
                        "width": "20%",
                        "targets": 5
                    }, // Location
                    {
                        "className": "text-center",
                        "targets": [0, 6, 7]
                    }
                ],
                "responsive": true,
                "dom": '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>' +
                    '<"row"<"col-sm-12"tr>>' +
                    '<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>'
            });

            // Status toggle functionality
            $(document).on('change', '.institution-status-toggle', function() {
                const $checkbox = $(this);
                const institutionId = $checkbox.data('institution-id');
                const isChecked = $checkbox.is(':checked');
                const $statusLabel = $checkbox.closest('.status-toggle-container').find('.status-label');
                const originalStatus = $checkbox.prop('checked');

                $.ajax({
                    type: 'POST',
                    url: '{{ url('admin/update-institution-status') }}',
                    data: {
                        _token: '{{ csrf_token() }}',
                        institution_id: institutionId,
                        status: isChecked ? 1 : 0
                    },
                    beforeSend: function() {
                        $checkbox.prop('disabled', true);
                    },
                    success: function(response) {
                        if (response.success) {
                            $statusLabel
                                .text(isChecked ? 'Active' : 'Inactive')
                                .toggleClass('active', isChecked)
                                .toggleClass('inactive', !isChecked);

                            // Show success message
                            showToast('Institution status updated successfully', 'success');
                        } else {
                            $checkbox.prop('checked', !isChecked);
                            showToast('Failed to update status. Please try again.', 'error');
                        }
                    },
                    error: function() {
                        $checkbox.prop('checked', !isChecked);
                        showToast('Unable to update status. Please check your connection.',
                            'error');
                    },
                    complete: function() {
                        $checkbox.prop('disabled', false);
                    }
                });
            });
        });

        // Utility function for toast notifications
        function showToast(message, type = 'info') {
            const bgColor = type === 'success' ? '#10b981' : type === 'error' ? '#ef4444' : '#3b82f6';
            const toastHtml = `
        <div class="toast align-items-center text-white bg-${type === 'success' ? 'success' : type === 'error' ? 'danger' : 'primary'} border-0" role="alert" style="position: fixed; top: 20px; right: 20px; z-index: 1055; min-width: 300px;">
            <div class="d-flex">
                <div class="toast-body">
                    ${message}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        </div>
    `;

            $('body').append(toastHtml);
            const toastElement = $('.toast:last');
            const toast = new bootstrap.Toast(toastElement[0]);
            toast.show();

            toastElement.on('hidden.bs.toast', function() {
                $(this).remove();
            });
        }

        function viewInstitution(id) {
            window.location.href = '{{ url('admin/institution-managements') }}/' + id;
        }

        function confirmDelete(id) {
            if (confirm(
                    "Are you sure you want to delete this institution? This action cannot be undone and will permanently remove all associated data."
                    )) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '{{ url('admin/institution-managements') }}/' + id;
                form.style.display = 'none';

                const csrfToken = document.createElement('input');
                csrfToken.type = 'hidden';
                csrfToken.name = '_token';
                csrfToken.value = '{{ csrf_token() }}';
                form.appendChild(csrfToken);

                const methodField = document.createElement('input');
                methodField.type = 'hidden';
                methodField.name = '_method';
                methodField.value = 'DELETE';
                form.appendChild(methodField);

                document.body.appendChild(form);
                form.submit();
            }
        }
    </script>
@endsection
