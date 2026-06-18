@extends('admin.layout.layout')

@section('content')
    <div class="main-panel">
        <div class="content-wrapper">
            <div class="row justify-content-center">
                <div class="col-lg-8 grid-margin stretch-card">
                    <div class="card shadow-sm border-0" style="border-radius: 16px; overflow: hidden;">
                        <div class="card-header bg-white border-0 pt-4 px-4">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h4 class="card-title text-primary mb-1 font-weight-bold" style="font-size: 1.4rem;">Old Book Settings</h4>
                                    <p class="text-muted mb-0 small">
                                        Manage student book selling settings on the platform.
                                    </p>
                                </div>
                                <div class="bg-light-primary p-2 rounded-circle">
                                    <i class="ti-settings text-primary" style="font-size: 1.5rem;"></i>
                                </div>
                            </div>
                        </div>
                        <div class="card-body px-4 pb-4">
                            {{-- Success message --}}
                            @if (session('success_message'))
                                <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert" style="border-radius: 12px; background-color: #e8f5e9; color: #2e7d32;">
                                    <strong>Success:</strong> {{ session('success_message') }}
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true" style="color: #2e7d32;">&times;</span>
                                    </button>
                                </div>
                            @endif

                            {{-- Error message --}}
                            @if (session('error_message'))
                                <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm" role="alert" style="border-radius: 12px; background-color: #ffebee; color: #c62828;">
                                    <strong>Error:</strong> {{ session('error_message') }}
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true" style="color: #c62828;">&times;</span>
                                    </button>
                                </div>
                            @endif

                            {{-- Validation errors --}}
                            @if ($errors->any())
                                <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm" role="alert" style="border-radius: 12px; background-color: #ffebee; color: #c62828;">
                                    <ul class="mb-0 pl-3">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true" style="color: #c62828;">&times;</span>
                                    </button>
                                </div>
                            @endif

                            <form method="POST" action="{{ route('admin.old-book-settings.update') }}" class="mt-4">
                                @csrf

                                <div class="card bg-light border-0 mb-4" style="border-radius: 12px;">
                                    <div class="card-body p-4">
                                        <div class="row align-items-center">
                                            <div class="col-md-8">
                                                <div class="form-group mb-0">
                                                    <label class="font-weight-bold text-dark mb-2" style="font-size: 1.1rem; display: block;">
                                                        Enable Sell Book Concept
                                                    </label>
                                                    <div class="custom-control custom-switch custom-switch-lg mt-2">
                                                        <input type="hidden" name="sell_book_concept_enabled" value="0">
                                                        <input type="checkbox"
                                                               class="custom-control-input"
                                                               id="sell_book_concept_enabled"
                                                               name="sell_book_concept_enabled"
                                                               value="1"
                                                               {{ $sellBookConceptEnabled ? 'checked' : '' }}>
                                                        <label class="custom-control-label font-weight-bold" for="sell_book_concept_enabled" style="cursor: pointer; padding-left: 10px; font-size: 1.05rem;">
                                                            <span id="status-label" class="{{ $sellBookConceptEnabled ? 'text-success' : 'text-danger' }}">
                                                                {{ $sellBookConceptEnabled ? 'Active / Enabled' : 'Disabled' }}
                                                            </span>
                                                        </label>
                                                    </div>
                                                    <small class="form-text text-muted mt-3" style="font-size: 0.9rem; line-height: 1.5;">
                                                        If enabled, students can list, update, and manage old books to sell directly from the student dashboard.
                                                        If disabled, visiting the sell book page will display a download badge prompting students to download the Android app.
                                                    </small>
                                                </div>
                                            </div>
                                            <div class="col-md-4 text-center d-none d-md-block">
                                                <div class="p-3">
                                                    <div id="status-icon-box" class="p-4 rounded-circle mx-auto d-flex align-items-center justify-content-center" style="width: 100px; height: 100px; transition: all 0.3s ease; background-color: {{ $sellBookConceptEnabled ? '#e8f5e9' : '#ffebee' }};">
                                                        <i id="status-icon" class="{{ $sellBookConceptEnabled ? 'mdi mdi-check-circle text-success' : 'mdi mdi-close-circle text-danger' }}" style="font-size: 3rem; transition: all 0.3s ease;"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-end align-items-center">
                                    <a href="{{ url('admin/dashboard') }}" class="btn btn-light mr-2 font-weight-bold px-4 py-2" style="border-radius: 10px; font-size: 0.95rem;">
                                        Cancel
                                    </a>
                                    <button type="submit" class="btn btn-primary font-weight-bold px-4 py-2" style="border-radius: 10px; font-size: 0.95rem; background-color: #435ebe; border-color: #435ebe; box-shadow: 0 4px 10px rgba(67, 94, 190, 0.2);">
                                        Save Changes
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.getElementById('sell_book_concept_enabled').addEventListener('change', function(e) {
            const isChecked = e.target.checked;
            const label = document.getElementById('status-label');
            const iconBox = document.getElementById('status-icon-box');
            const icon = document.getElementById('status-icon');

            if (isChecked) {
                label.innerText = 'Active / Enabled';
                label.className = 'text-success';
                iconBox.style.backgroundColor = '#e8f5e9';
                icon.className = 'mdi mdi-check-circle text-success';
            } else {
                label.innerText = 'Disabled';
                label.className = 'text-danger';
                iconBox.style.backgroundColor = '#ffebee';
                icon.className = 'mdi mdi-close-circle text-danger';
            }
        });
    </script>
    @endpush
@endsection
