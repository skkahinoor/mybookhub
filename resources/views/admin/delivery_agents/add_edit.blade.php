@extends('admin.layout.layout')

@section('content')
    <div class="main-panel">
        <div class="content-wrapper">
            <div class="row">
                <div class="col-md-12 grid-margin">
                    <div class="row">
                        <div class="col-12 col-xl-8 mb-4 mb-xl-0">
                            <h3 class="font-weight-bold">Delivery Agents Management</h3>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">{{ $user ? 'Edit' : 'Add' }} Delivery Agent</h4>
                            @if (Session::has('error_message'))
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <strong>Error:</strong> {{ Session::get('error_message') }}
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            @endif

                            @if ($errors->any())
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            @endif

                            <form class="forms-sample"
                                action="{{ $user ? route('delivery_agents.add_edit', $user->id) : route('delivery_agents.add_edit') }}"
                                method="post" enctype="multipart/form-data">
                                @csrf
                                <div class="form-group">
                                    <label for="name">Name</label>
                                    <input type="text" class="form-control" id="name" name="name"
                                        placeholder="Enter Name" value="{{ old('name', $user->name ?? '') }}" required>
                                </div>
                                <div class="form-group">
                                    <label for="email">Email</label>
                                    <input type="email" class="form-control" id="email" name="email"
                                        placeholder="Enter Email" value="{{ old('email', $user->email ?? '') }}" required>
                                </div>
                                <div class="form-group">
                                    <label for="phone">Phone</label>
                                    <input type="text" class="form-control" id="phone" name="phone"
                                        placeholder="Enter Phone" value="{{ old('phone', $user->phone ?? '') }}" required>
                                </div>
                                <div class="form-group">
                                    <label for="district_id">District</label>
                                    <select class="form-control" id="district_id" name="district_id" required>
                                        <option value="">Select District</option>
                                        @foreach ($districts as $district)
                                            <option value="{{ $district->id }}"
                                                {{ old('district_id', $user->district_id ?? '') == $district->id ? 'selected' : '' }}>
                                                {{ $district->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="vehicle_type">Vehicle Type</label>
                                    <input type="text" class="form-control" id="vehicle_type" name="vehicle_type"
                                        placeholder="e.g. Bike, Cycle" value="{{ old('vehicle_type', $deliveryAgent->vehicle_type ?? '') }}">
                                </div>
                                <div class="form-group">
                                    <label for="license_number">License Number</label>
                                    <input type="text" class="form-control" id="license_number" name="license_number"
                                        placeholder="Enter License Number" value="{{ old('license_number', $deliveryAgent->license_number ?? '') }}">
                                </div>
                                <div class="form-group">
                                    <label for="password">Password {{ $user ? '(Leave blank to keep current)' : '' }}</label>
                                    <input type="password" class="form-control" id="password" name="password"
                                        placeholder="Enter Password" {{ $user ? '' : 'required' }}>
                                </div>
                                <div class="form-group">
                                    <label for="password_confirmation">Confirm Password</label>
                                    <input type="password" class="form-control" id="password_confirmation"
                                        name="password_confirmation" placeholder="Confirm Password"
                                        {{ $user ? '' : 'required' }}>
                                </div>
                                <button type="submit" class="btn btn-primary mr-2">Submit</button>
                                <a href="{{ route('delivery_agents.index') }}" class="btn btn-light">Cancel</a>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- content-wrapper ends -->
        <!-- partial:partials/_footer.html -->
        @include('admin.layout.footer')
        <!-- partial -->
    </div>
@endsection
