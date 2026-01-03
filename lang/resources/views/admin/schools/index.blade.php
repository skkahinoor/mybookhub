@extends('admin.layout.layout')

@section('content')
<div class="container-fluid">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Custom CSS for enhanced styling -->
    <style>
        .stats-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 15px;
            border: none;
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .stats-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(0,0,0,0.15);
        }
        .stats-card.success {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        }
        .stats-card.warning {
            background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
        }
        .stats-card.info {
            background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%);
        }
        .stats-card.danger {
            background: linear-gradient(135deg, #ff9a9e 0%, #fecfef 100%);
        }

        .school-card {
            background: white;
            border-radius: 12px;
            border: 1px solid #e9ecef;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            transition: all 0.3s ease;
            margin-bottom: 20px;
        }
        .school-card:hover {
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
            transform: translateY(-2px);
        }

        .school-header {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
            padding: 20px;
            border-radius: 12px 12px 0 0;
            position: relative;
            overflow: hidden;
        }
        .school-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="25" cy="25" r="1" fill="white" opacity="0.1"/><circle cx="75" cy="75" r="1" fill="white" opacity="0.1"/><circle cx="50" cy="10" r="0.5" fill="white" opacity="0.1"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
        }

        .school-body {
            padding: 25px;
        }

        .school-info {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
        }

        .info-item {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .info-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 16px;
        }

        .info-content h6 {
            margin: 0;
            font-weight: 600;
            color: #495057;
        }

        .info-content p {
            margin: 0;
            color: #6c757d;
            font-size: 14px;
        }

        .action-buttons {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .btn-action {
            padding: 8px 16px;
            border-radius: 25px;
            border: none;
            font-weight: 500;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-view {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        .btn-view:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
            color: white;
        }

        .btn-edit {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
        }
        .btn-edit:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(240, 147, 251, 0.4);
            color: white;
        }

        .btn-delete {
            background: linear-gradient(135deg, #ff9a9e 0%, #fecfef 100%);
            color: white;
        }
        .btn-delete:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(255, 154, 158, 0.4);
            color: white;
        }

        .search-section {
            background: white;
            border-radius: 12px;
            padding: 25px;
            margin-bottom: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }

        .search-input {
            border: 2px solid #e9ecef;
            border-radius: 25px;
            padding: 12px 20px;
            transition: all 0.3s ease;
        }
        .search-input:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }

        .status-badge {
            padding: 6px 16px;
            border-radius: 20px;
            font-weight: 500;
            font-size: 12px;
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #6c757d;
        }

        .empty-state i {
            font-size: 64px;
            margin-bottom: 20px;
            opacity: 0.5;
        }

        .pagination-wrapper {
            margin-top: 30px;
        }

        .page-link {
            border-radius: 8px;
            margin: 0 2px;
            border: none;
            color: #667eea;
        }

        .page-item.active .page-link {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
        }
    </style>

    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="mb-1" style="color: #2c3e50; font-weight: 700;">
                       Schools Management
                    </h2>
                    <p class="text-muted mb-0">Manage and monitor all registered schools</p>
                </div>
                <a href="{{ route('admin.schools.create') }}" class="btn btn-primary btn-lg"
                   style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none; border-radius: 25px; padding: 12px 30px;">
                    <i class="fas fa-plus me-2"></i>Add New School
                </a>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card stats-card success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title mb-1">Total Schools</h6>
                            <h3 class="mb-0">{{ $schools->total() ?? 0 }}</h3>
                        </div>
                        <i class="fas fa-school fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card stats-card warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title mb-1">Active Schools</h6>
                            <h3 class="mb-0">{{ $schools->where('status', 1)->count() }}</h3>
                        </div>
                        <i class="fas fa-check-circle fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card stats-card info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title mb-1">Total Students</h6>
                            <h3 class="mb-0">{{ number_format($schools->sum('total_students')) }}</h3>
                        </div>
                        <i class="fas fa-user-graduate fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card stats-card danger text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title mb-1">Total Teachers</h6>
                            <h3 class="mb-0">{{ number_format($schools->sum('total_teachers')) }}</h3>
                        </div>
                        <i class="fas fa-chalkboard-teacher fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Success Message -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert"
             style="border-radius: 12px; border: none; background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Search Section -->
    <div class="search-section">
        <div class="row align-items-center">
            <div class="col-md-8">
                <div class="input-group">
                    <span class="input-group-text" style="border: none; background: transparent;">
                        <i class="fas fa-search text-muted"></i>
                    </span>
                    <input type="text" class="form-control search-input" id="searchInput"
                           placeholder="Search schools by name, address, or principal...">
                </div>
            </div>
            <div class="col-md-4 text-md-end mt-3 mt-md-0">
                <button class="btn btn-outline-secondary" onclick="clearSearch()">
                    <i class="fas fa-times me-2"></i>Clear
                </button>
            </div>
        </div>
    </div>

    <!-- Schools List -->
    <div class="row" id="schoolsContainer">
        @forelse($schools as $school)
            <div class="col-lg-6 col-xl-4 mb-4 school-item"
                 data-name="{{ strtolower($school->name ?? '') }}"
                 data-address="{{ strtolower($school->address ?? '') }}"
                 data-principal="{{ strtolower($school->principal_name ?? '') }}"
                 data-debug-name="{{ $school->name ?? 'NULL' }}"
                 data-debug-principal="{{ $school->principal_name ?? 'NULL' }}">
                <div class="school-card">
                    <div class="school-header">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h5 class="mb-1">{{ $school->name }}</h5>
                                <p class="mb-0 opacity-75">ID: {{ $school->id }}</p>
                            </div>
                            <span class="status-badge {{ $school->status ? 'bg-success' : 'bg-danger' }}">
                                {{ $school->status ? 'Active' : 'Inactive' }}
                            </span>
                        </div>
                    </div>
                    <div class="school-body">
                        <div class="school-info">
                            <div class="info-item">
                                <div class="info-icon" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                                    <i class="fas fa-map-marker-alt"></i>
                                </div>
                                <div class="info-content">
                                    <h6>Address</h6>
                                    <p>{{ Str::limit($school->address, 40) }}</p>
                                </div>
                            </div>

                            <div class="info-item">
                                <div class="info-icon" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                                    <i class="fas fa-phone"></i>
                                </div>
                                <div class="info-content">
                                    <h6>Phone</h6>
                                    <p>{{ $school->phone ?? 'N/A' }}</p>
                                </div>
                            </div>

                            <div class="info-item">
                                <div class="info-icon" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                                    <i class="fas fa-envelope"></i>
                                </div>
                                <div class="info-content">
                                    <h6>Email</h6>
                                    <p>{{ $school->email ?? 'N/A' }}</p>
                                </div>
                            </div>

                            <div class="info-item">
                                <div class="info-icon" style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);">
                                    <i class="fas fa-user-tie"></i>
                                </div>
                                <div class="info-content">
                                    <h6>Principal</h6>
                                    <p>{{ $school->principal_name ?? 'N/A' }}</p>
                                </div>
                            </div>

                            <div class="info-item">
                                <div class="info-icon" style="background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%);">
                                    <i class="fas fa-user-graduate"></i>
                                </div>
                                <div class="info-content">
                                    <h6>Students</h6>
                                    <p>{{ number_format($school->total_students) }}</p>
                                </div>
                            </div>

                            <div class="info-item">
                                <div class="info-icon" style="background: linear-gradient(135deg, #ff9a9e 0%, #fecfef 100%);">
                                    <i class="fas fa-chalkboard-teacher"></i>
                                </div>
                                <div class="info-content">
                                    <h6>Teachers</h6>
                                    <p>{{ number_format($school->total_teachers) }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="action-buttons">
                            <a href="{{ route('admin.schools.show', $school->id) }}"
                               class="btn-action btn-view" title="View Details">
                                <i class="fas fa-eye"></i>
                                <span>View</span>
                            </a>
                            <a href="{{ route('admin.schools.edit', $school->id) }}"
                               class="btn-action btn-edit" title="Edit School">
                                <i class="fas fa-edit"></i>
                                <span>Edit</span>
                            </a>
                            <form action="{{ route('admin.schools.destroy', $school->id) }}"
                                  method="POST" style="display: inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-action btn-delete"
                                        title="Delete School"
                                        onclick="return confirm('Are you sure you want to delete this school? This action cannot be undone.')">
                                    <i class="fas fa-trash"></i>
                                    <span>Delete</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="empty-state">
                    <i class="fas fa-school"></i>
                    <h4>No Schools Found</h4>
                    <p class="mb-4">There are no schools registered in the system yet.</p>
                    <a href="{{ route('admin.schools.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>Add Your First School
                    </a>
                </div>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($schools->hasPages())
        <div class="pagination-wrapper">
            <div class="d-flex justify-content-center">
                {{ $schools->links() }}
            </div>
        </div>
    @endif
</div>

<!-- Search JavaScript -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const schoolItems = document.querySelectorAll('.school-item');

    searchInput.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase().trim();

        schoolItems.forEach((item, index) => {
            // Get data attributes with fallbacks
            const name = (item.getAttribute('data-name') || '').toLowerCase();
            const address = (item.getAttribute('data-address') || '').toLowerCase();
            const principal = (item.getAttribute('data-principal') || '').toLowerCase();

            // Debug attributes (original values)
            const debugName = item.getAttribute('data-debug-name') || 'NULL';
            const debugPrincipal = item.getAttribute('data-debug-principal') || 'NULL';

            // Debug logging for first few items when searching
            if (searchTerm && searchTerm.length > 0 && index < 3) {
                console.log(`Item ${index}:`);
                console.log('  Search term:', searchTerm);
                console.log('  Original Name:', debugName);
                console.log('  Original Principal:', debugPrincipal);
                console.log('  Lowercase Name:', name);
                console.log('  Lowercase Address:', address);
                console.log('  Lowercase Principal:', principal);
                console.log('  Name match:', name.includes(searchTerm));
                console.log('  Address match:', address.includes(searchTerm));
                console.log('  Principal match:', principal.includes(searchTerm));
            }

            // Check if any field contains the search term
            const nameMatch = name.includes(searchTerm);
            const addressMatch = address.includes(searchTerm);
            const principalMatch = principal.includes(searchTerm);

            const matches = nameMatch || addressMatch || principalMatch;

            // Show/hide the item based on match
            if (searchTerm === '') {
                item.style.display = 'block';
            } else {
                item.style.display = matches ? 'block' : 'none';
            }
        });

        // Update statistics
        updateSearchStats();
    });
});

function clearSearch() {
    document.getElementById('searchInput').value = '';
    document.querySelectorAll('.school-item').forEach(item => {
        item.style.display = 'block';
    });
    updateSearchStats();
}

function updateSearchStats() {
    const visibleItems = document.querySelectorAll('.school-item[style*="block"], .school-item:not([style*="none"])');
    const totalItems = document.querySelectorAll('.school-item').length;

    // Update search results counter if you want to show it
    console.log(`Showing ${visibleItems.length} of ${totalItems} schools`);
}
</script>
@endsection
