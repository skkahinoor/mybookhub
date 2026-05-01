@extends('admin.layout.layout')
@section('content')
    <div class="main-panel">
        <div class="content-wrapper">
            <div class="row">
                <div class="col-lg-12 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">Sell Old Book Requests</h4>
                            <p class="card-description">Review and approve old book listings submitted by students and vendors.</p>

                            @if (Session::has('success_message'))
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    <strong>Success:</strong> {{ Session::get('success_message') }}
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>

                            @endif

                            <div class="table-responsive pt-3">
                                <table id="sell_requests" class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Type</th>
                                            <th>Seller Name</th>
                                            <th>Book Name</th>
                                            <th>ISBN</th>
                                            <th>Book Condition</th>
                                            <th>Selling Price</th>
                                            <th>Location</th>
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
        <footer class="footer">
            <div class="d-sm-flex justify-content-center justify-content-sm-between">
                <span class="text-muted text-center text-sm-left d-block d-sm-inline-block">Copyright &copy; 2024. All rights reserved.</span>
            </div>
        </footer>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $('#sell_requests').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ url()->current() }}",
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                    {data: 'type', name: 'admin_type'},
                    {data: 'seller_name', name: 'seller_name', orderable: false, searchable: false},
                    {data: 'book_name', name: 'product.product_name'},
                    {data: 'isbn', name: 'product.product_isbn'},
                    {data: 'book_condition', name: 'condition.name', orderable: false},
                    {data: 'selling_price', name: 'price', orderable: false, searchable: false},
                    {data: 'location', name: 'user_location_name'},
                    {data: 'status', name: 'admin_approved'},
                    {data: 'actions', name: 'actions', orderable: false, searchable: false},
                ],
                order: [[8, 'asc']] // Default sort by status
            });
        });
    </script>
@endpush
