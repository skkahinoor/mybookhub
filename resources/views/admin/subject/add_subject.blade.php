@extends('admin.layout.layout')

@section('content')
<div class="main-panel">
    <div class="content-wrapper">

        <div class="row">
            <div class="col-md-12 grid-margin">
                <div class="row">
                    <div class="col-12 col-xl-8 mb-4 mb-xl-0">
                        <h4 class="card-title">Subject</h4>
                    </div>
                    <div class="col-12 col-xl-4">
                        <div class="justify-content-end d-flex">
                            <div class="dropdown flex-md-grow-1 flex-xl-grow-0">
                                <button class="btn btn-sm btn-light bg-white dropdown-toggle"
                                        type="button" id="dropdownMenuDate2"
                                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                                    <i class="mdi mdi-calendar"></i> Today (10 Jan 2021)
                                </button>

                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuDate2">
                                    <a class="dropdown-item" href="#">January - March</a>
                                    <a class="dropdown-item" href="#">March - June</a>
                                    <a class="dropdown-item" href="#">June - August</a>
                                    <a class="dropdown-item" href="#">August - November</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Error Message --}}
        @if (Session::has('error_message'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong>Error:</strong> {{ Session::get('error_message') }}
                <button type="button" class="close" data-dismiss="alert">
                    <span>&times;</span>
                </button>
            </div>
        @endif

        {{-- Validation Errors --}}
        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="close" data-dismiss="alert">
                    <span>&times;</span>
                </button>
            </div>
        @endif

        {{-- Success Message --}}
        @if (Session::has('success_message'))
            <div class="alert alert-success alert-dismissible fade show">
                <strong>Success:</strong> {{ Session::get('success_message') }}
                <button type="button" class="close" data-dismiss="alert">
                    <span>&times;</span>
                </button>
            </div>
        @endif


        <div class="row">
            <div class="col-md-6 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Add Subject</h4>

                        @if ($adminType === 'vendor')
                            <form class="forms-sample" action="{{ route('vendor.store.subject') }}" method="POST">
                                @csrf

                                <div class="form-group">
                                    <label for="subject_name">Subject Name</label>
                                    <input type="text" class="form-control" id="subject_name"
                                           name="name" placeholder="Enter Subject Name"
                                           value="{{ $subject['name'] ?? old('name') }}">
                                </div>

                                <button type="submit" class="btn btn-primary mr-2">Submit</button>
                                <a href="{{ url('vendor/subjects') }}" class="btn btn-light">Cancel</a>
                            </form>
                        @else
                            <form class="forms-sample" action="{{ route('admin.store.subject') }}" method="POST">
                                @csrf

                                <div class="form-group">
                                    <label for="subject_name">Subject Name</label>
                                    <input type="text" class="form-control" id="subject_name"
                                           name="name" placeholder="Enter Subject Name"
                                           value="{{ $subject['name'] ?? old('name') }}">
                                </div>

                                <button type="submit" class="btn btn-primary mr-2">Submit</button>
                                <a href="{{ url('admin/subjects') }}" class="btn btn-light">Cancel</a>
                            </form>
                        @endif

                    </div>
                </div>
            </div>
        </div>

    </div>

    @include('admin.layout.footer')
</div>
@endsection
