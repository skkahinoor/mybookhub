@extends('admin.layout.layout')





@section('content')
    <div class="main-panel">
        <div class="content-wrapper">
            <div class="row">
                <div class="col-lg-12 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">Publisher</h4>
                            @if ($adminType === 'vendor')
                                <a href="{{ route('vendor.add_edit_publisher') }}"
                                    style="max-width: 150px; float: right; display: inline-block"
                                    class="btn btn-block btn-primary"><i class="mdi mdi-plus"></i> Add Publisher</a>
                                <a href="{{ route('vendor.export.publishers') }}"
                                    style="max-width: 150px; float: right; display: inline-block; margin-right: 10px; margin-top: 0px;"
                                    class="btn btn-block btn-success"><i class="mdi mdi-file-excel"></i> Export</a>
                                <button type="button"
                                    style="max-width: 150px; float: right; display: inline-block; margin-right: 10px; margin-top: 0px;"
                                    class="btn btn-block btn-info" data-toggle="modal" data-target="#importModal"><i class="mdi mdi-file-import"></i> Import</button>
                            @else
                                <a href="{{ route('admin.add_edit_publisher') }}"
                                    style="max-width: 150px; float: right; display: inline-block"
                                    class="btn btn-block btn-primary"><i class="mdi mdi-plus"></i> Add Publisher</a>
                                <a href="{{ route('admin.export.publishers') }}"
                                    style="max-width: 150px; float: right; display: inline-block; margin-right: 10px; margin-top: 0px;"
                                    class="btn btn-block btn-success"><i class="mdi mdi-file-excel"></i> Export</a>
                                <button type="button"
                                    style="max-width: 150px; float: right; display: inline-block; margin-right: 10px; margin-top: 0px;"
                                    class="btn btn-block btn-info" data-toggle="modal" data-target="#importModal"><i class="mdi mdi-file-import"></i> Import</button>
                            @endif

                            <!-- Import Modal -->
                            <div class="modal fade" id="importModal" tabindex="-1" role="dialog" aria-labelledby="importModalLabel" aria-hidden="true">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="importModalLabel">Import Publishers</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <form action="{{ $adminType === 'vendor' ? route('vendor.import.publishers') : route('admin.import.publishers') }}" method="POST" enctype="multipart/form-data">
                                            @csrf
                                            <div class="modal-body">
                                                <div class="form-group">
                                                    <label for="file">Choose Excel/CSV File (Columns: name)</label>
                                                    <input type="file" name="file" class="form-control" required>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                                <button type="submit" class="btn btn-primary">Import</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            {{-- Displaying The Validation Errors: https://laravel.com/docs/9.x/validation#quick-displaying-the-validation-errors AND https://laravel.com/docs/9.x/blade#validation-errors --}}
                            {{-- Determining If An Item Exists In The Session (using has() method): https://laravel.com/docs/9.x/session#determining-if-an-item-exists-in-the-session --}}
                            {{-- Our Bootstrap success message in case of updating admin password is successful: --}}
                            @if (Session::has('success_message'))
                                <!-- Check AdminController.php, updateAdminPassword() method -->
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    <strong>Success:</strong> {{ Session::get('success_message') }}
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            @endif


                            <div class="table-responsive pt-3">
                                {{-- DataTable --}}
                                <table id="publisher" class="table table-bordered"> {{-- using the id here for the DataTable --}}
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Name</th>
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
        <!-- content-wrapper ends -->
        <!-- partial:../../partials/_footer.html -->
        <footer class="footer">
            <div class="d-sm-flex justify-content-center justify-content-sm-between">
                <span class="text-muted text-center text-sm-left d-block d-sm-inline-block">Copyright © 2022. All rights
                    reserved.</span>
            </div>
        </footer>
        <!-- partial -->
    </div>

    <!-- DataTables Bootstrap 4 CSS CDN -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap4.min.css">

    <!-- jQuery CDN (required for DataTables) -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <!-- DataTables JS CDN -->
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap4.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#publisher').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ url()->current() }}",
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                    {data: 'name', name: 'name'},
                    {data: 'status', name: 'status'},
                    {data: 'actions', name: 'actions', orderable: false, searchable: false},
                ]
            });
        });
    </script>
@endsection
