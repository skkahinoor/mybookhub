<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Subject Duplicate Resolver | Admin Portal</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- MDI Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@mdi/font@7.2.96/css/materialdesignicons.min.css">
    
    <!-- Animate.css -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>

    <style>
        :root {
            --bg-primary: #020617;
            --bg-secondary: #0b1528;
            --accent-primary: #6366f1;
            --accent-secondary: #a855f7;
            --accent-glow: rgba(99, 102, 241, 0.15);
            --text-main: #f8fafc;
            --text-muted: #94a3b8;
            --border-color: rgba(255, 255, 255, 0.08);
            --glass-bg: rgba(15, 23, 42, 0.55);
            --success-color: #10b981;
            --error-color: #ef4444;
            --warning-color: #f59e0b;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            background-color: var(--bg-primary);
            background-image: 
                radial-gradient(circle at 10% 20%, rgba(99, 102, 241, 0.05) 0%, transparent 40%),
                radial-gradient(circle at 90% 80%, rgba(168, 85, 247, 0.05) 0%, transparent 40%);
            background-attachment: fixed;
            font-family: 'Plus Jakarta Sans', sans-serif;
            color: var(--text-main);
            min-height: 100vh;
            padding: 2rem 1.5rem;
            line-height: 1.5;
        }

        h1, h2, h3, h4, h5, h6, .brand-font {
            font-family: 'Outfit', sans-serif;
        }

        .container {
            max-width: 1300px;
            margin: 0 auto;
        }

        /* Header section */
        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2.5rem;
            background: var(--glass-bg);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid var(--border-color);
            padding: 1.5rem 2rem;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            animation: fadeInDown 0.8s ease;
        }

        .header-title-area {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .header-icon {
            background: linear-gradient(135deg, var(--accent-primary), var(--accent-secondary));
            width: 52px;
            height: 52px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 26px;
            color: #fff;
            box-shadow: 0 8px 20px rgba(99, 102, 241, 0.3);
        }

        .header-text h1 {
            font-size: 1.75rem;
            font-weight: 700;
            background: linear-gradient(to right, #fff, #cbd5e1);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .header-text p {
            font-size: 0.875rem;
            color: var(--text-muted);
        }

        .header-actions {
            display: flex;
            gap: 1rem;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            padding: 0.75rem 1.5rem;
            border-radius: 12px;
            font-family: 'Outfit', sans-serif;
            font-size: 0.95rem;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border: none;
        }

        .btn-outline {
            background: transparent;
            color: var(--text-main);
            border: 1px solid var(--border-color);
        }

        .btn-outline:hover {
            background: rgba(255, 255, 255, 0.05);
            border-color: var(--text-muted);
            transform: translateY(-2px);
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--accent-primary), var(--accent-secondary));
            color: #fff;
            box-shadow: 0 4px 15px rgba(99, 102, 241, 0.3);
        }

        .btn-primary:hover {
            box-shadow: 0 8px 25px rgba(99, 102, 241, 0.5);
            transform: translateY(-2px);
        }

        /* Stats Cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2.5rem;
            animation: fadeIn 1s ease 0.2s both;
        }

        .stat-card {
            background: var(--glass-bg);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid var(--border-color);
            border-radius: 20px;
            padding: 1.5rem;
            display: flex;
            align-items: center;
            gap: 1.25rem;
            position: relative;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
            transition: transform 0.3s ease, border-color 0.3s ease;
        }

        .stat-card::after {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 100px;
            height: 100px;
            background: radial-gradient(circle, rgba(99, 102, 241, 0.08) 0%, transparent 70%);
            border-radius: 50%;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            border-color: rgba(99, 102, 241, 0.25);
        }

        .stat-icon {
            width: 56px;
            height: 56px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid var(--border-color);
            color: var(--accent-primary);
        }

        .stat-card:nth-child(2) .stat-icon {
            color: var(--accent-secondary);
        }

        .stat-card:nth-child(3) .stat-icon {
            color: var(--warning-color);
        }

        .stat-info h3 {
            font-size: 1.75rem;
            font-weight: 700;
            line-height: 1.2;
            margin-bottom: 0.25rem;
        }

        .stat-info p {
            font-size: 0.85rem;
            color: var(--text-muted);
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* Notifications / Alerts */
        .notification-container {
            margin-bottom: 2rem;
            animation: fadeIn 0.5s ease;
        }

        .alert-box {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1.25rem 1.5rem;
            border-radius: 16px;
            border: 1px solid transparent;
            font-weight: 500;
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            animation: slideInLeft 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }

        .alert-box i {
            font-size: 24px;
        }

        .alert-success {
            background: rgba(16, 185, 129, 0.1);
            border-color: rgba(16, 185, 129, 0.25);
            color: #a7f3d0;
            box-shadow: 0 4px 20px rgba(16, 185, 129, 0.1);
        }

        .alert-error {
            background: rgba(239, 68, 68, 0.1);
            border-color: rgba(239, 68, 68, 0.25);
            color: #fecaca;
            box-shadow: 0 4px 20px rgba(239, 68, 68, 0.1);
        }

        /* Duplicate Groups list */
        .section-header {
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            animation: fadeIn 1s ease 0.3s both;
        }

        .section-header h2 {
            font-size: 1.5rem;
            font-weight: 700;
            background: linear-gradient(to right, #fff, var(--text-muted));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .section-header .badge {
            background: rgba(99, 102, 241, 0.1);
            color: var(--accent-primary);
            border: 1px solid rgba(99, 102, 241, 0.2);
            padding: 0.35rem 0.8rem;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
        }

        .duplicate-list {
            display: flex;
            flex-direction: column;
            gap: 2rem;
            animation: fadeIn 1s ease 0.4s both;
        }

        /* Glass Card for Groups */
        .group-card {
            background: var(--glass-bg);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid var(--border-color);
            border-radius: 24px;
            overflow: hidden;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
            transition: border-color 0.4s ease, box-shadow 0.4s ease;
        }

        .group-card:hover {
            border-color: rgba(168, 85, 247, 0.2);
            box-shadow: 0 20px 45px rgba(0, 0, 0, 0.3);
        }

        .group-header {
            background: rgba(255, 255, 255, 0.02);
            padding: 1.25rem 2rem;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .group-title {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .group-title-icon {
            font-size: 22px;
            color: var(--accent-secondary);
        }

        .group-title h3 {
            font-size: 1.25rem;
            font-weight: 700;
            color: #fff;
        }

        .group-meta {
            display: flex;
            align-items: center;
            gap: 1.5rem;
        }

        .meta-pill {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid var(--border-color);
            padding: 0.3rem 0.75rem;
            border-radius: 8px;
            font-size: 0.8rem;
            color: var(--text-muted);
            font-weight: 500;
        }

        .meta-pill strong {
            color: #fff;
        }

        /* Custom Modern Table */
        .table-wrapper {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
        }

        th {
            background: rgba(255, 255, 255, 0.01);
            color: var(--text-muted);
            font-weight: 600;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 1.1rem 2rem;
            border-bottom: 1px solid var(--border-color);
        }

        td {
            padding: 1.25rem 2rem;
            border-bottom: 1px solid var(--border-color);
            color: var(--text-main);
            font-size: 0.95rem;
            vertical-align: middle;
        }

        tr:last-child td {
            border-bottom: none;
        }

        tr {
            transition: background 0.3s ease;
        }

        tr:hover {
            background: rgba(255, 255, 255, 0.02);
        }

        .subject-id-badge {
            font-family: monospace;
            background: rgba(255, 255, 255, 0.05);
            padding: 0.25rem 0.5rem;
            border-radius: 6px;
            color: var(--text-muted);
            border: 1px solid var(--border-color);
        }

        .subject-icon-img {
            width: 44px;
            height: 44px;
            object-fit: cover;
            border-radius: 10px;
            border: 1px solid var(--border-color);
            background: rgba(255, 255, 255, 0.02);
        }

        .subject-icon-placeholder {
            width: 44px;
            height: 44px;
            border-radius: 10px;
            background: rgba(255, 255, 255, 0.03);
            border: 1px dashed var(--border-color);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--text-muted);
            font-size: 18px;
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            padding: 0.25rem 0.75rem;
            border-radius: 30px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .status-active {
            background: rgba(16, 185, 129, 0.1);
            color: var(--success-color);
            border: 1px solid rgba(16, 185, 129, 0.15);
        }

        .status-inactive {
            background: rgba(239, 68, 68, 0.1);
            color: var(--error-color);
            border: 1px solid rgba(239, 68, 68, 0.15);
        }

        .metric-value {
            font-family: 'Outfit', sans-serif;
            font-size: 1.05rem;
            font-weight: 600;
        }

        .metric-zero {
            color: var(--text-muted);
            font-weight: 400;
            font-size: 0.95rem;
        }

        .action-cell {
            text-align: right;
        }

        .btn-merge {
            background: rgba(99, 102, 241, 0.1);
            color: var(--accent-primary);
            border: 1px solid rgba(99, 102, 241, 0.2);
            padding: 0.45rem 1rem;
            border-radius: 10px;
            font-size: 0.85rem;
            font-weight: 600;
            font-family: 'Outfit', sans-serif;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-merge:hover {
            background: var(--accent-primary);
            color: #fff;
            box-shadow: 0 4px 12px rgba(99, 102, 241, 0.3);
            transform: translateY(-1px);
        }

        /* Empty state styling */
        .empty-state {
            background: var(--glass-bg);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid var(--border-color);
            border-radius: 24px;
            padding: 4rem 2rem;
            text-align: center;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
            animation: fadeIn 1.2s ease;
        }

        .empty-state-icon {
            font-size: 64px;
            color: var(--success-color);
            margin-bottom: 1.5rem;
            display: inline-flex;
            animation: pulse 2s infinite;
        }

        .empty-state h3 {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            color: #fff;
        }

        .empty-state p {
            color: var(--text-muted);
            font-size: 1rem;
            max-width: 500px;
            margin: 0 auto;
        }

        /* Modal / Confirmation Styling */
        .dialog-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(2, 6, 23, 0.8);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 999;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.3s ease;
        }

        .dialog-overlay.active {
            opacity: 1;
            pointer-events: auto;
        }

        .dialog-box {
            background: var(--bg-secondary);
            border: 1px solid var(--border-color);
            border-radius: 24px;
            width: 100%;
            max-width: 520px;
            padding: 2rem;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            transform: scale(0.9);
            transition: transform 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }

        .dialog-overlay.active .dialog-box {
            transform: scale(1);
        }

        .dialog-header {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-bottom: 1rem;
        }

        .dialog-header i {
            font-size: 28px;
            color: var(--warning-color);
        }

        .dialog-header h3 {
            font-size: 1.35rem;
            font-weight: 700;
        }

        .dialog-body {
            color: var(--text-muted);
            font-size: 0.95rem;
            margin-bottom: 2rem;
            line-height: 1.6;
        }

        .dialog-body strong {
            color: #fff;
        }

        .dialog-footer {
            display: flex;
            justify-content: flex-end;
            gap: 1rem;
        }

        .dialog-footer button {
            border: none;
            font-family: 'Outfit', sans-serif;
            font-weight: 600;
        }

        .btn-confirm {
            background: var(--accent-primary);
            color: #fff;
        }

        .btn-confirm:hover {
            background: #4f46e5;
        }

        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <header>
            <div class="header-title-area">
                <div class="header-icon">
                    <i class="mdi mdi-checkbox-multiple-blank"></i>
                </div>
                <div class="header-text">
                    <h1>Subject Duplicate Resolver</h1>
                    <p>Clean up database integrity issues by merging duplicate subjects in one click.</p>
                </div>
            </div>
            <div class="header-actions">
                <a href="{{ route('admin.subject') }}" class="btn btn-outline">
                    <i class="mdi mdi-arrow-left"></i> Back to Subjects
                </a>
            </div>
        </header>

        <!-- Stats Counter -->
        @php
            $uniqueDuplicatesCount = $groupedSubjects->count();
            $totalDuplicateRecords = 0;
            foreach ($groupedSubjects as $name => $grp) {
                $totalDuplicateRecords += $grp->count();
            }
            $redundantRecordsCount = $totalDuplicateRecords - $uniqueDuplicatesCount;
        @endphp

        <div class="stats-grid">
            <!-- Stat Card 1 -->
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="mdi mdi-format-list-bulleted-type"></i>
                </div>
                <div class="stat-info">
                    <h3>{{ $uniqueDuplicatesCount }}</h3>
                    <p>Duplicate Subject Names</p>
                </div>
            </div>

            <!-- Stat Card 2 -->
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="mdi mdi-database-outline"></i>
                </div>
                <div class="stat-info">
                    <h3>{{ $totalDuplicateRecords }}</h3>
                    <p>Total Duplicate Records</p>
                </div>
            </div>

            <!-- Stat Card 3 -->
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="mdi mdi-delete-sweep-outline"></i>
                </div>
                <div class="stat-info">
                    <h3>{{ $redundantRecordsCount }}</h3>
                    <p>Redundant Records to Clean</p>
                </div>
            </div>
        </div>

        <!-- Success/Error Messages -->
        <div class="notification-container">
            @if (Session::has('success_message'))
                <div class="alert-box alert-success animate__animated animate__fadeIn">
                    <i class="mdi mdi-check-circle-outline"></i>
                    <div>{{ Session::get('success_message') }}</div>
                </div>
            @endif

            @if (Session::has('error_message'))
                <div class="alert-box alert-error animate__animated animate__fadeIn">
                    <i class="mdi mdi-alert-circle-outline"></i>
                    <div>{{ Session::get('error_message') }}</div>
                </div>
            @endif
        </div>

        <!-- Duplicate Subjects List Section -->
        <div class="section-header">
            <h2>Detected Duplicates</h2>
            <span class="badge">{{ $uniqueDuplicatesCount }} Groups Found</span>
        </div>

        @if($groupedSubjects->isEmpty())
            <div class="empty-state">
                <div class="empty-state-icon">
                    <i class="mdi mdi-check-decagram-outline"></i>
                </div>
                <h3>All Clean!</h3>
                <p>No duplicate subject names found in your database. Keep up the clean structure!</p>
            </div>
        @else
            <div class="duplicate-list">
                @foreach($groupedSubjects as $name => $subjectGroup)
                    <div class="group-card">
                        <!-- Group Header -->
                        <div class="group-header">
                            <div class="group-title">
                                <i class="mdi mdi-folder-multiple-outline group-title-icon"></i>
                                <h3>{{ $name }}</h3>
                            </div>
                            <div class="group-meta">
                                <div class="meta-pill">
                                    Records: <strong>{{ $subjectGroup->count() }}</strong>
                                </div>
                                <div class="meta-pill">
                                    Total Products: <strong>{{ $subjectGroup->sum('products_count') }}</strong>
                                </div>
                            </div>
                        </div>

                        <!-- Group Table -->
                        <div class="table-wrapper">
                            <table>
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Icon</th>
                                        <th>Subject Name</th>
                                        <th>Status</th>
                                        <th>Products Linked</th>
                                        <th>Class Mappings</th>
                                        <th>Created Date</th>
                                        <th class="action-cell">Consolidation Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($subjectGroup as $subject)
                                        <tr>
                                            <td>
                                                <span class="subject-id-badge">#{{ $subject->id }}</span>
                                            </td>
                                            <td>
                                                @if($subject->subject_icon)
                                                    <img src="{{ $subject->subject_icon }}" alt="Icon" class="subject-icon-img">
                                                @else
                                                    <div class="subject-icon-placeholder">
                                                        <i class="mdi mdi-image-off-outline"></i>
                                                    </div>
                                                @endif
                                            </td>
                                            <td>
                                                <strong>{{ $subject->name }}</strong>
                                            </td>
                                            <td>
                                                @if($subject->status == 1)
                                                    <span class="status-badge status-active">
                                                        <i class="mdi mdi-circle-medium"></i> Active
                                                    </span>
                                                @else
                                                    <span class="status-badge status-inactive">
                                                        <i class="mdi mdi-circle-medium"></i> Inactive
                                                    </span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($subject->products_count > 0)
                                                    <span class="metric-value">{{ $subject->products_count }}</span>
                                                @else
                                                    <span class="metric-zero">0 products</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($subject->class_subject_mappings_count > 0)
                                                    <span class="metric-value">{{ $subject->class_subject_mappings_count }}</span>
                                                @else
                                                    <span class="metric-zero">0 mappings</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span style="font-size: 0.85rem; color: var(--text-muted);">
                                                    {{ $subject->created_at ? $subject->created_at->format('M d, Y') : 'N/A' }}
                                                </span>
                                            </td>
                                            <td class="action-cell">
                                                <button type="button" 
                                                        class="btn-merge" 
                                                        onclick="confirmMerge('{{ $subject->id }}', '{{ $name }}', '{{ $subject->products_count }}', '{{ $subject->class_subject_mappings_count }}')">
                                                    Merge Others Into This
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <!-- Confirmation Modal Dialog -->
    <div class="dialog-overlay" id="mergeModal">
        <div class="dialog-box">
            <div class="dialog-header">
                <i class="mdi mdi-alert-outline"></i>
                <h3>Confirm Subject Merge</h3>
            </div>
            <div class="dialog-body">
                <p>You are about to merge all duplicate records for subject "<strong id="modalSubjectName"></strong>" into subject record ID <strong id="modalTargetId"></strong>.</p>
                <br>
                <p>This action will automatically:</p>
                <ul style="padding-left: 1.25rem; margin-top: 0.5rem; display: flex; flex-direction: column; gap: 0.25rem;">
                    <li>Re-assign products currently pointing to other duplicates to this subject record.</li>
                    <li>Move class-subject mappings and prevent duplicate mappings.</li>
                    <li>Delete the other redundant duplicate subject records permanently.</li>
                </ul>
                <br>
                <p style="color: var(--warning-color); font-weight: 500;">Please verify this is correct before confirming.</p>
            </div>
            <div class="dialog-footer">
                <button type="button" class="btn btn-outline" onclick="closeMergeModal()">Cancel</button>
                <form id="mergeForm" action="{{ route('merge.duplicate.subjects') }}" method="POST">
                    @csrf
                    <input type="hidden" name="target_id" id="formTargetId">
                    <input type="hidden" name="duplicate_name" id="formDuplicateName">
                    <button type="submit" class="btn btn-primary btn-confirm">Proceed with Merge</button>
                </form>
            </div>
        </div>
    </div>

    <script>
        function confirmMerge(targetId, name, productsCount, mappingsCount) {
            document.getElementById('modalSubjectName').innerText = name;
            document.getElementById('modalTargetId').innerText = targetId;
            document.getElementById('formTargetId').value = targetId;
            document.getElementById('formDuplicateName').value = name;
            
            document.getElementById('mergeModal').classList.add('active');
        }

        function closeMergeModal() {
            document.getElementById('mergeModal').classList.remove('active');
        }

        // Close on clicking outside dialog box
        document.getElementById('mergeModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeMergeModal();
            }
        });
    </script>
</body>
</html>
