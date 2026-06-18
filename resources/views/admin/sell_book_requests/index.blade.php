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
                                            <th>Payout</th>
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
        
        <!-- Reject Reason Modal -->
        <div class="modal fade" id="rejectRequestModal" tabindex="-1" role="dialog" aria-hidden="true" style="font-family: 'Nunito', sans-serif;">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content" style="border-radius: 12px; border: none; box-shadow: 0 10px 40px rgba(0,0,0,0.1);">
                    <div class="modal-header border-0 pb-0 px-4 pt-4">
                        <h5 class="modal-title" style="font-weight: 800; color: #25396f; font-size: 1.2rem;"><i class="fas fa-exclamation-circle text-danger mr-2"></i> Reject Sell Request</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="outline: none;">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form id="list-reject-form" method="POST" action="">
                        @csrf
                        <input type="hidden" name="reason" id="list_hidden_reject_reason">
                        <div class="modal-body px-4 pb-4">
                            <p class="text-muted mb-4" style="font-size: 0.9rem;">Please specify why this book listing request is being rejected. The seller will receive this reason on WhatsApp.</p>
                            
                            <div class="form-group mb-3">
                                <label for="list_reject_reason_dropdown" class="font-weight-bold text-muted mb-1" style="font-size: 0.8rem; text-transform: uppercase;">Select Preset Reason:</label>
                                <select class="form-control" id="list_reject_reason_dropdown" style="border-radius: 8px; border: 1px solid #ced4da; height: calc(1.5em + .75rem + 2px);">
                                    <option value="">-- Choose predefined reason --</option>
                                    <option value="Area not serviceable">Area not serviceable</option>
                                    <option value="Incomplete/Poor quality book images">Incomplete/Poor quality book images</option>
                                    <option value="Damaged book condition">Damaged book condition</option>
                                    <option value="Invalid ISBN / Book details match">Invalid ISBN / Book details match</option>
                                    <option value="Duplicate book listing">Duplicate book listing</option>
                                    <option value="Other">Other (Enter custom reason below)</option>
                                </select>
                            </div>

                            <div class="form-group mb-0">
                                <label for="list_reject_reason_input" class="font-weight-bold text-muted mb-1" style="font-size: 0.8rem; text-transform: uppercase;">Custom / Detailed Reason:</label>
                                <textarea class="form-control" id="list_reject_reason_input" rows="3" style="border-radius: 8px; border: 1px solid #ced4da;" placeholder="Select a preset reason above or type here..."></textarea>
                            </div>
                        </div>
                        <div class="modal-footer border-0 px-4 pb-4 pt-0">
                            <button type="button" class="btn btn-light" data-dismiss="modal" style="border-radius: 8px; font-weight: 600; padding: 8px 18px;">Cancel</button>
                            <button type="submit" class="btn btn-danger" style="border-radius: 8px; font-weight: 600; padding: 8px 18px;">Confirm Reject & WhatsApp</button>
                        </div>
                    </form>
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
        function openListRejectModal(id) {
            // Set action URL on form
            $('#list-reject-form').attr('action', "{{ url('admin/sell-book-requests') }}/" + id + "/reject");
            
            // Reset form fields
            $('#list_reject_reason_dropdown').val('');
            $('#list_reject_reason_input').val('');
            $('#list_hidden_reject_reason').val('');
            
            // Open modal
            $('#rejectRequestModal').modal('show');
        }

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
                    {data: 'payout', name: 'payout', orderable: false, searchable: false},
                    {data: 'actions', name: 'actions', orderable: false, searchable: false},
                ],
                order: [[8, 'asc']] // Default sort by status
            });

            // Dropdown change handler
            $('#list_reject_reason_dropdown').on('change', function() {
                var val = $(this).val();
                if (val === 'Other') {
                    $('#list_reject_reason_input').val('').focus();
                } else if (val) {
                    $('#list_reject_reason_input').val(val);
                } else {
                    $('#list_reject_reason_input').val('');
                }
            });

            // Form submit handler
            $('#list-reject-form').on('submit', function(e) {
                var reason = $('#list_reject_reason_input').val().trim();
                if (!reason) {
                    e.preventDefault();
                    alert('Please select or specify a rejection reason.');
                    return false;
                }
                $('#list_hidden_reject_reason').val(reason);
            });
        });
    </script>
@endpush
