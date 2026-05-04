<!-- partial:partials/_sidebar.html -->
<style>
    /* Professional Sidebar Styling Override (Mazer Theme) */
    .sidebar {
        background: #ffffff !important;
        font-family: 'Nunito', sans-serif;
        box-shadow: 2px 0 10px rgba(0, 0, 0, 0.03);
    }

    .sidebar .nav {
        padding-top: 15px;
    }

    .sidebar .nav .nav-item {
        margin-bottom: 5px;
    }

    .sidebar .nav .nav-item .nav-link {
        color: #5a6a85 !important;
        font-weight: 600;
        border-radius: 8px !important;
        margin: 0 15px;
        padding: 12px 15px !important;
        transition: all 0.3s ease;
    }

    .sidebar .nav .nav-item .nav-link:hover {
        background: #90b7f5 !important;
        color: #435ebe !important;
    }

    .sidebar .menu-icon {
        color: #798bff !important;
        font-size: 1.25rem !important;
        margin-right: 15px !important;
    }

    /* Override old dark blue active main menus */
    .sidebar .nav .nav-item>a[style*="052CA3" i] {
        background: #435ebe !important;
        color: #ffffff !important;
        box-shadow: 0 3px 8px rgba(67, 94, 190, 0.25) !important;
    }

    .sidebar .nav .nav-item>a[style*="052CA3" i] .menu-icon {
        color: #ffffff !important;
    }

    /* Sub-menu background override */
    .sidebar .nav.sub-menu[style*="fff" i],
    .sidebar .nav.sub-menu {
        background: transparent !important;
        padding: 0 0 0 15px !important;
        border: none !important;
    }

    .sidebar .nav.sub-menu .nav-item .nav-link {
        margin: 4px 15px;
        padding: 10px 15px !important;
        font-size: 0.9rem !important;
        font-weight: 600 !important;
        border-radius: 8px !important;
    }

    /* Override old inactive sub-menus */
    .sidebar .nav.sub-menu .nav-item>a[style*="fff" i] {
        background: transparent !important;
        color: #4b5563 !important;
    }

    .sidebar .nav.sub-menu .nav-item>a[style*="fff" i]:hover {
        background: #f2f7ff !important;
        color: #435ebe !important;
    }

    /* Override old active sub-menus */
    .sidebar .nav.sub-menu .nav-item>a[style*="052CA3" i] {
        background: #f2f7ff !important;
        color: #435ebe !important;
        box-shadow: none !important;
    }

    .sidebar .menu-title {
        font-size: 1rem;
        letter-spacing: 0.3px;
    }
</style>
<nav class="sidebar sidebar-offcanvas" id="sidebar">
    <ul class="nav">

        {{-- In case the authenticated user (the logged-in user) (using the 'admin' Authentication Guard in auth.php) type is 'vendor' --}}
        @if (Auth::guard('admin')->user()->type == 'vendor')
            <li class="nav-item">
                <a @if (Session::get('page') == 'dashboard') style="background: #052CA3 !important; color: #FFF !important" @endif
                    class="nav-link" href="{{ route('vendor.dashboard') }}">
                    <i class="icon-grid menu-icon"></i>
                    <span class="menu-title">Dashboard</span>
                </a>
            </li>

            <li class="nav-item">
                <a @if (Session::get('page') == 'update_personal_details' ||
                        Session::get('page') == 'update_business_details' ||
                        Session::get('page') == 'update_bank_details') style="background: #052CA3 !important; color: #FFF !important" @endif
                    class="nav-link" data-toggle="collapse" href="#ui-vendors" aria-expanded="false"
                    aria-controls="ui-vendors">
                    <i class="icon-head menu-icon"></i>
                    <span class="menu-title">Vendor Details</span>
                    <i class="menu-arrow"></i>
                </a>
                <div class="collapse" id="ui-vendors">
                    <ul class="nav flex-column sub-menu" style="background: #fff !important; color: #052CA3 !important">
                        <li class="nav-item"> <a
                                @if (Session::get('page') == 'update_personal_details') style="background: #052CA3 !important; color: #FFF !important" @else style="background: #fff !important; color: #052CA3 !important" @endif
                                class="nav-link" href="{{ url('vendor/update-vendor-details/personal') }}">Personal
                                Details</a></li>
                        <li class="nav-item"> <a
                                @if (Session::get('page') == 'update_business_details') style="background: #052CA3 !important; color: #FFF !important" @else style="background: #fff !important; color: #052CA3 !important" @endif
                                class="nav-link" href="{{ url('vendor/update-vendor-details/business') }}">Business
                                Details</a></li>
                        <li class="nav-item"> <a
                                @if (Session::get('page') == 'update_bank_details') style="background: #052CA3 !important; color: #FFF !important" @else style="background: #fff !important; color: #052CA3 !important" @endif
                                class="nav-link" href="{{ url('vendor/update-vendor-details/bank') }}">Bank Details</a>
                        </li>
                    </ul>
                </div>
            </li>


            <li class="nav-item">
                <a @if (Session::get('page') == 'education-levels' ||
                        Session::get('page') == 'categories' ||
                        Session::get('page') == 'products' ||
                        Session::get('page') == 'ebooks' ||
                        Session::get('page') == 'publisher' ||
                        // Session::get('page') == 'filters' ||
                        Session::get('page') == 'authors' ||
                        Session::get('page') == 'subjects' ||
                        Session::get('page') == 'requestedbooks' ||
                        Session::get('page') == 'coupons') style="background: #052CA3 !important; color: #FFF !important" @endif
                    class="nav-link" data-toggle="collapse" href="#ui-catalogue" aria-expanded="false"
                    aria-controls="ui-catalogue">
                    <i class="icon-book menu-icon"></i>
                    <span class="menu-title">Catalogue Management</span>
                    <i class="menu-arrow"></i>
                </a>
                <div class="collapse" id="ui-catalogue">
                    <ul class="nav flex-column sub-menu" style="background: #fff !important; color: #052CA3 !important">
                        {{-- <li class="nav-item"> <a
                                @if (Session::get('page') == 'sections') style="background: #052CA3 !important; color: #FFF !important" @else style="background: #fff !important; color: #052CA3 !important" @endif
                                class="nav-link" href="{{ url('vendor/sections') }}">Sections</a></li>
                        <li class="nav-item"> <a
                                @if (Session::get('page') == 'categories') style="background: #052CA3 !important; color: #FFF !important" @else style="background: #fff !important; color: #052CA3 !important" @endif
                                class="nav-link" href="{{ url('vendor/categories') }}">Categories</a></li> --}}
                        {{-- <li class="nav-item"> <a
                                @if (Session::get('page') == 'publisher') style="background: #052CA3 !important; color: #FFF !important" @else style="background: #fff !important; color: #052CA3 !important" @endif
                                class="nav-link" href="{{ url('vendor/publisher') }}">Publisher</a></li>
                        <li class="nav-item"> <a
                                @if (Session::get('page') == 'authors') style="background: #052CA3 !important; color: #FFF !important" @else style="background: #fff !important; color: #052CA3 !important" @endif
                                class="nav-link" href="{{ url('vendor/authors') }}">Author</a></li> --}}
                        {{-- <li class="nav-item"> <a
                                @if (Session::get('page') == 'subjects') style="background: #052CA3 !important; color: #FFF !important" @else style="background: #fff !important; color: #052CA3 !important" @endif
                                class="nav-link" href="{{ url('vendor/subjects') }}">Subject</a></li>
                        <li class="nav-item"> <a
                                @if (Session::get('page') == 'languages') style="background: #052CA3 !important; color: #FFF !important" @else style="background: #fff !important; color: #052CA3 !important" @endif
                                class="nav-link" href="{{ url('vendor/languages') }}">Book Languages</a></li> --}}
                        @if (Auth::guard('admin')->user()->can('view_products'))
                            <li class="nav-item"> <a
                                    @if (Session::get('page') == 'products') style="background: #052CA3 !important; color: #FFF !important" @else style="background: #fff !important; color: #052CA3 !important" @endif
                                    class="nav-link" href="{{ url('vendor/products') }}">Products</a></li>
                        @endif
                        {{-- <li class="nav-item"> <a
                                @if (Session::get('page') == 'edition') style="background: #052CA3 !important; color: #FFF !important" @else style="background: #fff !important; color: #052CA3 !important" @endif
                                class="nav-link" href="{{ url('vendor/edition') }}">Edition</a></li> --}}
                        {{-- <li class="nav-item"> <a
                                @if (Session::get('page') == 'ebooks') style="background: #052CA3 !important; color: #FFF !important" @else style="background: #fff !important; color: #052CA3 !important" @endif
                                class="nav-link" href="{{ url('vendor/ebooks') }}">Ebooks</a></li> --}}
                        @if (Auth::guard('admin')->user()->can('view_coupons'))
                            <li class="nav-item"> <a
                                    @if (Session::get('page') == 'coupons') style="background: #052CA3 !important; color: #FFF !important" @else style="background: #fff !important; color: #052CA3 !important" @endif
                                    class="nav-link" href="{{ url('vendor/coupons') }}">Coupons</a></li>
                        @endif
                        {{-- <li class="nav-item"> <a
                                @if (Session::get('page') == 'filters') style="background: #052CA3 !important; color: #FFF !important" @else style="background: #fff !important; color: #052CA3 !important" @endif
                                class="nav-link" href="{{ url('vendor/filters') }}">Filters</a></li> --}}
                        @if (Auth::guard('admin')->user()->can('view_requested_books'))
                            <li class="nav-item"> <a
                                    @if (Session::get('page') == 'bookRequests') style="background: #052CA3 !important; color: #FFF !important" @else style="background: #fff !important; color: #052CA3 !important" @endif
                                    class="nav-link" href="{{ url('vendor/requestedbooks') }}">Requested Books</a></li>
                        @endif
                    </ul>
                </div>
            </li>

            {{-- If the authenticated/logged-in user is 'vendor', show ONLY the orders of the products added by that specific 'vendor' (In constrast to the case where the authenticated/logged-in user is 'admin', we show ALL orders) --}}
            <li class="nav-item">
                <a @if (Session::get('page') == 'orders') style="background: #052CA3 !important; color: #FFF !important" @endif
                    class="nav-link" data-toggle="collapse" href="#ui-orders" aria-expanded="false"
                    aria-controls="ui-orders">
                    <i class="icon-bag menu-icon"></i>
                    <span class="menu-title">Orders Management</span>
                    <i class="menu-arrow"></i>
                </a>
                <div class="collapse" id="ui-orders">
                    <ul class="nav flex-column sub-menu" style="background: #fff !important; color: #052CA3 !important">
                        @if (Auth::guard('admin')->user()->can('view_orders'))
                            <li class="nav-item"> <a
                                    @if (Session::get('page') == 'orders') style="background: #052CA3 !important; color: #FFF !important" @else style="background: #fff !important; color: #052CA3 !important" @endif
                                    class="nav-link" href="{{ url('vendor/orders') }}">Orders</a></li>
                            <li class="nav-item"> <a
                                    @if (Session::get('page') == 'returns') style="background: #052CA3 !important; color: #FFF !important" @else style="background: #fff !important; color: #052CA3 !important" @endif
                                    class="nav-link" href="{{ url('vendor/returns') }}">Returns</a></li>
                        @endif
                        @if (Auth::guard('admin')->user()->can('view_sales_concept'))
                            <li class="nav-item"> <a
                                    @if (Session::get('page') == 'sales_concept') style="background: #052CA3 !important; color: #FFF !important" @else style="background: #fff !important; color: #052CA3 !important" @endif
                                    class="nav-link" href="{{ url('vendor/sales-concept') }}">Sales Concept</a></li>
                        @endif
                        <li class="nav-item"> <a
                                @if (Session::get('page') == 'order_queries') style="background: #052CA3 !important; color: #FFF !important" @else style="background: #fff !important; color: #052CA3 !important" @endif
                                class="nav-link" href="{{ url('vendor/order-queries') }}">Order Queries</a></li>
                    </ul>
                </div>
            </li>
        @else
            {{-- In case the authenticated user (the logged-in user) (using the 'admin' Authentication Guard in auth.php) type is 'superadmin', or 'admin', or 'subadmin' --}}

            <li class="nav-item">
                <a @if (Session::get('page') == 'dashboard') style="background: #052CA3 !important; color: #FFF !important" @endif
                    class="nav-link" href="{{ route('admin.dashboard') }}">
                    <i class="icon-grid menu-icon"></i>
                    <span class="menu-title">Dashboard</span>
                </a>
            </li>

            {{-- Setting --}}
            <li class="nav-item">
                <a @if (Session::get('page') == 'logo' || Session::get('page') == 'favicon' || Session::get('page') == 'coming_soon_settings' || Session::get('page') == 'roles' || Session::get('page') == 'push_notifications') style="background: #052CA3 !important; color: #FFF !important" @endif
                    class="nav-link" data-toggle="collapse" href="#ui-settings" aria-expanded="false"
                    aria-controls="ui-settings">
                    <i class="icon-cog menu-icon"></i>
                    <span class="menu-title">Settings</span>
                    <i class="menu-arrow"></i>
                </a>
                <div class="collapse" id="ui-settings">
                    <ul class="nav flex-column sub-menu" style="background: #fff !important; color: #052CA3 !important">
                        <li class="nav-item"> <a
                                @if (Session::get('page') == 'logo') style="background: #052CA3 !important; color: #FFF !important" @else style="background: #fff !important; color: #052CA3 !important" @endif
                                class="nav-link" href="{{ route('admin.logo') }}">Logo</a>
                        </li>
                        <li class="nav-item"> <a
                                @if (Session::get('page') == 'favicon') style="background: #052CA3 !important; color: #FFF !important" @else style="background: #fff !important; color: #052CA3 !important" @endif
                                class="nav-link" href="{{ url('admin/favicon') }}">Favicon</a>
                        </li>
                        <li class="nav-item"> <a
                                @if (Session::get('page') == 'coming_soon_settings') style="background: #052CA3 !important; color: #FFF !important" @else style="background: #fff !important; color: #052CA3 !important" @endif
                                class="nav-link" href="{{ route('admin.coming.soon.settings') }}">Coming Soon</a>
                        </li>
                        @can('view_roles')
                            <li class="nav-item"> <a
                                    @if (Session::get('page') == 'roles') style="background: #052CA3 !important; color: #FFF !important" @else style="background: #fff !important; color: #052CA3 !important" @endif
                                    class="nav-link" href="{{ route('admin.roles.index') }}">Role & Permission</a></li>
                        @endcan
                        <li class="nav-item"> <a
                                @if (Session::get('page') == 'push_notifications') style="background: #052CA3 !important; color: #FFF !important" @else style="background: #fff !important; color: #052CA3 !important" @endif
                                class="nav-link" href="{{ route('admin.push-notifications.create') }}">Push Notification</a></li>
                    </ul>
                </div>
            </li>

            {{-- Report --}}
            <li class="nav-item">
                <a @if (Session::get('page') == 'sales_reports' || Session::get('page') == 'stock_report') style="background: #052CA3 !important; color: #FFF !important" @endif
                    class="nav-link" data-toggle="collapse" href="#ui-reports" aria-expanded="false"
                    aria-controls="ui-reports">
                    <i class="icon-bar-graph menu-icon"></i>
                    <span class="menu-title">Report</span>
                    <i class="menu-arrow"></i>
                </a>
                <div class="collapse" id="ui-reports">
                    <ul class="nav flex-column sub-menu" style="background: #fff !important; color: #052CA3 !important">
                        @can('view_reports')
                            <li class="nav-item"> <a
                                    @if (Session::get('page') == 'sales_reports') style="background: #052CA3 !important; color: #FFF !important" @else style="background: #fff !important; color: #052CA3 !important" @endif
                                    class="nav-link" href="{{ url('admin/reports/sales_reports') }}">Sales Report</a></li>
                            <li class="nav-item"> <a
                                    @if (Session::get('page') == 'stock_report') style="background: #052CA3 !important; color: #FFF !important" @else style="background: #fff !important; color: #052CA3 !important" @endif
                                    class="nav-link" href="{{ route('admin.reports.stock_report') }}">Stock Report</a></li>
                        @endcan
                    </ul>
                </div>
            </li>

            {{-- Admin Management --}}
            <li class="nav-item">
                <a @if (Session::get('page') == 'view_admins' || Session::get('page') == 'update_admin_password' || Session::get('page') == 'update_admin_details') style="background: #052CA3 !important; color: #FFF !important" @endif
                    class="nav-link" data-toggle="collapse" href="#ui-admin-mgmt" aria-expanded="false"
                    aria-controls="ui-admin-mgmt">
                    <i class="icon-head menu-icon"></i>
                    <span class="menu-title">Admin Management</span>
                    <i class="menu-arrow"></i>
                </a>
                <div class="collapse" id="ui-admin-mgmt">
                    <ul class="nav flex-column sub-menu" style="background: #fff !important; color: #052CA3 !important">
                        @can('view_admins')
                            <li class="nav-item"> <a
                                    @if (Session::get('page') == 'view_admins') style="background: #052CA3 !important; color: #FFF !important" @else style="background: #fff !important; color: #052CA3 !important" @endif
                                    class="nav-link" href="{{ url('admin/admins/admin') }}">Admins</a></li>
                        @endcan
                        <li class="nav-item"> <a
                                @if (Session::get('page') == 'update_admin_password') style="background: #052CA3 !important; color: #FFF !important" @else style="background: #fff !important; color: #052CA3 !important" @endif
                                class="nav-link" href="{{ url('admin/update-admin-password') }}">Update Admin Password</a></li>
                        <li class="nav-item"> <a
                                @if (Session::get('page') == 'update_admin_details') style="background: #052CA3 !important; color: #FFF !important" @else style="background: #fff !important; color: #052CA3 !important" @endif
                                class="nav-link" href="{{ url('admin/update-admin-details') }}">Update Admin Details</a></li>
                    </ul>
                </div>
            </li>

            {{-- Vendor Management --}}
            <li class="nav-item">
                <a @if (Session::get('page') == 'view_vendors' || Session::get('page') == 'plan_settings' || Session::get('page') == 'sales_concept') style="background: #052CA3 !important; color: #FFF !important" @endif
                    class="nav-link" data-toggle="collapse" href="#ui-vendor-mgmt" aria-expanded="false"
                    aria-controls="ui-vendor-mgmt">
                    <i class="icon-briefcase menu-icon"></i>
                    <span class="menu-title">Vendor Management</span>
                    <i class="menu-arrow"></i>
                </a>
                <div class="collapse" id="ui-vendor-mgmt">
                    <ul class="nav flex-column sub-menu" style="background: #fff !important; color: #052CA3 !important">
                        @can('view_vendors')
                            <li class="nav-item"> <a
                                    @if (Session::get('page') == 'view_vendors') style="background: #052CA3 !important; color: #FFF !important" @else style="background: #fff !important; color: #052CA3 !important" @endif
                                    class="nav-link" href="{{ url('admin/admins/vendor') }}">Vendors</a></li>
                        @endcan
                        <li class="nav-item"> <a
                                @if (Session::get('page') == 'plan_settings') style="background: #052CA3 !important; color: #FFF !important" @else style="background: #fff !important; color: #052CA3 !important" @endif
                                class="nav-link" href="{{ route('admin.plan.settings') }}">Vendor Plan Settings</a></li>
                        @can('view_sales_concept')
                            <li class="nav-item"> <a
                                    @if (Session::get('page') == 'sales_concept') style="background: #052CA3 !important; color: #FFF !important" @else style="background: #fff !important; color: #052CA3 !important" @endif
                                    class="nav-link" href="{{ url('admin/sales-concept') }}">Sales Concepts</a></li>
                        @endcan
                    </ul>
                </div>
            </li>

            {{-- Sales Management --}}
            <li class="nav-item">
                <a @if (Session::get('page') == 'view_sales') style="background: #052CA3 !important; color: #FFF !important" @endif
                    class="nav-link" data-toggle="collapse" href="#ui-sales-mgmt" aria-expanded="false"
                    aria-controls="ui-sales-mgmt">
                    <i class="icon-layout menu-icon"></i>
                    <span class="menu-title">Sales Management</span>
                    <i class="menu-arrow"></i>
                </a>
                <div class="collapse" id="ui-sales-mgmt">
                    <ul class="nav flex-column sub-menu" style="background: #fff !important; color: #052CA3 !important">
                        @can('view_sales')
                            <li class="nav-item"> <a
                                    @if (Session::get('page') == 'view_sales') style="background: #052CA3 !important; color: #FFF !important" @else style="background: #fff !important; color: #052CA3 !important" @endif
                                    class="nav-link" href="{{ url('admin/sales-executive') }}">Sales</a></li>
                        @endcan
                    </ul>
                </div>
            </li>

            {{-- Delivery Management --}}
            <li class="nav-item">
                <a @if (Session::get('page') == 'view_delivery_agents') style="background: #052CA3 !important; color: #FFF !important" @endif
                    class="nav-link" data-toggle="collapse" href="#ui-delivery-mgmt" aria-expanded="false"
                    aria-controls="ui-delivery-mgmt">
                    <i class="icon-location menu-icon"></i>
                    <span class="menu-title">Delivery Management</span>
                    <i class="menu-arrow"></i>
                </a>
                <div class="collapse" id="ui-delivery-mgmt">
                    <ul class="nav flex-column sub-menu" style="background: #fff !important; color: #052CA3 !important">
                        <li class="nav-item"> <a
                                @if (Session::get('page') == 'view_delivery_agents') style="background: #052CA3 !important; color: #FFF !important" @else style="background: #fff !important; color: #052CA3 !important" @endif
                                class="nav-link" href="{{ url('admin/delivery-agent') }}">Delivery Agents</a></li>
                    </ul>
                </div>
            </li>

            {{-- Student Management --}}
            <li class="nav-item">
                <a @if (Session::get('page') == 'students' || Session::get('page') == 'subscribers' || Session::get('page') == 'contact_queries') style="background: #052CA3 !important; color: #FFF !important" @endif
                    class="nav-link" data-toggle="collapse" href="#ui-student-mgmt" aria-expanded="false"
                    aria-controls="ui-student-mgmt">
                    <i class="icon-paper menu-icon"></i>
                    <span class="menu-title">Student Management</span>
                    <i class="menu-arrow"></i>
                </a>
                <div class="collapse" id="ui-student-mgmt">
                    <ul class="nav flex-column sub-menu" style="background: #fff !important; color: #052CA3 !important">
                        @can('view_students')
                            <li class="nav-item"> <a
                                    @if (Session::get('page') == 'students') style="background: #052CA3 !important; color: #FFF !important" @else style="background: #fff !important; color: #052CA3 !important" @endif
                                    class="nav-link" href="{{ url('admin/students') }}">Student</a></li>
                        @endcan
                        @can('view_subscribers')
                            <li class="nav-item"> <a
                                    @if (Session::get('page') == 'subscribers') style="background: #052CA3 !important; color: #FFF !important" @else style="background: #fff !important; color: #052CA3 !important" @endif
                                    class="nav-link" href="{{ url('admin/subscribers') }}">Subscribers</a></li>
                        @endcan
                        @can('view_contact_queries')
                            <li class="nav-item"> <a
                                    @if (Session::get('page') == 'contact_queries') style="background: #052CA3 !important; color: #FFF !important" @else style="background: #fff !important; color: #052CA3 !important" @endif
                                    class="nav-link" href="{{ url('admin/contact-queries') }}">Contact Query</a></li>
                        @endcan
                    </ul>
                </div>
            </li>

            {{-- Old Book Management --}}
            <li class="nav-item">
                <a @if (Session::get('page') == 'old_book_conditions' || Session::get('page') == 'sellBookRequests' || Session::get('page') == 'old_book_commissions_crud' || Session::get('page') == 'old_book_payouts') style="background: #052CA3 !important; color: #FFF !important" @endif
                    class="nav-link" data-toggle="collapse" href="#ui-old-book-mgmt" aria-expanded="false"
                    aria-controls="ui-old-book-mgmt">
                    <i class="icon-book menu-icon"></i>
                    <span class="menu-title">Old Book Management</span>
                    <i class="menu-arrow"></i>
                </a>
                <div class="collapse" id="ui-old-book-mgmt">
                    <ul class="nav flex-column sub-menu" style="background: #fff !important; color: #052CA3 !important">
                        <li class="nav-item"> <a
                                @if (Session::get('page') == 'old_book_conditions') style="background: #052CA3 !important; color: #FFF !important" @else style="background: #fff !important; color: #052CA3 !important" @endif
                                class="nav-link" href="{{ route('admin.old_book_conditions.index') }}">Old Book Condition</a></li>
                        <li class="nav-item"> <a
                                @if (Session::get('page') == 'sellBookRequests') style="background: #052CA3 !important; color: #FFF !important" @else style="background: #fff !important; color: #052CA3 !important" @endif
                                class="nav-link" href="{{ route('admin.sell-book-requests.index') }}">Sell Old Book Request</a></li>
                        <li class="nav-item"> <a
                                @if (Session::get('page') == 'old_book_commissions_crud') style="background: #052CA3 !important; color: #FFF !important" @else style="background: #fff !important; color: #052CA3 !important" @endif
                                class="nav-link" href="{{ route('admin.old_book_commissions.index') }}">Old Book Commission</a></li>
                        <li class="nav-item"> <a
                                @if (Session::get('page') == 'old_book_payouts') style="background: #052CA3 !important; color: #FFF !important" @else style="background: #fff !important; color: #052CA3 !important" @endif
                                class="nav-link" href="{{ route('admin.old_book_payouts') }}">Old Book Payout</a></li>
                    </ul>
                </div>
            </li>

            {{-- Institution Management --}}
            <li class="nav-item">
                <a @if (Session::get('page') == 'institution_managements' || Session::get('page') == 'blocks' || Session::get('page') == 'districts' || Session::get('page') == 'countries_states') style="background: #052CA3 !important; color: #FFF !important" @endif
                    class="nav-link" data-toggle="collapse" href="#ui-institution-mgmt" aria-expanded="false"
                    aria-controls="ui-institution-mgmt">
                    <i class="icon-map menu-icon"></i>
                    <span class="menu-title">Institution Management</span>
                    <i class="menu-arrow"></i>
                </a>
                <div class="collapse" id="ui-institution-mgmt">
                    <ul class="nav flex-column sub-menu" style="background: #fff !important; color: #052CA3 !important">
                        @can('view_institutions')
                            <li class="nav-item"> <a
                                    @if (Session::get('page') == 'institution_managements') style="background: #052CA3 !important; color: #FFF !important" @else style="background: #fff !important; color: #052CA3 !important" @endif
                                    class="nav-link" href="{{ url('admin/institution-managements') }}">Institution</a></li>
                        @endcan
                        @can('view_blocks')
                            <li class="nav-item"> <a
                                    @if (Session::get('page') == 'blocks') style="background: #052CA3 !important; color: #FFF !important" @else style="background: #fff !important; color: #052CA3 !important" @endif
                                    class="nav-link" href="{{ url('admin/blocks') }}">Block</a></li>
                        @endcan
                        <li class="nav-item"> <a
                                @if (Session::get('page') == 'districts') style="background: #052CA3 !important; color: #FFF !important" @else style="background: #fff !important; color: #052CA3 !important" @endif
                                class="nav-link" href="{{ route('admin.districts.index') }}">District</a></li>
                        <li class="nav-item"> <a
                                @if (Session::get('page') == 'countries_states') style="background: #052CA3 !important; color: #FFF !important" @else style="background: #fff !important; color: #052CA3 !important" @endif
                                class="nav-link" href="{{ route('admin.countries_states.index') }}">State & Country</a></li>
                    </ul>
                </div>
            </li>

            {{-- Order Management --}}
            <li class="nav-item">
                <a @if (Session::get('page') == 'orders' || Session::get('page') == 'returns' || Session::get('page') == 'order_queries' || Session::get('page') == 'movs' || Session::get('page') == 'shipping_charges') style="background: #052CA3 !important; color: #FFF !important" @endif
                    class="nav-link" data-toggle="collapse" href="#ui-order-mgmt" aria-expanded="false"
                    aria-controls="ui-order-mgmt">
                    <i class="icon-bag menu-icon"></i>
                    <span class="menu-title">Order Management</span>
                    <i class="menu-arrow"></i>
                </a>
                <div class="collapse" id="ui-order-mgmt">
                    <ul class="nav flex-column sub-menu" style="background: #fff !important; color: #052CA3 !important">
                        @if (Auth::guard('admin')->user()->can('view_orders'))
                            <li class="nav-item"> <a
                                    @if (Session::get('page') == 'orders') style="background: #052CA3 !important; color: #FFF !important" @else style="background: #fff !important; color: #052CA3 !important" @endif
                                    class="nav-link" href="{{ url('admin/orders') }}">Orders</a></li>
                            <li class="nav-item"> <a
                                    @if (Session::get('page') == 'returns') style="background: #052CA3 !important; color: #FFF !important" @else style="background: #fff !important; color: #052CA3 !important" @endif
                                    class="nav-link" href="{{ url('admin/returns') }}">Returns</a></li>
                        @endif
                        <li class="nav-item"> <a
                                @if (Session::get('page') == 'order_queries') style="background: #052CA3 !important; color: #FFF !important" @else style="background: #fff !important; color: #052CA3 !important" @endif
                                class="nav-link" href="{{ url('admin/order-queries') }}">Order Queries</a></li>
                        <li class="nav-item"> <a
                                @if (Session::get('page') == 'movs') style="background: #052CA3 !important; color: #FFF !important" @else style="background: #fff !important; color: #052CA3 !important" @endif
                                class="nav-link" href="{{ url('admin/movs') }}">MOV Changes</a></li>
                        <li class="nav-item"> <a
                                @if (Session::get('page') == 'shipping_charges') style="background: #052CA3 !important; color: #FFF !important" @else style="background: #fff !important; color: #052CA3 !important" @endif
                                class="nav-link" href="{{ url('admin/shipping-charges') }}">Shipping Charges</a></li>
                    </ul>
                </div>
            </li>

            {{-- Catalogue Management (Remaining Items) --}}
            <li class="nav-item">
                <a @if (Session::get('page') == 'education-levels' || Session::get('page') == 'categories' || Session::get('page') == 'subcategories' || Session::get('page') == 'publisher' || Session::get('page') == 'authors' || Session::get('page') == 'subjects' || Session::get('page') == 'class_subjects' || Session::get('page') == 'languages' || Session::get('page') == 'types' || Session::get('page') == 'products' || Session::get('page') == 'edition' || Session::get('page') == 'coupons' || Session::get('page') == 'bookRequests') style="background: #052CA3 !important; color: #FFF !important" @endif
                    class="nav-link" data-toggle="collapse" href="#ui-catalogue" aria-expanded="false"
                    aria-controls="ui-catalogue">
                    <i class="icon-book menu-icon"></i>
                    <span class="menu-title">Catalogue Management</span>
                    <i class="menu-arrow"></i>
                </a>
                <div class="collapse" id="ui-catalogue">
                    <ul class="nav flex-column sub-menu" style="background: #fff !important; color: #052CA3 !important">
                        @can('view_sections')
                            <li class="nav-item"> <a
                                    @if (Session::get('page') == 'education-levels') style="background: #052CA3 !important; color: #FFF !important" @else style="background: #fff !important; color: #052CA3 !important" @endif
                                    class="nav-link" href="{{ url('admin/education-levels') }}">Education Levels</a></li>
                        @endcan
                        @can('view_categories')
                            <li class="nav-item"> <a
                                    @if (Session::get('page') == 'categories') style="background: #052CA3 !important; color: #FFF !important" @else style="background: #fff !important; color: #052CA3 !important" @endif
                                    class="nav-link" href="{{ url('admin/categories') }}">Board</a></li>
                            <li class="nav-item"> <a
                                    @if (Session::get('page') == 'subcategories') style="background: #052CA3 !important; color: #FFF !important" @else style="background: #fff !important; color: #052CA3 !important" @endif
                                    class="nav-link" href="{{ url('admin/subcategories') }}">Class</a></li>
                        @endcan
                        @can('view_publishers')
                            <li class="nav-item"> <a
                                    @if (Session::get('page') == 'publisher') style="background: #052CA3 !important; color: #FFF !important" @else style="background: #fff !important; color: #052CA3 !important" @endif
                                    class="nav-link" href="{{ url('admin/publisher') }}">Publisher</a></li>
                        @endcan
                        @can('view_authors')
                            <li class="nav-item"> <a
                                    @if (Session::get('page') == 'authors') style="background: #052CA3 !important; color: #FFF !important" @else style="background: #fff !important; color: #052CA3 !important" @endif
                                    class="nav-link" href="{{ url('admin/authors') }}">Author</a></li>
                        @endcan
                        @can('view_subjects')
                            <li class="nav-item"> <a
                                    @if (Session::get('page') == 'subjects') style="background: #052CA3 !important; color: #FFF !important" @else style="background: #fff !important; color: #052CA3 !important" @endif
                                    class="nav-link" href="{{ url('admin/subjects') }}">Subject</a></li>
                            <li class="nav-item"> <a
                                    @if (Session::get('page') == 'class_subjects') style="background: #052CA3 !important; color: #FFF !important" @else style="background: #fff !important; color: #052CA3 !important" @endif
                                    class="nav-link" href="{{ route('admin.class_subjects.index') }}">Class Subjects</a></li>
                        @endcan
                        @can('view_languages')
                            <li class="nav-item"> <a
                                    @if (Session::get('page') == 'languages') style="background: #052CA3 !important; color: #FFF !important" @else style="background: #fff !important; color: #052CA3 !important" @endif
                                    class="nav-link" href="{{ url('admin/languages') }}">Book Languages</a></li>
                        @endcan
                        @can('view_types')
                            <li class="nav-item"> <a
                                    @if (Session::get('page') == 'types') style="background: #052CA3 !important; color: #FFF !important" @else style="background: #fff !important; color: #052CA3 !important" @endif
                                    class="nav-link" href="{{ url('admin/types') }}">Book Types</a></li>
                        @endcan
                        @can('view_products')
                            <li class="nav-item"> <a
                                    @if (Session::get('page') == 'products') style="background: #052CA3 !important; color: #FFF !important" @else style="background: #fff !important; color: #052CA3 !important" @endif
                                    class="nav-link" href="{{ url('admin/products') }}">Products</a></li>
                        @endcan
                        @can('view_editions')
                            <li class="nav-item"> <a
                                    @if (Session::get('page') == 'edition') style="background: #052CA3 !important; color: #FFF !important" @else style="background: #fff !important; color: #052CA3 !important" @endif
                                    class="nav-link" href="{{ url('admin/edition') }}">Edition</a></li>
                        @endcan
                        @can('view_coupons')
                            <li class="nav-item"> <a
                                    @if (Session::get('page') == 'coupons') style="background: #052CA3 !important; color: #FFF !important" @else style="background: #fff !important; color: #052CA3 !important" @endif
                                    class="nav-link" href="{{ url('admin/coupons') }}">Coupons</a></li>
                        @endcan
                        @can('view_requested_books')
                            <li class="nav-item"> <a
                                    @if (Session::get('page') == 'bookRequests') style="background: #052CA3 !important; color: #FFF !important" @else style="background: #fff !important; color: #052CA3 !important" @endif
                                    class="nav-link" href="{{ url('admin/requestedbooks') }}">Requested Books</a></li>
                        @endcan
                    </ul>
                </div>
            </li>

            {{-- Other Managements --}}
            <li class="nav-item">
                <a @if (Session::get('page') == 'ratings' || Session::get('page') == 'withdrawals' || Session::get('page') == 'banners' || Session::get('page') == 'dynamic_modals' || Session::get('page') == 'commission_settings') style="background: #052CA3 !important; color: #FFF !important" @endif
                    class="nav-link" data-toggle="collapse" href="#ui-others" aria-expanded="false"
                    aria-controls="ui-others">
                    <i class="icon-layers menu-icon"></i>
                    <span class="menu-title">Other Managements</span>
                    <i class="menu-arrow"></i>
                </a>
                <div class="collapse" id="ui-others">
                    <ul class="nav flex-column sub-menu" style="background: #fff !important; color: #052CA3 !important">
                        @can('view_ratings')
                            <li class="nav-item"> <a
                                    @if (Session::get('page') == 'ratings') style="background: #052CA3 !important; color: #FFF !important" @else style="background: #fff !important; color: #052CA3 !important" @endif
                                    class="nav-link" href="{{ url('admin/ratings') }}">Product Ratings</a></li>
                        @endcan
                        @can('view_withdrawals')
                            <li class="nav-item"> <a
                                    @if (Session::get('page') == 'withdrawals') style="background: #052CA3 !important; color: #FFF !important" @else style="background: #fff !important; color: #052CA3 !important" @endif
                                    class="nav-link" href="{{ route('admin.withdrawals.index') }}">Withdrawals</a></li>
                        @endcan
                        @can('view_banners')
                            <li class="nav-item"> <a
                                    @if (Session::get('page') == 'banners') style="background: #052CA3 !important; color: #FFF !important" @else style="background: #fff !important; color: #052CA3 !important" @endif
                                    class="nav-link" href="{{ url('admin/banners') }}">Home Banners</a></li>
                        @endcan
                        <li class="nav-item">
                            <a @if (Session::get('page') == 'dynamic_modals') style="background: #052CA3 !important; color: #FFF !important" @else style="background: #fff !important; color: #052CA3 !important" @endif
                                class="nav-link" href="{{ url('admin/dynamic-modals') }}">Dynamic Modals</a>
                        </li>
                        <li class="nav-item"> <a
                                @if (Session::get('page') == 'commission_settings') style="background: #052CA3 !important; color: #FFF !important" @else style="background: #fff !important; color: #052CA3 !important" @endif
                                class="nav-link" href="{{ route('admin.commission.settings') }}">Commission Settings</a></li>
                    </ul>
                </div>
            </li>
        @endif

    </ul>
</nav>
