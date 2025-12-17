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
.details-container {
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

.detail-label {
    font-weight: 600;
    color: #274472;
    margin-bottom: 8px;
    display: flex;
    align-items: center;
    gap: 8px;
}
.detail-value {
    background: #f8f9fa;
    border: 2px solid #e9ecef;
    border-radius: 8px;
    padding: 11px 16px;
    font-size: 0.97rem;
    color: #274472;
    margin-bottom: 20px;
}
.detail-group {
    margin-bottom: 26px;
}
.detail-row {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
}
.detail-icon {
    color: #25836e;
    width: 16px;
}

.btn-back {
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
    background: #dde3ec;
    color: #274472;
    border: 1px solid #ced5de;
    text-decoration: none;
}
.btn-back:hover {
    background: #b7c7e2;
    color: #274472;
    text-decoration: none;
}

.class-item {
    background: #f8f9fa;
    border: 2px solid #e9ecef !important;
    border-radius: 8px;
    padding: 15px;
    margin-bottom: 15px;
}

.class-item .detail-label {
    font-size: 0.85rem;
    font-weight: 600;
    color: #274472;
    margin-bottom: 5px;
}

/* Responsive tweaks for mobile devices */
@media (max-width: 576px) {
    .detail-row {
        grid-template-columns: 1fr;
        gap: 0;
    }
    .details-container, .page-header {
        padding: 20px 10px;
    }
    .page-title { font-size: 1.3rem; }
}
    </style>

    <div class="row">
        <div class="col-12">
            <div class="page-header">
                <h1 class="page-title">
                    <i class="fas fa-eye"></i>
                    Institution Details
                </h1>
                <p class="page-subtitle">View institution information</p>
            </div>

            <div class="details-container">
                <div class="detail-row">
                    <div class="detail-group">
                        <div class="detail-label">
                            <i class="fas fa-school detail-icon"></i>
                            Institution Name
                        </div>
                        <div class="detail-value">{{ $institution->name }}</div>
                    </div>

                    <div class="detail-group">
                        <div class="detail-label">
                            <i class="fas fa-building detail-icon"></i>
                            Institution Type
                        </div>
                        <div class="detail-value">{{ ucfirst($institution->type) }}</div>
                    </div>
                </div>

                <div class="detail-row">
                    <div class="detail-group">
                        <div class="detail-label">
                            <i class="fas fa-certificate detail-icon"></i>
                            Board
                        </div>
                        <div class="detail-value">{{ $institution->board }}</div>
                    </div>

                    <div class="detail-group">
                        <div class="detail-label">
                            <i class="fas fa-user detail-icon"></i>
                            Principal Name
                        </div>
                        <div class="detail-value">{{ $institution->principal_name }}</div>
                    </div>

                    <div class="detail-group">
                        <div class="detail-label">
                            <i class="fas fa-phone detail-icon"></i>
                            Contact Number
                        </div>
                        <div class="detail-value">{{ $institution->contact_number }}</div>
                    </div>
                </div>

                <div class="detail-row">
                    <div class="detail-group">
                        <div class="detail-label">
                            <i class="fas fa-flag detail-icon"></i>
                            Country
                        </div>
                        <div class="detail-value">{{ $institution->country ? $institution->country->name : 'N/A' }}</div>
                    </div>

                    <div class="detail-group">
                        <div class="detail-label">
                            <i class="fas fa-map detail-icon"></i>
                            State
                        </div>
                        <div class="detail-value">{{ $institution->state ? $institution->state->name : 'N/A' }}</div>
                    </div>
                </div>

                <div class="detail-row">
                    <div class="detail-group">
                        <div class="detail-label">
                            <i class="fas fa-map-marker-alt detail-icon"></i>
                            District
                        </div>
                        <div class="detail-value">{{ $institution->district ? $institution->district->name : 'N/A' }}</div>
                    </div>
                    <div class="detail-group">
                        <div class="detail-label">
                            <i class="fas fa-cube detail-icon"></i>
                            Block
                        </div>
                        <div class="detail-value">{{ $institution->block ? $institution->block->name : 'N/A' }}</div>
                    </div>
                </div>

                <div class="detail-row">
                    <div class="detail-group">
                        <div class="detail-label">
                            <i class="fas fa-mail-bulk detail-icon"></i>
                            Pincode
                        </div>
                        <div class="detail-value">{{ $institution->pincode }}</div>
                    </div>
                    <div>
                        <div class="detail-label">
                            <i class="fas fa-check-circle detail-icon"></i>
                            Status
                        </div>
                        <div class="detail-value">{{ $institution->status ? 'Active' : 'Inactive' }}</div>
                        @if($institution->status == 0)
                            <div class="text-danger">This institution is inactive. Please activate it to make it visible to students.</div>
                        @endif
                    </div>
                </div>

                @if($institution->institutionClasses && $institution->institutionClasses->count() > 0)
                    <div class="detail-group">
                        <div class="detail-label">
                            <i class="fas fa-layer-group detail-icon"></i>
                            Classes & Strength
                        </div>
                        <div class="detail-value">
                            @foreach($institution->institutionClasses as $instClass)
                                <div class="class-item">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="detail-label">Class Name</div>
                                            <div class="detail-value">{{ $instClass->class_name }}</div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="detail-label">Number of Enrollments</div>
                                            <div class="detail-value">{{ $instClass->total_strength }}</div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="detail-label">Total Strength</div>
                                            <div class="detail-value">{{ $instClass->total_strength }}</div>
                                        </div>
                                    </div>

                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <div class="detail-group text-center mt-4">
                    <a href="{{ url('sales/institution-managements') }}" class="btn-back">
                        <i class="fas fa-arrow-left"></i>
                        Back to List
                    </a>
                    <a href="{{ url('sales/institution-managements/'.$institution->id.'/edit') }}" class="btn-back" style="background: #25836e; color: #fff; margin-left: 12px;">
                        <i class="fas fa-edit"></i>
                        Edit Institution
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
