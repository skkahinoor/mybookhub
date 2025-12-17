@extends('admin.layout.layout')

@section('content')
<div class="container-fluid">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Custom CSS for enhanced styling -->
    <style>
        .stats-card {
            background-color: #253858; /* dark blue for generic stats */
            border-radius: 15px;
            border: none;
            box-shadow: 0 8px 24px rgba(30,35,90,0.12);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .stats-card.success {
            background-color: #25836e; /* teal for success */
        }
        .stats-card.warning {
            background-color: #333840; /* dark gray for warning */
        }
        .stats-card.info {
            background-color: #dbe9fa; /* pale blue for info */
            color: #1d3354;
        }
        .stats-card.danger {
            background-color: #b91c1c; /* red for danger */
        }

        .student-card {
            background: #fff;
            border-radius: 12px;
            border: 1px solid #e9ecef;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            transition: all 0.3s ease;
            margin-bottom: 20px;
        }
        .student-card:hover {
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
            transform: translateY(-2px);
        }
        .student-header {
            background-color: #253858; /* same dark blue */
            color: white;
            padding: 20px;
            border-radius: 12px 12px 0 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 8px;
        }
        .student-name {
            margin: 0;
            font-size: 1.25rem;
            font-weight: 600;
        }
        .student-roll {
            font-size: 0.9rem;
            opacity: 0.9;
            margin: 0;
            flex-shrink: 0;
            white-space: nowrap;
        }

        .btn-edit {
            background-color: #25836e;
            border: none;
            color: white;
            padding: 4px 8px;
            border-radius: 4px;
            text-decoration: none;
            font-size: 0.85rem;
            transition: all 0.3s ease;
        }
        .btn-edit:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(37, 131, 110, 0.28);
            color: white;
            text-decoration: none;
        }
        .btn-delete {
            background-color: #b91c1c;
            border: none;
            color: white;
            padding: 4px 8px;
            border-radius: 4px;
            text-decoration: none;
            font-size: 0.85rem;
            transition: all 0.3s ease;
        }
        .btn-delete:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(185, 28, 28, 0.25);
            color: white;
            text-decoration: none;
        }
        .add-student-btn {
            background-color: #25836e;
            border: none;
            color: white;
            padding: 12px 24px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 30px;
        }
        .add-student-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 32px rgba(37, 131, 110, 0.14);
            color: white;
            text-decoration: none;
        }
        .institution-badge {
            background-color: #253858;
            color: white;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 0.75rem;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .no-institution {
            background: #636e72;
            color: white;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 0.75rem;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
    </style>

    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="page-title">Student Management</h2>
                    <p class="page-subtitle">Manage student records and information</p>
                </div>
                <a href="{{ url('admin/students/create') }}" class="add-student-btn">
                    <i class="fas fa-plus"></i>
                    Add New Student
                </a>
            </div>

            @if(Session::has('success_message'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <strong>Success!</strong> {{ Session::get('success_message') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            <!-- Statistics Cards -->
            <div class="row mb-4">
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="card stats-card success">
                        <div class="card-body text-center">
                            <i class="fas fa-user-graduate fa-2x mb-3" style="color: white;"></i>
                            <h4 class="mb-1" style="color: white;">{{ $students->count() }}</h4>
                            <p class="mb-0" style="color: white; opacity: 0.9;">Total Students</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="card stats-card warning">
                        <div class="card-body text-center">
                            <i class="fas fa-male fa-2x mb-3" style="color: white;"></i>
                            <h4 class="mb-1" style="color: white;">{{ $students->where('gender', 'male')->count() }}</h4>
                            <p class="mb-0" style="color: white; opacity: 0.9;">Male Students</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="card stats-card success">
                        <div class="card-body text-center">
                            <i class="fas fa-female fa-2x mb-3" style="color: white;"></i>
                            <h4 class="mb-1" style="color: white;">{{ $students->where('gender', 'female')->count() }}</h4>
                            <p class="mb-0" style="color: white; opacity: 0.9;">Female Students</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="card stats-card warning">
                        <div class="card-body text-center">
                            <i class="fas fa-school fa-2x mb-3" style="color: white;"></i>
                            <h4 class="mb-1" style="color: white;">{{ $students->whereNotNull('institution_id')->count() }}</h4>
                            <p class="mb-0" style="color: white; opacity: 0.9;">With Institution</p>
                        </div>
                    </div>
                </div>
            </div>

            @if($students->count() > 0)
                <div class="row">
                    @foreach($students as $student)
                        <div class="col-lg-6 col-xl-4">
                            <div class="student-card">
                                <div class="student-header">
                                    <div>
                                        <h5 class="student-name">{{ $student->name }}</h5>
                                        <p class="student-roll">
                                            <i class="fas fa-id-card"></i>
                                            Roll: {{ $student->roll_number }}
                                        </p>
                                    </div>

                                    @if($student->institution && $student->institution->type)
                                        <span class="badge badge-{{ $student->institution->type == 'school' ? 'primary' : ($student->institution->type == 'college' ? 'success' : 'info') }}">
                                            <i class="fas fa-{{ $student->institution->type == 'school' ? 'school' : ($student->institution->type == 'college' ? 'graduation-cap' : 'university') }}"></i>
                                            {{ ucfirst($student->institution->type) }}
                                        </span>
                                    @endif
                                </div>
                                <div class="student-body">
                                    <div class="student-info">
                                        <div class="info-item">
                                            <i class="fas fa-envelope info-icon"></i>
                                            <span class="info-text">{{ $student->email ?: 'No email' }}</span>
                                        </div>
                                        <div class="info-item">
                                            <i class="fas fa-phone info-icon"></i>
                                            <span class="info-text">{{ $student->phone }}</span>
                                        </div>
                                        <div class="info-item">
                                            <i class="fas fa-layer-group info-icon"></i>
                                            <span class="info-text">{{ $student->class }}</span>
                                        </div>
                                        
                                        <div class="info-item">
                                            <i class="fas fa-birthday-cake info-icon"></i>
                                            <span class="info-text">{{ \Carbon\Carbon::parse($student->dob)->format('d M Y') }}</span>
                                        </div>
                                    </div>

                                    @if($student->institution)
                                        <div class="mt-3">
                                            <span class="institution-badge">
                                                <i class="fas fa-school"></i>
                                                {{ $student->institution->name }}
                                            </span>
                                        </div>
                                    @else
                                        <div class="mt-3">
                                            <span class="no-institution">
                                                <i class="fas fa-exclamation-triangle"></i>
                                                No Institution Assigned
                                            </span>
                                        </div>
                                    @endif

                                    <div class="action-buttons mt-3 d-flex gap-2">
                                        <a href="{{ url('admin/students/'.$student->id.'/edit') }}" class="btn-edit">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                        <a href="javascript:void(0)" class="btn-delete" onclick="confirmDelete({{ $student->id }})">
                                            <i class="fas fa-trash"></i> Delete
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="empty-state text-center py-5">
                    <i class="fas fa-user-graduate fa-4x mb-3"></i>
                    <h4>No Students Found</h4>
                    <p>Start by adding your first student to the system.</p>
                    <a href="{{ url('admin/students/create') }}" class="add-student-btn">
                        <i class="fas fa-plus"></i>
                        Add New Student
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
function confirmDelete(id) {
    if (confirm("Are you sure you want to delete this student? This action cannot be undone.")) {
        var form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ url("admin/students") }}/' + id;

        var csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        form.appendChild(csrfToken);

        var methodField = document.createElement('input');
        methodField.type = 'hidden';
        methodField.name = '_method';
        methodField.value = 'DELETE';
        form.appendChild(methodField);

        document.body.appendChild(form);
        form.submit();
    }
}
</script>

@endsection
