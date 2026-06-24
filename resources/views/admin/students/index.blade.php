@extends('admin.layout.layout')

@section('content')
<div class="container-fluid">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Custom CSS for enhanced styling -->
    <style>
        .stats-card {
            background-color: #253858; /* dark blue for generic stats */
            border-radius: 15px;
            border: none;
            box-shadow: 0 8px 24px rgba(30,35,90,0.12);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .stats-card.success {
            background-color: #25836e; /* teal for success */
        }
        .stats-card.warning {
            background-color: #333840; /* dark gray for warning */
        }
        .stats-card.info {
            background-color: #dbe9fa; /* pale blue for info */
            color: #1d3354;
        }
        .stats-card.danger {
            background-color: #b91c1c; /* red for danger */
        }

        .student-card {
            background: #fff;
            border-radius: 12px;
            border: 1px solid #e9ecef;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            transition: all 0.3s ease;
            margin-bottom: 20px;
        }
        .student-card:hover {
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
            transform: translateY(-2px);
        }
        .student-header {
            background-color: #253858; /* same dark blue */
            color: white;
            padding: 20px;
            border-radius: 12px 12px 0 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 8px;
        }
        .student-name {
            margin: 0;
            font-size: 1.25rem;
            font-weight: 600;
        }
        .student-roll {
            font-size: 0.9rem;
            opacity: 0.9;
            margin: 0;
            flex-shrink: 0;
            white-space: nowrap;
        }

        .btn-edit {
            background-color: #25836e;
            border: none;
            color: white;
            padding: 4px 8px;
            border-radius: 4px;
            text-decoration: none;
            font-size: 0.85rem;
            transition: all 0.3s ease;
        }
        .btn-edit:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(37, 131, 110, 0.28);
            color: white;
            text-decoration: none;
        }
        .btn-delete {
            background-color: #b91c1c;
            border: none;
            color: white;
            padding: 4px 8px;
            border-radius: 4px;
            text-decoration: none;
            font-size: 0.85rem;
            transition: all 0.3s ease;
        }
        .btn-delete:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(185, 28, 28, 0.25);
            color: white;
            text-decoration: none;
        }
        .add-student-btn {
            background-color: #25836e;
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
        .add-student-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 32px rgba(37, 131, 110, 0.14);
            color: white;
            text-decoration: none;
        }
        .institution-badge {
            background-color: #253858;
            color: white;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 0.75rem;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .no-institution {
            background: #636e72;
            color: white;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 0.75rem;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
    </style>

    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="page-title">Student Management</h2>
                    <p class="page-subtitle">Manage student records and information</p>
                </div>
                <div class="d-flex align-items-center">
                    <button type="button" class="btn btn-danger mr-2" id="bulk-delete-students-btn" style="display: none; padding: 12px 24px; border-radius: 8px; font-weight: 600; height: 50px; align-items: center; justify-content: center; gap: 8px;">
                        <i class="fas fa-trash-sweep"></i> Delete Selected (<span id="selected-students-count">0</span>)
                    </button>
                    <a href="{{ url('admin/students/create') }}" class="add-student-btn" style="margin-bottom: 0;">
                        <i class="fas fa-plus"></i>
                        Add New Student
                    </a>
                </div>
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
                            <i class="fas fa-user-graduate fa-2x mb-3" style="color: white;"></i>
                            <h4 class="mb-1" style="color: white;">{{ $students->count() }}</h4>
                            <p class="mb-0" style="color: white; opacity: 0.9;">Total Students</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="card stats-card warning">
                        <div class="card-body text-center">
                            <i class="fas fa-male fa-2x mb-3" style="color: white;"></i>
                            <h4 class="mb-1" style="color: white;">{{ $students->where('gender', 'male')->count() }}</h4>
                            <p class="mb-0" style="color: white; opacity: 0.9;">Male Students</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="card stats-card success">
                        <div class="card-body text-center">
                            <i class="fas fa-female fa-2x mb-3" style="color: white;"></i>
                            <h4 class="mb-1" style="color: white;">{{ $students->where('gender', 'female')->count() }}</h4>
                            <p class="mb-0" style="color: white; opacity: 0.9;">Female Students</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="card stats-card warning">
                        <div class="card-body text-center">
                            <i class="fas fa-school fa-2x mb-3" style="color: white;"></i>
                            <h4 class="mb-1" style="color: white;">{{ $students->whereNotNull('institution_id')->count() }}</h4>
                            <p class="mb-0" style="color: white; opacity: 0.9;">With Institution</p>
                        </div>
                    </div>
                </div>
            </div>

            @if($students->count() > 0)

            <div class="card mt-4">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-table"></i> Student List
                    </h5>
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table id="studentsTable" class="table table-bordered table-striped">
                            <thead class="thead-dark">
                                <tr>
                                    <th style="width: 40px; text-align: center;"><input type="checkbox" id="select-all-students" style="transform: scale(1.3); cursor: pointer;"></th>
                                    <th>#</th>
                                    <th>Name</th>
                                    <th>Mobile No</th>
                                    <th>Location</th>
                                    {{-- <th>Class</th> --}}
                                    <th>DOB</th>
                                    <th>Institution</th>
                                    {{-- <th>Type</th> --}}
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            @else
            <div class="empty-state text-center py-5">
                <i class="fas fa-user-graduate fa-4x mb-3"></i>
                <h4>No Students Found</h4>
                <p>Start by adding your first student to the system.</p>
                <a href="{{ url('admin/students/create') }}" class="add-student-btn">
                    <i class="fas fa-plus"></i> Add New Student
                </a>
            </div>
            @endif

        </div>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap4.min.js"></script>
 
<script>
    $(document).ready(function () {
        var table = $('#studentsTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ url()->current() }}",
            columns: [
                {data: 'checkbox', name: 'checkbox', orderable: false, searchable: false, class: 'text-center'},
                {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                {data: 'name', name: 'name'},
                {data: 'phone', name: 'phone'},
                {data: 'location', name: 'location', orderable: false, searchable: false},
                {data: 'dob', name: 'dob'},
                {data: 'institution', name: 'institution'},
                {data: 'status', name: 'status', orderable: false, searchable: false},
                {data: 'actions', name: 'actions', orderable: false, searchable: false},
            ],
            order: [[1, 'desc']],
            drawCallback: function() {
                $('#select-all-students').prop('checked', false);
                updateBulkDeleteButton();
            }
        });

        // Toggle select all
        $('#select-all-students').on('click', function() {
            var checked = this.checked;
            $('.select-student-checkbox').each(function() {
                this.checked = checked;
            });
            updateBulkDeleteButton();
        });

        // Individual checkbox click
        $(document).on('click', '.select-student-checkbox', function() {
            var allChecked = $('.select-student-checkbox').length === $('.select-student-checkbox:checked').length;
            $('#select-all-students').prop('checked', allChecked);
            updateBulkDeleteButton();
        });

        function updateBulkDeleteButton() {
            var checkedCount = $('.select-student-checkbox:checked').length;
            if (checkedCount > 0) {
                $('#selected-students-count').text(checkedCount);
                $('#bulk-delete-students-btn').css('display', 'inline-flex');
            } else {
                $('#bulk-delete-students-btn').css('display', 'none');
            }
        }

        // Bulk delete click handler
        $('#bulk-delete-students-btn').on('click', function() {
            var selectedIds = [];
            $('.select-student-checkbox:checked').each(function() {
                selectedIds.push($(this).val());
            });

            if (selectedIds.length > 0) {
                if (confirm("Are you sure you want to delete the " + selectedIds.length + " selected students? This action cannot be undone.")) {
                    $.ajax({
                        url: "{{ route('admin.students.bulkDelete') }}",
                        type: "POST",
                        data: {
                            ids: selectedIds,
                            _token: "{{ csrf_token() }}"
                        },
                        success: function(response) {
                            if (response.success) {
                                alert(response.message);
                                table.ajax.reload();
                            } else {
                                alert(response.message || "An error occurred while deleting.");
                            }
                        },
                        error: function() {
                            alert("Failed to perform bulk delete action.");
                        }
                    });
                }
            }
        });
    });
</script>
<script>
function confirmDelete(id) {
    if (confirm("Are you sure you want to delete this student? This action cannot be undone.")) {
        var form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ url("admin/students") }}/' + id;

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

<!-- Manage Wallet Modal -->
<div class="modal fade" id="creditWalletModal" tabindex="-1" role="dialog" aria-labelledby="creditWalletModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content shadow-lg border-0" style="border-radius: 16px;">
            <div class="modal-header bg-primary text-white" style="border-top-left-radius: 16px; border-top-right-radius: 16px;">
                <h5 class="modal-title font-weight-bold" id="creditWalletModalLabel">
                    <i class="fas fa-wallet mr-2"></i> Manage Student Wallet
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="creditWalletForm" method="POST" action="{{ route('admin.students.creditWallet') }}">
                @csrf
                <input type="hidden" name="user_id" id="credit_student_id">
                <div class="modal-body p-4">
                    <div class="form-group mb-3">
                        <label class="font-weight-bold text-muted small text-uppercase">Student Name</label>
                        <input type="text" class="form-control bg-light border-0" id="credit_student_name" readonly style="font-weight: 600; border-radius: 8px;">
                    </div>
                    <div class="form-group mb-3">
                        <label class="font-weight-bold text-muted small text-uppercase">Current Balance</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text bg-light border-0">₹</span>
                            </div>
                            <input type="text" class="form-control bg-light border-0" id="credit_student_balance" readonly style="font-weight: 600; border-radius: 8px;">
                        </div>
                    </div>
                    <div class="form-group mb-3">
                        <label for="transaction_type" class="font-weight-bold text-dark">Action Type <span class="text-danger">*</span></label>
                        <select class="form-control" id="transaction_type" name="transaction_type" style="border-radius: 8px; font-weight: 600;">
                            <option value="credit">Credit (+)</option>
                            <option value="debit">Debit (-)</option>
                        </select>
                    </div>
                    <div class="form-group mb-3">
                        <label for="credit_amount" class="font-weight-bold text-dark" id="amount_label">Amount to Credit (₹) <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="credit_amount" name="amount" min="0.01" step="0.01" required style="border-radius: 8px;" placeholder="e.g. 100">
                    </div>
                    <div class="form-group mb-3">
                        <label for="credit_description" class="font-weight-bold text-dark">Description / Remark</label>
                        <textarea class="form-control" id="credit_description" name="description" rows="2" style="border-radius: 8px;" placeholder="e.g. Manual credit by admin"></textarea>
                    </div>
                    <div class="form-group mb-3">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="send_notification" name="send_notification" value="1" checked>
                            <label class="custom-control-label font-weight-bold text-dark" for="send_notification" style="cursor: pointer;">Send Notification to Student</label>
                        </div>
                    </div>
                    <div class="form-group mb-0">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="show_in_history" name="show_in_history" value="1" checked>
                            <label class="custom-control-label font-weight-bold text-dark" for="show_in_history" style="cursor: pointer;">Display in Transaction History</label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light" style="border-bottom-left-radius: 16px; border-bottom-right-radius: 16px;">
                    <button type="button" class="btn btn-secondary font-weight-bold px-4" data-dismiss="modal" style="border-radius: 8px;">Cancel</button>
                    <button type="submit" id="submit_btn" class="btn btn-primary font-weight-bold px-4" style="border-radius: 8px; background-color: #253858; border-color: #253858;">Credit Wallet</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openCreditModal(id, name, balance) {
    $('#credit_student_id').val(id);
    $('#credit_student_name').val(name);
    $('#credit_student_balance').val(parseFloat(balance).toFixed(2));
    $('#credit_amount').val('');
    $('#transaction_type').val('credit');
    $('#amount_label').html('Amount to Credit (₹) <span class="text-danger">*</span>');
    $('#credit_description').val('Manual credit by admin');
    $('#send_notification').prop('checked', true);
    $('#show_in_history').prop('checked', true);
    $('#submit_btn').text('Credit Wallet').removeClass('btn-danger').addClass('btn-primary').css({'background-color': '#253858', 'border-color': '#253858'});
    $('#creditWalletModalLabel').html('<i class="fas fa-wallet mr-2"></i> Manage Student Wallet');
    $('#creditWalletModal').modal('show');
}

$(document).ready(function() {
    $('#transaction_type').on('change', function() {
        var val = $(this).val();
        if (val === 'credit') {
            $('#amount_label').html('Amount to Credit (₹) <span class="text-danger">*</span>');
            $('#credit_description').val('Manual credit by admin');
            $('#submit_btn').text('Credit Wallet').removeClass('btn-danger').addClass('btn-primary').css({'background-color': '#253858', 'border-color': '#253858'});
        } else {
            $('#amount_label').html('Amount to Debit (₹) <span class="text-danger">*</span>');
            $('#credit_description').val('Manual debit by admin');
            $('#submit_btn').text('Debit Wallet').removeClass('btn-primary').addClass('btn-danger').css({'background-color': '#b91c1c', 'border-color': '#b91c1c'});
        }
    });
});
</script>

@endsection
