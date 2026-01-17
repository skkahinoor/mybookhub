@extends('admin.layout.layout')


@section('content')
    <div class="main-panel">
        <div class="content-wrapper">
            <div class="row">
                <div class="col-lg-12 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">Sections</h4>




                            <a href="{{ url('admin/add-edit-section') }}"
                                style="max-width: 150px; float: right; display: inline-block"
                                class="btn btn-block btn-primary"><i class="mdi mdi-plus"></i> Add Section</a>

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
                                <table id="sections" class="table table-bordered"> {{-- using the id here for the DataTable --}}
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Name</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($sections as $key => $section)
                                            <tr>
                                                <td>{{ __($key + 1) }}</td>
                                                <td>{{ $section['name'] }}</td>
                                                <td>
                                                    @if ($adminType === 'vendor')
                                                        <a class="updateSectionStatus" id="section-{{ $section['id'] }}"
                                                            section_id="{{ $section['id'] }}"
                                                            data-url="{{ route('vendor.updatesectionstatus') }}"
                                                            href="javascript:void(0)">
                                                            <i style="font-size: 25px" class="mdi mdi-bookmark-check"
                                                                status="Active"></i>
                                                        </a>
                                                    @else
                                                        @if ($section['status'] == 1)
                                                            <a class="updateSectionStatus" id="section-{{ $section['id'] }}"
                                                                section_id="{{ $section['id'] }}"
                                                                data-url="{{ route('admin.updatesectionstatus') }}"
                                                                href="javascript:void(0)">
                                                                <i style="font-size: 25px" class="mdi mdi-bookmark-check"
                                                                    status="Active"></i>
                                                            </a>
                                                        @else
                                                            <a class="updateSectionStatus"
                                                                id="section-{{ $section['id'] }}"
                                                                section_id="{{ $section['id'] }}"
                                                                data-url="{{ route('admin.updatesectionstatus') }}"
                                                                href="javascript:void(0)">
                                                                <i style="font-size: 25px" class="mdi mdi-bookmark-outline"
                                                                    status="Inactive"></i>
                                                            </a>
                                                        @endif
                                                    @endif
                                                </td>

                                                <td>
                                                    <a href="{{ url('admin/add-edit-section/' . $section['id']) }}">
                                                        <i style="font-size: 25px" class="mdi mdi-pencil-box"></i>
                                                        {{-- Icons from Skydash Admin Panel Template --}}
                                                    </a>

                                                    {{-- Confirm Deletion JS alert and Sweet Alert --}}
                                                    {{-- <a title="Section" class="confirmDelete" href="{{ url('admin/delete-section/' . $section['id']) }}"> --}}
                                                    {{-- <i style="font-size: 25px" class="mdi mdi-file-excel-box"></i> --}} {{-- Icons from Skydash Admin Panel Template --}}
                                                    {{-- </a> --}}


                                                    <a href="{{ url('admin/delete-section/' . $section['id']) }}"
                                                        onclick="return confirm('Are you sure you want to delete this section?')">
                                                        <i style="font-size: 25px" class="mdi mdi-file-excel-box"></i>
                                                        {{-- Icons from Skydash Admin Panel Template --}}
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
