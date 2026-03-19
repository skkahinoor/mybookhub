@extends('admin.layout.layout')

@section('content')
    <div class="main-panel">
        <div class="content-wrapper">
            <div class="row">
                <div class="col-lg-12 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">Minimum Order Value (MOV) Cashback</h4>

                            <a href="{{ url('admin/add-edit-mov') }}"
                                style="max-width: 150px; float: right; display: inline-block"
                                class="btn btn-block btn-primary"><i class="mdi mdi-plus"></i> Add MOV</a>

                            @if (Session::has('success_message'))
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    <strong>Success:</strong> {{ Session::get('success_message') }}
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            @endif

                            <div class="table-responsive pt-3">
                                <table id="movs" class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Minimum Cart Value</th>
                                            <th>Cashback Percentage (%)</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($movs as $key => $mov)
                                            <tr>
                                                <td>{{ $key + 1 }}</td>
                                                <td>₹{{ number_format($mov->price, 2) }}</td>
                                                <td>{{ $mov->cashback_percentage }}%</td>
                                                <td>
                                                    <a href="{{ url('admin/add-edit-mov/' . $mov->id) }}">
                                                        <i style="font-size: 25px" class="mdi mdi-pencil-box"></i>
                                                    </a>
                                                    <a href="javascript:void(0)" class="confirmDelete"
                                                        data-module="mov"
                                                        data-url="{{ url('admin/delete-mov/' . $mov->id) }}">
                                                        <i style="font-size: 25px" class="mdi mdi-file-excel-box"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <footer class="footer">
            <div class="d-sm-flex justify-content-center justify-content-sm-between">
                <span class="text-muted text-center text-sm-left d-block d-sm-inline-block">Copyright © {{ date('Y') }}. All rights reserved.</span>
            </div>
        </footer>
    </div>
@endsection
