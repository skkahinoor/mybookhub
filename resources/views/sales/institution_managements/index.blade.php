@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">

    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #253858 0%, #1e3a5f 100%);
            --accent-color: #25836e;
            --border-radius: 12px;
        }

        .page-header {
            background: var(--primary-gradient);
            color: white;
            padding: 2rem;
            border-radius: var(--border-radius);
            margin-bottom: 2rem;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: #ffffff;
            padding: 1.5rem;
            border-radius: var(--border-radius);
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
            display: flex;
            align-items: center;
            justify-content: space-between;
            transition: transform 0.2s;
        }

        .stat-card:hover {
            transform: translateY(-5px);
        }

        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }

        .table-card {
            background: #ffffff;
            border-radius: var(--border-radius);
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            overflow: hidden;
            border: none;
        }

        .table thead th {
            background: #f8fafc;
            color: #475569;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.8rem;
            letter-spacing: 0.05em;
            border-bottom: 2px solid #e2e8f0;
            padding: 1rem;
        }

        .table tbody td {
            padding: 1rem;
            vertical-align: middle;
            color: #1e293b;
            font-size: 0.9rem;
        }

        .badge-status {
            padding: 0.5rem 0.75rem;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.75rem;
        }

        .btn-action {
            width: 32px;
            height: 32px;
            padding: 0;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            margin: 0 2px;
        }
    </style>

    <div class="page-header d-flex justify-content-between align-items-center">
        <div>
            <h1 class="h3 mb-1">Institution Management</h1>
            <p class="mb-0 text-white-50">Manage and oversee all educational institutions</p>
        </div>
        @if(Auth::guard('sales')->user()->can('add_institutions'))
        <a href="{{ route('sales.institution_managements.create') }}" class="btn btn-light px-4 font-weight-bold">
            <i class="fas fa-plus me-2"></i>Add Institution
        </a>
        @endif
    </div>

    @if(Session::has('success_message'))
        <div class="alert alert-success alert-dismissible fade show mb-4 border-0 shadow-sm" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            {{ Session::get('success_message') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="stats-grid">
        <div class="stat-card">
            <div>
                <span class="text-muted small">Total Institutions</span>
                <h3 class="mb-0">{{ $institutions->total() }}</h3>
            </div>
            <div class="stat-icon bg-primary text-white">
                <i class="fas fa-university"></i>
            </div>
        </div>
        <div class="stat-card">
            <div>
                <span class="text-muted small">Active Schools</span>
                <h3 class="mb-0">{{ \App\Models\InstitutionManagement::where('added_by', Auth::guard('sales')->id())->where('type', 'school')->count() }}</h3>
            </div>
            <div class="stat-icon bg-success text-white">
                <i class="fas fa-school"></i>
            </div>
        </div>
        <div class="stat-card">
            <div>
                <span class="text-muted small">Pending Approval</span>
                <h3 class="mb-0">{{ \App\Models\InstitutionManagement::where('added_by', Auth::guard('sales')->id())->where('status', 0)->count() }}</h3>
            </div>
            <div class="stat-icon bg-warning text-white">
                <i class="fas fa-clock"></i>
            </div>
        </div>
    </div>

    <div class="card table-card">
        <div class="card-body p-0">
            <div class="table-responsive p-3">
                <table id="institutionTable" class="table table-hover align-middle mb-0" style="width:100%">
                    <thead>
                        <tr>
                            <th class="text-center">#</th>
                            <th>Institution Name</th>
                            <th>Type</th>
                            <th>Board</th>
                            <th>Principal Name</th>
                            <th>Contact</th>
                            <th>Pincode</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($institutions as $index => $institution)
                            <tr>
                                <td class="text-center text-muted">{{ $index + 1 }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="ms-1">
                                            <div class="fw-bold">{{ $institution->name }}</div>
                                            <div class="text-muted small">{{ ucfirst($institution->type) }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ ucfirst($institution->type) }}</td>
                                <td>{{ $institution->board }}</td>
                                <td>{{ $institution->principal_name }}</td>
                                <td>
                                    <div class="small"><i class="fas fa-phone me-1 text-muted"></i>{{ $institution->contact_number }}</div>
                                </td>
                                <td><span class="badge bg-light text-dark">{{ $institution->pincode }}</span></td>
                                <td class="text-center">
                                    @if($institution->status == 1)
                                        <span class="badge-status bg-success-subtle text-success">
                                            <i class="fas fa-check-circle me-1"></i>Approved
                                        </span>
                                    @else
                                        <span class="badge-status bg-warning-subtle text-warning">
                                            <i class="fas fa-clock me-1"></i>Pending
                                        </span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center">
                                        @if(Auth::guard('sales')->user()->can('view_institutions'))
                                        <a href="{{ route('sales.institution_managements.show', $institution->id) }}"
                                            class="btn btn-info btn-action text-white" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @endif
                                        @if(Auth::guard('sales')->user()->can('edit_institutions'))
                                        <a href="{{ route('sales.institution_managements.edit', $institution->id) }}"
                                            class="btn btn-primary btn-action" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-5 text-muted">
                                    <i class="fas fa-university fa-3x mb-3 d-block opacity-25"></i>
                                    No institutions found in your records.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($institutions->hasPages())
                <div class="px-3 pb-3 d-flex justify-content-center">
                    {{ $institutions->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>

<script>
$(document).ready(function() {
    const $table = $('#institutionTable');
    
    // Safety check: Count headers and cells
    const initDataTable = () => {
        const headerCount = $table.find('thead tr').first().children('th').length;
        let mismatchFound = false;

        $table.find('tbody tr').each(function() {
            const $tr = $(this);
            const tdCount = $tr.children('td').length;

            if (tdCount !== headerCount && tdCount > 0) {
                mismatchFound = true;
                console.warn(`DataTables Mismatch: Row has ${tdCount} cells, expected ${headerCount}. Padding now.`);
                if (tdCount < headerCount) {
                    for (let i = 0; i < (headerCount - tdCount); i++) {
                        $tr.append('<td></td>');
                    }
                }
            }
        });

        // Initialize even if empty, as long as it's not malformed
        try {
            $table.DataTable({
                "pageLength": 10,
                "lengthMenu": [[5, 10, 25, 50, -1], [5, 10, 25, 50, "All"]],
                "ordering": true,
                "responsive": true,
                "dom": '<"row mb-3"<"col-md-6"l><"col-md-6"f>>rt<"row mt-3"<"col-md-6"i><"col-md-6"p>>',
                "columnDefs": [
                    { "orderable": false, "targets": [8] }
                ],
                "language": {
                    "search": "",
                    "searchPlaceholder": "Search institutions...",
                    "paginate": {
                        "previous": "<i class=\"fas fa-chevron-left\"></i>",
                        "next": "<i class=\"fas fa-chevron-right\"></i>"
                    }
                }
            });
            $('.dataTables_filter input').addClass('form-control shadow-sm border-0 bg-light px-3 py-2');
            $('.dataTables_length select').addClass('form-select shadow-sm border-0 bg-light');
        } catch (e) {
            console.error("DataTable initialization failed:", e);
        }
    };

    // Ensure DOM is fully ready
    if ($table.length > 0) {
        setTimeout(initDataTable, 100);
    }
});
</script>
@endsection
