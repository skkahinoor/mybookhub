<link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;600;700;800&display=swap" rel="stylesheet">
<style>
    /* Mazer-inspired Navbar Design */
    .navbar {
        font-family: 'Nunito', sans-serif;
        background-color: #ffffff !important;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.03);
        border-bottom: 1px solid #f2f7ff;
    }
    
    .navbar-brand-wrapper {
        background-color: #ffffff !important;
        border-right: none !important;
    }

    .navbar .navbar-menu-wrapper {
        background-color: #ffffff !important;
    }

    .count-indicator {
        position: relative;
    }

    .count {
        position: absolute;
        top: 6px;
        right: 0px;
        background: #ef4444; /* Clean Red */
        color: white;
        border-radius: 50%;
        width: 18px;
        height: 18px;
        font-size: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        box-shadow: 0 2px 4px rgba(239, 68, 68, 0.3);
    }

    .count:empty {
        display: none;
    }

    /* Dropdown UI improvements */
    .navbar .navbar-menu-wrapper .navbar-nav .nav-item.dropdown .dropdown-menu {
        border: none;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
        border-radius: 12px;
        overflow: hidden;
        padding: 0;
        min-width: 280px;
    }

    .dropdown-header {
        padding: 15px 20px;
        font-weight: 700;
        color: #25396f;
        background-color: #f8f9fa;
        border-bottom: 1px solid #edf2f9;
        font-size: 0.95rem;
    }

    .preview-item {
        padding: 12px 20px;
        border-bottom: 1px solid #edf2f9;
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
    }

    .preview-item:hover {
        background-color: #f2f7ff;
    }

    .preview-item:last-child {
        border-bottom: none;
    }

    .preview-thumbnail {
        width: 45px;
        height: 45px;
        flex-shrink: 0;
        margin-right: 15px;
    }

    .preview-icon {
        width: 100%;
        height: 100%;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        font-size: 1.2rem;
    }
    
    .bg-info { background-color: #55c6e8 !important; }

    .preview-item-content {
        flex-grow: 1;
        overflow: hidden;
    }

    .preview-subject {
        margin: 0 0 3px 0;
        font-size: 14px;
        font-weight: 600;
        color: #25396f;
    }

    .small-text {
        font-size: 12px;
        color: #7e8299;
    }

    .dropdown-item-text {
        padding: 15px 20px;
        color: #25396f;
        background-color: #f8f9fa;
        border-bottom: 1px solid #edf2f9;
    }

    .dropdown-item-text strong {
        font-size: 1.1rem;
    }

    .navbar .navbar-menu-wrapper .navbar-nav .nav-item.dropdown .dropdown-item {
        padding: 12px 20px;
        font-weight: 600;
        color: #4b5563;
        transition: all 0.2s;
    }
    
    .navbar .navbar-menu-wrapper .navbar-nav .nav-item.dropdown .dropdown-item i {
        margin-right: 10px;
        font-size: 1.1rem;
        vertical-align: middle;
    }

    .navbar .navbar-menu-wrapper .navbar-nav .nav-item.dropdown .dropdown-item:hover {
        background-color: #f2f7ff;
        color: #435ebe;
    }
    
    /* Search Bar */
    .nav-search .input-group {
        background: #f2f7ff;
        border-radius: 10px;
        overflow: hidden;
    }
    
    .nav-search .input-group-prepend .input-group-text {
        background: transparent;
        border: none;
        color: #435ebe;
    }
    
    .nav-search .form-control {
        background: transparent;
        border: none;
        color: #25396f;
        font-weight: 600;
    }
    
    .nav-profile img {
        border-radius: 50%;
        border: 2px solid #e2e8f0;
        padding: 2px;
        object-fit: cover;
    }
</style>

@if (Auth::guard('admin')->user()->type == 'superadmin' || Auth::guard('admin')->user()->type == 'admin')
    <!-- partial:partials/_navbar.html -->
    <nav class="navbar col-lg-12 col-12 p-0 fixed-top d-flex flex-row">
        <div class="text-center navbar-brand-wrapper d-flex align-items-center justify-content-center">
            @if (!empty($logos))
                <img src="{{ asset('uploads/logos/' . $logos->first()->logo) }}" alt="BookHub" height="50"
                    width="150">
            @endif

        </div>
        <div class="navbar-menu-wrapper d-flex align-items-center justify-content-end">
            <button class="navbar-toggler navbar-toggler align-self-center" type="button" data-toggle="minimize">
                <span class="icon-menu"></span>
            </button>
            <ul class="navbar-nav mr-lg-2">
                <li class="nav-item nav-search d-none d-lg-block">
                    <div class="input-group">
                        <div class="input-group-prepend hover-cursor" id="navbar-search-icon">
                            <span class="input-group-text" id="search">
                                <i class="icon-search"></i>
                            </span>
                        </div>
                        <input type="text" class="form-control" id="navbar-search-input" placeholder="Search now"
                            aria-label="search" aria-describedby="search">
                    </div>
                </li>
            </ul>
            <ul class="navbar-nav navbar-nav-right">
                {{-- Notification Bell --}}
                {{-- Notifications are filtered by admin type: superadmin sees all, vendor sees only their own --}}
                <li class="nav-item dropdown">
                    <a class="nav-link count-indicator dropdown-toggle" id="notificationDropdown" href="#"
                        data-toggle="dropdown" aria-expanded="false">
                        <i class="ti-bell"></i>
                        <span class="count" id="notificationCount" style="display: none;">0</span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right navbar-dropdown preview-list"
                        aria-labelledby="notificationDropdown">
                        <p class="mb-0 font-weight-normal float-left dropdown-header">
                            @if (Auth::guard('admin')->user()->type === 'vendor')
                                My Notifications
                            @else
                                Notifications
                            @endif
                        </p>
                        <div id="notificationsList">
                            <div class="dropdown-item preview-item">
                                <div class="preview-thumbnail">
                                    <div class="preview-icon bg-info">
                                        <i class="ti-info-alt mx-0"></i>
                                    </div>
                                </div>
                                <div class="preview-item-content">
                                    <h6 class="preview-subject font-weight-normal">Loading...</h6>
                                    <p class="font-weight-light small-text mb-0 text-muted">Please wait</p>
                                </div>
                            </div>
                        </div>
                        <div class="dropdown-divider"></div>
                        @if (Auth::guard('admin')->user()->type === 'vendor')
                            <a class="dropdown-item preview-item" href="{{ url('vendor/notifications') }}">
                                <p class="mb-0 font-weight-normal float-left">See all notifications</p>
                                <span class="float-right">
                                    <i class="ti-arrow-right"></i>
                                </span>
                            </a>
                        @else
                            <a class="dropdown-item preview-item" href="{{ url('admin/notifications') }}">
                                <p class="mb-0 font-weight-normal float-left">See all notifications</p>
                                <span class="float-right">
                                    <i class="ti-arrow-right"></i>
                                </span>
                            </a>
                        @endif
                    </div>
                </li>
                <li class="nav-item nav-profile dropdown">
                    <a class="nav-link dropdown-toggle" href="{{ url('admin/update-admin-details') }}"
                        data-toggle="dropdown" id="profileDropdown">


                        {{-- Show the admin image if exists --}}
                        @if (!empty(Auth::guard('admin')->user()->image))
                            {{-- Accessing Specific Guard Instances: https://laravel.com/docs/9.x/authentication#accessing-specific-guard-instances --}}
                            <img src="{{ url('admin/images/photos/' . Auth::guard('admin')->user()->image) }}"
                                alt="profile"> {{-- Accessing Specific Guard Instances: https://laravel.com/docs/9.x/authentication#accessing-specific-guard-instances --}}
                        @else
                            <img src="{{ url('admin/images/photos/no-image.gif') }}" alt="profile">
                        @endif


                    </a>
                    <div class="dropdown-menu dropdown-menu-right navbar-dropdown" aria-labelledby="profileDropdown">
                        <div class="dropdown-item-text">
                            <strong>{{ Auth::guard('admin')->user()->name }}</strong>
                            @if (Auth::guard('admin')->user()->type === 'vendor')
                                @php
                                    $vendor = \App\Models\Vendor::find(Auth::guard('admin')->user()->vendor_id);
                                @endphp
                                @if ($vendor)
                                    @if ($vendor->plan === 'pro')
                                        <span class="badge badge-primary ml-2"
                                            style="font-size: 0.65rem; padding: 0.25rem 0.5rem;">PRO</span>
                                    @else
                                        <span class="badge badge-secondary ml-2"
                                            style="font-size: 0.65rem; padding: 0.25rem 0.5rem;">FREE</span>
                                    @endif
                                @endif
                            @endif
                        </div>
                        <div class="dropdown-divider"></div>
                        <a href="{{ url('admin/update-admin-details') }}" class="dropdown-item">
                            <i class="ti-settings text-primary"></i>
                            Details setting
                        </a>
                        @if (Auth::guard('admin')->user()->type === 'vendor')
                            <a href="{{ route('vendor.plan.manage') }}" class="dropdown-item">
                                <i class="ti-crown text-warning"></i>
                                Plan Management
                            </a>
                        @endif
                        <form action="{{ route('admin.logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="dropdown-item">
                                <i class="ti-power-off text-primary"></i>
                                Logout
                            </button>
                        </form>

                    </div>
                </li>
            </ul>
            <button class="navbar-toggler navbar-toggler-right d-lg-none align-self-center" type="button"
                data-toggle="offcanvas">
                <span class="icon-menu"></span>
            </button>
        </div>
    </nav>
@elseif (Auth::guard('admin')->user()->type == 'vendor')
    <nav class="navbar col-lg-12 col-12 p-0 fixed-top d-flex flex-row">
        <div class="text-center navbar-brand-wrapper d-flex align-items-center justify-content-center">
            @if (!empty($logos))
                <img src="{{ asset('uploads/logos/' . $logos->first()->logo) }}" alt="" height="50"
                    width="150">
            @endif
        </div>
        <div class="navbar-menu-wrapper d-flex align-items-center justify-content-end">
            <button class="navbar-toggler navbar-toggler align-self-center" type="button" data-toggle="minimize">
                <span class="icon-menu"></span>
            </button>
            <ul class="navbar-nav mr-lg-2">
                <li class="nav-item nav-search d-none d-lg-block">
                    <div class="input-group">
                        <div class="input-group-prepend hover-cursor" id="navbar-search-icon">
                            <span class="input-group-text" id="search">
                                <i class="icon-search"></i>
                            </span>
                        </div>
                        <input type="text" class="form-control" id="navbar-search-input" placeholder="Search now"
                            aria-label="search" aria-describedby="search">
                    </div>
                </li>
            </ul>
            <ul class="navbar-nav navbar-nav-right">
                {{-- Notification Bell --}}
                {{-- Notifications are filtered by admin type: superadmin sees all, vendor sees only their own --}}
                <li class="nav-item dropdown">
                    <a class="nav-link count-indicator dropdown-toggle" id="notificationDropdown" href="#"
                        data-toggle="dropdown" aria-expanded="false">
                        <i class="ti-bell"></i>
                        <span class="count" id="notificationCount" style="display: none;">0</span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right navbar-dropdown preview-list"
                        aria-labelledby="notificationDropdown">
                        <p class="mb-0 font-weight-normal float-left dropdown-header">
                            @if (Auth::guard('admin')->user()->type === 'vendor')
                                My Notifications
                            @else
                                Notifications
                            @endif
                        </p>
                        <div id="notificationsList">
                            <div class="dropdown-item preview-item">
                                <div class="preview-thumbnail">
                                    <div class="preview-icon bg-info">
                                        <i class="ti-info-alt mx-0"></i>
                                    </div>
                                </div>
                                <div class="preview-item-content">
                                    <h6 class="preview-subject font-weight-normal">Loading...</h6>
                                    <p class="font-weight-light small-text mb-0 text-muted">Please wait</p>
                                </div>
                            </div>
                        </div>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item preview-item" href="{{ url('vendor/notifications') }}">
                            <p class="mb-0 font-weight-normal float-left">See all notifications</p>
                            <span class="float-right">
                                <i class="ti-arrow-right"></i>
                            </span>
                        </a>
                    </div>
                </li>
                <li class="nav-item nav-profile dropdown">
                    <a class="nav-link dropdown-toggle" href="{{ url('vendor/update-admin-details') }}"
                        data-toggle="dropdown" id="profileDropdown">


                        {{-- Show the admin image if exists --}}
                        @if (!empty(Auth::guard('admin')->user()->image))
                            {{-- Accessing Specific Guard Instances: https://laravel.com/docs/9.x/authentication#accessing-specific-guard-instances --}}
                            <img src="{{ url('admin/images/photos/' . Auth::guard('admin')->user()->image) }}"
                                alt="profile"> {{-- Accessing Specific Guard Instances: https://laravel.com/docs/9.x/authentication#accessing-specific-guard-instances --}}
                        @else
                            <img src="{{ url('admin/images/photos/no-image.gif') }}" alt="profile">
                        @endif


                    </a>
                    <div class="dropdown-menu dropdown-menu-right navbar-dropdown" aria-labelledby="profileDropdown">
                        <div class="dropdown-item-text">
                            <strong>{{ Auth::guard('admin')->user()->name }}</strong>
                            @if (Auth::guard('admin')->user()->type === 'vendor')
                                @php
                                    $vendor = \App\Models\Vendor::find(Auth::guard('admin')->user()->vendor_id);
                                @endphp
                                @if ($vendor)
                                    @if ($vendor->plan === 'pro')
                                        <span class="badge badge-primary ml-2"
                                            style="font-size: 0.65rem; padding: 0.25rem 0.5rem;">PRO</span>
                                    @else
                                        <span class="badge badge-secondary ml-2"
                                            style="font-size: 0.65rem; padding: 0.25rem 0.5rem;">FREE</span>
                                    @endif
                                @endif
                            @endif
                        </div>
                        <div class="dropdown-divider"></div>
                        <a href="{{ url('vendor/update-vendor-details') }}" class="dropdown-item">
                            <i class="ti-settings text-primary"></i>
                            Details setting
                        </a>
                        @if (Auth::guard('admin')->user()->type === 'vendor')
                            <a href="{{ route('vendor.plan.manage') }}" class="dropdown-item">
                                <i class="ti-crown text-warning"></i>
                                Plan Management
                            </a>
                        @endif
                        <form action="{{ route('vendor.logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="dropdown-item">
                                <i class="ti-power-off text-primary"></i>
                                Logout
                            </button>
                        </form>
                    </div>
                </li>
            </ul>
            <button class="navbar-toggler navbar-toggler-right d-lg-none align-self-center" type="button"
                data-toggle="offcanvas">
                <span class="icon-menu"></span>
            </button>
        </div>
    </nav>
@endif
