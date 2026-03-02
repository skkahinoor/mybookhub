@extends('admin.layout.layout')

@section('content')
    <div class="main-panel">
        <div class="content-wrapper">
            <div class="row">
                <div class="col-md-6 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">Add Class</h4>

                            @if (Session::has('error_message'))
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <strong>Error:</strong> {{ Session::get('error_message') }}
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            @endif

                            @if ($errors->any())
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            @endif

                            <form class="forms-sample"
                                @if (empty($subcategory['id'])) action="{{ url('admin/add-edit-subcategory') }}" @else action="{{ url('admin/add-edit-subcategory/' . $subcategory['id']) }}" @endif
                                method="post" enctype="multipart/form-data"> @csrf

                                <div class="form-group">
                                    <label for="subcategory_name">Class Name</label>
                                    <input type="text" class="form-control" id="subcategory_name"
                                        placeholder="Enter Subcategory Name" name="subcategory_name"
                                        @if (!empty($subcategory['subcategory_name'])) value="{{ $subcategory['subcategory_name'] }}" @else value="{{ old('subcategory_name') }}" @endif>
                                </div>

                                <div class="form-group">
                                    <label for="subcategory_icon">Class Icon</label>
                                    <input type="file" class="form-control" id="subcategory_icon"
                                        name="subcategory_icon">
                                    @if (!empty($subcategory['subcategory_icon']))
                                        <div style="margin-top: 10px;">
                                            <img src="{{ asset('admin/images/subcategory_icons/' . $subcategory['subcategory_icon']) }}"
                                                alt="Current Icon" style="width: 50px; height: 50px;">
                                        </div>
                                    @endif
                                </div>

                                <button type="submit" class="btn btn-primary mr-2">Submit</button>
                                <a href="{{ url('admin/subcategories') }}" class="btn btn-light">Cancel</a>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @include('admin.layout.footer')
    </div>
@endsection
