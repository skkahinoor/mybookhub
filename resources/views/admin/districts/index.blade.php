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
                    top: 0;
                    left: 0;
                    right: 0;
                    bottom: 0;
                    background: #cbd5e1;
                    transition: var(--transition);
                    border-radius: 20px;
                }

                .toggle-slider:before {
                    position: absolute;
                    content: "";
                    height: 14px;
                    width: 14px;
                    left: 3px;
                    bottom: 3px;
                    background-color: white;
                    transition: var(--transition);
                    border-radius: 50%;
                }

                .toggle-switch input:checked+.toggle-slider {
                    background: var(--success-color);
                }

                .toggle-switch input:checked+.toggle-slider:before {
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

                .action-btn.edit {
                    background: var(--info-color);
                }

                .action-btn.delete {
                    background: var(--danger-color);
                }

                .action-btn:hover {
                    transform: translateY(-2px);
                    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
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
                                <h1 class="page-title-custom">District Management</h1>
                                <p class="page-subtitle-custom">Configure districts for the system across different states
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="main-table-card p-4">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h4 class="mb-0 text-dark font-weight-bold">Registered Districts</h4>
                            <button type="button" class="add-new-btn-custom" onclick="openDistrictModal()">
                                <i class="fas fa-plus"></i> Add District
                            </button>
                        </div>
                        <div class="table-responsive">
                            <table id="districtsTable" class="table table-hover">
                                <thead>
                                    <tr>
                                        <th class="text-center">ID</th>
                                        <th>District Name</th>
                                        <th>State</th>
                                        <th class="text-center">Status</th>
                                        <th class="text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($districts as $district)
                                        <tr>
                                            <td class="text-center">{{ $district->id }}</td>
                                            <td><strong class="text-dark">{{ $district->name }}</strong></td>
                                            <td>{{ $district->state->name ?? 'N/A' }}</td>
                                            <td class="text-center">
                                                <div class="status-toggle-container">
                                                    <label class="toggle-switch">
                                                        <input type="checkbox" class="updateDistrictStatus"
                                                            id="district-{{ $district->id }}"
                                                            district_id="{{ $district->id }}"
                                                            @if ($district->status == 1) checked @endif>
                                                        <span class="toggle-slider"></span>
                                                    </label>
                                                    <small
                                                        class="district-status-text-{{ $district->id }} text-muted font-weight-bold">
                                                        {{ $district->status == 1 ? 'ACTIVE' : 'INACTIVE' }}
                                                    </small>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <div class="action-buttons">
                                                    <button class="action-btn edit"
                                                        onclick="openDistrictModal({{ $district->id }})" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <a href="{{ url('admin/delete-district/' . $district->id) }}"
                                                        class="action-btn delete confirmDelete" title="Delete">
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

        <!-- District Modal -->
        <div class="modal fade" id="districtModal" tabindex="-1" role="dialog" aria-labelledby="districtModalLabel"
            aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="districtModalLabel">Add District</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form id="districtForm" method="post" action="">
                        @csrf
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="state_id" class="font-weight-bold">State</label>
                                <select class="form-control" id="district_state_id" name="state_id" required>
                                    <option value="">Select State</option>
                                    @foreach ($states as $state)
                                        <option value="{{ $state->id }}">{{ $state->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="district_name" class="font-weight-bold">District Name</label>
                                <input type="text" class="form-control" id="district_name" name="district_name" required
                                    placeholder="Enter district name">
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
            $('#districtsTable').DataTable({
                "order": [
                    [0, "desc"]
                ],
                "pageLength": 10
            });

            // Update District Status
            $(document).on("click", ".updateDistrictStatus", function() {
                var status = $(this).prop('checked') ? "Active" : "Inactive";
                var district_id = $(this).attr("district_id");
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: 'post',
                    url: "{{ url('admin/update-district-status') }}",
                    data: {
                        status: status,
                        district_id: district_id
                    },
                    success: function(resp) {
                        if (resp['status'] == 0) {
                            $("#district-" + district_id).prop('checked', false);
                            $(".district-status-text-" + district_id).text("INACTIVE");
                        } else if (resp['status'] == 1) {
                            $("#district-" + district_id).prop('checked', true);
                            $(".district-status-text-" + district_id).text("ACTIVE");
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

        function openDistrictModal(id = null) {
            if (id) {
                $.get("{{ url('admin/add-edit-district') }}/" + id, function(data) {
                    $('#districtModalLabel').text(data.title);
                    $('#district_state_id').val(data.district.state_id);
                    $('#district_name').val(data.district.name);
                    $('#districtForm').attr('action', "{{ url('admin/add-edit-district') }}/" + id);
                    $('#districtModal').modal('show');
                });
            } else {
                $('#districtModalLabel').text('Add District');
                $('#district_state_id').val('');
                $('#district_name').val('');
                $('#districtForm').attr('action', "{{ url('admin/add-edit-district') }}");
                $('#districtModal').modal('show');
            }
        }
    </script>
@endpush
