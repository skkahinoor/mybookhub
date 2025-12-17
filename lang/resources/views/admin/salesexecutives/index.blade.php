@extends('admin.layout.layout')

@section('content')
    <div class="main-panel">
        <div class="content-wrapper">
            <div class="row">
                <div class="col-lg-12 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h4 class="card-title mb-0">{{ $title }}</h4>
                                <a href="{{ url('admin/add-edit-sales-executive') }}" class="btn btn-primary">
                                    <i class="mdi mdi-plus"></i> Add Sales Executive
                                </a>
                            </div>

                            @if (Session::has('success_message'))
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    <strong>Success:</strong> {{ Session::get('success_message') }}
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            @endif

                            <div class="table-responsive pt-3">
                                <table class="table table-bordered" id="sales-table">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Name</th>
                                            <th>Email</th>
                                            <th>Phone</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($salesExecutives as $se)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $se->name }}</td>
                                                <td>{{ $se->email }}</td>
                                                <td>{{ $se->phone }}</td>
                                                <td>
                                                    <div class="d-flex align-items-center" style="gap: 10px;">
                                                        <a href="{{ url('admin/add-edit-sales-executive/' . $se->id) }}" title="Edit">
                                                            <i style="font-size: 20px" class="mdi mdi-pencil"></i>
                                                        </a>
                                                        <a href="{{ url('admin/delete-sales-executive/' . $se->id) }}" title="Delete" onclick="return confirm('Delete this sales executive?');">
                                                            <i style="font-size: 20px; color: #e74c3c;" class="mdi mdi-delete"></i>
                                                        </a>
                                                    </div>
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
        <footer class="footer">
            <div class="d-sm-flex justify-content-center justify-content-sm-between">
                <span class="text-muted text-center text-sm-left d-block d-sm-inline-block">Copyright © 2022. All rights reserved.</span>
            </div>
        </footer>
    </div>

    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap4.min.css">
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap4.min.js"></script>
    <script>
        $(function(){
            $('#sales-table').DataTable({
                pageLength: 10,
                lengthMenu: [5,10,25,50,100],
                ordering: true,
                columnDefs: [ { orderable: false, targets: [4] } ]
            });
        });
    </script>
@endsection


