@include('user.layout.header')

<div class="container-fluid page-body-wrapper">
    @include('user.layout.sidebar')

    <div class="main-panel">
        <div class="content-wrapper">
            <div class="row">
                <div class="col-md-12 grid-margin stretch-card">
                    <div class="card shadow-sm border-0" style="border-radius: 12px; overflow: hidden;">
                        <div class="card-body p-4">
                            <h4 class="card-title text-primary mb-4" style="font-weight: 700;">
                                <i class="fas fa-question-circle mr-2"></i> My Order Queries
                            </h4>

                            @if (Session::has('success_message'))
                                <div class="alert alert-success alert-dismissible fade show" role="alert" style="border-radius: 8px;">
                                    <strong>Success:</strong> {{ Session::get('success_message') }}
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            @endif

                            <div class="table-responsive">
                                <table id="queries_table" class="table table-hover table-bordered" style="width:100%">
                                    <thead class="bg-light">
                                        <tr>
                                            <th>S.No</th>
                                            <th>Ticket ID</th>
                                            <th>Date</th>
                                            <th>Order ID</th>
                                            <th>Product</th>
                                            <th>Subject</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- View Query Detail Modal -->
<div class="modal fade" id="viewQueryModal" tabindex="-1" role="dialog" aria-labelledby="viewQueryModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 12px;">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="viewQueryModalLabel">Query Details</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-4">
                <div class="mb-3">
                    <label class="font-weight-bold text-muted small text-uppercase">Your Message:</label>
                    <div id="display_message" class="p-3 bg-light rounded" style="white-space: pre-wrap; line-height: 1.6;"></div>
                </div>
                <div class="mb-0">
                    <label class="font-weight-bold text-muted small text-uppercase">Admin Reply:</label>
                    <div id="display_reply" class="p-3 rounded border" style="white-space: pre-wrap; min-height: 50px; background-color: #f0f7ff; line-height: 1.6;">
                        <span class="text-muted italic">Waiting for response...</span>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

@include('user.layout.footer')

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<!-- DataTables -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap4.min.css">
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap4.min.js"></script>

<script>
$(document).ready(function() {
    var table = $('#queries_table').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('student.orders.queries') }}",
        columns: [
            {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
            {data: 'date', name: 'date'},
            {data: 'order_id', name: 'order_id'},
            {data: 'product_name', name: 'product_name'},
            {data: 'subject', name: 'subject'},
            {data: 'status', name: 'status'},
            {data: 'action', name: 'action', orderable: false, searchable: false}
        ],
        order: [[1, 'desc']],
        language: {
            paginate: {
                previous: "<i class='fas fa-chevron-left'></i>",
                next: "<i class='fas fa-chevron-right'></i>"
            }
        }
    });

    $(document).on('click', '.view-query', function() {
        const message = $(this).data('message');
        const reply = $(this).data('reply');

        $('#display_message').text(message);
        if (reply && reply !== 'null' && reply !== '') {
            $('#display_reply').html(reply).css('background-color', '#f0f7ff');
        } else {
            $('#display_reply').html('<span class="text-muted italic">No reply yet. Our team is looking into it.</span>').css('background-color', '#fff9f0');
        }
        
        $('#viewQueryModal').modal('show');
    });
});
</script>

<style>
.badge {
    padding: 6px 12px;
    border-radius: 4px;
    font-weight: 500;
}
.italic { font-style: italic; }
.card-title i { color: #6366f1; }
</style>
