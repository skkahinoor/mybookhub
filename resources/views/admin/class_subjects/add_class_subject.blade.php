@extends('admin.layout.layout')

@section('content')
    <div class="main-panel">
        <div class="content-wrapper">
            <div class="row">
                <div class="col-md-12 grid-margin">
                    <div class="row">
                        <div class="col-12 col-xl-8 mb-4 mb-xl-0">
                            <h3 class="font-weight-bold">Assign Subjects to Class</h3>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">Select Class and Subjects</h4>
                            @if ($errors->any())
                                <div class="alert alert-danger">
                                    <ul>
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                            <form class="forms-sample" action="{{ route('admin.class_subjects.store') }}" method="POST">
                                @csrf
                                <div class="form-group">
                                    <label for="subcategory_id">Select Class</label>
                                    <select name="subcategory_id" id="subcategory_id" class="form-control" required>
                                        <option value="">Select Class</option>
                                        @foreach ($classes as $class)
                                            <option value="{{ $class->id }}">{{ $class->subcategory_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Select Subjects</label>
                                    <div class="row">
                                        @foreach ($subjects as $subject)
                                            <div class="col-md-6">
                                                <div class="form-check">
                                                    <label class="form-check-label">
                                                        <input type="checkbox" name="subject_ids[]"
                                                            value="{{ $subject->id }}" class="form-check-input">
                                                        {{ $subject->name }}
                                                    </label>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary mr-2">Assign Subjects</button>
                                <a href="{{ route('admin.class_subjects.index') }}" class="btn btn-light">Cancel</a>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
