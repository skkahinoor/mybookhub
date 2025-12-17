@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex align-items-center justify-content-between mb-3">
        <h4 class="mb-0">Institutions</h4>
        <a href="{{ route('sales.institution_managements.create') }}" class="btn btn-primary">Add Institution</a>
    </div>

    @if(Session::has('success_message'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <strong>Success!</strong> {{ Session::get('success_message') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive p-3">
                <table id="institutionTable" class="table table-striped table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Type</th>
                            <th>Board</th>
                            <th>Principal Name</th>
                            <th>Contact</th>
                            <th>Pincode</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($institutions as $institution)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $institution->name }}</td>
                                <td>{{ $institution->type }}</td>
                                <td>{{ $institution->board }}</td>
                                <td>{{ $institution->principal_name }}</td>
                                <td>{{ $institution->contact_number }}</td>
                                <td>{{ $institution->pincode }}</td>
                                 <td>@if($institution->status == 1)
                                        <span class="badge bg-success">Approved</span>
                                    @else
                                        <span class="badge bg-warning text-dark">Pending</span>
                                    @endif
                                    </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('sales.institution_managements.show', $institution->id) }}"
                                            class="btn btn-sm btn-info" title="View">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('sales.institution_managements.edit', $institution->id) }}"
                                            class="btn btn-sm btn-primary" title="Edit">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4">No institutions found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                <div class="d-flex justify-content-center mt-3">
                    {{ $institutions->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap 5 DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>

<script>
$(document).ready(function() {
    $('#institutionTable').DataTable({
        "pageLength": 10,
        "lengthMenu": [5, 10, 20, 30, 50, 100],
        "ordering": true,
        "columnDefs": [
            { "orderable": false, "targets": [6] } // Disable sorting for the Action column
        ]
    });
    // function viewInstitution(id) {
    // $.ajax({
    //     url: `/sales/institution_managements/${id}`,
    //     type: 'GET',
    //     success: function(data) {
    //         $('#instName').text(data.name);
    //         $('#instType').text(data.type);
    //         $('#instBoard').text(data.board);
    //         $('#instContact').text(data.contact_number);
    //         $('#instPincode').text(data.pincode);
    //         $('#institutionModal').modal('show');
    //     },
    //     error: function(xhr) {
    //         console.error(xhr.responseText);
    //         alert('Failed to fetch institution details. Check console for details.');
    //     }
    // });
});
</script>
@endsection
