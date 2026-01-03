@extends('admin.layout.layout')

@section('content')
<div class="container-fluid">
    <h4 class="mb-4">OTP Records</h4>

    <div class="card">
        <div class="card-body">
            <table class="table table-bordered table-striped">
                <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>Phone</th>
                        <th>OTP</th>
                        <th>Created At</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($otps as $key => $otp)
                        <tr>
                            <td>{{ $key+1 }}</td>
                            <td>{{ $otp->phone ?? 'N/A' }}</td>
                            <td>{{ $otp->otp }}</td>
                            <td>{{ $otp->created_at->format('Y-m-d H:i') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center">No OTP records found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
