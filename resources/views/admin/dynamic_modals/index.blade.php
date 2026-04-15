@extends('admin.layout.layout')

@section('content')
    <div class="main-panel">
        <div class="content-wrapper">
            <div class="row">
                <div class="col-lg-12 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">Dynamic Modals</h4>

                            <a href="{{ url('admin/add-edit-dynamic-modal') }}"
                                style="max-width: 200px; float: right; display: inline-block"
                                class="btn btn-block btn-primary">
                                <i class="mdi mdi-plus"></i> Add Dynamic Modal
                            </a>

                            @if (Session::has('success_message'))
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    <strong>Success:</strong> {{ Session::get('success_message') }}
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            @endif

                            <div class="table-responsive pt-3">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Image</th>
                                            <th>Text</th>
                                            <th>Link</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($dynamicModals as $dynamicModal)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>
                                                    @if (!empty($dynamicModal->image))
                                                        <img style="width: 120px; height: 80px; object-fit: cover;"
                                                            src="{{ $dynamicModal->image }}" alt="Dynamic modal image">
                                                    @else
                                                        N/A
                                                    @endif
                                                </td>
                                                <td>{{ $dynamicModal->text ? \Illuminate\Support\Str::limit($dynamicModal->text, 80) : 'N/A' }}</td>
                                                <td>{{ $dynamicModal->link ?: 'N/A' }}</td>
                                                <td>
                                                    <span class="badge {{ $dynamicModal->status ? 'badge-success' : 'badge-secondary' }}">
                                                        {{ $dynamicModal->status ? 'Active' : 'Inactive' }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <a href="{{ url('admin/add-edit-dynamic-modal/' . $dynamicModal->id) }}"
                                                        class="btn btn-sm btn-primary">Edit</a>
                                                    <a href="{{ url('admin/delete-dynamic-modal/' . $dynamicModal->id) }}"
                                                        class="btn btn-sm btn-danger"
                                                        onclick="return confirm('Delete this dynamic modal?')">Delete</a>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6">No dynamic modals found.</td>
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

