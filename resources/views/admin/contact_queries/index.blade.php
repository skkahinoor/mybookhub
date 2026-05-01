@extends('admin.layout.layout')

@section('content')
<div class="main-panel">
    <div class="content-wrapper">
        <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Contact Us Queries</h4>

                        @if(Session::has('success_message'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <strong>Success:</strong> {{ Session::get('success_message') }}
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        @endif

                        @if(Session::has('error_message'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <strong>Error:</strong> {{ Session::get('error_message') }}
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        @endif

                        <div class="table-responsive pt-3">
                            <table class="table table-bordered" id="contact_queries">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Phone</th>
                                        <th>Email</th>
                                        <th>Subject</th>
                                        <th>Message</th>
                                        <th>Status</th>
                                        <th>Date</th>
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
    @include('admin.layout.footer')
</div>

<script>
    $(document).ready(function() {
        $('#contact_queries').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ url()->current() }}",
            columns: [
                {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                {data: 'name', name: 'name'},
                {data: 'phone', name: 'phone'},
                {data: 'email', name: 'email'},
                {data: 'subject', name: 'subject'},
                {data: 'message_trimmed', name: 'message'},
                {data: 'status_dropdown', name: 'status', orderable: false, searchable: false},
                {data: 'date', name: 'created_at'},
                {data: 'actions', name: 'actions', orderable: false, searchable: false},
            ],
            order: [[7, 'desc']]
        });

        // Update status via AJAX
        $(document).on('change', '.updateStatus', function() {
            var queryId = $(this).data('query-id');
            var status = $(this).val();

            $.ajax({
                url: "{{ url('admin/update-contact-status') }}",
                type: 'POST',
                data: {
                    query_id: queryId,
                    status: status,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    alert('Status updated successfully!');
                },
                error: function(xhr) {
                    alert('Error updating status. Please try again.');
                    location.reload();
                }
            });
        });
    });
</script>
@endsection

