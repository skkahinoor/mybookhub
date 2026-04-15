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

                            <form class="forms-sample"
                                action="{{ url('admin/add-edit-dynamic-modal' . (!empty($dynamicModal->id) ? '/' . $dynamicModal->id : '')) }}"
                                method="POST" enctype="multipart/form-data">
                                @csrf

                                <div class="form-group">
                                    <label>Text (Optional)</label>
                                    <textarea name="text" class="form-control" rows="4" placeholder="Enter modal text">{{ old('text', $dynamicModal->text ?? '') }}</textarea>
                                </div>

                                <div class="form-group">
                                    <label>Link (Optional)</label>
                                    <input type="text" name="link" class="form-control"
                                        value="{{ old('link', $dynamicModal->link ?? '') }}"
                                        placeholder="https://example.com">
                                </div>

                                <div class="form-group">
                                    <label>Status</label>
                                    <select name="status" class="form-control">
                                        <option value="1" {{ old('status', $dynamicModal->status ?? 1) == 1 ? 'selected' : '' }}>Active</option>
                                        <option value="0" {{ old('status', $dynamicModal->status ?? 1) == 0 ? 'selected' : '' }}>Inactive</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label>Image (Optional)</label>
                                    <input type="file" name="image" class="form-control">
                                    @if (!empty($dynamicModal->image))
                                        <div class="mt-2">
                                            <img src="{{ $dynamicModal->image }}" alt="Dynamic modal image" style="width: 220px">
                                        </div>
                                    @endif
                                </div>

                                <button type="submit" class="btn btn-primary mr-2">Save</button>
                                <a href="{{ url('admin/dynamic-modals') }}" class="btn btn-light">Cancel</a>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

