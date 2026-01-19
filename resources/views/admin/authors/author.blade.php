@extends('admin.layout.layout')


@section('content')
    <div class="main-panel">
        <div class="content-wrapper">
            <div class="row">
                <div class="col-lg-12 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">Author</h4>
                            @if ($adminType === 'vendor')
                                <a href="{{ route('vendor.add.author') }}"
                                    style="max-width: 150px; float: right; display: inline-block"
                                    class="btn btn-block btn-primary"><i class="mdi mdi-plus"></i> Add Author</a>
                            @else
                                <a href="{{ route('admin.add.author') }}"
                                    style="max-width: 150px; float: right; display: inline-block"
                                    class="btn btn-block btn-primary"><i class="mdi mdi-plus"></i> Add Author</a>
                            @endif

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
                                <table id="author" class="table table-bordered"> {{-- using the id here for the DataTable --}}
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Name</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($authors as $key => $author)
                                            <tr>
                                                <td>{{ __($key + 1) }}</td>
                                                <td>{{ $author['name'] }}</td>
                                                <td>
                                                    @if ($adminType === 'vendor')
                                                        <a class="updateAuthorStatus" id="author-{{ $author['id'] }}"
                                                            author_id="{{ $author['id'] }}"
                                                            data-url="{{ route('vendor.updateauthorstatus') }}"
                                                            href="javascript:void(0)">
                                                            <i style="font-size: 25px" class="mdi mdi-bookmark-check"
                                                                status="Active"></i>
                                                        </a>
                                                    @else
                                                        @if ($author['status'] == 1)
                                                            <a class="updateAuthorStatus" id="author-{{ $author['id'] }}"
                                                                author_id="{{ $author['id'] }}"
                                                                data-url="{{ route('admin.updateauthorstatus') }}"
                                                                href="javascript:void(0)">
                                                                {{-- Using HTML Custom Attributes. Check admin/js/custom.js --}}
                                                                <i style="font-size: 25px" class="mdi mdi-bookmark-check"
                                                                    status="Active"></i> {{-- Icons from Skydash Admin Panel Template --}}
                                                            </a>
                                                        @else
                                                            {{-- if the admin status is inactive --}}
                                                            <a class="updateAuthorStatus" id="author-{{ $author['id'] }}"
                                                                author_id="{{ $author['id'] }}"
                                                                data-url="{{ route('admin.updateauthorstatus') }}"
                                                                href="javascript:void(0)">
                                                                {{-- Using HTML Custom Attributes. Check admin/js/custom.js --}}
                                                                <i style="font-size: 25px" class="mdi mdi-bookmark-outline"
                                                                    status="Inactive"></i> {{-- Icons from Skydash Admin Panel Template --}}
                                                            </a>
                                                        @endif
                                                    @endif
                                                </td>
                                                <td>

                                                    @if ($adminType === 'vendor')
                                                        <a href="{{ route('vendor.edit.author', $author->id) }}">
                                                            <i style="font-size: 25px" class="mdi mdi-pencil-box"></i>
                                                            {{-- Icons from Skydash Admin Panel Template --}}
                                                        </a>

                                                        {{-- Confirm Deletion JS alert and Sweet Alert  --}}
                                                        <a title="author" class="confirmDelete"
                                                            href="{{ route('vendor.delete.author', $author->id) }}">
                                                            <i style="font-size: 25px" class="mdi mdi-file-excel-box"></i>
                                                            {{-- Icons from Skydash Admin Panel Template --}}
                                                        </a>
                                                        {{-- <a href="JavaScript:void(0)" class="confirmDelete" module="author" moduleid="{{ $subject['id'] }}">
                                                        <i style="font-size: 25px" class="mdi mdi-file-excel-box"></i> Icons from Skydash Admin Panel Template
                                                    </a> --}}
                                                </td>
                                            @else
                                                <a href="{{ route('admin.edit.author', $author->id) }}">
                                                    <i style="font-size: 25px" class="mdi mdi-pencil-box"></i>
                                                    {{-- Icons from Skydash Admin Panel Template --}}
                                                </a>

                                                {{-- Confirm Deletion JS alert and Sweet Alert  --}}
                                                <a title="author" class="confirmDelete"
                                                    href="{{ route('admin.delete.author', $author->id) }}">
                                                    <i style="font-size: 25px" class="mdi mdi-file-excel-box"></i>
                                                    {{-- Icons from Skydash Admin Panel Template --}}
                                                </a>
                                                {{-- <a href="JavaScript:void(0)" class="confirmDelete" module="author" moduleid="{{ $subject['id'] }}">
                                                    <i style="font-size: 25px" class="mdi mdi-file-excel-box"></i> Icons from Skydash Admin Panel Template
                                                </a> --}}
                                                </td>
                                        @endif
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
@endsection

@push('scripts')
    <!-- DataTables Bootstrap 4 CSS CDN -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap4.min.css">

    <!-- DataTables JS CDN (jQuery is already loaded in layout) -->
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap4.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#author').DataTable();
        });
    </script>
@endpush
