@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Custom CSS for enhanced styling -->
    <style>
       .page-header {
    background: #274472;
    color: #fff;
    padding: 32px 30px;
    border-radius: 12px;
    margin-bottom: 32px;
    box-shadow: 0 6px 22px rgba(39,68,114,0.10);
}
.form-container {
    background: #ffffff;
    border-radius: 12px;
    box-shadow: 0 4px 18px rgba(60,72,100,0.11);
    padding: 32px 30px;
    margin-bottom: 28px;
}
.page-title {
    font-size: 2rem;
    font-weight: 700;
    margin: 0 0 8px 0;
    letter-spacing: 0.02em;
}
.page-subtitle {
    font-size: 1rem;
    color: #dde3ec;
    margin: 0;
}

.form-label {
    font-weight: 600;
    color: #274472;
    margin-bottom: 8px;
    display: flex;
    align-items: center;
    gap: 8px;
}
.form-control {
    border: 2px solid #e9ecef;
    border-radius: 8px;
    padding: 11px 16px;
    font-size: 0.97rem;
    transition: border-color 0.16s;
    background: #fbfcfd;
    color: #274472;
}
.form-control:focus {
    border-color: #274472;
    box-shadow: 0 0 6px rgba(39,68,114,0.11);
    background: #fff;
}
.form-group {
    margin-bottom: 26px;
}
.form-row {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
}
.form-icon {
    color: #25836e;
    width: 16px;
}

.required {
    color: #d13e3e;
}

.error-message {
    color: #d13e3e;
    font-size: 0.89rem;
    margin-top: 5px;
}

.btn-submit, .btn-cancel {
    font-weight: 600;
    border-radius: 8px;
    padding: 11px 28px;
    border: none;
    transition: all 0.23s;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    font-size: 1rem;
    cursor: pointer;
}
.btn-submit {
    background: #25836e;
    color: #fff;
    box-shadow: 0 3px 18px rgba(37, 131, 110, 0.09);
}
.btn-submit:hover {
    background: #176276;
    box-shadow: 0 7px 26px rgba(37, 131, 110, 0.15);
}
.btn-cancel {
    background: #dde3ec;
    color: #274472;
    border: 1px solid #ced5de;
    margin-right: 12px;
}
.btn-cancel:hover {
    background: #b7c7e2;
    color: #274472;
    text-decoration: none;
}

.checkbox-container {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-top: 10px;
}

.checkbox-container input[type="checkbox"] {
    width: 18px;
    height: 18px;
    accent-color: #25836e;
}

/* Responsive tweaks for mobile devices */
@media (max-width: 576px) {
    .form-row {
        grid-template-columns: 1fr;
        gap: 0;
    }
    .form-container, .page-header {
        padding: 20px 10px;
    }
    .page-title { font-size: 1.3rem; }
}
    </style>

    <div class="row">
        <div class="col-12">
            <div class="page-header">
                <h1 class="page-title">
                    <i class="fas fa-plus-circle"></i>
                    Add New Block
                </h1>
                <p class="page-subtitle">Create a new block record for your system</p>
            </div>

            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <strong>Please fix the following errors:</strong>
                    <ul class="mb-0 mt-2">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            <div class="form-container">
                <form method="post" action="{{ url('sales/blocks') }}">
                    @csrf

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">
                                <i class="fas fa-cube form-icon"></i>
                                Block Name <span class="required">*</span>
                            </label>
                            <input type="text" name="name" class="form-control" value="{{ old('name') }}" placeholder="Enter block name" required>
                            @error('name')
                                <div class="error-message">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label class="form-label">
                                <i class="fas fa-map-marker-alt form-icon"></i>
                                District <span class="required">*</span>
                            </label>
                        
                            <!-- 🔍 Search Input -->
                            <input type="text" id="districtSearch" class="form-control mb-2" placeholder="Search district...">
                        
                            <!-- 🏙️ District Dropdown -->
                            <select name="district_id" id="districtSelect" class="form-control" required>
                                <option value="">Select District</option>
                                @foreach($districts as $district)
                                    <option value="{{ $district->id }}" {{ old('district_id') == $district->id ? 'selected' : '' }}>
                                        {{ $district->name }} ({{ $district->state->name ?? 'N/A' }})
                                    </option>
                                @endforeach
                            </select>
                        
                            @error('district_id')
                                <div class="error-message">{{ $message }}</div>
                            @enderror
                        </div>
                        
                    </div>

                    

                    <div class="form-group text-center mt-4">
                        <a href="{{ url('sales/blocks') }}" class="btn-cancel">
                            <i class="fas fa-arrow-left"></i>
                            Cancel
                        </a>
                        <button type="submit" class="btn-submit">
                            <i class="fas fa-save"></i>
                            Create Block
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    document.getElementById('districtSearch').addEventListener('keyup', function () {
        let filter = this.value.toLowerCase();
        let options = document.querySelectorAll('#districtSelect option');
    
        options.forEach(option => {
            let text = option.textContent.toLowerCase();
            option.style.display = text.includes(filter) || option.value === '' ? '' : 'none';
        });
    });
    </script>

@endsection
