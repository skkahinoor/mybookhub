@extends('admin.layout.layout')

@section('content')
    <div class="main-panel">
        <div class="content-wrapper">
            <div class="row">
                <div class="col-lg-12 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">Home Page Banners</h4>

                            <a href="{{ url('admin/add-edit-banner') }}" style="max-width: 150px; float: right; display: inline-block" class="btn btn-block btn-primary"><i class="mdi mdi-plus"></i> Add Banner</a>

                            @if (Session::has('success_message'))
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    <strong>Success:</strong> {{ Session::get('success_message') }}
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            @endif

                            <div class="table-responsive pt-3">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Image</th>
                                            <th>Type</th>
                                            <th>Link</th>
                                            <th>Title</th>
                                            <th>Alt</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($banners as $banner)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>
                                                    @if (!empty($banner->image))
                                                        <img style="width: 100%; height:100px; border-radius: 0px !important;" src="{{ asset('front/images/banner_images/' . $banner->image) }}" alt="{{ $banner->alt }}">
                                                    @endif
                                                </td>
                                                <td>{{ $banner->type }}</td>
                                                <td>{{ $banner->link }}</td>
                                                <td>{{ $banner->title }}</td>
                                                <td>{{ $banner->alt }}</td>
                                                <td>
                                                    <form action="{{ url('admin/update-banner-status') }}" method="POST">
                                                        @csrf
                                                        <input type="hidden" name="banner_id" value="{{ $banner->id }}">
                                                        <input type="hidden" name="status" value="{{ $banner->status ? 0 : 1 }}">
                                                        <button type="submit" class="btn btn-sm {{ $banner->status ? 'btn-success' : 'btn-secondary' }}">
                                                            {{ $banner->status ? 'Active' : 'Inactive' }}
                                                        </button>
                                                    </form>
                                                </td>
                                                <td>
                                                    <a href="{{ url('admin/add-edit-banner/' . $banner->id) }}" class="btn btn-sm btn-primary">Edit</a>
                                                    <a href="{{ url('admin/delete-banner/' . $banner->id) }}" class="btn btn-sm btn-danger" onclick="return confirm('Delete this banner?')">Delete</a>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="8">No banners found.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


