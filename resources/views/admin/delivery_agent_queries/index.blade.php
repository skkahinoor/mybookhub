@extends('admin.layout.layout')

@section('content')
    <div class="main-panel">
        <div class="content-wrapper">
            <div class="row">
                <div class="col-lg-12 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">{{ $title }}</h4>

                            @if (Session::has('success_message'))
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    <strong>Success:</strong> {{ Session::get('success_message') }}
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            @endif

                            <div class="table-responsive pt-3">
                                <table class="table table-bordered" id="queries-table">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Agent Name</th>
                                            <th>Subject</th>
                                            <th>Status</th>
                                            <th>Created At</th>
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

    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap4.min.css">
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap4.min.js"></script>
    
    <script>
        $(function() {
            $('#queries-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('admin.delivery_agent_queries.index') }}",
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                    {data: 'agent_name', name: 'user.name'},
                    {data: 'subject', name: 'subject'},
                    {data: 'status', name: 'status'},
                    {
                        data: 'created_at', 
                        name: 'created_at',
                        render: function(data, type, row) {
                            if(type === 'display' && data) {
                                var date = new Date(data);
                                return date.toLocaleDateString() + ' ' + date.toLocaleTimeString();
                            }
                            return data;
                        }
                    },
                    {data: 'actions', name: 'actions', orderable: false, searchable: false},
                ],
                order: [[4, 'desc']]
            });
        });
    </script>
@endsection
