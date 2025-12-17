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
                                        <th>Email</th>
                                        <th>Subject</th>
                                        <th>Message</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($queries as $key => $query)
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td>{{ $query['name'] }}</td>
                                        <td>{{ $query['email'] }}</td>
                                        <td>{{ $query['subject'] }}</td>
                                        <td>{{ Str::limit($query['message'], 50) }}</td>
                                        <td>
                                            <select class="form-control updateStatus" data-query-id="{{ $query['id'] }}" style="width: auto; display: inline-block;">
                                                <option value="pending" {{ $query['status'] == 'pending' ? 'selected' : '' }}>Pending</option>
                                                <option value="in_progress" {{ $query['status'] == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                                <option value="resolved" {{ $query['status'] == 'resolved' ? 'selected' : '' }}>Resolved</option>
                                            </select>
                                        </td>
                                        <td>{{ date('Y-m-d H:i:s', strtotime($query['created_at'])) }}</td>
                                        <td>
                                            <a href="{{ url('admin/contact-queries/reply/'.$query['id']) }}" title="Reply">
                                                <i style="font-size:25px;" class="mdi mdi-reply"></i>
                                            </a>
                                            &nbsp;
                                            <a href="javascript:void(0)" class="confirmDelete" module="contact_query" moduleid="{{ $query['id'] }}" title="Delete">
                                                <i style="font-size:25px;" class="mdi mdi-file-excel-box"></i>
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
    @include('admin.layout.footer')
</div>

<script>
    $(document).ready(function() {
        $('#contact_queries').DataTable({
            "pageLength": 10,
            "lengthMenu": [5, 10, 25, 50, 100],
            "ordering": true,
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

