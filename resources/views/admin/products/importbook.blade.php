@extends('admin.layout.layout')

@section('content')
    <div class="main-panel">
        <div class="content-wrapper">
            <div class="row">
                <div class="col-md-12 grid-margin">
                    <div class="row">
                        <div class="col-12 col-xl-8 mb-4 mb-xl-0">
                            <h3 class="font-weight-bold">Import Book</h3>
                        </div>

                    </div>
                </div>
            </div>

            <div class="row">
    <div class="col-lg-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">

                <!-- Header -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="card-title mb-0">Upload Excel File</h4>

                    <a href="{{ url('admin/import-product/template') }}" class="btn btn-success btn-sm">
                        <i class="mdi mdi-download"></i> Download Template
                    </a>
                </div>

                {{-- Error Messages --}}
                @if (Session::has('error_message'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <strong>Error:</strong> {{ Session::get('error_message') }}
                        <button type="button" class="close" data-dismiss="alert">
                            <span>&times;</span>
                        </button>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
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

                @if (Session::has('success_message'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <strong>Success:</strong> {{ Session::get('success_message') }}
                        <button type="button" class="close" data-dismiss="alert">
                            <span>&times;</span>
                        </button>
                    </div>
                @endif

                <!-- Upload Form -->
                <form method="POST" action="{{ url('admin/import-product') }}" enctype="multipart/form-data">
                    @csrf

                    <div class="form-group">
                        <label class="font-weight-bold">Select Excel File</label>
                        <input type="file" name="import" class="form-control" required>
                        <small class="text-muted">
                            Allowed formats: .xls, .xlsx, .csv
                        </small>
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary">
                            <i class="mdi mdi-upload"></i> Import
                        </button>
                        <a href="{{ url('admin/products') }}" class="btn btn-outline-danger ml-2">
                            Cancel
                        </a>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>

        </div>
        <!-- content-wrapper ends -->
        @include('admin.layout.footer')
        <!-- partial -->
    </div>
@endsection
