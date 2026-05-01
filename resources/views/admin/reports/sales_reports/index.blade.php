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
            var table = $('#salesReportsTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('admin.reports.sales_reports.index') }}",
                    data: function(d) {
                        d.country_id = $('#filter-country').val();
                        d.state_id = $('#filter-state').val();
                        d.district_id = $('#filter-district').val();
                        d.block_id = $('#filter-block').val();
                    }
                },
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                    {data: 'name', name: 'users.name'},
                    {data: 'phone', name: 'users.phone'},
                    {data: 'status', name: 'sales_executives.status'},
                    {data: 'action', name: 'action', orderable: false, searchable: false},
                ],
                order: [[1, 'asc']]
            });

            $('#sales-report-filters').on('submit', function(e) {
                e.preventDefault();
                table.draw();
            });
        });
    </script>
@endpush