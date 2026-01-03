@extends('admin.layout.layout')

@section('content')
    <div class="main-panel">
        <div class="content-wrapper">
            <div class="row">
                <div class="col-md-8 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">{{ $title }}</h4>

                            @if ($errors->any())
                                <div class="alert alert-danger">
                                    <ul class="mb-0">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <form class="forms-sample" action="{{ url('admin/add-edit-banner' . (!empty($banner->id) ? '/' . $banner->id : '')) }}" method="POST" enctype="multipart/form-data">
                                @csrf

                                <div class="form-group">
                                    <label>Type</label>
                                    <select name="type" class="form-control">
                                        <option value="Slider" {{ old('type', $banner->type ?? '') === 'Slider' ? 'selected' : '' }}>Slider</option>
                                        <option value="Fix" {{ old('type', $banner->type ?? '') === 'Fix' ? 'selected' : '' }}>Fix</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label>Link</label>
                                    <input type="text" name="link" class="form-control" value="{{ old('link', $banner->link ?? '') }}" placeholder="e.g. spring-collection">
                                </div>

                                <div class="form-group">
                                    <label>Title</label>
                                    <input type="text" name="title" class="form-control" value="{{ old('title', $banner->title ?? '') }}" placeholder="Banner Title">
                                </div>

                                <div class="form-group">
                                    <label>Alt</label>
                                    <input type="text" name="alt" class="form-control" value="{{ old('alt', $banner->alt ?? '') }}" placeholder="Image ALT text">
                                </div>

                                <div class="form-group">
                                    <label>Status</label>
                                    <select name="status" class="form-control">
                                        <option value="1" {{ old('status', $banner->status ?? 1) == 1 ? 'selected' : '' }}>Active</option>
                                        <option value="0" {{ old('status', $banner->status ?? 1) == 0 ? 'selected' : '' }}>Inactive</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label>Image</label>
                                    <input type="file" name="image" class="form-control">
                                    @if (!empty($banner->image))
                                        <div class="mt-2">
                                            <img src="{{ asset('front/images/banner_images/' . $banner->image) }}" alt="{{ $banner->alt }}" style="width: 220px">
                                        </div>
                                    @endif
                                </div>

                                <button type="submit" class="btn btn-primary mr-2">Save</button>
                                <a href="{{ url('admin/banners') }}" class="btn btn-light">Cancel</a>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


