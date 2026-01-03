@extends('admin.layout.layout')

@section('content')
<div class="container mt-5">
    <h4>Upload Favicon</h4>

    @if(session('success_message'))
        <div class="alert alert-success">{{ session('success_message') }}</div>
    @endif

    <form method="POST" action="{{ route('favicon') }}" enctype="multipart/form-data">
        @csrf
        <div class="form-group">
            <label>Choose Favicon</label>
            <input type="file" name="favicon" class="form-control">
        </div>

        @if($logos && $logos->favicon)
            <p class="mt-2">Current Favicon:</p>
            <img src="{{ asset('uploads/favicons/' . $logos->favicon) }}" height="32" width="32" alt="Current favicon">
        @else
            <p class="mt-2 text-muted">No favicon uploaded yet.</p>
        @endif

        <button class="btn btn-primary mt-3" type="submit">Upload</button>
    </form>
</div>
@endsection
