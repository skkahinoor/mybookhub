@extends('admin.layout.layout')
@section('content')
<div class="main-panel">
    <div class="content-wrapper">
        <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">
                            {{ isset($ebook) ? 'Edit Ebook' : 'Add Ebook' }}
                        </h4>

                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form class="forms-sample"
                              @if(isset($ebook))
                                  action="{{ url('admin/add-edit-ebook/'.$ebook->id) }}"
                              @else
                                  action="{{ url('admin/add-edit-ebook') }}"
                              @endif
                              method="POST" enctype="multipart/form-data">
                            @csrf

                            <div class="form-group">
                                <label for="title">Ebook Title</label>
                                <input type="text" class="form-control" id="title" name="title"
                                    value="{{ old('title', $ebook->title ?? '') }}" required>
                            </div>

                            <div class="form-group">
                                <label for="isbn">ISBN</label>
                                <input type="text" class="form-control" id="isbn" name="isbn"
                                    value="{{ old('isbn', $ebook->isbn ?? '') }}" required>
                            </div>

                            <div class="form-group">
                                <label for="author">Author</label>
                                <input type="text" class="form-control" id="author" name="author"
                                    value="{{ old('author', $ebook->author ?? '') }}">
                            </div>

                            <div class="form-group">
                                <label for="cover_image">Cover Image</label>
                                <input type="file" class="form-control" id="cover_image" name="cover_image">
                                @if(isset($ebook) && $ebook->cover_image)
                                    <img src="{{ asset('storage/'.$ebook->cover_image) }}" style="width:120px; height:100px; margin-top:10px;">
                                @endif
                            </div>

                            <div class="form-group">
                                <label for="file">Ebook File (PDF/EPUB)</label>
                                <input type="file" class="form-control" id="file" name="file">
                                @if(isset($ebook) && $ebook->file)
                                    <a href="{{ asset('storage/'.$ebook->file) }}" target="_blank" style="display:block; margin-top:10px;">View Current File</a>
                                @endif
                            </div>

                            <div class="form-group">
                                <label for="category_id">Category</label>
                                <select class="form-control" id="category_id" name="category_id">
                                    <option value="">Select Category</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}"
                                            {{ old('category_id', $ebook->category_id ?? '') == $category->id ? 'selected' : '' }}>
                                            {{ $category->category_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="section_id">Section</label>
                                <select class="form-control" id="section_id" name="section_id">
                                    <option value="">Select Section</option>
                                    @foreach($sections as $section)
                                        <option value="{{ $section->id }}"
                                            {{ old('section_id', $ebook->section_id ?? '') == $section->id ? 'selected' : '' }}>
                                            {{ $section->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="status">Status</label>
                                <select class="form-control" id="status" name="status">
                                    <option value="1" {{ old('status', $ebook->status ?? 1) == 1 ? 'selected' : '' }}>Active</option>
                                    <option value="0" {{ old('status', $ebook->status ?? 1) == 0 ? 'selected' : '' }}>Inactive</option>
                                </select>
                            </div>

                            <button type="submit" class="btn btn-primary mr-2">
                                {{ isset($ebook) ? 'Update' : 'Add' }} Ebook
                            </button>
                            <a href="{{ url('admin/ebooks') }}" class="btn btn-light">Cancel</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
