@extends('admin.layout.layout')

@section('content')
    <style>
        /* Premium Visual Styles */
        .vendor-profile-wrapper {
            font-family: 'Inter', sans-serif;
            color: #333;
        }
        .profile-card {
            border: none;
            border-radius: 16px;
            background: linear-gradient(145deg, #ffffff, #f8f9fc);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.04);
            transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
            position: relative;
            overflow: hidden;
        }
        .profile-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 6px;
            background: linear-gradient(90deg, #4b49ac, #7978e9);
        }
        .profile-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.08);
        }
        .profile-avatar-container {
            position: relative;
            display: inline-block;
        }
        .profile-avatar {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid #fff;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
        }
        .status-badge {
            position: absolute;
            bottom: 5px;
            right: 5px;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            border: 3px solid #fff;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        }
        .status-active { background-color: #28a745; }
        .status-inactive { background-color: #dc3545; }

        .nav-tabs-custom {
            border-bottom: 2px solid #eef2f6;
            margin-bottom: 25px;
        }
        .nav-tabs-custom .nav-link {
            border: none;
            color: #6c757d;
            font-weight: 600;
            font-size: 15px;
            padding: 14px 22px;
            position: relative;
            transition: color 0.3s ease;
            background: transparent;
        }
        .nav-tabs-custom .nav-link:hover {
            color: #4b49ac;
        }
        .nav-tabs-custom .nav-link.active {
            color: #4b49ac;
            background: transparent;
        }
        .nav-tabs-custom .nav-link.active::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 100%;
            height: 3px;
            background: linear-gradient(90deg, #4b49ac, #7978e9);
            border-radius: 3px 3px 0 0;
        }

        /* Stats Cards */
        .stat-widget {
            border: none;
            border-radius: 12px;
            background-color: #ffffff;
            box-shadow: 0 4px 15px rgba(0,0,0,0.02);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            overflow: hidden;
            position: relative;
        }
        .stat-widget::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            height: 100%;
            width: 4px;
        }
        .stat-widget-indigo::before { background-color: #4b49ac; }
        .stat-widget-success::before { background-color: #28a745; }
        .stat-widget-info::before { background-color: #17a2b8; }
        .stat-widget-warning::before { background-color: #ffc107; }

        .stat-widget:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.05);
        }
        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
        }

        /* Timeline Activities */
        .timeline-container {
            position: relative;
            padding-left: 20px;
        }
        .timeline-container::before {
            content: '';
            position: absolute;
            left: 5px;
            top: 5px;
            bottom: 5px;
            width: 2px;
            background-color: #eef2f6;
        }
        .timeline-card {
            position: relative;
            margin-bottom: 20px;
            padding-left: 20px;
        }
        .timeline-card::before {
            content: '';
            position: absolute;
            left: -19px;
            top: 3px;
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background-color: #fff;
            border: 2px solid #4b49ac;
            z-index: 2;
        }
        .timeline-card.success::before { border-color: #28a745; }
        .timeline-card.primary::before { border-color: #4b49ac; }

        .timeline-card-content {
            background-color: #fcfdfe;
            border: 1px solid #eef2f6;
            border-radius: 8px;
            padding: 12px 16px;
        }

        /* Form Customizing */
        .form-control-custom {
            border-radius: 8px;
            border: 1px solid #ced4da;
            padding: 10px 15px;
            transition: border-color 0.2s;
        }
        .form-control-custom:focus {
            border-color: #4b49ac;
            box-shadow: 0 0 0 0.2rem rgba(75, 73, 172, 0.25);
        }
        .form-control-disabled {
            background-color: #f8f9fa !important;
            color: #495057 !important;
            cursor: not-allowed;
            border: 1px solid #eef2f6;
        }

        /* Book Thumbnails */
        .book-thumbnail {
            width: 50px;
            height: 70px;
            object-fit: cover;
            border-radius: 6px;
            box-shadow: 0 3px 8px rgba(0,0,0,0.1);
            transition: transform 0.2s;
        }
        .book-thumbnail:hover {
            transform: scale(1.08);
        }
        
        .table-premium th {
            background-color: #f8f9fc;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 12px;
            letter-spacing: 0.5px;
            color: #555;
            border-top: none;
            padding: 15px;
        }
        .table-premium td {
            padding: 15px;
            vertical-align: middle;
        }
    </style>

    <div class="main-panel vendor-profile-wrapper">
        <div class="content-wrapper">
            <!-- Header Block -->
            <div class="row mb-4">
                <div class="col-md-12">
                    <div class="d-flex justify-content-between align-items-center flex-wrap">
                        <div>
                            <h3 class="font-weight-bold mb-1">Vendor Dashboard</h3>
                            <h6 class="text-muted mb-0">
                                <a href="{{ url('admin/admins/vendor') }}" class="text-decoration-none">
                                    <i class="mdi mdi-arrow-left-bold mr-1"></i> Back to Vendors List
                                </a>
                            </h6>
                        </div>
                        <div class="mt-2 mt-md-0">
                            @if (($vendorDetails['status'] ?? 0) == 1)
                                <span class="badge badge-success px-3 py-2 font-weight-semibold" style="font-size: 13px; border-radius: 20px;">
                                    <i class="mdi mdi-check-circle mr-1"></i> Active Vendor Account
                                </span>
                            @else
                                <span class="badge badge-danger px-3 py-2 font-weight-semibold" style="font-size: 13px; border-radius: 20px;">
                                    <i class="mdi mdi-alert-circle mr-1"></i> Pending/Inactive Account
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Profile Summary & Navigation Row -->
            <div class="row">
                <!-- Left Sidebar: Vendor Avatar Card -->
                <div class="col-lg-3 mb-4">
                    <div class="card profile-card text-center p-4">
                        <div class="card-body p-0">
                            <div class="profile-avatar-container mb-3">
                                @if (!empty($vendorDetails['profile_image']))
                                    <img class="profile-avatar" src="{{ asset('admin/images/photos/' . $vendorDetails['profile_image']) }}" alt="{{ $vendorDetails['name'] }}">
                                @else
                                    <img class="profile-avatar" src="{{ asset('admin/images/photos/no-image.gif') }}" alt="No Photo">
                                @endif
                                <div class="status-badge {{ ($vendorDetails['status'] ?? 0) == 1 ? 'status-active' : 'status-inactive' }}"></div>
                            </div>
                            <h4 class="font-weight-bold mb-1">{{ $vendorDetails['name'] ?? 'Vendor Name' }}</h4>
                            <p class="text-muted font-weight-normal mb-3">{{ $vendorDetails['email'] ?? 'vendor@email.com' }}</p>
                            <hr class="my-3" style="border-top: 1px solid #eef2f6;">
                            
                            <div class="text-left">
                                <div class="mb-2">
                                    <small class="text-muted d-block uppercase font-weight-semibold" style="font-size: 10px; letter-spacing: 0.5px;">Current Plan</small>
                                    <span class="font-weight-bold text-primary">{{ ucfirst($vendorDetails['vendor_personal']['plan'] ?? 'Free') }} Plan</span>
                                </div>
                            
                                <div>
                                    <small class="text-muted d-block uppercase font-weight-semibold" style="font-size: 10px; letter-spacing: 0.5px;">Member Since</small>
                                    <span class="font-weight-bold text-dark">{{ date('d M Y', strtotime($vendorDetails['created_at'])) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Area: Details & Actions Grid -->
                <div class="col-lg-9">
                    <!-- Tabs Menu -->
                    <ul class="nav nav-tabs nav-tabs-custom" id="vendorTabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="overview-tab" data-toggle="tab" href="#overview" role="tab" aria-controls="overview" aria-selected="true">
                                <i class="mdi mdi-view-dashboard mr-1"></i> Overview
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="personal-tab" data-toggle="tab" href="#personal" role="tab" aria-controls="personal" aria-selected="false">
                                <i class="mdi mdi-account mr-1"></i> Personal details
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="business-tab" data-toggle="tab" href="#business" role="tab" aria-controls="business" aria-selected="false">
                                <i class="mdi mdi-briefcase mr-1"></i> Business Details
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="bank-tab" data-toggle="tab" href="#bank" role="tab" aria-controls="bank" aria-selected="false">
                                <i class="mdi mdi-bank mr-1"></i> Bank Details
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="books-tab" data-toggle="tab" href="#books" role="tab" aria-controls="books" aria-selected="false">
                                <i class="mdi mdi-book-open-page-variant mr-1"></i> Books & Stock ({{ $totalBooks }})
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="security-tab" data-toggle="tab" href="#security" role="tab" aria-controls="security" aria-selected="false">
                                <i class="mdi mdi-lock-open-outline mr-1"></i> Security & Settings
                            </a>
                        </li>
                    </ul>

                    <!-- Tabs Content Panels -->
                    <div class="tab-content p-0" id="vendorTabsContent">
                        
                        <!-- TAB 1: OVERVIEW -->
                        <div class="tab-pane fade show active" id="overview" role="tabpanel" aria-labelledby="overview-tab">
                            <!-- Statistics Row -->
                            <div class="row mb-4">
                                <div class="col-sm-6 col-md-3 mb-3 mb-md-0">
                                    <div class="card stat-widget stat-widget-indigo p-3">
                                        <div class="d-flex align-items-center">
                                            <div class="stat-icon bg-light-primary text-primary mr-3">
                                                <i class="mdi mdi-book-open-page-variant" style="color: #4b49ac;"></i>
                                            </div>
                                            <div>
                                                <h3 class="font-weight-bold mb-0">{{ $totalBooks }}</h3>
                                                <small class="text-muted">Total Books</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6 col-md-3 mb-3 mb-md-0">
                                    <div class="card stat-widget stat-widget-success p-3">
                                        <div class="d-flex align-items-center">
                                            <div class="stat-icon bg-light-success text-success mr-3">
                                                <i class="mdi mdi-cube-outline" style="color: #28a745;"></i>
                                            </div>
                                            <div>
                                                <h3 class="font-weight-bold mb-0">{{ $totalStock }}</h3>
                                                <small class="text-muted">Total Stocks</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6 col-md-3 mb-3 mb-md-0">
                                    <div class="card stat-widget stat-widget-info p-3">
                                        <div class="d-flex align-items-center">
                                            <div class="stat-icon bg-light-info text-info mr-3">
                                                <i class="mdi mdi-cart-outline" style="color: #17a2b8;"></i>
                                            </div>
                                            <div>
                                                <h3 class="font-weight-bold mb-0">{{ $totalOrders }}</h3>
                                                <small class="text-muted">Total Orders</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                               
                                <div class="col-sm-6 col-md-3">
                                    <div class="card stat-widget stat-widget-warning p-3">
                                        <div class="d-flex align-items-center">
                                            <div class="stat-icon bg-light-warning text-warning mr-3">
                                                <i class="mdi mdi-star-circle" style="color: #ffc107;"></i>
                                            </div>
                                            <div>
                                                <h3 class="font-weight-bold mb-0" style="font-size: 20px;">{{ ucfirst($vendorDetails['vendor_personal']['plan'] ?? 'Free') }}</h3>
                                                <small class="text-muted">Active Plan</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Activity Timeline Section -->
                            <div class="card border-0 shadow-sm" style="border-radius: 12px;">
                                <div class="card-body">
                                    <h4 class="card-title font-weight-bold mb-4"><i class="mdi mdi-history text-primary mr-1"></i> Latest activities</h4>
                                    <div class="timeline-container">
                                        @forelse ($latestActivities as $activity)
                                            <div class="timeline-card {{ $activity['color'] }}">
                                                <div class="timeline-card-content">
                                                    <div class="d-flex justify-content-between align-items-center mb-1 flex-wrap">
                                                        <h6 class="font-weight-bold mb-0 text-dark">
                                                            @if ($activity['type'] == 'book_added')
                                                                <i class="mdi mdi-book-plus text-success mr-1"></i>
                                                            @else
                                                                <i class="mdi mdi-cart-arrow-down text-primary mr-1"></i>
                                                            @endif
                                                            {{ $activity['title'] }}
                                                        </h6>
                                                        <small class="text-muted font-weight-light">
                                                            <i class="mdi mdi-clock-outline mr-1"></i>
                                                            {{ \Carbon\Carbon::parse($activity['timestamp'])->diffForHumans() }}
                                                        </small>
                                                    </div>
                                                    <p class="text-muted mb-0 font-weight-normal">{{ $activity['description'] }}</p>
                                                </div>
                                            </div>
                                        @empty
                                            <div class="text-center py-5 text-muted">
                                                <i class="mdi mdi-bell-off-outline d-block mb-2" style="font-size: 30px; color: #ced4da;"></i>
                                                <span>No recent activity logged for this vendor.</span>
                                            </div>
                                        @endforelse
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- TAB 2: PERSONAL DETAILS (EDITABLE) -->
                        <div class="tab-pane fade" id="personal" role="tabpanel" aria-labelledby="personal-tab">
                            <div class="card border-0 shadow-sm" style="border-radius: 12px;">
                                <div class="card-body">
                                    <h4 class="card-title font-weight-bold mb-4 text-primary"><i class="mdi mdi-account-card-details mr-1"></i> Edit Personal Information</h4>
                                    
                                    @php
                                        $adminPrefix = request()->is('staff') || request()->is('staff/*') ? 'staff' : 'admin';
                                    @endphp
                                    
                                    <form method="post" action="{{ url($adminPrefix . '/update-vendor-profile') }}" enctype="multipart/form-data">
                                        @csrf
                                        <input type="hidden" name="vendor_user_id" value="{{ $vendorDetails['id'] }}">
                                        <input type="hidden" name="section" value="personal">

                                        <div class="row">
                                            <div class="col-md-6 form-group">
                                                <label class="font-weight-semibold">Full Name <span class="text-danger">*</span></label>
                                                <input type="text" name="name" class="form-control form-control-custom" value="{{ $vendorDetails['name'] ?? '' }}" required>
                                            </div>
                                            <div class="col-md-6 form-group">
                                                <label class="font-weight-semibold">Email Address <small class="text-muted">(Read-Only)</small></label>
                                                <input type="text" class="form-control form-control-custom form-control-disabled" value="{{ $vendorDetails['email'] ?? '' }}" readonly>
                                            </div>
                                            <div class="col-md-6 form-group">
                                                <label class="font-weight-semibold">Mobile Number <span class="text-danger">*</span></label>
                                                <input type="text" name="phone" class="form-control form-control-custom" value="{{ $vendorDetails['phone'] ?? '' }}" required>
                                            </div>
                                            <div class="col-md-6 form-group">
                                                <label class="font-weight-semibold">Pin Code</label>
                                                <input type="text" name="pincode" class="form-control form-control-custom" value="{{ $vendorDetails['pincode'] ?? '' }}">
                                            </div>
                                            <div class="col-md-6 form-group">
                                                <label class="font-weight-semibold">Profile Photo</label>
                                                <input type="file" name="profile_image" class="form-control form-control-custom">
                                            </div>
                                            <div class="col-md-6 form-group">
                                                <label class="font-weight-semibold">Location Coordinates (Lat,Lng)</label>
                                                <input type="text" name="location" class="form-control form-control-custom" value="{{ $vendorDetails['vendor_personal']['location'] ?? '' }}" placeholder="e.g. 28.6139,77.2090">
                                            </div>
                                            <div class="col-md-12 form-group">
                                                <label class="font-weight-semibold">Personal Address</label>
                                                <textarea name="address" class="form-control form-control-custom" rows="3">{{ $vendorDetails['address'] ?? '' }}</textarea>
                                            </div>
                                        </div>

                                        <hr class="my-4" style="border-top: 1px solid #eef2f6;">
                                        <h5 class="font-weight-bold mb-3 text-dark">Location Details</h5>
                                        <div class="row">
                                            <div class="col-md-3 form-group">
                                                <label class="font-weight-semibold">Country</label>
                                                <select class="form-control form-control-custom" name="country_id" id="vendor_country_id">
                                                    <option value="">Select Country</option>
                                                    @foreach ($countries as $country)
                                                        <option value="{{ $country['id'] }}" @if (isset($currentCountryId) && $country['id'] == $currentCountryId) selected @endif>{{ $country['name'] }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-3 form-group">
                                                <label class="font-weight-semibold">State</label>
                                                <select class="form-control form-control-custom" name="state_id" id="vendor_state_id">
                                                    <option value="">Select State</option>
                                                </select>
                                            </div>
                                            <div class="col-md-3 form-group">
                                                <label class="font-weight-semibold">District</label>
                                                <select class="form-control form-control-custom" name="district_id" id="vendor_district_id">
                                                    <option value="">Select District</option>
                                                </select>
                                            </div>
                                            <div class="col-md-3 form-group">
                                                <label class="font-weight-semibold">Block</label>
                                                <select class="form-control form-control-custom" name="block_id" id="vendor_block_id">
                                                    <option value="">Select Block</option>
                                                </select>
                                            </div>
                                        </div>

                                        <button type="submit" class="btn btn-primary px-4 py-2 font-weight-semibold mt-3" style="border-radius: 8px;">
                                            <i class="mdi mdi-content-save mr-1"></i> Save Personal Details
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- TAB 3: BUSINESS DETAILS (EDITABLE) -->
                        <div class="tab-pane fade" id="business" role="tabpanel" aria-labelledby="business-tab">
                            <div class="card border-0 shadow-sm" style="border-radius: 12px;">
                                <div class="card-body">
                                    <h4 class="card-title font-weight-bold mb-4 text-primary"><i class="mdi mdi-store mr-1"></i> Edit Business Details</h4>
                                    
                                    <form method="post" action="{{ url($adminPrefix . '/update-vendor-profile') }}" enctype="multipart/form-data">
                                        @csrf
                                        <input type="hidden" name="vendor_user_id" value="{{ $vendorDetails['id'] }}">
                                        <input type="hidden" name="section" value="business">

                                        <div class="row">
                                            <div class="col-md-6 form-group">
                                                <label class="font-weight-semibold">Shop Name <span class="text-danger">*</span></label>
                                                <input type="text" name="shop_name" class="form-control form-control-custom" value="{{ $vendorDetails['vendor_business']['shop_name'] ?? '' }}" required>
                                            </div>
                                            <div class="col-md-6 form-group">
                                                <label class="font-weight-semibold">Shop Mobile <span class="text-danger">*</span></label>
                                                <input type="text" name="shop_mobile" class="form-control form-control-custom" value="{{ $vendorDetails['vendor_business']['shop_mobile'] ?? '' }}" required>
                                            </div>
                                            <div class="col-md-6 form-group">
                                                <label class="font-weight-semibold">Shop Website</label>
                                                <input type="text" name="shop_website" class="form-control form-control-custom" value="{{ $vendorDetails['vendor_business']['shop_website'] ?? '' }}">
                                            </div>
                                            <div class="col-md-6 form-group">
                                                <label class="font-weight-semibold">Shop Pincode</label>
                                                <input type="text" name="shop_pincode" class="form-control form-control-custom" value="{{ $vendorDetails['vendor_business']['shop_pincode'] ?? '' }}">
                                            </div>
                                            <div class="col-md-12 form-group">
                                                <label class="font-weight-semibold">Shop Address</label>
                                                <textarea name="shop_address" class="form-control form-control-custom" rows="2">{{ $vendorDetails['vendor_business']['shop_address'] ?? '' }}</textarea>
                                            </div>
                                        </div>

                                        <hr class="my-4" style="border-top: 1px solid #eef2f6;">
                                        <h5 class="font-weight-bold mb-3 text-dark">Legal & Document Details</h5>
                                        <div class="row">
                                            <div class="col-md-4 form-group">
                                                <label class="font-weight-semibold">GST Number</label>
                                                <input type="text" name="gst_number" class="form-control form-control-custom" value="{{ $vendorDetails['vendor_business']['gst_number'] ?? '' }}">
                                            </div>
                                            <div class="col-md-4 form-group">
                                                <label class="font-weight-semibold">PAN Number</label>
                                                <input type="text" name="pan_number" class="form-control form-control-custom" value="{{ $vendorDetails['vendor_business']['pan_number'] ?? '' }}">
                                            </div>
                                            <div class="col-md-4 form-group">
                                                <label class="font-weight-semibold">Business License Number</label>
                                                <input type="text" name="business_license_number" class="form-control form-control-custom" value="{{ $vendorDetails['vendor_business']['business_license_number'] ?? '' }}">
                                            </div>
                                            <div class="col-md-6 form-group">
                                                <label class="font-weight-semibold">Address Proof Document Type</label>
                                                <input type="text" name="address_proof" class="form-control form-control-custom" value="{{ $vendorDetails['vendor_business']['address_proof'] ?? '' }}">
                                            </div>
                                            <div class="col-md-6 form-group">
                                                <label class="font-weight-semibold">Upload Address Proof Image</label>
                                                <input type="file" name="address_proof_image" class="form-control form-control-custom">
                                            </div>
                                            @if (!empty($vendorDetails['vendor_business']['address_proof_image']))
                                                <div class="col-md-12 mt-2">
                                                    <label class="font-weight-semibold d-block">Current Address Proof</label>
                                                    <a href="{{ asset('admin/images/proofs/' . $vendorDetails['vendor_business']['address_proof_image']) }}" target="_blank" class="d-inline-block p-1 border rounded bg-light hover-shadow">
                                                        <img style="max-height: 150px; border-radius: 4px;" src="{{ asset('admin/images/proofs/' . $vendorDetails['vendor_business']['address_proof_image']) }}" alt="Address Proof">
                                                    </a>
                                                </div>
                                            @endif
                                        </div>

                                        <button type="submit" class="btn btn-primary px-4 py-2 font-weight-semibold mt-3" style="border-radius: 8px;">
                                            <i class="mdi mdi-content-save mr-1"></i> Save Business Details
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- TAB 4: BANK DETAILS (EDITABLE) -->
                        <div class="tab-pane fade" id="bank" role="tabpanel" aria-labelledby="bank-tab">
                            <div class="card border-0 shadow-sm" style="border-radius: 12px;">
                                <div class="card-body">
                                    <h4 class="card-title font-weight-bold mb-4 text-primary"><i class="mdi mdi-bank mr-1"></i> Edit Bank Account Information</h4>
                                    
                                    <form method="post" action="{{ url($adminPrefix . '/update-vendor-profile') }}">
                                        @csrf
                                        <input type="hidden" name="vendor_user_id" value="{{ $vendorDetails['id'] }}">
                                        <input type="hidden" name="section" value="bank">

                                        <div class="row">
                                            <div class="col-md-6 form-group">
                                                <label class="font-weight-semibold">Account Holder Name <span class="text-danger">*</span></label>
                                                <input type="text" name="account_holder_name" class="form-control form-control-custom" value="{{ $vendorDetails['vendor_bank']['account_holder_name'] ?? '' }}" required>
                                            </div>
                                            <div class="col-md-6 form-group">
                                                <label class="font-weight-semibold">Bank Name <span class="text-danger">*</span></label>
                                                <input type="text" name="bank_name" class="form-control form-control-custom" value="{{ $vendorDetails['vendor_bank']['bank_name'] ?? '' }}" required>
                                            </div>
                                            <div class="col-md-6 form-group">
                                                <label class="font-weight-semibold">Account Number <span class="text-danger">*</span></label>
                                                <input type="text" name="account_number" class="form-control form-control-custom" value="{{ $vendorDetails['vendor_bank']['account_number'] ?? '' }}" required>
                                            </div>
                                            <div class="col-md-6 form-group">
                                                <label class="font-weight-semibold">Bank IFSC Code <span class="text-danger">*</span></label>
                                                <input type="text" name="bank_ifsc_code" class="form-control form-control-custom" value="{{ $vendorDetails['vendor_bank']['bank_ifsc_code'] ?? '' }}" required>
                                            </div>
                                        </div>

                                        <button type="submit" class="btn btn-primary px-4 py-2 font-weight-semibold mt-3" style="border-radius: 8px;">
                                            <i class="mdi mdi-content-save mr-1"></i> Save Bank Details
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- TAB 5: BOOKS & STOCK -->
                        <div class="tab-pane fade" id="books" role="tabpanel" aria-labelledby="books-tab">
                            <div class="card border-0 shadow-sm" style="border-radius: 12px;">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap">
                                        <h4 class="card-title font-weight-bold mb-2 text-primary"><i class="mdi mdi-library mr-1"></i> Listed Books & Inventory</h4>
                                        <div class="form-group mb-2" style="width: 250px;">
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text bg-transparent border-right-0"><i class="mdi mdi-magnify text-muted"></i></span>
                                                </div>
                                                <input type="text" id="bookSearchInput" class="form-control form-control-custom border-left-0" style="height: auto; padding: 6px 12px;" placeholder="Search listed books...">
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="table-responsive">
                                        <table class="table table-hover table-premium table-custom">
                                            <thead>
                                                <tr>
                                                    <th>Book Image</th>
                                                    <th>Book Details</th>
                                                    <th>ISBN</th>
                                                    <th>Pricing (INR)</th>
                                                    <th>Stock Level</th>
                                                    <th>Listing Status</th>
                                                    <th>Condition</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody id="booksTableBody">
                                                @forelse ($vendorProducts as $prodAttr)
                                                    @php
                                                        $discountDetails = \App\Models\Product::getDiscountPriceDetailsByAttribute($prodAttr->id, $prodAttr);
                                                        $imageUrl = !empty($prodAttr->product->product_image) 
                                                            ? getBookCoverUrl($prodAttr->product->product_image) 
                                                            : getBookCoverUrl('no-image.png');
                                                    @endphp
                                                    <tr>
                                                        <td>
                                                            <img src="{{ $imageUrl }}" class="book-thumbnail" alt="Cover">
                                                        </td>
                                                        <td>
                                                            <span class="font-weight-semibold text-dark d-block" style="font-size: 14px;">{{ $prodAttr->product->product_name ?? 'N/A' }}</span>
                                                            <small class="text-muted">{{ $prodAttr->product->category->category_name ?? 'N/A' }}</small>
                                                        </td>
                                                        <td>
                                                            <span class="text-secondary font-weight-medium">{{ $prodAttr->product->product_isbn ?? 'N/A' }}</span>
                                                        </td>
                                                        <td>
                                                            @if ($discountDetails['discount'] > 0)
                                                                <span class="text-muted text-decoration-line-through" style="text-decoration: line-through; font-size: 12px;">₹{{ $discountDetails['product_price'] }}</span>
                                                                <span class="text-danger font-weight-bold d-block">₹{{ $discountDetails['final_price'] }}</span>
                                                                <small class="text-success">({{ $discountDetails['discount_percent'] }}% off)</small>
                                                            @else
                                                                <span class="font-weight-bold text-dark">₹{{ $discountDetails['product_price'] }}</span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @if ($prodAttr->stock <= 0)
                                                                <span class="badge badge-danger">Out of Stock</span>
                                                            @elseif ($prodAttr->stock <= 5)
                                                                <span class="badge badge-warning">{{ $prodAttr->stock }} Low Stock</span>
                                                            @else
                                                                <span class="badge badge-success">{{ $prodAttr->stock }} Available</span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @if ($prodAttr->status == 1)
                                                                <span class="badge badge-success px-2 py-1"><i class="mdi mdi-check"></i> Active</span>
                                                            @else
                                                                <span class="badge badge-secondary px-2 py-1"><i class="mdi mdi-close"></i> Inactive</span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @if ($prodAttr->condition)
                                                                <span class="badge badge-info">{{ $prodAttr->condition->name }}</span>
                                                            @else
                                                                <span class="badge badge-light text-dark text-capitalize">{{ $prodAttr->product->condition ?? 'New' }}</span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            <a href="{{ url('admin/add-edit-product/' . $prodAttr->product_id) }}" class="btn btn-sm btn-outline-primary" style="border-radius: 20px;">
                                                                <i class="mdi mdi-pencil mr-1"></i> Edit Book
                                                            </a>
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="8" class="text-center py-5 text-muted">
                                                            <i class="mdi mdi-book-open-outline d-block mb-2" style="font-size: 30px; color: #ced4da;"></i>
                                                            <span>No books uploaded by this vendor yet.</span>
                                                        </td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- TAB 6: SECURITY & SETTINGS -->
                        <div class="tab-pane fade" id="security" role="tabpanel" aria-labelledby="security-tab">
                            <div class="row">
                                <!-- Col 1: Password Update -->
                                <div class="col-md-6 mb-4">
                                    <div class="card border-0 shadow-sm" style="border-radius: 12px; height: 100%;">
                                        <div class="card-body">
                                            <h4 class="card-title font-weight-bold mb-4 text-primary"><i class="mdi mdi-security mr-1"></i> Update Password</h4>
                                            
                                            <div class="alert alert-info border-0 mb-4" style="border-radius: 8px;">
                                                <i class="mdi mdi-information-outline mr-2" style="font-size: 16px; vertical-align: middle;"></i>
                                                <span>Update the vendor's password. Securely communicate it to them after.</span>
                                            </div>

                                            <form method="post" action="{{ url($adminPrefix . '/update-vendor-password') }}">
                                                @csrf
                                                <input type="hidden" name="vendor_user_id" value="{{ $vendorDetails['id'] }}">
                                                
                                                <div class="form-group mb-3">
                                                    <label class="font-weight-semibold" for="password">New Password</label>
                                                    <input type="password" name="password" id="password" class="form-control form-control-custom" placeholder="Enter new password" required minlength="6">
                                                </div>
                                                
                                                <div class="form-group mb-4">
                                                    <label class="font-weight-semibold" for="password_confirmation">Confirm Password</label>
                                                    <input type="password" name="password_confirmation" id="password_confirmation" class="form-control form-control-custom" placeholder="Confirm new password" required minlength="6">
                                                </div>
                                                
                                                <button type="submit" class="btn btn-primary px-4 py-2 font-weight-semibold" style="border-radius: 8px;">
                                                    <i class="mdi mdi-key-variant mr-1"></i> Update Password
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                                <!-- Col 2: Administrative Settings (Plan, Commission, Status) -->
                                <div class="col-md-6 mb-4">
                                    <div class="card border-0 shadow-sm" style="border-radius: 12px; height: 100%;">
                                        <div class="card-body">
                                            <h4 class="card-title font-weight-bold mb-4 text-primary"><i class="mdi mdi-settings mr-1"></i> Administrative Settings</h4>
                                            
                                            <div class="alert alert-info border-0 mb-4" style="border-radius: 8px;">
                                                <i class="mdi mdi-information-outline mr-2" style="font-size: 16px; vertical-align: middle;"></i>
                                                <span>Configure account status, plan level, and sales commission percentages.</span>
                                            </div>

                                            <form method="post" action="{{ url($adminPrefix . '/update-vendor-profile') }}">
                                                @csrf
                                                <input type="hidden" name="vendor_user_id" value="{{ $vendorDetails['id'] }}">
                                                <input type="hidden" name="section" value="settings">

                                                <div class="form-group mb-3">
                                                    <label class="font-weight-semibold" for="plan">Vendor Plan <span class="text-danger">*</span></label>
                                                    <select name="plan" id="plan" class="form-control form-control-custom" required>
                                                        <option value="free" @if(strtolower($vendorDetails['vendor_personal']['plan'] ?? '') === 'free') selected @endif>Free Plan</option>
                                                        <option value="pro" @if(strtolower($vendorDetails['vendor_personal']['plan'] ?? '') === 'pro') selected @endif>Pro Plan</option>
                                                    </select>
                                                </div>



                                                <div class="form-group mb-4">
                                                    <label class="font-weight-semibold d-block">Account Status</label>
                                                    <div class="custom-control custom-switch" style="padding-left: 2.25rem;">
                                                        <input type="checkbox" class="custom-control-input" id="statusSwitch" name="status" value="1" @if(($vendorDetails['status'] ?? 0) == 1) checked @endif style="cursor: pointer;">
                                                        <label class="custom-control-label" for="statusSwitch" style="cursor: pointer;">Active Account</label>
                                                    </div>
                                                </div>

                                                <button type="submit" class="btn btn-primary px-4 py-2 font-weight-semibold" style="border-radius: 8px;">
                                                    <i class="mdi mdi-content-save-settings mr-1"></i> Update Settings
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

        </div>
        <!-- content-wrapper ends -->
        @include('admin.layout.footer')
    </div>

    @if (isset($vendorDetails) && !empty($vendorDetails))
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            // Client-side quick filter for listed books table
            $("#bookSearchInput").on("keyup", function() {
                var value = $(this).val().toLowerCase();
                $("#booksTableBody tr").filter(function() {
                    $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
                });
            });

            // Store current values for locations
            var currentCountryId = {{ $currentCountryId ?? 'null' }};
            var currentStateId = {{ $currentStateId ?? 'null' }};
            var currentDistrictId = {{ $currentDistrictId ?? 'null' }};
            var currentBlockId = {{ $currentBlockId ?? 'null' }};

            // Event listeners for dropdown updates
            $('#vendor_country_id').on('change', function() {
                var countryId = $(this).val();
                currentStateId = null;
                currentDistrictId = null;
                currentBlockId = null;
                loadVendorStates(countryId);
            });

            $('#vendor_state_id').on('change', function() {
                var stateId = $(this).val();
                currentDistrictId = null;
                currentBlockId = null;
                loadVendorDistricts(stateId);
            });

            $('#vendor_district_id').on('change', function() {
                var districtId = $(this).val();
                currentBlockId = null;
                loadVendorBlocks(districtId);
            });

            // Load states based on country
            function loadVendorStates(countryId) {
                if (!countryId) {
                    $('#vendor_state_id').empty().append('<option value="">Select State</option>');
                    $('#vendor_district_id').empty().append('<option value="">Select District</option>');
                    $('#vendor_block_id').empty().append('<option value="">Select Block</option>');
                    return Promise.resolve();
                }

                return new Promise(function(resolve, reject) {
                    $.ajax({
                        url: '{{ route('vendor_states') }}',
                        type: 'GET',
                        data: { country: countryId },
                        dataType: 'json',
                        success: function(response) {
                            var stateSelect = $('#vendor_state_id');
                            stateSelect.empty();
                            stateSelect.append('<option value="">Select State</option>');

                            $.each(response, function(key, value) {
                                stateSelect.append('<option value="' + key + '">' + value + '</option>');
                            });

                            // Clear dependent dropdowns
                            $('#vendor_district_id').empty().append('<option value="">Select District</option>');
                            $('#vendor_block_id').empty().append('<option value="">Select Block</option>');

                            // Set current value if exists
                            if (currentStateId && currentStateId !== null) {
                                stateSelect.val(currentStateId);
                                loadVendorDistricts(currentStateId).then(resolve);
                            } else {
                                resolve();
                            }
                        },
                        error: function(xhr, status, error) {
                            console.log('Error loading states:', error);
                            reject(error);
                        }
                    });
                });
            }

            // Load districts based on state
            function loadVendorDistricts(stateId) {
                if (!stateId) {
                    $('#vendor_district_id').empty().append('<option value="">Select District</option>');
                    $('#vendor_block_id').empty().append('<option value="">Select Block</option>');
                    return Promise.resolve();
                }

                return new Promise(function(resolve, reject) {
                    $.ajax({
                        url: '{{ route('vendor_districts') }}',
                        type: 'GET',
                        data: { state: stateId },
                        dataType: 'json',
                        success: function(response) {
                            var districtSelect = $('#vendor_district_id');
                            districtSelect.empty();
                            districtSelect.append('<option value="">Select District</option>');

                            $.each(response, function(key, value) {
                                districtSelect.append('<option value="' + key + '">' + value + '</option>');
                            });

                            // Clear dependent dropdowns
                            $('#vendor_block_id').empty().append('<option value="">Select Block</option>');

                            // Set current value if exists
                            if (currentDistrictId && currentDistrictId !== null) {
                                districtSelect.val(currentDistrictId);
                                loadVendorBlocks(currentDistrictId).then(resolve);
                            } else {
                                resolve();
                            }
                        },
                        error: function(xhr, status, error) {
                            console.log('Error loading districts:', error);
                            reject(error);
                        }
                    });
                });
            }

            // Load blocks based on district
            function loadVendorBlocks(districtId) {
                if (!districtId) {
                    $('#vendor_block_id').empty().append('<option value="">Select Block</option>');
                    return Promise.resolve();
                }

                return new Promise(function(resolve, reject) {
                    $.ajax({
                        url: '{{ route('vendor_blocks') }}',
                        type: 'GET',
                        data: { district: districtId },
                        dataType: 'json',
                        success: function(response) {
                            var blockSelect = $('#vendor_block_id');
                            blockSelect.empty();
                            blockSelect.append('<option value="">Select Block</option>');

                            $.each(response, function(key, value) {
                                blockSelect.append('<option value="' + key + '">' + value + '</option>');
                            });

                            // Set current value if exists
                            if (currentBlockId && currentBlockId !== null) {
                                blockSelect.val(currentBlockId);
                            }

                            resolve();
                        },
                        error: function(xhr, status, error) {
                            console.log('Error loading blocks:', error);
                            reject(error);
                        }
                    });
                });
            }

            // Initialize form with current values on page load
            if (currentCountryId && currentCountryId !== null) {
                $('#vendor_country_id').val(currentCountryId);
                loadVendorStates(currentCountryId);
            } else {
                // If country dropdown has a value, load states
                var selectedCountry = $('#vendor_country_id').val();
                if (selectedCountry) {
                    loadVendorStates(selectedCountry);
                }
            }
        });
    </script>
    @endif
@endsection