<!-- partial:partials/_sidebar.html -->
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
                    <i class="icon-layout menu-icon"></i>
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
                <a @if (Session::get('page') == 'sections' ||
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
                    <i class="icon-layout menu-icon"></i>
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
                    <i class="icon-layout menu-icon"></i>
                    <span class="menu-title">Orders Management</span>
                    <i class="menu-arrow"></i>
                </a>
                <div class="collapse" id="ui-orders">
                    <ul class="nav flex-column sub-menu" style="background: #fff !important; color: #052CA3 !important">
                        @if (Auth::guard('admin')->user()->can('view_orders'))
                            <li class="nav-item"> <a
                                    @if (Session::get('page') == 'orders') style="background: #052CA3 !important; color: #FFF !important" @else style="background: #fff !important; color: #052CA3 !important" @endif
                                    class="nav-link" href="{{ url('vendor/orders') }}">Orders</a></li>
                        @endif
                        @if (Auth::guard('admin')->user()->can('view_sales_concept'))
                            <li class="nav-item"> <a
                                    @if (Session::get('page') == 'sales_concept') style="background: #052CA3 !important; color: #FFF !important" @else style="background: #fff !important; color: #052CA3 !important" @endif
                                    class="nav-link" href="{{ url('vendor/sales-concept') }}">Sales Concept</a></li>
                        @endif
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

            <li class="nav-item">
                <a @if (Session::get('page') == 'update_admin_password' || Session::get('page') == 'update_admin_details') style="background: #052CA3 !important; color: #FFF !important" @endif
                    class="nav-link" data-toggle="collapse" href="#ui-settings" aria-expanded="false"
                    aria-controls="ui-settings">
                    <i class="icon-layout menu-icon"></i>
                    <span class="menu-title">Settings</span>
                    <i class="menu-arrow"></i>
                </a>
                <div class="collapse" id="ui-settings">
                    <ul class="nav flex-column sub-menu" style="background: #fff !important; color: #052CA3 !important">
                        <li class="nav-item"> <a
                                @if (Session::get('page') == 'update_admin_password') style="background: #052CA3 !important; color: #FFF !important" @else style="background: #fff !important; color: #052CA3 !important" @endif
                                class="nav-link" href="{{ url('admin/update-admin-password') }}">Update Admin
                                Password</a></li>
                        <li class="nav-item"> <a
                                @if (Session::get('page') == 'update_admin_details') style="background: #052CA3 !important; color: #FFF !important" @else style="background: #fff !important; color: #052CA3 !important" @endif
                                class="nav-link" href="{{ url('admin/update-admin-details') }}">Update Admin
                                Details</a>
                        </li>
                        <li class="nav-item"> <a
                                @if (Session::get('page') == 'logo') style="background: #052CA3 !important; color: #FFF !important" @else style="background: #fff !important; color: #052CA3 !important" @endif
                                class="nav-link" href="{{ route('admin.logo') }}">Logo</a>
                        </li>
                        <li class="nav-item"> <a
                                @if (Session::get('page') == 'favicon') style="background: #052CA3 !important; color: #FFF !important" @else style="background: #fff !important; color: #052CA3 !important" @endif
                                class="nav-link" href="{{ url('admin/favicon') }}">Favicon</a>
                        </li>
                        <li class="nav-item"> <a
                                @if (Session::get('page') == 'plan_settings') style="background: #052CA3 !important; color: #FFF !important" @else style="background: #fff !important; color: #052CA3 !important" @endif
                                class="nav-link" href="{{ route('admin.plan.settings') }}">Vendor Plan Settings</a>
                        </li>
                        <li class="nav-item"> <a
                                @if (Session::get('page') == 'coming_soon_settings') style="background: #052CA3 !important; color: #FFF !important" @else style="background: #fff !important; color: #052CA3 !important" @endif
                                class="nav-link" href="{{ route('admin.coming.soon.settings') }}">Coming Soon
                                Settings</a>
                        </li>
                        <li class="nav-item"> <a
                                @if (Session::get('page') == 'commission_settings') style="background: #052CA3 !important; color: #FFF !important" @else style="background: #fff !important; color: #052CA3 !important" @endif
                                class="nav-link" href="{{ route('admin.commission.settings') }}">Commission
                                Settings</a>
                        </li>
                    </ul>
                </div>
            </li>
            <li class="nav-item">
                <a @if (Session::get('page') == 'view_admins' ||
                        Session::get('page') == 'view_vendors' ||
                        Session::get('page') == 'view_sales' ||
                        Session::get('page') == 'view_all') style="background: #052CA3 !important; color: #FFF !important" @endif
                    class="nav-link" data-toggle="collapse" href="#ui-admins" aria-expanded="false"
                    aria-controls="ui-admins">
                    <i class="icon-layout menu-icon"></i>
                    <span class="menu-title">Admin Management</span>
                    <i class="menu-arrow"></i>
                </a>
                <div class="collapse" id="ui-admins">
                    <ul class="nav flex-column sub-menu"
                        style="background: #fff !important; color: #052CA3 !important">
                        @can('view_admins')
                            <li class="nav-item"> <a
                                    @if (Session::get('page') == 'view_admins') style="background: #052CA3 !important; color: #FFF !important" @else style="background: #fff !important; color: #052CA3 !important" @endif
                                    class="nav-link" href="{{ url('admin/admins/admin') }}">Admins</a></li>
                        @endcan
                        @can('view_vendors')
                            <li class="nav-item"> <a
                                    @if (Session::get('page') == 'view_vendors') style="background: #052CA3 !important; color: #FFF !important" @else style="background: #fff !important; color: #052CA3 !important" @endif
                                    class="nav-link" href="{{ url('admin/admins/vendor') }}">Vendors</a></li>
                        @endcan
                        @can('view_sales')
                            <li class="nav-item"> <a
                                    @if (Session::get('page') == 'view_sales') style="background: #052CA3 !important; color: #FFF !important" @else style="background: #fff !important; color: #052CA3 !important" @endif
                                    class="nav-link" href="{{ url('admin/sales-executive') }}">Sales
                                    Executives</a></li>
                        @endcan
                        @can('view_roles')
                            <li class="nav-item"> <a
                                    @if (Session::get('page') == 'roles') style="background: #052CA3 !important; color: #FFF !important" @else style="background: #fff !important; color: #052CA3 !important" @endif
                                    class="nav-link" href="{{ route('admin.roles.index') }}">Roles & Permissions</a></li>
                        @endcan
                    </ul>
                </div>
            </li>
            <li class="nav-item">
                <a @if (Session::get('page') == 'sections' ||
                        Session::get('page') == 'categories' ||
                        Session::get('page') == 'products' ||
                        Session::get('page') == 'edition' ||
                        Session::get('page') == 'ebooks' ||
                        Session::get('page') == 'publisher' ||
                        // Session::get('page') == 'filters' ||
                        Session::get('page') == 'authors' ||
                        Session::get('page') == 'subjects' ||
                        Session::get('page') == 'requestedbooks' ||
                        Session::get('page') == 'sellBookRequests' ||
                        Session::get('page') == 'coupons') style="background: #052CA3 !important; color: #FFF !important" @endif
                    class="nav-link" data-toggle="collapse" href="#ui-catalogue" aria-expanded="false"
                    aria-controls="ui-catalogue">
                    <i class="icon-layout menu-icon"></i>
                    <span class="menu-title">Catalogue Management</span>
                    <i class="menu-arrow"></i>
                </a>
                <div class="collapse" id="ui-catalogue">
                    <ul class="nav flex-column sub-menu"
                        style="background: #fff !important; color: #052CA3 !important">
                        @can('view_sections')
                            <li class="nav-item"> <a
                                    @if (Session::get('page') == 'sections') style="background: #052CA3 !important; color: #FFF !important" @else style="background: #fff !important; color: #052CA3 !important" @endif
                                    class="nav-link" href="{{ url('admin/sections') }}">Sections</a></li>
                        @endcan
                        @can('view_categories')
                            <li class="nav-item"> <a
                                    @if (Session::get('page') == 'categories') style="background: #052CA3 !important; color: #FFF !important" @else style="background: #fff !important; color: #052CA3 !important" @endif
                                    class="nav-link" href="{{ url('admin/categories') }}">Categories</a></li>
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
                        @endcan
                        @can('view_languages')
                            <li class="nav-item"> <a
                                    @if (Session::get('page') == 'languages') style="background: #052CA3 !important; color: #FFF !important" @else style="background: #fff !important; color: #052CA3 !important" @endif
                                    class="nav-link" href="{{ url('admin/languages') }}">Book Languages</a></li>
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
                            <li class="nav-item"> <a
                                    @if (Session::get('page') == 'sellBookRequests') style="background: #052CA3 !important; color: #FFF !important" @else style="background: #fff !important; color: #052CA3 !important" @endif
                                    class="nav-link" href="{{ url('admin/sell-book-requests') }}">Sell Old Books
                                    Requests</a></li>
                        @endcan
                    </ul>
                </div>
            </li>
            <li class="nav-item">
                <a @if (Session::get('page') == 'orders') style="background: #052CA3 !important; color: #FFF !important" @endif
                    class="nav-link" data-toggle="collapse" href="#ui-orders" aria-expanded="false"
                    aria-controls="ui-orders">
                    <i class="icon-layout menu-icon"></i>
                    <span class="menu-title">Orders Management</span>
                    <i class="menu-arrow"></i>
                </a>
                <div class="collapse" id="ui-orders">
                    <ul class="nav flex-column sub-menu"
                        style="background: #fff !important; color: #052CA3 !important">
                        @can('view_orders')
                            <li class="nav-item"> <a
                                    @if (Session::get('page') == 'orders') style="background: #052CA3 !important; color: #FFF !important" @else style="background: #fff !important; color: #052CA3 !important" @endif
                                    class="nav-link" href="{{ url('admin/orders') }}">Orders</a></li>
                        @endcan
                        @can('view_sales_concept')
                            <li class="nav-item"> <a
                                    @if (Session::get('page') == 'sales_concept') style="background: #052CA3 !important; color: #FFF !important" @else style="background: #fff !important; color: #052CA3 !important" @endif
                                    class="nav-link" href="{{ url('admin/sales-concept') }}">Sales Concept</a></li>
                        @endcan
                    </ul>
                </div>
            </li>
            <li class="nav-item">
                <a @if (Session::get('page') == 'ratings') style="background: #052CA3 !important; color: #FFF !important" @endif
                    class="nav-link" data-toggle="collapse" href="#ui-ratings" aria-expanded="false"
                    aria-controls="ui-ratings">
                    <i class="icon-layout menu-icon"></i>
                    <span class="menu-title">Ratings Management</span>
                    <i class="menu-arrow"></i>
                </a>
                <div class="collapse" id="ui-ratings">
                    <ul class="nav flex-column sub-menu"
                        style="background: #fff !important; color: #052CA3 !important">
                        @can('view_ratings')
                            <li class="nav-item"> <a
                                    @if (Session::get('page') == 'ratings') style="background: #052CA3 !important; color: #FFF !important" @else style="background: #fff !important; color: #052CA3 !important" @endif
                                    class="nav-link" href="{{ url('admin/ratings') }}">Product Ratings & Reviews</a></li>
                        @endcan
                    </ul>
                </div>
            </li>
            <li class="nav-item">
                <a @if (Session::get('page') == 'users' || Session::get('page') == 'subscribers') style="background: #052CA3 !important; color: #FFF !important" @endif
                    class="nav-link" data-toggle="collapse" href="#ui-users" aria-expanded="false"
                    aria-controls="ui-users">
                    <i class="icon-layout menu-icon"></i>
                    <span class="menu-title">Users Management</span>
                    <i class="menu-arrow"></i>
                </a>
                <div class="collapse" id="ui-users">
                    <ul class="nav flex-column sub-menu"
                        style="background: #fff !important; color: #052CA3 !important">
                        @can('view_users')
                            <li class="nav-item"> <a
                                    @if (Session::get('page') == 'users') style="background: #052CA3 !important; color: #FFF !important" @else style="background: #fff !important; color: #052CA3 !important" @endif
                                    class="nav-link" href="{{ url('admin/users') }}">Users</a></li>
                        @endcan
                        @can('view_subscribers')
                            <li class="nav-item"> <a
                                    @if (Session::get('page') == 'subscribers') style="background: #052CA3 !important; color: #FFF !important" @else style="background: #fff !important; color: #052CA3 !important" @endif
                                    class="nav-link" href="{{ url('admin/subscribers') }}">Subscribers</a></li>
                        @endcan
                        @can('view_contact_queries')
                            <li class="nav-item"> <a
                                    @if (Session::get('page') == 'contact_queries') style="background: #052CA3 !important; color: #FFF !important" @else style="background: #fff !important; color: #052CA3 !important" @endif
                                    class="nav-link" href="{{ url('admin/contact-queries') }}">Contact Queries</a></li>
                        @endcan
                    </ul>
                </div>
            </li>

            <li class="nav-item">
                <a @if (Session::get('page') == 'institution_managements' ||
                        Session::get('page') == 'students' ||
                        Session::get('page') == 'blocks') style="background: #052CA3 !important; color: #FFF !important" @endif
                    class="nav-link" data-toggle="collapse" href="#ui-institutions" aria-expanded="false"
                    aria-controls="ui-institutions">
                    <i class="icon-layout menu-icon"></i>
                    <span class="menu-title">Institution Management</span>
                    <i class="menu-arrow"></i>
                </a>
                <div class="collapse" id="ui-institutions">
                    <ul class="nav flex-column sub-menu"
                        style="background: #fff !important; color: #052CA3 !important">
                        @can('view_institutions')
                            <li class="nav-item"> <a
                                    @if (Session::get('page') == 'institution_managements') style="background: #052CA3 !important; color: #FFF !important" @else style="background: #fff !important; color: #052CA3 !important" @endif
                                    class="nav-link" href="{{ url('admin/institution-managements') }}">Institution</a>
                            </li>
                        @endcan
                        @can('view_blocks')
                            <li class="nav-item"> <a
                                    @if (Session::get('page') == 'blocks') style="background: #052CA3 !important; color: #FFF !important" @else style="background: #fff !important; color: #052CA3 !important" @endif
                                    class="nav-link" href="{{ url('admin/blocks') }}">Blocks</a></li>
                        @endcan
                        @can('view_students')
                            <li class="nav-item"> <a
                                    @if (Session::get('page') == 'students') style="background: #052CA3 !important; color: #FFF !important" @else style="background: #fff !important; color: #052CA3 !important" @endif
                                    class="nav-link" href="{{ url('admin/students') }}">Students</a></li>
                        @endcan
                        <li class="nav-item"> <a
                                @if (Session::get('page') == 'districts') style="background: #052CA3 !important; color: #FFF !important" @else style="background: #fff !important; color: #052CA3 !important" @endif
                                class="nav-link" href="{{ route('admin.districts.index') }}">Districts</a></li>
                        <li class="nav-item"> <a
                                @if (Session::get('page') == 'countries_states') style="background: #052CA3 !important; color: #FFF !important" @else style="background: #fff !important; color: #052CA3 !important" @endif
                                class="nav-link" href="{{ route('admin.countries_states.index') }}">Country &
                                State</a></li>
                    </ul>
                </div>

            </li>

            <li class="nav-item">
                <a @if (Session::get('page') == 'withdrawals') style="background: #052CA3 !important; color: #FFF !important" @endif
                    class="nav-link" data-toggle="collapse" href="#ui-withdrawals" aria-expanded="false"
                    aria-controls="ui-withdrawals">
                    <i class="icon-layout menu-icon"></i>
                    <span class="menu-title">Withdrawals Management</span>
                    <i class="menu-arrow"></i>
                </a>
                <div class="collapse" id="ui-withdrawals">
                    <ul class="nav flex-column sub-menu"
                        style="background: #fff !important; color: #052CA3 !important">
                        @can('view_withdrawals')
                            <li class="nav-item"> <a
                                    @if (Session::get('page') == 'withdrawals') style="background: #052CA3 !important; color: #FFF !important" @else style="background: #fff !important; color: #052CA3 !important" @endif
                                    class="nav-link" href="{{ route('admin.withdrawals.index') }}">Withdrawal
                                    Requests</a></li>
                        @endcan
                    </ul>
                </div>
            </li>

            <li class="nav-item">
                <a @if (Session::get('page') == 'banners') style="background: #052CA3 !important; color: #FFF !important" @endif
                    class="nav-link" data-toggle="collapse" href="#ui-banners" aria-expanded="false"
                    aria-controls="ui-banners">
                    <i class="icon-layout menu-icon"></i>
                    <span class="menu-title">Banners Management</span>
                    <i class="menu-arrow"></i>
                </a>
                <div class="collapse" id="ui-banners">
                    <ul class="nav flex-column sub-menu"
                        style="background: #fff !important; color: #052CA3 !important">
                        @can('view_banners')
                            <li class="nav-item"> <a
                                    @if (Session::get('page') == 'banners') style="background: #052CA3 !important; color: #FFF !important" @else style="background: #fff !important; color: #052CA3 !important" @endif
                                    class="nav-link" href="{{ url('admin/banners') }}">Home Page Banners</a></li>
                        @endcan
                    </ul>
                </div>
            </li>


            <li class="nav-item">
                <a @if (Session::get('page') == 'shipping') style="background: #052CA3 !important; color: #FFF !important" @endif
                    class="nav-link" data-toggle="collapse" href="#ui-shipping" aria-expanded="false"
                    aria-controls="ui-shipping">
                    <i class="icon-layout menu-icon"></i>
                    <span class="menu-title">Shipping Management</span>
                    <i class="menu-arrow"></i>
                </a>
                <div class="collapse" id="ui-shipping">
                    <ul class="nav flex-column sub-menu"
                        style="background: #fff !important; color: #052CA3 !important">
                        @can('view_shipping')
                            <li class="nav-item"> <a
                                    @if (Session::get('page') == 'shipping') style="background: #052CA3 !important; color: #FFF !important" @else style="background: #fff !important; color: #052CA3 !important" @endif
                                    class="nav-link" href="{{ url('admin/shipping-charges') }}">Shipping Charges</a></li>
                        @endcan
                    </ul>
                </div>
            </li>
            {{-- Otp Management  --}}
            <li class="nav-item">
                <a @if (Session::get('page') == 'otp_management') style="background: #052CA3 !important; color: #FFF !important" @endif
                    class="nav-link" data-toggle="collapse" href="#ui-otp-management" aria-expanded="false"
                    aria-controls="ui-otp-management">
                    <i class="icon-layout menu-icon"></i>
                    <span class="menu-title">Otp Management</span>
                    <i class="menu-arrow"></i>
                </a>
            </li>
            <div class="collapse" id="ui-otp-management">
                <ul class="nav flex-column sub-menu" style="background: #fff !important; color: #052CA3 !important">
                    @can('view_otp')
                        <li class="nav-item"> <a
                                @if (Session::get('page') == 'otp_management') style="background: #052CA3 !important; color: #FFF !important" @else style="background: #fff !important; color: #052CA3 !important" @endif
                                class="nav-link" href="{{ route('admin.otps') }}">Otp Management</a></li>
                    @endcan
                </ul>
            </div>
            <li class="nav-item">
                <a @if (Session::get('page') == 'report_management') style="background: #052CA3 !important; color: #FFF !important" @endif
                    class="nav-link" data-toggle="collapse" href="#ui-report-management" aria-expanded="false"
                    aria-controls="ui-report-management">
                    <i class="icon-layout menu-icon"></i>
                    <span class="menu-title">Report Management</span>
                    <i class="menu-arrow"></i>
                </a>
            </li>
            <div class="collapse" id="ui-report-management">
                <ul class="nav flex-column sub-menu" style="background: #fff !important; color: #052CA3 !important">
                    @can('view_reports')
                        <li class="nav-item"> <a
                                @if (Session::get('page') == 'sales_reports') style="background: #052CA3 !important; color: #FFF !important" @else style="background: #fff !important; color: #052CA3 !important" @endif
                                class="nav-link" href="{{ url('admin/reports/sales_reports') }}">Sales Reports</a></li>
                        <li class="nav-item"> <a
                                @if (Session::get('page') == 'stock_report') style="background: #052CA3 !important; color: #FFF !important" @else style="background: #fff !important; color: #052CA3 !important" @endif
                                class="nav-link" href="{{ route('admin.reports.stock_report') }}">Stock Report</a></li>
                    @endcan
                </ul>
            </div>
        @endif

    </ul>
</nav>
