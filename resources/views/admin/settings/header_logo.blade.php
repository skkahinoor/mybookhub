@extends('admin.layout.layout')

@section('content')
<div class="container mt-5">
    <h4>Upload Header Logo</h4>

    @if(session('success_message'))
        <div class="alert alert-success">{{ session('success_message') }}</div>
    @endif

    <form method="POST" action="{{ url('/admin/header-logo') }}" enctype="multipart/form-data">
        @csrf
        <div class="form-group">
            <label>Choose Logo</label>
            <input type="file" name="logo" class="form-control">
        </div>

       {{-- @foreach ($logos as $logo) --}}
        @if($logos && $logos->logo)
            <p class="mt-2">Current Logo:</p>
            <img src="{{ asset('uploads/logos/' . $logos->logo) }}" height="60" alt="Current header logo">
        @else
            <p class="mt-2 text-muted">No logo uploaded yet.</p>
        @endif
      {{-- @endforeach --}}

        <button class="btn btn-primary mt-3" type="submit">Upload</button>
    </form>
</div>
@endsection
