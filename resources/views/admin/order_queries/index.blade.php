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
                                    @foreach($queries as $query)
                                    <tr>
                                        <td><strong>{{ $query->ticket_id }}</strong></td>
                                        <td>{{ $query->created_at->format('d-m-Y') }}</td>
                                        <td>
                                            {{ $query->user->name }}<br>
                                            <small>{{ $query->user->email }}</small>
                                        </td>
                                        <td>#{{ $query->order_id }}</td>
                                        <td>{{ $query->orderProduct->product_name ?? 'N/A' }}</td>
                                        <td>{{ $query->subject }}</td>
                                        <td>
                                            <select class="form-control updateQueryStatus" data-query-id="{{ $query->id }}" style="height: 35px; padding: 2px 10px;">
                                                <option value="pending" @if($query->status == 'pending') selected @endif text-warning>Pending</option>
                                                <option value="ongoing" @if($query->status == 'ongoing') selected @endif text-info>Ongoing</option>
                                                <option value="resolved" @if($query->status == 'resolved') selected @endif text-success>Resolved</option>
                                                <option value="closed" @if($query->status == 'closed') selected @endif text-secondary>Closed</option>
                                            </select>
                                        </td>
                                        <td>
                                            <a href="{{ url('admin/order-query/reply/'.$query->id) }}" title="Reply/View Detail">
                                                <i class="mdi mdi-reply" style="font-size: 25px;"></i>
                                            </a>
                                            &nbsp;&nbsp;
                                            <a href="javascript:void(0)" class="confirmDelete" module="order-query" moduleid="{{ $query->id }}" title="Delete Query">
                                                <i class="mdi mdi-delete" style="font-size: 25px;"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
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
    $(".updateQueryStatus").change(function(){
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
