@extends('admin.layout.layout')

@section('content')
<div class="main-panel">
    <div class="content-wrapper">
        <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h4 class="card-title mb-0">Old Book Commission Percentages</h4>
                            @if ($commissionCount < 1)
                                <a href="{{ route('admin.old_book_commissions.create') }}"
                                   class="btn btn-primary btn-sm">
                                    <i class="mdi mdi-plus"></i> Add Percentage
                                </a>
                            @endif
                        </div>

                        @if (Session::has('success_message'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <strong>Success:</strong> {{ Session::get('success_message') }}
                                <button type="button" class="close" data-dismiss="alert">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        @endif

                        @if (Session::has('error_message'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <strong>Error:</strong> {{ Session::get('error_message') }}
                                <button type="button" class="close" data-dismiss="alert">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        @endif

                        <div class="table-responsive pt-3">
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th style="width:60px">#</th>
                                        <th style="width:160px">Commission Percentage (%)</th>
                                        <th style="width:180px">Created At</th>
                                        <th style="width:180px">Updated At</th>
                                        <th style="width:130px">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($commissions as $key => $comm)
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td>
                                                <span style="font-weight:700;font-size:15px;color:#16a34a;">
                                                    {{ $comm->percentage }}%
                                                </span>
                                            </td>
                                            <td>{{ $comm->created_at ? $comm->created_at->format('d M Y, h:i A') : 'N/A' }}</td>
                                            <td>{{ $comm->updated_at ? $comm->updated_at->format('d M Y, h:i A') : 'N/A' }}</td>
                                            <td>
                                                <a href="{{ route('admin.old_book_commissions.edit', $comm->id) }}"
                                                   title="Edit" class="text-primary">
                                                    <i style="font-size:22px" class="mdi mdi-pencil-box"></i>
                                                </a>
                                                &nbsp;
                                                <a href="{{ route('admin.old_book_commissions.destroy', $comm->id) }}"
                                                   title="Delete"
                                                   onclick="return confirm('Are you sure you want to delete this commission percentage \'{{ $comm->percentage }}%\'?')"
                                                   class="text-danger">
                                                    <i style="font-size:22px;color:red" class="mdi mdi-delete"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center text-muted py-4">
                                                No commission percentages found. <a href="{{ route('admin.old_book_commissions.create') }}">Add the first one</a>.
                                            </td>
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
    <footer class="footer">
        <div class="d-sm-flex justify-content-center justify-content-sm-between">
            <span class="text-muted text-center text-sm-left d-block d-sm-inline-block">Copyright &copy; 2024. All rights reserved.</span>
        </div>
    </footer>
</div>
@endsection
