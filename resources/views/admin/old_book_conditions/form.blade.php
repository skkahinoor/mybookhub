@extends('admin.layout.layout')

@section('content')
<div class="main-panel">
    <div class="content-wrapper">
        <div class="row justify-content-center">
            <div class="col-lg-6 col-md-8">
                <div class="card">
                    <div class="card-body">

                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h4 class="card-title mb-0">{{ $title }}</h4>
                            <a href="{{ route('admin.old_book_conditions.index') }}"
                               class="btn btn-outline-secondary btn-sm">
                                <i class="mdi mdi-arrow-left"></i> Back to List
                            </a>
                        </div>

                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        @php
                            $isEdit  = $condition->id !== null;
                            $action  = $isEdit
                                ? route('admin.old_book_conditions.update', $condition->id)
                                : route('admin.old_book_conditions.store');
                        @endphp

                        <form action="{{ $action }}" method="POST">
                            @csrf
                            @if($isEdit)
                                @method('PUT')
                            @endif

                            {{-- Condition Name --}}
                            <div class="form-group mb-4">
                                <label for="name" class="font-weight-bold">
                                    Condition Name
                                    <span class="text-danger">*</span>
                                </label>
                                <input type="text"
                                       id="name"
                                       name="name"
                                       class="form-control @error('name') is-invalid @enderror"
                                       value="{{ old('name', $condition->name) }}"
                                       placeholder="e.g. Excellent, Good, Fair, Poor"
                                       maxlength="100"
                                       required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">The label shown to students for this book condition.</small>
                            </div>

                            {{-- Percentage --}}
                            <div class="form-group mb-4">
                                <label for="percentage" class="font-weight-bold">
                                    Price Percentage (%)
                                    <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <input type="number"
                                           id="percentage"
                                           name="percentage"
                                           class="form-control @error('percentage') is-invalid @enderror"
                                           value="{{ old('percentage', $condition->percentage) }}"
                                           placeholder="e.g. 80"
                                           min="0"
                                           max="100"
                                           step="0.01"
                                           required>
                                    <div class="input-group-append">
                                        <span class="input-group-text">%</span>
                                    </div>
                                    @error('percentage')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <small class="text-muted">
                                    The percentage of the original price offered for this condition.
                                    e.g. <strong>80%</strong> means the student receives 80% of the book's price.
                                </small>

                                {{-- Live preview --}}
                                <div id="percentagePreview" class="mt-2" style="display:none;">
                                    <span class="badge"
                                          style="background:#ecfdf5;color:#16a34a;padding:6px 14px;border-radius:8px;font-size:13px;font-weight:600;">
                                        For a book priced ₹100 → seller gets
                                        <strong id="previewValue">₹80</strong>
                                    </span>
                                </div>
                            </div>

                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="mdi mdi-content-save"></i>
                                    {{ $isEdit ? 'Update Condition' : 'Save Condition' }}
                                </button>
                                <a href="{{ route('admin.old_book_conditions.index') }}"
                                   class="btn btn-light ms-2 ml-2">Cancel</a>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>
    <footer class="footer">
        <div class="d-sm-flex justify-content-center justify-content-sm-between">
            <span class="text-muted text-center text-sm-left d-block d-sm-inline-block">Copyright &copy; 2024. All rights reserved.</span>
        </div>
    </footer>
</div>
@endsection

@push('scripts')
<script>
    (function () {
        var pct = document.getElementById('percentage');
        var preview = document.getElementById('percentagePreview');
        var val = document.getElementById('previewValue');

        function refresh() {
            var p = parseFloat(pct.value);
            if (!isNaN(p) && p >= 0 && p <= 100) {
                val.textContent = '₹' + p.toFixed(2);
                preview.style.display = 'block';
            } else {
                preview.style.display = 'none';
            }
        }

        pct.addEventListener('input', refresh);
        refresh(); // trigger on load if editing
    })();
</script>
@endpush
