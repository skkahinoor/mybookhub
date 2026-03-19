@extends('admin.layout.layout')

@section('content')
    <div class="main-panel">
        <div class="content-wrapper">
            <div class="row">
                <div class="col-md-12 grid-margin">
                    <div class="row">
                        <div class="col-12 col-xl-8 mb-4 mb-xl-0">
                            <h4 class="card-title">{{ $title }}</h4>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">MOV Cashback Configuration</h4>

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
                                @if (empty($mov->id)) action="{{ url('admin/add-edit-mov') }}" @else action="{{ url('admin/add-edit-mov/' . $mov->id) }}" @endif
                                method="post"> @csrf
                                
                                <div class="form-group">
                                    <label for="price">Minimum Cart Value (₹)</label>
                                    <input type="number" step="0.01" class="form-control" id="price"
                                        placeholder="Enter Minimum Price" name="price"
                                        @if (!empty($mov->price)) value="{{ $mov->price }}" @else value="{{ old('price') }}" @endif>
                                </div>

                                <div class="form-group">
                                    <label for="cashback_percentage">Cashback Percentage (%)</label>
                                    <input type="number" step="0.01" class="form-control" id="cashback_percentage"
                                        placeholder="Enter Cashback Percentage" name="cashback_percentage"
                                        @if (!empty($mov->cashback_percentage)) value="{{ $mov->cashback_percentage }}" @else value="{{ old('cashback_percentage') }}" @endif>
                                </div>

                                <button type="submit" class="btn btn-primary mr-2">Submit</button>
                                <a href="{{ url('admin/movs') }}" class="btn btn-light">Cancel</a>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @include('admin.layout.footer')
    </div>
@endsection
