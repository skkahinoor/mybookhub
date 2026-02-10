@extends('admin.layout.layout')

@section('content')
    <div class="main-panel">
        <div class="content-wrapper">
            <!-- Font Awesome -->
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

            <style>
                :root {
                    --primary-color: #253858;
                    --success-color: #25836e;
                    --warning-color: #f59e0b;
                    --danger-color: #dc2626;
                    --info-color: #3b82f6;
                    --light-bg: #f8fafc;
                    --card-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
                    --card-shadow-hover: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
                    --border-radius: 12px;
                    --transition: all 0.2s ease-in-out;
                }

                .page-header-custom {
                    position: relative;
                    background: linear-gradient(135deg, var(--primary-color) 0%, #1e3a5f 100%);
                    color: white;
                    padding: 2rem;
                    margin-bottom: 2rem;
                    border-radius: var(--border-radius);
                    box-shadow: var(--card-shadow);
                    overflow: hidden;
                }

                .page-header-custom::after {
                    content: '';
                    position: absolute;
                    width: 220px;
                    height: 220px;
                    top: -60px;
                    right: -60px;
                    background: rgba(255, 255, 255, 0.08);
                    border-radius: 50%;
                    filter: blur(0.5px);
                }

                .page-header-content {
                    position: relative;
                    z-index: 1;
                    display: flex;
                    align-items: center;
                    justify-content: space-between;
                    gap: 2rem;
                    flex-wrap: wrap;
                }

                .page-title-custom {
                    font-size: 1.75rem;
                    font-weight: 700;
                    margin: 0;
                    color: white;
                }

                .page-subtitle-custom {
                    font-size: 0.95rem;
                    margin: 0;
                    opacity: 0.9;
                    color: rgba(255, 255, 255, 0.85);
                }

                .nav-tabs-custom {
                    display: flex;
                    gap: 1rem;
                    margin-bottom: 1.5rem;
                    border: none;
                }

                .nav-tabs-custom .nav-link {
                    background: white;
                    border: 1px solid #e2e8f0;
                    color: #64748b;
                    padding: 0.75rem 1.5rem;
                    border-radius: 8px;
                    font-weight: 600;
                    transition: var(--transition);
                }

                .nav-tabs-custom .nav-link.active {
                    background: var(--primary-color) !important;
                    color: white !important;
                    border-color: var(--primary-color) !important;
                    box-shadow: var(--card-shadow);
                }

                .nav-tabs-custom .nav-link:hover:not(.active) {
                    background: #f1f5f9;
                    color: var(--primary-color);
                }

                .main-table-card {
                    background: white;
                    border-radius: var(--border-radius);
                    box-shadow: var(--card-shadow);
                    overflow: hidden;
                    border: 1px solid #e2e8f0;
                    margin-bottom: 2rem;
                }

                .table thead th {
                    background: linear-gradient(135deg, var(--primary-color) 0%, #1e3a5f 100%);
                    color: white;
                    font-weight: 600;
                    font-size: 0.85rem;
                    text-transform: uppercase;
                    letter-spacing: 0.05em;
                    border: none;
                    padding: 1rem 0.75rem;
                }

                .table tbody td {
                    padding: 0.875rem 0.75rem;
                    vertical-align: middle;
                    border-color: #f1f5f9;
                    font-size: 0.875rem;
                }

                .status-toggle-container {
                    display: flex;
                    flex-direction: column;
                    align-items: center;
                    gap: 0.25rem;
                }

                .toggle-switch {
                    position: relative;
                    display: inline-block;
                    width: 42px;
                    height: 20px;
                }

                .toggle-switch input {
                    opacity: 0;
                    width: 0;
                    height: 0;
                }

                .toggle-slider {
                    position: absolute;
                    cursor: pointer;
                    top: 0; left: 0; right: 0; bottom: 0;
                    background: #cbd5e1;
                    transition: var(--transition);
                    border-radius: 20px;
                }

                .toggle-slider:before {
                    position: absolute;
                    content: "";
                    height: 14px; width: 14px;
                    left: 3px; bottom: 3px;
                    background-color: white;
                    transition: var(--transition);
                    border-radius: 50%;
                }

                .toggle-switch input:checked + .toggle-slider {
                    background: var(--success-color);
                }

                .toggle-switch input:checked + .toggle-slider:before {
                    transform: translateX(22px);
                }

                .action-buttons {
                    display: flex;
                    gap: 0.5rem;
                    justify-content: center;
                }

                .action-btn {
                    width: 32px;
                    height: 32px;
                    border-radius: 6px;
                    border: none;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    transition: var(--transition);
                    color: white !important;
                }

                .action-btn.edit { background: var(--info-color); }
                .action-btn.delete { background: var(--danger-color); }

                .action-btn:hover {
                    transform: translateY(-2px);
                    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
                }

                .add-new-btn-custom {
                    background: linear-gradient(135deg, var(--success-color) 0%, #166534 100%);
                    color: white;
                    padding: 0.6rem 1.2rem;
                    border-radius: 8px;
                    font-weight: 600;
                    display: inline-flex;
                    align-items: center;
                    gap: 0.5rem;
                    border: none;
                    transition: var(--transition);
                }

                .add-new-btn-custom:hover {
                    transform: translateY(-2px);
                    box-shadow: var(--card-shadow-hover);
                    color: white;
                    text-decoration: none;
                }
            </style>

            <div class="row">
                <div class="col-12">
                    <div class="page-header-custom">
                        <div class="page-header-content">
                            <div class="page-header-text">
                                <h1 class="page-title-custom">Country & State Management</h1>
                                <p class="page-subtitle-custom">Configure countries and their respective states for the system</p>
                            </div>
                        </div>
                    </div>

                    <ul class="nav nav-tabs nav-tabs-custom" id="locationTabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="countries-tab" data-toggle="tab" href="#countries" role="tab" aria-controls="countries" aria-selected="true">
                                <i class="fas fa-globe-americas mr-2"></i> Countries
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="states-tab" data-toggle="tab" href="#states" role="tab" aria-controls="states" aria-selected="false">
                                <i class="fas fa-map-marked-alt mr-2"></i> States
                            </a>
                        </li>
                    </ul>

                    <div class="tab-content" id="locationTabsContent">
                        <!-- Countries Tab -->
                        <div class="tab-pane fade show active" id="countries" role="tabpanel" aria-labelledby="countries-tab">
                            <div class="main-table-card p-4">
                                <div class="d-flex justify-content-between align-items-center mb-4">
                                    <h4 class="mb-0 text-dark font-weight-bold">Registered Countries</h4>
                                    <button type="button" class="add-new-btn-custom" onclick="openCountryModal()">
                                        <i class="fas fa-plus"></i> Add Country
                                    </button>
                                </div>
                                <div class="table-responsive">
                                    <table id="countriesTable" class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th class="text-center">ID</th>
                                                <th>Country Name</th>
                                                <th>Country Code</th>
                                                <th class="text-center">Status</th>
                                                <th class="text-center">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($countries as $country)
                                                <tr>
                                                    <td class="text-center">{{ $country->id }}</td>
                                                    <td><strong class="text-dark">{{ $country->name }}</strong></td>
                                                    <td><span class="badge badge-light px-3 py-2">{{ $country->code }}</span></td>
                                                    <td class="text-center">
                                                        <div class="status-toggle-container">
                                                            <label class="toggle-switch">
                                                                <input type="checkbox" class="updateCountryStatus" id="country-{{ $country->id }}" country_id="{{ $country->id }}" @if($country->status==1) checked @endif>
                                                                <span class="toggle-slider"></span>
                                                            </label>
                                                            <small class="status-text-{{ $country->id }} text-muted font-weight-bold">
                                                                {{ $country->status == 1 ? 'ACTIVE' : 'INACTIVE' }}
                                                            </small>
                                                        </div>
                                                    </td>
                                                    <td class="text-center">
                                                        <div class="action-buttons">
                                                            <button class="action-btn edit" onclick="openCountryModal({{ $country->id }})" title="Edit">
                                                                <i class="fas fa-edit"></i>
                                                            </button>
                                                            <a href="{{ url('admin/delete-country/'.$country->id) }}" class="action-btn delete confirmDelete" title="Delete">
                                                                <i class="fas fa-trash"></i>
                                                            </a>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- States Tab -->
                        <div class="tab-pane fade" id="states" role="tabpanel" aria-labelledby="states-tab">
                            <div class="main-table-card p-4">
                                <div class="d-flex justify-content-between align-items-center mb-4">
                                    <h4 class="mb-0 text-dark font-weight-bold">Registered States</h4>
                                    <button type="button" class="add-new-btn-custom" onclick="openStateModal()">
                                        <i class="fas fa-plus"></i> Add State
                                    </button>
                                </div>
                                <div class="table-responsive">
                                    <table id="statesTable" class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th class="text-center">ID</th>
                                                <th>State Name</th>
                                                <th>Country</th>
                                                <th>State Code</th>
                                                <th class="text-center">Status</th>
                                                <th class="text-center">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($states as $state)
                                                <tr>
                                                    <td class="text-center">{{ $state->id }}</td>
                                                    <td><strong class="text-dark">{{ $state->name }}</strong></td>
                                                    <td>{{ $state->country->name ?? 'N/A' }}</td>
                                                    <td><span class="badge badge-light px-3 py-2">{{ $state->code }}</span></td>
                                                    <td class="text-center">
                                                        <div class="status-toggle-container">
                                                            <label class="toggle-switch">
                                                                <input type="checkbox" class="updateStateStatus" id="state-{{ $state->id }}" state_id="{{ $state->id }}" @if($state->status==1) checked @endif>
                                                                <span class="toggle-slider"></span>
                                                            </label>
                                                            <small class="state-status-text-{{ $state->id }} text-muted font-weight-bold">
                                                                {{ $state->status == 1 ? 'ACTIVE' : 'INACTIVE' }}
                                                            </small>
                                                        </div>
                                                    </td>
                                                    <td class="text-center">
                                                        <div class="action-buttons">
                                                            <button class="action-btn edit" onclick="openStateModal({{ $state->id }})" title="Edit">
                                                                <i class="fas fa-edit"></i>
                                                            </button>
                                                            <a href="{{ url('admin/delete-state/'.$state->id) }}" class="action-btn delete confirmDelete" title="Delete">
                                                                <i class="fas fa-trash"></i>
                                                            </a>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Country Modal -->
        <div class="modal fade" id="countryModal" tabindex="-1" role="dialog" aria-labelledby="countryModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="countryModalLabel">Add Country</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form id="countryForm" method="post" action="">
                        @csrf
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="country_name" class="font-weight-bold">Country Name</label>
                                <input type="text" class="form-control" id="country_name" name="country_name" required placeholder="Enter country name">
                            </div>
                            <div class="form-group">
                                <label for="country_code" class="font-weight-bold">Country Code</label>
                                <input type="text" class="form-control" id="country_code" name="country_code" required placeholder="e.g. IN, US, UK">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Save Changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- State Modal -->
        <div class="modal fade" id="stateModal" tabindex="-1" role="dialog" aria-labelledby="stateModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="stateModalLabel">Add State</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form id="stateForm" method="post" action="">
                        @csrf
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="state_country_id" class="font-weight-bold">Country</label>
                                <select class="form-control" id="state_country_id" name="country_id" required>
                                    <option value="">Select Country</option>
                                    @foreach($countries as $country)
                                        <option value="{{ $country->id }}">{{ $country->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="state_name" class="font-weight-bold">State Name</label>
                                <input type="text" class="form-control" id="state_name" name="state_name" required placeholder="Enter state name">
                            </div>
                            <div class="form-group">
                                <label for="state_code" class="font-weight-bold">State Code</label>
                                <input type="text" class="form-control" id="state_code" name="state_code" required placeholder="e.g. OR, MH, NY">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Save Changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        @include('admin.layout.footer')
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $('#countriesTable').DataTable({
                "order": [[0, "desc"]],
                "pageLength": 10
            });
            $('#statesTable').DataTable({
                "order": [[0, "desc"]],
                "pageLength": 10
            });

            // Update Country Status
            $(document).on("click", ".updateCountryStatus", function() {
                var status = $(this).prop('checked') ? "Active" : "Inactive";
                var country_id = $(this).attr("country_id");
                $.ajax({
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    type: 'post',
                    url: '/admin/update-country-status',
                    data: { status: status, country_id: country_id },
                    success: function(resp) {
                        if (resp['status'] == 0) {
                            $("#country-" + country_id).prop('checked', false);
                            $(".status-text-" + country_id).text("INACTIVE");
                        } else if (resp['status'] == 1) {
                            $("#country-" + country_id).prop('checked', true);
                            $(".status-text-" + country_id).text("ACTIVE");
                        }
                    }
                });
            });

            // Update State Status
            $(document).on("click", ".updateStateStatus", function() {
                var status = $(this).prop('checked') ? "Active" : "Inactive";
                var state_id = $(this).attr("state_id");
                $.ajax({
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    type: 'post',
                    url: '/admin/update-state-status',
                    data: { status: status, state_id: state_id },
                    success: function(resp) {
                        if (resp['status'] == 0) {
                            $("#state-" + state_id).prop('checked', false);
                            $(".state-status-text-" + state_id).text("INACTIVE");
                        } else if (resp['status'] == 1) {
                            $("#state-" + state_id).prop('checked', true);
                            $(".state-status-text-" + state_id).text("ACTIVE");
                        }
                    }
                });
            });

            // Confirm Delete
            $(document).on("click", ".confirmDelete", function(e) {
                e.preventDefault();
                var href = $(this).attr('href');
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = href;
                    }
                });
            });
        });

        function openCountryModal(id = null) {
            if (id) {
                $.get('/admin/add-edit-country/' + id, function(data) {
                    $('#countryModalLabel').text(data.title);
                    $('#country_name').val(data.country.name);
                    $('#country_code').val(data.country.code);
                    $('#countryForm').attr('action', '/admin/add-edit-country/' + id);
                    $('#countryModal').modal('show');
                });
            } else {
                $('#countryModalLabel').text('Add Country');
                $('#country_name').val('');
                $('#country_code').val('');
                $('#countryForm').attr('action', '/admin/add-edit-country');
                $('#countryModal').modal('show');
            }
        }

        function openStateModal(id = null) {
            if (id) {
                $.get('/admin/add-edit-state/' + id, function(data) {
                    $('#stateModalLabel').text(data.title);
                    $('#state_country_id').val(data.state.country_id);
                    $('#state_name').val(data.state.name);
                    $('#state_code').val(data.state.code);
                    $('#stateForm').attr('action', '/admin/add-edit-state/' + id);
                    $('#stateModal').modal('show');
                });
            } else {
                $('#stateModalLabel').text('Add State');
                $('#state_country_id').val('');
                $('#state_name').val('');
                $('#state_code').val('');
                $('#stateForm').attr('action', '/admin/add-edit-state');
                $('#stateModal').modal('show');
            }
        }
    </script>
@endpush
