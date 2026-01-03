@extends('layouts.app')

@section('title')
    Vendor Management
@endsection

@section('content')
<div class="container-fluid py-4">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="page-title mb-0">Vendor Management</h2>
            <p class="text-muted mb-0">Review vendors added by sales team</p>
        </div>
        <a href="{{ route('sales.vendors.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Add New Vendor
        </a>
    </div>

    @if(Session::has('success_message'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <strong>Success!</strong> {{ Session::get('success_message') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table id="vendorsTable" class="table table-striped table-bordered align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Mobile</th>
                            <th>Status</th>
                            {{-- <th>Confirmed</th> --}}
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($vendors as $index => $vendor)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $vendor->name }}</td>
                                <td>{{ $vendor->email }}</td>
                                <td>{{ $vendor->mobile }}</td>
                                <td>
                                    @if($vendor->status)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-warning text-dark">Pending</span>
                                    @endif
                                </td>
                                {{-- <td>
                                    @if($vendor->confirm === 'Yes')
                                        <span class="badge bg-info text-dark">Yes</span>
                                    @else
                                        <span class="badge bg-secondary">No</span>
                                    @endif
                                </td> --}}
                                <td>
                                    <a href="{{ route('sales.vendors.show', $vendor) }}" class="btn btn-sm btn-primary">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @php
                                        $adminStatus = $adminStatuses[$vendor->id] ?? 0;
                                    @endphp
                                    @if(!($vendor->status == 1 && $adminStatus == 1))
                                        <form action="{{ route('sales.vendors.destroy', $vendor) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Delete this vendor? This will remove the linked admin account too.');">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4">No vendors found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
<script>
    (function() {
        $(function() {
            $('#vendorsTable').DataTable({
                pageLength: 10,
                ordering: true,
                searching: true,
                lengthChange: true,
                responsive: true,
                language: {
                    search: "_INPUT_",
                    searchPlaceholder: "Search vendors..."
                }
            });
        });
    })();
</script>
@endsection

