<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="base-url" content="{{ url('/') }}">
    <title>BookHub - User Panel</title>
    <!-- plugins:css -->
    <link rel="stylesheet" href="{{ asset('user/vendors/feather/feather.css') }}">
    <link rel="stylesheet" href="{{ asset('user/vendors/ti-icons/css/themify-icons.css') }}">
    <link rel="stylesheet" href="{{ asset('user/vendors/css/vendor.bundle.base.css') }}">
    <!-- endinject -->
    <!-- Plugin css for this page -->
    <link rel="stylesheet" href="{{ asset('user/vendors/datatables.net-bs4/dataTables.bootstrap4.css') }}">
    <link rel="stylesheet" href="{{ asset('user/vendors/ti-icons/css/themify-icons.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('user/js/select.dataTables.min.css') }}">
    <!-- End plugin css for this page -->
    <!-- inject:css -->
    <link rel="stylesheet" href="{{ asset('user/css/vertical-layout-light/style.css') }}">
    <!-- endinject -->
    @if(isset($headerLogo) && filled($headerLogo->favicon))
        <link rel="shortcut icon" href="{{ asset('uploads/logos/' . $headerLogo->favicon) }}" />
    @endif

    <style>
        /* Global modern skin for Student/User panel */
        :root{
            --ub-bg: #f6f8ff;
            --ub-card: #ffffff;
            --ub-text: #0f172a;
            --ub-muted: #64748b;
            --ub-border: rgba(17,24,39,.08);
            --ub-shadow: 0 10px 30px rgba(17,24,39,.06);
            --ub-radius: 16px;
            --ub-primary: #4f46e5;
            --ub-primary2: #7c3aed;
        }

        body.user-modern { background: var(--ub-bg); }
        body.user-modern .content-wrapper { background: var(--ub-bg); }
        body.user-modern .card { border: 0; border-radius: var(--ub-radius); box-shadow: var(--ub-shadow); }
        body.user-modern .badge { border-radius: 999px; }
        body.user-modern .progress.progress-sm { height: 8px; border-radius: 999px; }
        body.user-modern .progress .progress-bar { border-radius: 999px; }

        /* Navbar */
        body.user-modern .navbar.fixed-top {
            backdrop-filter: blur(12px);
            background: rgba(255,255,255,.85) !important;
            border-bottom: 1px solid var(--ub-border);
        }
        body.user-modern .navbar-brand-wrapper { background: transparent !important; }
        body.user-modern .navbar .nav-search .form-control {
            border-radius: 999px;
            border: 1px solid var(--ub-border);
            background: rgba(255,255,255,.9);
        }
        body.user-modern .wallet-pill{
            display:flex; align-items:center; gap:8px;
            color:#ff9900; font-weight:700;
            background:#fff5e6;
            padding:8px 16px;
            border-radius:999px;
            border:1px solid #ffe3b3;
        }

        /* Sidebar */
        body.user-modern .sidebar {
            border-right: 1px solid var(--ub-border);
            background: rgba(255,255,255,.92);
            backdrop-filter: blur(12px);
        }
        body.user-modern .sidebar .nav .nav-item .nav-link{
            border-radius: 12px;
            margin: 4px 10px;
            padding: 10px 12px;
        }
        body.user-modern .sidebar .nav .nav-item .nav-link:hover{
            background: rgba(79,70,229,.08);
        }
        body.user-modern .sidebar .nav .nav-item.active > .nav-link{
            background: linear-gradient(135deg, rgba(79,70,229,.18), rgba(124,58,237,.10));
            border: 1px solid rgba(79,70,229,.22);
        }
        body.user-modern .sidebar-profile{
            margin: 12px 12px 6px;
            padding: 12px;
            border-radius: 16px;
            background: var(--ub-card);
            box-shadow: var(--ub-shadow);
            border: 1px solid var(--ub-border);
        }
        body.user-modern .sidebar-profile img{
            width:40px; height:40px; border-radius: 14px; object-fit: cover;
        }
        body.user-modern .sidebar-profile .name{ font-weight: 700; color: var(--ub-text); line-height: 1.1; }
        body.user-modern .sidebar-profile .meta{ color: var(--ub-muted); font-size: 12px; }

        /* When sidebar is collapsed (template uses this class on body) */
        body.user-modern.sidebar-icon-only .sidebar-profile{
            padding: 10px;
            margin: 10px 8px 6px;
            border-radius: 14px;
        }
        body.user-modern.sidebar-icon-only .sidebar-profile .sidebar-profile-row{
            justify-content: center;
        }
        body.user-modern.sidebar-icon-only .sidebar-profile .flex-grow-1{
            display: none;
        }
        body.user-modern.sidebar-icon-only .sidebar-profile img{
            width: 36px;
            height: 36px;
            border-radius: 14px;
        }

        /* Special gradient card */
        body.user-modern .data-icon-card-primary {
            background: linear-gradient(135deg, var(--ub-primary) 0%, var(--ub-primary2) 55%, #ec4899 120%) !important;
            border-radius: 18px;
        }
    </style>
</head>

<body class="user-modern">
    <div class="container-scroller">
        @include('user.layout.navbar')
