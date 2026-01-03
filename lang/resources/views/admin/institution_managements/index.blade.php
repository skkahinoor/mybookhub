@extends('admin.layout.layout')

@section('content')
<div class="container-fluid">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap4.min.css">

    <!-- Custom CSS for enhanced styling -->
    <style>
    .stats-card {
        background: #253858;
        border-radius: 15px;
        border: none;
        box-shadow: 0 8px 24px rgba(30, 35, 90, 0.12);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .stats-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 16px 40px rgba(20, 30, 70, 0.14);
    }
    .stats-card.success { background: #25836e; }
    .stats-card.warning { background: #333840; }
    .stats-card.info { background: #dbe9fa; color: #1d3354; }
    .stats-card.danger { background: #b91c1c; }

    .badge-primary { background: #253858; color: #fff; }
    .badge-success { background: #25836e; color: #fff; }
    .badge-info { background: #dbe9fa; color: #1d3354; }
    .badge-secondary { background: #636e72; color: #fff; }

    .add-institution-btn {
        background: #25836e;
        border: none;
        color: white;
        padding: 12px 24px;
        border-radius: 8px;
        text-decoration: none;
        font-weight: 600;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 30px;
    }
    .add-institution-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 32px rgba(40, 70, 120, 0.22);
        color: white;
        text-decoration: none;
    }
    </style>

    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="page-title">Institution Management</h2>
                    <p class="page-subtitle">Manage schools, colleges, and universities</p>
                </div>
                <a href="{{ url('admin/institution-managements/create') }}" class="add-institution-btn">
                    <i class="fas fa-plus"></i>
                    Add New Institution 
                </a>
            </div>

            @if(Session::has('success_message'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <strong>Success!</strong> {{ Session::get('success_message') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            <!-- Statistics Cards -->
            <div class="row mb-4">
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="card stats-card success">
                        <div class="card-body text-center">
                            <i class="fas fa-school fa-2x mb-3" style="color: white;"></i>
                            <h4 class="mb-1" style="color: white;">{{ $institutions->where('type', 'school')->count() }}</h4>
                            <p class="mb-0" style="color: white; opacity: 0.9;">Schools</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="card stats-card warning">
                        <div class="card-body text-center">
                            <i class="fas fa-graduation-cap fa-2x mb-3" style="color: white;"></i>
                            <h4 class="mb-1" style="color: white;">{{ $institutions->where('type', 'college')->count() }}</h4>
                            <p class="mb-0" style="color: white; opacity: 0.9;">Colleges</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="card stats-card success">
                        <div class="card-body text-center">
                            <i class="fas fa-university fa-2x mb-3" style="color: white;"></i>
                            <h4 class="mb-1" style="color: white;">{{ $institutions->where('type', 'university')->count() }}</h4>
                            <p class="mb-0" style="color: white; opacity: 0.9;">Universities</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="card stats-card warning">
                        <div class="card-body text-center">
                            <i class="fas fa-building fa-2x mb-3" style="color: white;"></i>
                            <h4 class="mb-1" style="color: white;">{{ $institutions->count() }}</h4>
                            <p class="mb-0" style="color: white; opacity: 0.9;">Total</p>
                        </div>
                    </div>
                </div>
            </div>

            @if($institutions->count() > 0)
                <div class="table-responsive">
                    <table id="institutionsTable" class="table table-striped table-bordered">
                        <thead class="thead-dark">
                            <tr>
                                <th>#</th>
                                <th>Institution Name</th>
                                <th>Type</th>
                                <th>Board</th>
                                <th>Contact</th>
                                <th>Location</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($institutions as $index => $institution)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td><strong>{{ $institution->name }}</strong></td>
                                    <td>
                                        <span class="badge badge-{{ $institution->type == 'school' ? 'primary' : ($institution->type == 'college' ? 'success' : 'info') }}">
                                            <i class="fas fa-{{ $institution->type == 'school' ? 'school' : ($institution->type == 'college' ? 'graduation-cap' : 'university') }}"></i>
                                            {{ ucfirst($institution->type) }}
                                        </span>
                                    </td>
                                    <td>{{ $institution->board }}</td>
                                    <td><i class="fas fa-phone text-success"></i> {{ $institution->contact_number }}</td>
                                    <td>
                                        <div class="location-info">
                                            <div><i class="fas fa-map-marker-alt text-danger"></i> {{ $institution->state->name ?? '' }}</div>
                                            <div class="text-muted small">
                                                {{ $institution->district->name ?? '' }}
                                                @if($institution->block)
                                                    , {{ $institution->block->name ?? '' }}
                                                @endif
                                                , {{ $institution->country->name ?? '' }}
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge badge-{{ $institution->status == 1 ? 'success' : 'danger' }}">
                                            {{ $institution->status == 1 ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ url('admin/institution-managements/'.$institution->id.'/edit') }}" class="btn btn-sm btn-primary" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-info" onclick="viewInstitution({{ $institution->id }})" title="View">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-danger" onclick="confirmDelete({{ $institution->id }})" title="Delete">
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
                <div class="empty-state text-center mt-5">
                    <i class="fas fa-school fa-3x text-secondary mb-3"></i>
                    <h4>No Institutions Found</h4>
                    <p>Start by adding your first institution to the system.</p>
                    <a href="{{ url('admin/institution-managements/create') }}" class="add-institution-btn">
                        <i class="fas fa-plus"></i> Add New Institution
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>

<!-- Bootstrap + DataTables JS -->
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap4.min.js"></script>

<script>
$(document).ready(function() {
    $('#institutionsTable').DataTable({
        "pageLength": 10,
        "lengthMenu": [5, 10, 25, 50],
        "ordering": true,
        "columnDefs": [
            { "orderable": false, "targets": [6] } // Disable sorting for Actions column
        ]
    });
});

function viewInstitution(id) {
    window.location.href = '{{ url("admin/institution-managements") }}/' + id;
}

function confirmDelete(id) {
    if (confirm("Are you sure you want to delete this institution? This action cannot be undone.")) {
        var form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ url("admin/institution-managements") }}/' + id;

        var csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        form.appendChild(csrfToken);

        var methodField = document.createElement('input');
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
