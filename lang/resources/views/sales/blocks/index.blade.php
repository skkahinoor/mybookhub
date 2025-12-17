@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap4.min.css">

    <!-- Custom CSS -->
    <style>
        .page-header {
            background: #274472;
            color: #fff;
            padding: 32px 30px;
            border-radius: 12px;
            margin-bottom: 32px;
            box-shadow: 0 6px 22px rgba(39,68,114,0.10);
        }
        .page-title {
            font-size: 2rem;
            font-weight: 700;
            margin: 0 0 8px 0;
            letter-spacing: 0.02em;
        }
        .page-subtitle {
            font-size: 1rem;
            color: #dde3ec;
            margin: 0;
        }
        .btn-add {
            background: #25836e;
            color: #fff;
            border: none;
            border-radius: 8px;
            padding: 11px 28px;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.23s;
        }
        .btn-add:hover {
            background: #176276;
            color: #fff;
            text-decoration: none;
            box-shadow: 0 7px 26px rgba(37, 131, 110, 0.15);
        }
        .table-container {
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 4px 18px rgba(60,72,100,0.11);
            overflow: hidden;
        }
        .table {
            margin-bottom: 0;
        }
        .table thead th {
            background: #f8f9fa;
            border-bottom: 2px solid #e9ecef;
            font-weight: 600;
            color: #274472;
            padding: 16px 12px;
        }
        .table tbody td {
            padding: 16px 12px;
            vertical-align: middle;
            border-bottom: 1px solid #e9ecef;
        }
        .status-badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
        }
        .status-active {
            background: #d4edda;
            color: #155724;
        }
        .status-inactive {
            background: #f8d7da;
            color: #721c24;
        }
        .btn-action {
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 0.85rem;
            text-decoration: none;
            margin-right: 5px;
        }
        .btn-edit {
            background: #17a2b8;
            color: #fff;
        }
        .btn-edit:hover {
            background: #138496;
            color: #fff;
            text-decoration: none;
        }
        .btn-delete {
            background: #dc3545;
            color: #fff;
        }
        .btn-delete:hover {
            background: #c82333;
            color: #fff;
            text-decoration: none;
        }
    </style>

    <div class="row">
        <div class="col-12">
            <div class="page-header">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="page-title">
                            <i class="fas fa-cube"></i>
                            Blocks Management
                        </h1>
                        <p class="page-subtitle">Manage all blocks in your system</p>
                    </div>
                    <a href="{{ url('sales/blocks/create') }}" class="btn-add">
                        <i class="fas fa-plus"></i>
                        Add New Block
                    </a>
                </div>
            </div>
            @if(Session::has('success_message'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <strong>Success!</strong> {{ Session::get('success_message') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

            <div class="table-container">
                <div class="table-responsive">
                    <table id="blocksTable" class="table table-hover table-bordered">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Block Name</th>
                                <th>District</th>
                                <th>State</th>
                                <th>Country</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($blocks as $index => $block)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $block->name }}</td>
                                    <td>{{ $block->district->name ?? 'N/A' }}</td>
                                    <td>{{ $block->district->state->name ?? 'N/A' }}</td>
                                    <td>{{ $block->district->state->country->name ?? 'N/A' }}</td>
                                    <td>
                                        {{ $block->status ? 'Active' : 'Inactive' }}
                                    </td>
                                    <td>
                                        <a href="{{ route('sales.blocks.edit', $block->id) }}" class="btn-action btn-edit">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                        <a href="javascript:void(0)" class="btn-action btn-delete"
                                           onclick="confirmDelete({{ $block->id }}, '{{ $block->name }}')">
                                            <i class="fas fa-trash"></i> Delete
                                        </a>
                                        <form id="delete-block-{{ $block->id }}"
                                              action="{{ route('sales.blocks.destroy', $block->id) }}"
                                              method="POST" style="display:none;">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4">
                                        <i class="fas fa-cube fa-3x text-muted mb-3"></i>
                                        <p class="text-muted">No blocks found.
                                            <a href="{{ url('sales/blocks/create') }}">Add your first block</a>
                                        </p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap4.min.js"></script>

<script>
$(document).ready(function() {
    $('#blocksTable').DataTable({
        "pageLength": 10,
        "lengthMenu": [5, 10, 25, 50, 100],
        "ordering": true,
        "autoWidth": false,
        "columnDefs": [
            { "orderable": false, "targets": [6] } // Disable sorting for Actions
        ]
    });
});

function updateBlockStatus(blockId, status) {
    $.ajax({
        type: 'POST',
        url: '{{ url("sales/update-block-status") }}',
        data: {
            block_id: blockId,
            status: status,
            _token: '{{ csrf_token() }}'
        },
        success: function(response) {
            if (response.status == 0) {
                $('#block-' + blockId)
                    .removeClass('status-inactive')
                    .addClass('status-active')
                    .text('Active')
                    .attr('onclick', 'updateBlockStatus(' + blockId + ', "Active")');
            } else {
                $('#block-' + blockId)
                    .removeClass('status-active')
                    .addClass('status-inactive')
                    .text('Inactive')
                    .attr('onclick', 'updateBlockStatus(' + blockId + ', "Inactive")');
            }
        },
        error: function() {
            alert('Error updating block status');
        }
    });
}

function confirmDelete(blockId, blockName) {
    if (confirm('Are you sure you want to delete the block "' + blockName + '"? This action cannot be undone.')) {
        document.getElementById('delete-block-' + blockId).submit();
    }
}
</script>

@endsection
