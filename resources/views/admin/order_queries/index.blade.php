@extends('admin.layout.layout')
@section('content')
<div class="main-panel">
    <div class="content-wrapper">
        <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Order Queries (Tickets)</h4>
                        <p class="card-description">
                            Manage customer queries regarding delivered orders.
                        </p>
                        
                        <div class="table-responsive pt-3">
                            <table id="order_queries" class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Ticket ID</th>
                                        <th>Date</th>
                                        <th>User</th>
                                        <th>Order ID</th>
                                        <th>Product</th>
                                        <th>Subject</th>
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
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function(){
    $('#order_queries').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ url()->current() }}",
        columns: [
            {data: 'ticket_id', name: 'ticket_id'},
            {data: 'date', name: 'created_at'},
            {data: 'user_info', name: 'user.name'},
            {data: 'order_id_formatted', name: 'order_id'},
            {data: 'product_name', name: 'orderProduct.product_name'},
            {data: 'subject', name: 'subject'},
            {data: 'status_dropdown', name: 'status', orderable: false, searchable: false},
            {data: 'actions', name: 'actions', orderable: false, searchable: false},
        ],
        order: [[1, 'desc']]
    });

    $(document).on("change", ".updateQueryStatus", function(){
        var status = $(this).val();
        var query_id = $(this).attr("data-query-id");
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            type:'post',
            url:'{{ route("admin.order_queries.update_status") }}',
            data:{status:status, query_id:query_id},
            success:function(resp){
                if(resp['status']){
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: resp['message']
                    });
                }
            },error:function(){
                alert("Error");
            }
        });
    });

    // Confirm Delete (using existing confirmDelete logic if available in custom.js, otherwise simple alert)
    $(document).on("click", ".confirmDelete", function(){
        var module = $(this).attr("module");
        var moduleid = $(this).attr("moduleid");
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = "{{ url('admin/delete-order-query') }}/"+moduleid;
            }
        });
    });
});
</script>
@endpush
