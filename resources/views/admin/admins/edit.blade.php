@extends('admin.layout.layout')

@section('content')
    <div class="main-panel">
        <div class="content-wrapper">
            <div class="row">
                <div class="col-md-12 grid-margin">
                    <div class="row">
                        <div class="col-12 col-xl-8 mb-4 mb-xl-0">
                             <h3 class="font-weight-bold">{{ isset($admin['type']) && $admin['type'] == 'vendor' ? 'Edit Vendor Details' : 'Edit Admin' }}</h3>
                            <h6 class="font-weight-normal mb-0"><a href="{{ url('admin/admins') }}">Back to Admins</a></h6>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                             <h4 class="card-title">{{ isset($admin['type']) && $admin['type'] == 'vendor' ? 'Personal Information' : 'Admin Information' }}</h4>

                            {{-- Error Messages --}}
                            @if (Session::has('error_message'))
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <strong>Error:</strong> {{ Session::get('error_message') }}
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            @endif

                            @if ($errors->any())
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            @endif

                            @if (Session::has('success_message'))
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    <strong>Success:</strong> {{ Session::get('success_message') }}
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            @endif

                            <form method="POST" action="{{ url('admin/add-edit-admin') }}" enctype="multipart/form-data">
                                @csrf
                                 <input type="hidden" name="admin_id" value="{{ $admin['id'] ?? '' }}">

                                <div class="form-group">
                                    <label>Name <span class="text-danger">*</span></label>
                                     <input type="text" name="name" class="form-control" value="{{ $admin['name'] ?? '' }}" required>
                                </div>

                                <div class="form-group">
                                    <label>Email <span class="text-danger">*</span></label>
                                     <input type="email" name="email" class="form-control" value="{{ $admin['email'] ?? '' }}" required>
                                </div>

                                <div class="form-group">
                                    <label>Mobile <span class="text-danger">*</span></label>
                                     <input type="text" name="mobile" class="form-control" value="{{ $admin['mobile'] ?? '' }}" required>
                                </div>

                                {{-- <div class="form-group">
                                    <label>Type <span class="text-danger">*</span></label>
                                    <input type="text" name="type" class="form-control" value="{{ $admin['type'] }}" readonly>
                                </div> --}}

                                <div class="form-group">
                                    <label>Current Image</label>
                                    <br>
                                    @if (!empty($admin['image']))
                                        <img style="width: 100px; height: 100px; object-fit: cover; border-radius: 8px;"
                                             src="{{ url('admin/images/photos/' . $admin['image']) }}" alt="Admin Photo">
                                    @else
                                        <img style="width: 100px; height: 100px; object-fit: cover; border-radius: 8px;"
                                             src="{{ url('admin/images/photos/no-image.gif') }}" alt="No Image">
                                    @endif
                                     <input type="hidden" name="current_admin_image" value="{{ $admin['image'] ?? '' }}">
                                </div>

                                <div class="form-group">
                                    <label>New Photo</label>
                                    <input type="file" name="admin_image" class="form-control">
                                    <small class="text-muted">Leave blank if you don't want to change the photo</small>
                                </div>

                                <button type="submit" class="btn btn-primary">Update Admin</button>
                            </form>
                        </div>
                    </div>
                </div>

                @if (isset($vendorPersonal) || isset($vendorBusiness) || isset($vendorBank))
                    {{-- Vendor Personal Information --}}
                    @if (isset($vendorPersonal) && !empty($vendorPersonal))
                    <div class="col-md-6 grid-margin stretch-card">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title">Personal Information (Vendor)</h4>
                                <div class="form-group">
                                    <label>Address</label>
                                    <input class="form-control" value="{{ $vendorPersonal['address'] ?? '' }}" readonly>
                                </div>
                                <div class="form-group">
                                    <label>City</label>
                                    <input class="form-control" value="{{ $vendorPersonal['city'] ?? '' }}" readonly>
                                </div>
                                <div class="form-group">
                                    <label>State</label>
                                    <input class="form-control" value="{{ $vendorPersonal['state'] ?? '' }}" readonly>
                                </div>
                                <div class="form-group">
                                    <label>Country</label>
                                    <input class="form-control" value="{{ $vendorPersonal['country'] ?? '' }}" readonly>
                                </div>
                                <div class="form-group">
                                    <label>Pincode</label>
                                    <input class="form-control" value="{{ $vendorPersonal['pincode'] ?? '' }}" readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    {{-- Vendor Business Information --}}
                    @if (isset($vendorBusiness) && !empty($vendorBusiness))
                    <div class="col-md-6 grid-margin stretch-card">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title">Business Information (Vendor)</h4>
                                <div class="form-group">
                                    <label>Shop Name</label>
                                    <input class="form-control" value="{{ $vendorBusiness['shop_name'] ?? '' }}" readonly>
                                </div>
                                <div class="form-group">
                                    <label>Shop Address</label>
                                    <input class="form-control" value="{{ $vendorBusiness['shop_address'] ?? '' }}" readonly>
                                </div>
                                <div class="form-group">
                                    <label>Shop Mobile</label>
                                    <input class="form-control" value="{{ $vendorBusiness['shop_mobile'] ?? '' }}" readonly>
                                </div>
                                <div class="form-group">
                                    <label>Business License Number</label>
                                    <input class="form-control" value="{{ $vendorBusiness['business_license_number'] ?? '' }}" readonly>
                                </div>
                                <div class="form-group">
                                    <label>GST Number</label>
                                    <input class="form-control" value="{{ $vendorBusiness['gst_number'] ?? '' }}" readonly>
                                </div>
                                <div class="form-group">
                                    <label>PAN Number</label>
                                    <input class="form-control" value="{{ $vendorBusiness['pan_number'] ?? '' }}" readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    {{-- Vendor Bank Information --}}
                    @if (isset($vendorBank) && !empty($vendorBank))
                    <div class="col-md-6 grid-margin stretch-card">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title">Bank Information (Vendor)</h4>
                                <div class="form-group">
                                    <label>Account Holder Name</label>
                                    <input class="form-control" value="{{ $vendorBank['account_holder_name'] ?? '' }}" readonly>
                                </div>
                                <div class="form-group">
                                    <label>Bank Name</label>
                                    <input class="form-control" value="{{ $vendorBank['bank_name'] ?? '' }}" readonly>
                                </div>
                                <div class="form-group">
                                    <label>Account Number</label>
                                    <input class="form-control" value="{{ $vendorBank['account_number'] ?? '' }}" readonly>
                                </div>
                                <div class="form-group">
                                    <label>Bank IFSC Code</label>
                                    <input class="form-control" value="{{ $vendorBank['bank_ifsc_code'] ?? '' }}" readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                @endif
            </div>
        </div>
        <!-- content-wrapper ends -->
        @include('admin.layout.footer')
        <!-- partial -->
    </div>
@endsection

