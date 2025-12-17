@extends('admin.layout.layout')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="card-title">School Details: {{ $school->name }}</h4>
                        <div>
                            <a href="{{ route('admin.schools.edit', $school->id) }}" class="btn btn-warning">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            <a href="{{ route('admin.schools.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Back to List
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <th width="150">School Name:</th>
                                    <td>{{ $school->name }}</td>
                                </tr>
                                <tr>
                                    <th>Principal Name:</th>
                                    <td>{{ $school->principal_name ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Phone:</th>
                                    <td>{{ $school->phone ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Email:</th>
                                    <td>{{ $school->email ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Website:</th>
                                    <td>
                                        @if($school->website)
                                            <a href="{{ $school->website }}" target="_blank">{{ $school->website }}</a>
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Established Year:</th>
                                    <td>{{ $school->established_year ?? 'N/A' }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <th width="150">Total Students:</th>
                                    <td>{{ number_format($school->total_students) }}</td>
                                </tr>
                                <tr>
                                    <th>Total Teachers:</th>
                                    <td>{{ number_format($school->total_teachers) }}</td>
                                </tr>
                                <tr>
                                    <th>Status:</th>
                                    <td>
                                        <span class="badge {{ $school->status ? 'bg-success' : 'bg-danger' }}">
                                            {{ $school->status ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Created At:</th>
                                    <td>{{ $school->created_at->format('F j, Y \a\t g:i A') }}</td>
                                </tr>
                                <tr>
                                    <th>Updated At:</th>
                                    <td>{{ $school->updated_at->format('F j, Y \a\t g:i A') }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-12">
                            <h5>Address:</h5>
                            <p class="border p-3 bg-light">{{ $school->address }}</p>
                        </div>
                    </div>

                    @if($school->description)
                        <div class="row mt-4">
                            <div class="col-12">
                                <h5>Description:</h5>
                                <p class="border p-3 bg-light">{{ $school->description }}</p>
                            </div>
                        </div>
                    @endif

                    <div class="row mt-4">
                        <div class="col-12">
                            <form action="{{ route('admin.schools.destroy', $school->id) }}" method="POST"
                                  onsubmit="return confirm('Are you sure you want to delete this school?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger">
                                    <i class="fas fa-trash"></i> Delete School
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
