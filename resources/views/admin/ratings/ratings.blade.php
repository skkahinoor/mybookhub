{{-- This view is rendered by ratings() method in Admin/RatingController.php --}}
@extends('admin.layout.layout')


@section('content')
    <div class="main-panel">
        <div class="content-wrapper">
            <div class="row">
                <div class="col-lg-12 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">Ratings</h4>

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
                                <table id="ratings" class="table table-bordered"> {{-- using the id here for the DataTable --}}
                                    <thead>
                                        <tr>
                                            <th>Sl No.</th>
                                            <th>Product Name</th>
                                            <th>User Email</th>
                                            <th>Review</th>
                                            <th>Rating</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($ratings as $rating)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>
                                                    @if (!empty($rating['product_attribute']))
                                                        <a target="_blank"
                                                            href="{{ url('product/' . $rating['product_attribute_id']) }}">
                                                            {{ $rating['product_attribute']['product']['product_name'] ?? 'View Product' }}
                                                        </a>
                                                    @else
                                                        <span class="text-danger">Product attribute not found</span>
                                                    @endif
                                                </td>


                                                <td>{{ $rating['user']['email'] }}</td>
                                                <td>{{ $rating['review'] }}</td>
                                                <td>{{ $rating['rating'] }}</td>
                                                <td>
                                                    @if ($adminType === 'vendor')
                                                        <a class="updateRatingStatus" id="rating-{{ $rating['id'] }}"
                                                            rating_id="{{ $rating['id'] }}"
                                                            data-url="{{ route('vendor.updateratingstatus') }}"
                                                            href="javascript:void(0)">
                                                            <i style="font-size: 25px" class="mdi mdi-bookmark-check"
                                                                status="Active"></i>
                                                        </a>
                                                    @else
                                                        @if ($rating['status'] == 1)
                                                            <a class="updateRatingStatus" id="rating-{{ $rating['id'] }}"
                                                                rating_id="{{ $rating['id'] }}"
                                                                data-url="{{ route('admin.updateratingstatus') }}"
                                                                href="javascript:void(0)"> {{-- Using HTML Custom Attributes. Check admin/js/custom.js --}}
                                                                <i style="font-size: 25px" class="mdi mdi-bookmark-check"
                                                                    status="Active"></i> {{-- Icons from Skydash Admin Panel Template --}}
                                                            </a>
                                                        @else
                                                            {{-- if the admin status is inactive --}}
                                                            <a class="updateRatingStatus" id="rating-{{ $rating['id'] }}"
                                                                rating_id="{{ $rating['id'] }}"
                                                                data-url="{{ route('admin.updateratingstatus') }}"
                                                                href="javascript:void(0)"> {{-- Using HTML Custom Attributes. Check admin/js/custom.js --}}
                                                                <i style="font-size: 25px" class="mdi mdi-bookmark-outline"
                                                                    status="Inactive"></i> {{-- Icons from Skydash Admin Panel Template --}}
                                                            </a>
                                                        @endif
                                                    @endif
                                                </td>
                                                <td>
                                                    <a href="javascript:void(0)" class="confirmDelete" data-module="rating"
                                                        data-url="{{ route('admin.deleteRating', $rating['id']) }}">
                                                        <i style="font-size:25px" class="mdi mdi-file-excel-box"></i>
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
