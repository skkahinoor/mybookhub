@extends('admin.layout.layout')

@section('content')
    <div class="main-panel">
        <div class="content-wrapper">
            <div class="row justify-content-center">
                <div class="col-lg-10 grid-margin stretch-card">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div>
                                    <h4 class="card-title mb-1">Commission Settings</h4>
                                    <p class="text-muted mb-0 small">
                                        Configure global default commission rates for sales executives.
                                    </p>
                                </div>
                            </div>

                            {{-- Success message --}}
                            @if (session('success_message'))
                                <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
                                    <strong>Success:</strong> {{ session('success_message') }}
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            @endif

                            {{-- Error message --}}
                            @if (session('error_message'))
                                <div class="alert alert-danger alert-dismissible fade show mt-3" role="alert">
                                    <strong>Error:</strong> {{ session('error_message') }}
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            @endif

                            {{-- Validation errors --}}
                            @if ($errors->any())
                                <div class="alert alert-danger alert-dismissible fade show mt-3" role="alert">
                                    <ul class="mb-0">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            @endif

                            <form method="POST" action="{{ route('admin.commission.settings.update') }}" class="mt-4">
                                @csrf

                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="default_income_per_target">Default Income Per Student Registration
                                                (₹)</label>
                                            <input type="number" class="form-control form-control-lg"
                                                id="default_income_per_target" name="default_income_per_target"
                                                value="{{ $defaultIncomePerTarget }}" min="0" step="0.01"
                                                required>
                                            <small class="form-text text-muted">
                                                Earned when a student referred by the executive becomes active.
                                            </small>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="default_income_per_vendor">Default Income Per Vendor Free Plan
                                                (₹)</label>
                                            <input type="number" class="form-control form-control-lg"
                                                id="default_income_per_vendor" name="default_income_per_vendor"
                                                value="{{ $defaultIncomePerVendor }}" min="0" step="0.01"
                                                required>
                                            <small class="form-text text-muted">
                                                Earned when a vendor referred by the executive becomes active on Free plan.
                                            </small>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="default_income_per_pro_vendor">Default Income Per Vendor Pro Plan
                                                (₹)</label>
                                            <input type="number" class="form-control form-control-lg"
                                                id="default_income_per_pro_vendor" name="default_income_per_pro_vendor"
                                                value="{{ $defaultIncomePerProVendor }}" min="0" step="0.01"
                                                required>
                                            <small class="form-text text-muted">
                                                Earned when a vendor referred by the executive takes or upgrades to Pro
                                                plan.
                                            </small>
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-4 d-flex justify-content-end">
                                    <a href="{{ url('admin/dashboard') }}" class="btn btn-light mr-2">
                                        Cancel
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        Update Settings
                                    </button>
                                </div>
                            </form>

                        </div> {{-- card-body --}}
                    </div> {{-- card --}}
                </div>
            </div>
        </div>
    </div>
@endsection
