@extends('front.layout.layout3')

@section('content')
{{-- Hybrid code combining old tab concept with new styling --}}
    <style>
        /* WooCommerce inspired professional styling */
        .woocommerce-account {
            background-color: #f5f5f5;
            min-height: 100vh;
            padding: 40px 0;
        }

        .woocommerce-MyAccount-wrapper {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .woocommerce-MyAccount-navigation {
            background: #fff;
            border-right: 1px solid #e5e5e5;
            padding: 0;
        }

        .woocommerce-MyAccount-navigation ul {
            list-style: none;
            margin: 0;
            padding: 0;
        }

        .woocommerce-MyAccount-navigation li {
            margin: 0;
            border-bottom: 1px solid #e5e5e5;
        }

        .woocommerce-MyAccount-navigation li:last-child {
            border-bottom: none;
        }

        .woocommerce-MyAccount-navigation a {
            display: block;
            padding: 18px 24px;
            color: #333;
            text-decoration: none;
            font-weight: 500;
            position: relative;
            transition: all 0.3s ease;
            border-left: 3px solid transparent;
        }

        .woocommerce-MyAccount-navigation a:hover {
            background-color: #f8f9fa;
            color: #0073aa;
            border-left-color: #0073aa;
        }

        .woocommerce-MyAccount-navigation a.active {
            background-color: #0073aa;
            color: white;
            border-left-color: #005177;
        }

        .woocommerce-MyAccount-navigation a::before {
            content: '';
            width: 20px;
            height: 20px;
            margin-right: 12px;
            display: inline-block;
            vertical-align: middle;
        }

        /* Navigation Icons */
        .nav-dashboard::before {
            content: '📊';
        }

        .nav-orders::before {
            content: '📚';
        }

        .nav-edit-account::before {
            content: '👤';
        }

        .nav-edit-address::before {
            content: '📍';
        }

        .nav-customer-logout::before {
            content: '🚪';
        }

        .nav-my-queries::before {
            content: '💬';
        }

        .woocommerce-MyAccount-content {
            padding: 40px;
            background: white;
        }

        .woocommerce-account-header {
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #f0f0f0;
        }

        .woocommerce-account-header h1 {
            margin: 0 0 10px;
            color: #333;
            font-size: 28px;
            font-weight: 600;
        }

        .woocommerce-account-header p {
            margin: 0;
            color: #666;
            font-size: 16px;
        }

        /* Dashboard specific styles */
        .woocommerce-MyAccount-dashboard {
            color: #666;
            line-height: 1.6;
        }

        .dashboard-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: #f8f9fa;
            padding: 25px 20px;
            border-radius: 8px;
            text-align: center;
            border: 1px solid #e5e5e5;
            transition: transform 0.2s ease;
        }

        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .stat-card .stat-number {
            font-size: 32px;
            font-weight: bold;
            color: #0073aa;
            margin-bottom: 8px;
            display: block;
        }

        .stat-card .stat-label {
            color: #666;
            font-size: 14px;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* Account details styles */
        .woocommerce-account-details {
            background: #f8f9fa;
            padding: 30px;
            border-radius: 8px;
            margin-top: 20px;
            border: 1px solid #e5e5e5;
        }

        .account-info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 25px;
        }

        .info-item {
            background: white;
            padding: 20px;
            border-radius: 6px;
            border: 1px solid #e5e5e5;
        }

        .info-label {
            font-size: 12px;
            color: #666;
            text-transform: uppercase;
            font-weight: 600;
            letter-spacing: 0.5px;
            margin-bottom: 5px;
        }

        .info-value {
            font-size: 16px;
            color: #333;
            font-weight: 500;
        }

        /* Form styles */
        .woocommerce-form {
            max-width: 600px;
        }

        .woocommerce-form-row {
            margin-bottom: 20px;
        }

        .woocommerce-form label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #333;
        }

        .woocommerce-form .required {
            color: #e74c3c;
        }

        .woocommerce-form input[type="text"],
        .woocommerce-form input[type="email"],
        .woocommerce-form input[type="password"],
        .woocommerce-form select,
        .woocommerce-form textarea {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid #e5e5e5;
            border-radius: 4px;
            font-size: 16px;
            transition: border-color 0.3s ease;
            background: white;
        }

        .woocommerce-form input:focus,
        .woocommerce-form select:focus,
        .woocommerce-form textarea:focus {
            outline: none;
            border-color: #0073aa;
            box-shadow: 0 0 0 3px rgba(0, 115, 170, 0.1);
        }

        .woocommerce-form-row--wide {
            width: 100%;
        }

        .woocommerce-form-row--first {
            width: 48%;
            float: left;
        }

        .woocommerce-form-row--last {
            width: 48%;
            float: right;
        }

        .clear {
            clear: both;
        }

        /* Button styles */
        .woocommerce-Button {
            background: #0073aa;
            color: white;
            padding: 14px 30px;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }

        .woocommerce-Button:hover {
            background: #005177;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .woocommerce-Button--alt {
            background: #e74c3c;
        }

        .woocommerce-Button--alt:hover {
            background: #c0392b;
        }

        /* Table styles */
        .woocommerce-orders-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background: white;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            overflow: hidden;
        }

        .woocommerce-orders-table th,
        .woocommerce-orders-table td {
            padding: 16px;
            text-align: left;
            border-bottom: 1px solid #e5e5e5;
        }

        .woocommerce-orders-table th {
            background: #f8f9fa;
            font-weight: 600;
            color: #333;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .woocommerce-orders-table tbody tr:hover {
            background: #f8f9fa;
        }

        /* Status badges */
        .status-badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .status-pending {
            background: #fff3cd;
            color: #856404;
            border: 1px solid #ffeaa7;
        }

        .status-available {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .status-unavailable {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        /* Alerts */
        .woocommerce-message,
        .woocommerce-error {
            padding: 16px 20px;
            margin-bottom: 20px;
            border-radius: 4px;
            font-weight: 500;
        }

        .woocommerce-message {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .woocommerce-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #666;
        }

        .empty-state-icon {
            font-size: 64px;
            margin-bottom: 20px;
            opacity: 0.5;
        }

        .empty-state h3 {
            margin-bottom: 10px;
            color: #333;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .woocommerce-MyAccount-wrapper {
                margin: 20px;
            }

            .woocommerce-MyAccount-content {
                padding: 20px;
            }

            .woocommerce-form-row--first,
            .woocommerce-form-row--last {
                width: 100%;
                float: none;
                margin-bottom: 20px;
            }

            .dashboard-stats {
                grid-template-columns: 1fr;
            }

        .account-info-grid {
            grid-template-columns: 1fr;
        }
    }

    /* Accordion Custom Styles */
    .accordion-button {
        box-shadow: none !important;
        font-weight: 500;
    }

    .accordion-button:not(.collapsed) {
        background-color: #f8f9fa !important;
        color: #333 !important;
    }

    .accordion-button:focus {
        box-shadow: none !important;
        border-color: #e5e5e5 !important;
    }

    .accordion-item {
        border: 1px solid #e5e5e5 !important;
    }

    .accordion-button::after {
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16' fill='%23333'%3e%3cpath fill-rule='evenodd' d='M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z'/%3e%3c/svg%3e");
    }

    .accordion-button:not(.collapsed)::after {
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16' fill='%23333'%3e%3cpath fill-rule='evenodd' d='M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z'/%3e%3c/svg%3e");
        transform: rotate(180deg);
    }
    </style>

    <div class="woocommerce-account">
        <div class="container">
            <div class="woocommerce-MyAccount-wrapper">
                <div class="row g-0">
                    <!-- Navigation Sidebar -->
                    <div class="col-lg-3">
                        <nav class="woocommerce-MyAccount-navigation">
                            <ul class="nav flex-column nav-pills" id="account-tabs" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <a href="#dashboard" class="nav-dashboard nav-link active" id="dashboard-tab"
                                        data-bs-toggle="tab" role="tab" aria-controls="dashboard"
                                        aria-selected="true">Dashboard</a>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <a href="#edit-account" class="nav-edit-account nav-link" id="edit-account-tab"
                                        data-bs-toggle="tab" role="tab" aria-controls="edit-account"
                                        aria-selected="false">Account Details</a>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <a href="#change-password" class="nav-change-password nav-link" id="change-password-tab"
                                        data-bs-toggle="tab" role="tab" aria-controls="change-password"
                                        aria-selected="false">Change Password</a>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <a href="#orders" class="nav-orders nav-link" id="orders-tab" data-bs-toggle="tab"
                                        role="tab" aria-controls="orders" aria-selected="false">My Book Requests</a>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <a href="#my-queries" class="nav-my-queries nav-link" id="my-queries-tab"
                                        data-bs-toggle="tab" role="tab" aria-controls="my-queries"
                                        aria-selected="false">My Queries</a>
                                </li>

                                <!-- Logout link -->
                                <li class="nav-item" role="presentation">
                                    <a href="{{ route('logout') }}"
                                        onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                                        class="nav-customer-logout nav-link">Logout</a>

                                    <form id="logout-form" action="{{ route('logout') }}" method="POST"
                                        style="display: none;">
                                        @csrf
                                    </form>
                                </li>

                            </ul>
                        </nav>
                    </div>

                    <!-- Content Area -->
                    <div class="col-lg-9">
                        <div class="woocommerce-MyAccount-content">
                            <div class="tab-content" id="account-tabs-content">

                                <!-- Dashboard Tab -->
                                <div class="tab-pane fade show active" id="dashboard" role="tabpanel"
                                    aria-labelledby="dashboard-tab">
                                    <div class="woocommerce-account-header">
                                        <h1>My Account</h1>
                                        <p>Hello <strong>{{ explode(' ', $user->name)[0] }}</strong>, welcome to your
                                            account dashboard.</p>
                                    </div>

                                    <div class="woocommerce-MyAccount-dashboard">
                                        <!-- Quick Stats -->
                                        <div class="dashboard-stats">
                                            <div class="stat-card">
                                                <span class="stat-number">{{ $requestedBooks->count() }}</span>
                                                <span class="stat-label">Total Requests</span>
                                            </div>
                                            <div class="stat-card">
                                                <span
                                                    class="stat-number">{{ $requestedBooks->where('status', 1)->count() }}</span>
                                                <span class="stat-label">Available Books</span>
                                            </div>
                                            <div class="stat-card">
                                                <span
                                                    class="stat-number">{{ $requestedBooks->where('status', 0)->count() }}</span>
                                                <span class="stat-label">Pending Requests</span>
                                            </div>
                                        </div>

                                        <!-- Account Overview -->
                                        <div class="woocommerce-account-details">
                                            <h3 style="margin-bottom: 20px; color: #333;">Account Information</h3>
                                            <div class="account-info-grid">
                                                <div class="info-item">
                                                    <div class="info-label">Full Name</div>
                                                    <div class="info-value">{{ $user->name }}</div>
                                                </div>
                                                <div class="info-item">
                                                    <div class="info-label">Email Address</div>
                                                    <div class="info-value">{{ $user->email }}</div>
                                                </div>
                                                <div class="info-item">
                                                    <div class="info-label">Mobile Number</div>
                                                    <div class="info-value">{{ $user->mobile ?: 'Not provided' }}</div>
                                                </div>
                                                <div class="info-item">
                                                    <div class="info-label">City</div>
                                                    <div class="info-value">{{ $user->city ?: 'Not provided' }}</div>
                                                </div>
                                                <div class="info-item">
                                                    <div class="info-label">State</div>
                                                    <div class="info-value">{{ $user->state ?: 'Not provided' }}</div>
                                                </div>
                                                <div class="info-item">
                                                    <div class="info-label">Country</div>
                                                    <div class="info-value">{{ $user->country ?: 'Not provided' }}</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                               

                                <!-- Account Details Tab -->
                                <div class="tab-pane fade" id="edit-account" role="tabpanel"
                                    aria-labelledby="edit-account-tab">
                                    <div class="woocommerce-account-header">
                                        <h1>Account Details</h1>
                                        <p>Edit your account information and personal details.</p>
                                    </div>

                                    @if (session('success_message'))
                                        <div class="alert alert-success">{{ session('success_message') }}</div>
                                    @endif

                                    @if ($errors->any())
                                        <div class="alert alert-danger">
                                            <ul style="margin:0;">
                                                @foreach ($errors->all() as $error)
                                                    <li>{{ $error }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif

                                    <form class="woocommerce-form" id="accountForm" action="{{ route('useraccount') }}"
                                        method="POST">
                                        @csrf
                                        <div class="woocommerce-form-row woocommerce-form-row--wide">
                                            <label>Email address <span class="required">*</span></label>
                                            <input type="email" id="user-email" name="email" value="{{ Auth::user()->email }}" required>
                                        </div>

                                        <div class="woocommerce-form-row woocommerce-form-row--wide">
                                            <label>Full name <span class="required">*</span></label>
                                            <input type="text" id="user-name" name="name"
                                                value="{{ Auth::user()->name }}">
                                        </div>

                                        <div class="woocommerce-form-row woocommerce-form-row--wide">
                                            <label>Address <span class="required">*</span></label>
                                            <input type="text" id="user-address" name="address"
                                                value="{{ Auth::user()->address }}">
                                        </div>

                                        <div class="woocommerce-form-row woocommerce-form-row--first">
                                            <label>City <span class="required">*</span></label>
                                            <input type="text" id="user-city" name="city"
                                                value="{{ Auth::user()->city }}">
                                        </div>

                                        <div class="woocommerce-form-row woocommerce-form-row--last">
                                            <label>State <span class="required">*</span></label>
                                            <input type="text" id="user-state" name="state"
                                                value="{{ Auth::user()->state }}">
                                        </div>

                                        <div class="clear"></div>

                                        <div class="woocommerce-form-row woocommerce-form-row--wide">
                                            <label>Country <span class="required">*</span></label>
                                            <select id="user-country" name="country">
                                                <option value="">Select Country</option>
                                                @foreach ($countries as $country)
                                                    <option value="{{ $country->name }}"
                                                        @if ($country->name == Auth::user()->country) selected @endif>
                                                        {{ $country->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="woocommerce-form-row woocommerce-form-row--first">
                                            <label>Pincode <span class="required">*</span></label>
                                            <input type="text" id="user-pincode" name="pincode"
                                                value="{{ Auth::user()->pincode }}">
                                        </div>

                                        <div class="woocommerce-form-row woocommerce-form-row--last">
                                            <label>Mobile number <span class="required">*</span></label>
                                            <input type="text" id="user-mobile" name="mobile"
                                                value="{{ Auth::user()->mobile }}">
                                        </div>

                                        <div class="clear"></div>

                                        <div class="woocommerce-form-row">
                                            <button type="submit" class="woocommerce-Button">Save changes</button>
                                        </div>
                                    </form>
                                </div>

                                <!-- Change Password Tab -->
                                <div class="tab-pane fade" id="change-password" role="tabpanel"
                                    aria-labelledby="change-password-tab">
                                <div class="woocommerce-account-header">
                                    <h1>Change Password</h1>
                                    <p>Update your account password.</p>
                                </div>

                                    @if (session('success_message'))
                                        <div class="alert alert-success">{{ session('success_message') }}</div>
                                    @endif

                                    @if ($errors->any())
                                        <div class="alert alert-danger">
                                            <ul style="margin:0;">
                                                @foreach ($errors->all() as $error)
                                                    <li>{{ $error }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif

                                    <form class="woocommerce-form" id="passwordForm"
                                        action="{{ route('updatePassword') }}" method="POST" style="max-width: 400px;">
                                        @csrf
                                        <div class="woocommerce-form-row woocommerce-form-row--wide">
                                            <label>Current password <span class="required">*</span></label>
                                            <input type="password" id="current-password" name="current_password">
                                        </div>
                                        <div class="woocommerce-form-row woocommerce-form-row--wide">
                                            <label>New password <span class="required">*</span></label>
                                            <input type="password" id="new-password" name="new_password">
                                        </div>
                                        <div class="woocommerce-form-row woocommerce-form-row--wide">
                                            <label>Confirm new password <span class="required">*</span></label>
                                            <input type="password" id="confirm-password" name="confirm_password">
                                        </div>
                                        <div class="woocommerce-form-row">
                                            <button type="submit" class="woocommerce-Button">Update password</button>
                                        </div>
                                    </form>
                                </div>

                                 <!-- Orders/Requests Tab -->
                                 <div class="tab-pane fade" id="orders" role="tabpanel" aria-labelledby="orders-tab">
                                    <div class="woocommerce-account-header">
                                        <h1>My Book Requests</h1>
                                        <p>View and track all your book requests from your account.</p>
                                    </div>

                                    @if ($requestedBooks->isEmpty())
                                        <div class="empty-state">
                                            <div class="empty-state-icon">📚</div>
                                            <h3>No requests yet</h3>
                                            <p>You haven't made any book requests yet.</p>
                                            <a href="{{ route('requestbook.index') }}" class="woocommerce-Button">Browse
                                                Books</a>
                                        </div>
                                    @else
                                        <table class="woocommerce-orders-table">
                                            <thead>
                                                <tr>
                                                    <th>Request ID</th>
                                                    <th>Book Title</th>
                                                    <th>Author</th>
                                                    <th>Status</th>
                                                    <th>Date</th>
                                                    <th>Message</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($requestedBooks as $key => $book)
                                                    <tr>
                                                        <td>{{ $key + 1 }}
                                                        </td>
                                                        <td>{{ $book->book_title }}</td>
                                                        <td>{{ $book->author_name }}</td>
                                                        <td>
                                                            @if ($book->status == 0)
                                                                <span class="status-badge status-pending">Pending</span>
                                                            @elseif ($book->status == 1)
                                                                <span class="status-badge status-available">Book
                                                                    Available</span>
                                                            @else
                                                                <span
                                                                    class="status-badge status-unavailable">Unavailable</span>
                                                            @endif
                                                        </td>
                                                        <td>{{ $book->created_at->format('M d, Y') }}</td>
                                                        <td>{{ Str::limit($book->message, 30) }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    @endif
                                </div>

                                <!-- My Queries Tab -->
                                <div class="tab-pane fade" id="my-queries" role="tabpanel"
                                    aria-labelledby="my-queries-tab">
                                    <div class="woocommerce-account-header">
                                        <h1>My Contact Queries</h1>
                                        <p>View the status and replies for all your contact form submissions.</p>
                                    </div>

                                    @if (session('success_message'))
                                        <div class="woocommerce-message">{{ session('success_message') }}</div>
                                    @endif

                                    @if (session('error_message'))
                                        <div class="woocommerce-error">{{ session('error_message') }}</div>
                                    @endif

                                    @if ($contactQueries->isEmpty())
                                        <div class="empty-state">
                                            <div class="empty-state-icon">💬</div>
                                            <h3>No queries yet</h3>
                                            <p>You haven't submitted any contact queries yet.</p>
                                            <a href="{{ url('/contact') }}" class="woocommerce-Button">Contact Us</a>
                                        </div>
                                    @else
                                        <!-- Accordion Style Query Display -->
                                        <div class="accordion" id="queriesAccordion" style="margin-top: 20px;">
                                            @foreach ($contactQueries as $key => $query)
                                                @php
                                                    $collapseId = 'collapse' . $query->id;
                                                    $headingId = 'heading' . $query->id;
                                                    // Expand first query or if status is not resolved, collapse resolved queries by default
                                                    $isExpanded = ($key == 0 && $query->status != 'resolved') || ($key == 0 && $query->status == 'resolved' && $contactQueries->count() == 1);
                                                @endphp
                                                
                                                <div class="accordion-item" style="border: 1px solid #e5e5e5; border-radius: 8px; margin-bottom: 15px; overflow: hidden;">
                                                    <h2 class="accordion-header" id="{{ $headingId }}">
                                                        <button class="accordion-button {{ $isExpanded ? '' : 'collapsed' }}" type="button" 
                                                                data-bs-toggle="collapse" data-bs-target="#{{ $collapseId }}" 
                                                                aria-expanded="{{ $isExpanded ? 'true' : 'false' }}" 
                                                                aria-controls="{{ $collapseId }}"
                                                                style="background: #f8f9fa; padding: 15px 20px;">
                                                            <div style="flex: 1; display: flex; justify-content: space-between; align-items: center;">
                                                                <div style="flex: 1;">
                                                                    <strong style="color: #333; font-size: 16px; display: block; margin-bottom: 5px;">{{ $query->subject }}</strong>
                                                                    <small style="color: #666; font-size: 13px;">Submitted on {{ $query->created_at->format('M d, Y h:i A') }}</small>
                                                                </div>
                                                                <div style="margin-left: 15px;">
                                                                    @if ($query->status == 'pending')
                                                                        <span class="status-badge status-pending">Pending</span>
                                                                    @elseif ($query->status == 'in_progress')
                                                                        <span class="status-badge" style="background: #cce5ff; color: #004085; border: 1px solid #b3d7ff;">In Progress</span>
                                                                    @elseif ($query->status == 'resolved')
                                                                        <span class="status-badge status-available">Resolved</span>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </button>
                                                    </h2>
                                                    
                                                    <div id="{{ $collapseId }}" 
                                                         class="accordion-collapse collapse {{ $isExpanded ? 'show' : '' }}" 
                                                         aria-labelledby="{{ $headingId }}" 
                                                         data-bs-parent="#queriesAccordion">
                                                        <div class="accordion-body" style="padding: 20px; background: #fff;">
                                                            <!-- Original Query -->
                                                            <div style="background: #f0f7ff; padding: 15px; border-radius: 6px; margin-bottom: 15px; border-left: 4px solid #0073aa;">
                                                                <strong style="color: #0073aa; display: block; margin-bottom: 8px;">Your Original Message:</strong>
                                                                <p style="margin: 0; color: #333; line-height: 1.6;">{{ $query->message }}</p>
                                                                <small style="color: #999; display: block; margin-top: 8px;">{{ $query->created_at->format('F d, Y h:i A') }}</small>
                                                            </div>

                                                            <!-- Admin Reply (if exists in admin_reply field) -->
                                                            @if ($query->admin_reply)
                                                                <div style="background: #e8f5e9; padding: 15px; border-radius: 6px; margin-bottom: 15px; border-left: 4px solid #28a745;">
                                                                    <strong style="color: #28a745; display: block; margin-bottom: 8px;">👨‍💼 Admin Reply:</strong>
                                                                    <p style="margin: 0; color: #333; line-height: 1.6;">{{ $query->admin_reply }}</p>
                                                                    <small style="color: #999; display: block; margin-top: 8px;">{{ $query->updated_at->format('F d, Y h:i A') }}</small>
                                                                </div>
                                                            @endif

                                                            <!-- Conversation Thread (Replies) -->
                                                            @if ($query->replies && $query->replies->count() > 0)
                                                                <div style="margin-bottom: 15px;">
                                                                    <strong style="color: #333; display: block; margin-bottom: 10px; font-size: 14px;">Conversation Thread:</strong>
                                                                    <div style="max-height: 400px; overflow-y: auto; padding-right: 10px;">
                                                                        @foreach ($query->replies as $reply)
                                                                            @if ($reply->reply_by == 'admin')
                                                                                <div style="background: #e8f5e9; padding: 12px; border-radius: 6px; margin-bottom: 10px; border-left: 4px solid #28a745;">
                                                                                    <strong style="color: #28a745; display: block; margin-bottom: 5px; font-size: 13px;">👨‍💼 Admin:</strong>
                                                                                    <p style="margin: 0; color: #333; line-height: 1.6; font-size: 14px;">{{ $reply->message }}</p>
                                                                                    <small style="color: #999; display: block; margin-top: 5px; font-size: 12px;">{{ $reply->created_at->format('F d, Y h:i A') }}</small>
                                                                                </div>
                                                                            @else
                                                                                <div style="background: #e3f2fd; padding: 12px; border-radius: 6px; margin-bottom: 10px; border-left: 4px solid #2196f3;">
                                                                                    <strong style="color: #2196f3; display: block; margin-bottom: 5px; font-size: 13px;">👤 You:</strong>
                                                                                    <p style="margin: 0; color: #333; line-height: 1.6; font-size: 14px;">{{ $reply->message }}</p>
                                                                                    <small style="color: #999; display: block; margin-top: 5px; font-size: 12px;">{{ $reply->created_at->format('F d, Y h:i A') }}</small>
                                                                                </div>
                                                                            @endif
                                                                        @endforeach
                                                                    </div>
                                                                </div>
                                                            @endif

                                                            <!-- Reply Form or Resolved Message -->
                                                            @if ($query->status == 'resolved')
                                                                <div style="background: #d4edda; padding: 20px; border-radius: 6px; border-left: 4px solid #28a745; text-align: center;">
                                                                    <div style="font-size: 48px; margin-bottom: 15px;">✅</div>
                                                                    <h5 style="color: #155724; margin-bottom: 10px; font-weight: 600;">Query Resolved Successfully!</h5>
                                                                    <p style="margin: 0; color: #155724; font-size: 16px;">Admin has successfully resolved your query. If you have any further questions, please feel free to submit a new query.</p>
                                                                </div>
                                                            @elseif ($query->admin_reply || ($query->replies && $query->replies->where('reply_by', 'admin')->count() > 0))
                                                                <div style="background: white; padding: 15px; border-radius: 6px; border: 1px solid #e5e5e5;">
                                                                    <h5 style="margin-bottom: 15px; color: #333;">Reply to Admin</h5>
                                                                    <form action="{{ route('user.query.reply', $query->id) }}" method="POST">
                                                                        @csrf
                                                                        <div class="woocommerce-form-row woocommerce-form-row--wide">
                                                                            <label>Your Reply <span class="required">*</span></label>
                                                                            <textarea name="message" rows="4" class="woocommerce-form" style="width: 100%; padding: 12px; border: 2px solid #e5e5e5; border-radius: 4px; font-size: 16px;" required minlength="10" placeholder="Type your reply here..."></textarea>
                                                                            @error('message')
                                                                                <small style="color: #e74c3c; display: block; margin-top: 5px;">{{ $message }}</small>
                                                                            @enderror
                                                                        </div>
                                                                        <div class="woocommerce-form-row">
                                                                            <button type="submit" class="woocommerce-Button">Send Reply</button>
                                                                        </div>
                                                                    </form>
                                                                </div>
                                                            @else
                                                                <div style="background: #fff3cd; padding: 15px; border-radius: 6px; border-left: 4px solid #ffc107;">
                                                                    <p style="margin: 0; color: #856404;">Your query is being reviewed. We'll get back to you soon.</p>
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
        $(document).ready(function() {
            // Bootstrap 5 handles tab switching automatically
            // This ensures the sidebar active state is in sync
            $('a[data-bs-toggle="tab"]').on('shown.bs.tab', function(e) {
                $('.woocommerce-MyAccount-navigation .nav-link').removeClass('active');
                $(e.target).addClass('active');
            });
        });
    </script>
@endsection
