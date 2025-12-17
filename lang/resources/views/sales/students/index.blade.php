@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="page-title mb-0">Student Management</h2>
            <p class="text-muted mb-0">Manage student records and information</p>
        </div>
        <a href="{{ url('sales/students/create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Add New Student
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
                <table id="studentsTable" class="table table-striped table-bordered align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Roll No</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Gender</th>
                            <th>Class</th>
                            <th>DOB</th>
                            <th>Institution</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($students as $index => $student)
                            <tr data-student-id="{{ $student->id }}">
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $student->name ?? 'N/A' }}</td>
                                <td>{{ $student->roll_number ?? 'N/A' }}</td>
                                <td>{{ $student->email ?? 'N/A' }}</td>
                                <td>{{ $student->phone ?? 'N/A' }}</td>
                                <td>{{ $student->gender ? ucfirst($student->gender) : 'N/A' }}</td>
                                <td>{{ $student->class ?? 'N/A' }}</td>
                                <td>{{ $student->dob ? \Carbon\Carbon::parse($student->dob)->format('d M Y') : 'N/A' }}</td>
                                <td>{{ $student->institution?->name ?? 'No Institution' }}</td>
                                <td>
                                    <a href="{{ url('sales/students/'.$student->id.'/edit') }}" class="btn btn-sm btn-success">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button class="btn btn-sm btn-danger" onclick="confirmDelete({{ $student->id }})">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center py-4">No students found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- jQuery + DataTables JS -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>

<script>
(function() {
    // Wait until DOM ready
    $(function() {

        // Step 1: Validate header column count
        const $table = $('#studentsTable');
        const headerCount = $table.find('thead tr').first().children('th').length;

        // Step 2: Inspect each tbody row and fix if needed
        let problemRows = [];
        $table.find('tbody tr').each(function(rowIndex) {
            const $tr = $(this);
            const tdCount = $tr.children('td').length;

            if (tdCount < headerCount) {
                // Pad the row with empty <td> so DataTables sees consistent columns
                const missing = headerCount - tdCount;
                for (let i = 0; i < missing; i++) {
                    $tr.append('<td></td>');
                }
                problemRows.push({
                    row: rowIndex,
                    reason: 'missing_cells',
                    before: tdCount,
                    after: headerCount,
                    studentId: $tr.data('student-id') || null
                });
            } else if (tdCount > headerCount) {
                // Log rows with extra cells — do not remove automatically
                problemRows.push({
                    row: rowIndex,
                    reason: 'extra_cells',
                    before: tdCount,
                    after: headerCount,
                    studentId: $tr.data('student-id') || null
                });
            }
        });

        // If any problems were found, print details to console so you can inspect server data
        if (problemRows.length > 0) {
            console.warn('DataTables: Found table row/column mismatches. Padded missing cells for initialization. Details:', problemRows);
            console.warn('Look at the HTML for the listed rows (studentId) and check for null/invalid values or malformed HTML.');
        }

        // Step 3: Initialize DataTable after ensuring consistent column counts
        $table.DataTable({
            pageLength: 10,
            lengthChange: true,
            searching: true,
            ordering: true,
            info: true,
            responsive: true,
            language: {
                search: "_INPUT_",
                searchPlaceholder: "Search students..."
            }
        });
    });
})();

// Delete helper (unchanged)
function confirmDelete(id) {
    if (confirm("Are you sure you want to delete this student? This action cannot be undone.")) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ url("sales/students") }}/' + id;

        const csrf = document.createElement('input');
        csrf.type = 'hidden';
        csrf.name = '_token';
        csrf.value = '{{ csrf_token() }}';
        form.appendChild(csrf);

        const method = document.createElement('input');
        method.type = 'hidden';
        method.name = '_method';
        method.value = 'DELETE';
        form.appendChild(method);

        document.body.appendChild(form);
        form.submit();
    }
}
</script>
@endsection
