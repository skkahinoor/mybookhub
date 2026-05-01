@extends('admin.layout.layout')


@section('content')
    <div class="main-panel">
        <div class="content-wrapper">
            <div class="row">
                <div class="col-lg-12 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">Sections</h4>




                            <a href="{{ url('admin/add-edit-type') }}"
                                style="max-width: 150px; float: right; display: inline-block"
                                class="btn btn-block btn-primary"><i class="mdi mdi-plus"></i> Add Type</a>

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
                                <table id="sections" class="table table-bordered"> {{-- using the id here for the DataTable --}}
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Icon</th>
                                            <th>Type</th>
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
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $('#sections').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ url('admin/types') }}",
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                    {data: 'icon', name: 'icon', orderable: false, searchable: false},
                    {data: 'book_type', name: 'book_type'},
                    {data: 'status', name: 'status', orderable: false, searchable: false},
                    {data: 'actions', name: 'actions', orderable: false, searchable: false},
                ],
                order: [[0, 'desc']]
            });

            $(document).on("click", ".updateTypeStatus", function() {

                var status = $(this).find("i").attr("status");
                var type_id = $(this).attr("type_id");

                $.ajax({
                    type: 'POST',
                    url: "{{ route('admin.updatetypestatus') }}",
                    data: {
                        status: status,
                        type_id: type_id,
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(resp) {

                        if (resp.status == 0) {
                            $("#type-" + type_id).html(
                                "<i class='mdi mdi-bookmark-outline' style='font-size:25px' status='Inactive'></i>"
                            );
                        } else {
                            $("#type-" + type_id).html(
                                "<i class='mdi mdi-bookmark-check' style='font-size:25px' status='Active'></i>"
                            );
                        }

                    }
                });

            });

        });
    </script>
@endpush
