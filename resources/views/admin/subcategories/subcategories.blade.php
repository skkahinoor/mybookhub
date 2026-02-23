@extends('admin.layout.layout')

@section('content')
    <div class="main-panel">
        <div class="content-wrapper">
            <div class="row">
                <div class="col-lg-12 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">Subcategories</h4>

                            <a href="{{ url('admin/add-edit-subcategory') }}"
                                style="max-width: 150px; float: right; display: inline-block"
                                class="btn btn-block btn-primary"><i class="mdi mdi-plus"></i> Add Subcategory</a>

                            @if (Session::has('success_message'))
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    <strong>Success:</strong> {{ Session::get('success_message') }}
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            @endif

                            <div class="table-responsive pt-3">
                                <table id="subcategories" class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Subcategory Name</th>
                                            <th>Category</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($subcategories as $key => $subcategory)
                                            <tr>
                                                <td>{{ $key + 1 }}</td>
                                                <td>{{ $subcategory['subcategory_name'] }}</td>
                                                <td>{{ $subcategory['category']['category_name'] ?? 'N/A' }}</td>
                                                <td>
                                                    @if ($subcategory['status'] == 1)
                                                        <a class="updateSubcategoryStatus"
                                                            id="subcategory-{{ $subcategory['id'] }}"
                                                            subcategory_id="{{ $subcategory['id'] }}"
                                                            data-url="{{ route('admin.updatesubcategorystatus') }}"
                                                            href="javascript:void(0)">
                                                            <i style="font-size: 25px" class="mdi mdi-bookmark-check"
                                                                status="Active"></i>
                                                        </a>
                                                    @else
                                                        <a class="updateSubcategoryStatus"
                                                            id="subcategory-{{ $subcategory['id'] }}"
                                                            subcategory_id="{{ $subcategory['id'] }}"
                                                            data-url="{{ route('admin.updatesubcategorystatus') }}"
                                                            href="javascript:void(0)">
                                                            <i style="font-size: 25px" class="mdi mdi-bookmark-outline"
                                                                status="Inactive"></i>
                                                        </a>
                                                    @endif
                                                </td>
                                                <td>
                                                    <a
                                                        href="{{ url('admin/add-edit-subcategory/' . $subcategory['id']) }}">
                                                        <i style="font-size: 25px" class="mdi mdi-pencil-box"></i>
                                                    </a>
                                                    <a href="{{ url('admin/delete-subcategory/' . $subcategory['id']) }}"
                                                        class="confirmDelete" data-module="subcategory"
                                                        data-url="{{ url('admin/delete-subcategory/' . $subcategory['id']) }}">
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
        @include('admin.layout.footer')
    </div>
@endsection
