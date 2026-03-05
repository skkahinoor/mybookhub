@extends('admin.layout.layout')


@section('content')
    <div class="main-panel">
        <div class="content-wrapper">
            <div class="row">
                <div class="col-lg-12 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">Education Levels</h4>




                            <a href="{{ route('admin.add_edit_education_level') }}"
                                style="max-width: 150px; float: right; display: inline-block"
                                class="btn btn-block btn-primary"><i class="mdi mdi-plus"></i> Add Education Level</a>

                            @if (Session::has('success_message'))
                                <!-- Check AdminController.php, updateAdminPassword() method -->
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    <strong>Success:</strong> {{ Session::get('success_message') }}
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            @endif


                            <div class="table-responsive pt-3">
                                {{-- DataTable --}}
                                <table id="education-levels" class="table table-bordered"> {{-- using the id here for the DataTable --}}
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Icon</th>
                                            <th>Name</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($sections as $key => $section)
                                            <tr>
                                                <td>{{ $key + 1 }}</td>

                                                <!-- IMAGE COLUMN -->
                                                <td>
                                                    @if (!empty($section['image']))
                                                        <img src="{{ $section['image'] }}" width="60" height="60"
                                                            style="object-fit: cover; border-radius: 6px; border:1px solid #ddd;">
                                                    @else
                                                        <img src="{{ asset('admin/images/no-image.png') }}" width="60"
                                                            height="60"
                                                            style="object-fit: cover; border-radius: 6px; border:1px solid #ddd;">
                                                    @endif
                                                </td>

                                                <td>{{ $section['name'] }}</td>

                                                <td>
                                                    @if ($adminType === 'vendor')
                                                        <a class="updateSectionStatus"
                                                            id="education-level-{{ $section['id'] }}"
                                                            education_level_id="{{ $section['id'] }}"
                                                            data-url="{{ route('vendor.updateeducationstatus') }}"
                                                            href="javascript:void(0)">
                                                            <i style="font-size: 25px" class="mdi mdi-bookmark-check"
                                                                status="Active"></i>
                                                        </a>
                                                    @else
                                                        @if ($section['status'] == 1)
                                                            <a class="updateSectionStatus"
                                                                id="education-level-{{ $section['id'] }}"
                                                                education_level_id="{{ $section['id'] }}"
                                                                data-url="{{ route('admin.updateeducationstatus') }}"
                                                                href="javascript:void(0)">
                                                                <i style="font-size: 25px" class="mdi mdi-bookmark-check"
                                                                    status="Active"></i>
                                                            </a>
                                                        @else
                                                            <a class="updateSectionStatus"
                                                                id="education-level-{{ $section['id'] }}"
                                                                education_level_id="{{ $section['id'] }}"
                                                                data-url="{{ route('admin.updateeducationstatus') }}"
                                                                href="javascript:void(0)">
                                                                <i style="font-size: 25px" class="mdi mdi-bookmark-outline"
                                                                    status="Inactive"></i>
                                                            </a>
                                                        @endif
                                                    @endif
                                                </td>

                                                <td>
                                                    @php
                                                        $editRoute =
                                                            $adminType === 'vendor'
                                                                ? 'vendor.add_edit_education_level'
                                                                : 'admin.add_edit_education_level';
                                                    @endphp
                                                    <a href="{{ route($editRoute, $section['id']) }}">
                                                        <i style="font-size: 25px" class="mdi mdi-pencil-box"></i>
                                                    </a>

                                                    @php
                                                        $deleteRoute =
                                                            $adminType === 'vendor'
                                                                ? 'vendor.delete_education'
                                                                : 'admin.delete_education';
                                                    @endphp
                                                    <a href="{{ route($deleteRoute, $section['id']) }}"
                                                        onclick="return confirm('Are you sure you want to delete this education level?')">
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
        <!-- content-wrapper ends -->
        <!-- partial:../../partials/_footer.html -->
        <footer class="footer">
            <div class="d-sm-flex justify-content-center justify-content-sm-between">
                <span class="text-muted text-center text-sm-left d-block d-sm-inline-block">Copyright © 2022. All rights
                    reserved.</span>
            </div>
        </footer>
        <!-- partial -->
    </div>
@endsection
