@extends('admin.layout.layout')

@section('content')
<div class="main-panel">
    <div class="content-wrapper">
        <div class="row">
            <div class="col-md-6 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">{{ $title }}</h4>
                        <p class="card-description">Set the commission percentage for old books.</p>

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
                              action="{{ is_null($commission->id) ? route('admin.old_book_commissions.store') : route('admin.old_book_commissions.update', $commission->id) }}"
                              method="POST">
                            @csrf
                            @if (!is_null($commission->id))
                                @method('PUT')
                            @endif

                            <div class="form-group">
                                <label for="percentage">Commission Percentage (%)</label>
                                <div class="input-group">
                                    <input type="number" step="0.01" class="form-control"
                                           id="percentage" name="percentage"
                                           placeholder="Enter percentage (e.g. 15.00)"
                                           value="{{ old('percentage', $commission->percentage) }}" required>
                                    <div class="input-group-append">
                                        <span class="input-group-text">%</span>
                                    </div>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary mr-2">Submit</button>
                            <a href="{{ route('admin.old_book_commissions.index') }}" class="btn btn-light">Cancel</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
