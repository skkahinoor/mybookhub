@extends('admin.layout.layout')

@section('content')
    <div class="main-panel">
        <div class="content-wrapper">
            <div class="row mb-4">
                <div class="col-12">
                    <h4 class="mb-3">Sales Executive Reports</h4>

                    @php
                        $countries = $countries ?? [];
                        $states = $states ?? [];
                        $districts = $districts ?? [];
                        $blocks = $blocks ?? [];
                    @endphp

                    <div class="card">
                        <div class="card-body">
                            <form id="sales-report-filters" class="row g-3" method="GET"
                                action="{{ route('admin.reports.sales_reports.index') }}">
                                <div class="col-md-3">
                                    <label class="form-label">Country</label>
                                    <select name="country_id" id="filter-country" class="form-control">
                                        <option value="">All</option>
                                        @foreach ($countries as $country)
                                            <option value="{{ $country->id ?? $country['id'] ?? '' }}"
                                                {{ request('country_id') == ($country->id ?? $country['id'] ?? '') ? 'selected' : '' }}>
                                                {{ $country->name ?? $country['name'] ?? '' }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">State</label>
                                    <select name="state_id" id="filter-state" class="form-control">
                                        <option value="">All</option>
                                        @foreach ($states as $state)
                                            <option value="{{ $state->id ?? $state['id'] ?? '' }}"
                                                {{ request('state_id') == ($state->id ?? $state['id'] ?? '') ? 'selected' : '' }}>
                                                {{ $state->name ?? $state['name'] ?? '' }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">District</label>
                                    <select name="district_id" id="filter-district" class="form-control">
                                        <option value="">All</option>
                                        @foreach ($districts as $district)
                                            <option value="{{ $district->id ?? $district['id'] ?? '' }}"
                                                {{ request('district_id') == ($district->id ?? $district['id'] ?? '') ? 'selected' : '' }}>
                                                {{ $district->name ?? $district['name'] ?? '' }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Block</label>
                                    <select name="block_id" id="filter-block" class="form-control">
                                        <option value="">All</option>
                                        @foreach ($blocks as $block)
                                            <option value="{{ $block->id ?? $block['id'] ?? '' }}"
                                                {{ request('block_id') == ($block->id ?? $block['id'] ?? '') ? 'selected' : '' }}>
                                                {{ $block->name ?? $block['name'] ?? '' }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-12 d-flex gap-2 mt-3">
                                    <button type="submit" class="btn btn-primary">Filter</button>
                                    <a href="{{ route('admin.reports.sales_reports.index') }}"
                                        class="btn btn-light border">Reset</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="salesReportsTable" class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Name</th>
                                            <th>Phone</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($salesExecutives as $salesExecutive)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $salesExecutive->name }}</td>
                                                <td>{{ $salesExecutive->phone }}</td>
                                                <td>
                                                    @if ($salesExecutive->status)
                                                        <span class="badge badge-success">Active</span>
                                                    @else
                                                        <span class="badge badge-secondary">Inactive</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <a href="{{ route('admin.reports.sales_reports.show', $salesExecutive->id) }}"
                                                        class="btn btn-sm btn-outline-primary">View</a>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="text-center text-muted">No sales executives found.</td>
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

@push('scripts')
    <script>
        $(document).ready(function() {
            // Optional: enable client-side DataTable (no AJAX)
            $('#salesReportsTable').DataTable({
                order: [[0, 'asc']]
            });
        });
    </script>
@endpush