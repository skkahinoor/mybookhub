@extends('admin.layout.layout')

@section('content')
<div class="container-fluid">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

<style>
    * { font-family: 'Inter', sans-serif; }

    .analytics-header {
        background: linear-gradient(135deg, #1e3a8a 0%, #1d4ed8 50%, #3b82f6 100%);
        border-radius: 20px;
        padding: 32px 36px;
        margin-bottom: 28px;
        position: relative;
        overflow: hidden;
        box-shadow: 0 10px 40px rgba(29,78,216,0.3);
    }
    .analytics-header h1 {
        color: white;
        font-weight: 800;
        font-size: 2.2rem;
        margin-bottom: 8px;
        letter-spacing: -0.5px;
    }
    .analytics-header p {
        color: rgba(255,255,255,0.85);
        font-size: 1.05rem;
        font-weight: 400;
        margin: 0;
    }

    .analytics-table {
        background: white; border-radius: 20px;
        box-shadow: 0 4px 24px rgba(0,0,0,0.04);
        padding: 24px; margin-bottom: 24px;
        border: 1px solid #f3f4f6;
    }
    .analytics-table-header {
        display: flex; justify-content: space-between; align-items: center;
        padding-bottom: 16px; border-bottom: 1px solid #e5e7eb; margin-bottom: 20px;
    }
    .analytics-table-header h5 {
        margin: 0; font-weight: 700; color: #111827; font-size: 1.15rem;
    }

    .analytics-table table { width: 100%; border-collapse: separate; border-spacing: 0; }
    .analytics-table thead th {
        background: #f9fafb; padding: 12px 16px;
        font-size: 0.75rem; font-weight: 700; color: #6b7280;
        text-transform: uppercase; letter-spacing: 0.5px;
        border-bottom: 1px solid #e5e7eb; text-align: left;
    }
    .analytics-table tbody td {
        padding: 13px 16px; font-size: 0.875rem; color: #374151;
        border-bottom: 1px solid #f9fafb; vertical-align: middle;
    }
    .analytics-table tbody tr:hover { background: #fafbff; }
</style>

<div class="analytics-header">
    <div style="position: relative; z-index: 1;">
        <h1><i class="fas fa-search" style="margin-right:10px;"></i>Search Analytics</h1>
        <p>Monitor keywords and books students are searching for on the platform</p>
        <div class="mt-3" style="background: #ffc107; padding: 10px 15px; border-radius: 8px; display: inline-block; font-size: 0.85rem; color: #000; font-weight: 500; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
            <i class="fas fa-exclamation-triangle me-1"></i> <strong>Note:</strong> To maintain clean storage, a maximum of 100 recent searches are stored. Oldest queries are automatically deleted as new ones arrive.
        </div>
    </div>
</div>

<div class="analytics-table">
    <div class="analytics-table-header">
        <h5><i class="fas fa-list" style="color:#3b82f6;"></i> Latest Search Queries</h5>
        <a href="{{ route('admin.reports.analytics') }}" class="btn btn-sm btn-outline-secondary">
            <i class="fas fa-arrow-left"></i> Back to Analytics
        </a>
    </div>
    
    <div class="table-responsive">
        <table class="table table-hover table-bordered">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Keyword / Search Term</th>
                    <th>Results Found</th>
                    <th>User</th>
                    <th>IP & Location</th>
                    <th>Date & Time</th>
                </tr>
            </thead>
            <tbody>
                @forelse($searchQueries as $index => $query)
                <tr>
                    <td>{{ $searchQueries->firstItem() + $index }}</td>
                    <td class="fw-bold text-primary">{{ $query->keyword }}</td>
                    <td>
                        @if($query->results_count > 0)
                            <span class="badge badge-success" style="padding:5px 10px; background-color: #10b981;">{{ $query->results_count }}</span>
                        @else
                            <span class="badge badge-danger" style="padding:5px 10px; background-color: #ef4444;">0</span>
                        @endif
                    </td>
                    <td>
                        @if($query->user)
                            <a href="{{ url('admin/view-student-details/'.$query->user->id) }}" target="_blank" class="text-decoration-none text-primary">
                                {{ $query->user->name }}
                            </a>
                            <div style="font-size: 0.75rem; color: #6b7280;">{{ $query->user->email }}</div>
                        @else
                            <span class="text-muted">Guest User</span>
                        @endif
                    </td>
                    <td>
                        <div class="mb-1"><span class="text-muted"><i class="fas fa-network-wired" style="font-size:0.75rem;"></i> {{ $query->ip_address ?? 'N/A' }}</span></div>
                        @if($query->latitude && $query->longitude)
                            <div class="geo-location-container" data-lat="{{ $query->latitude }}" data-lng="{{ $query->longitude }}">
                                <span class="location-name text-dark fw-bold" style="font-size: 0.8rem;"><i class="fas fa-spinner fa-spin"></i> Loading location...</span><br>
                                <a href="https://www.google.com/maps/search/?api=1&query={{ $query->latitude }},{{ $query->longitude }}" target="_blank" class="text-primary text-decoration-none mt-1 d-inline-block" style="font-size: 0.75rem;">
                                    <i class="fas fa-map-marker-alt text-danger"></i> View in Map
                                </a>
                            </div>
                        @else
                            <span class="text-muted" style="font-size: 0.8rem;"><i class="fas fa-map-marker-alt text-secondary"></i> Location not available</span>
                        @endif
                    </td>
                    <td>{{ $query->created_at->format('d M Y, h:i A') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center text-muted py-4">No search queries logged yet.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $searchQueries->links('pagination::bootstrap-5') }}
    </div>
</div>

</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const apiKey = "{{ env('GOOGLE_MAPS_KEY') }}";
        const containers = document.querySelectorAll('.geo-location-container');

        containers.forEach(container => {
            const lat = container.getAttribute('data-lat');
            const lng = container.getAttribute('data-lng');
            const nameEl = container.querySelector('.location-name');

            if (apiKey && apiKey !== '') {
                fetch(`https://maps.googleapis.com/maps/api/geocode/json?latlng=${lat},${lng}&key=${apiKey}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'OK' && data.results.length > 0) {
                            // Try to find the city/locality name or fallback to formatted address
                            let locality = '';
                            let state = '';
                            
                            for (let component of data.results[0].address_components) {
                                if (component.types.includes('locality')) {
                                    locality = component.long_name;
                                }
                                if (component.types.includes('administrative_area_level_1')) {
                                    state = component.long_name;
                                }
                            }
                            
                            if (locality) {
                                nameEl.innerHTML = `${locality}${state ? ', ' + state : ''}`;
                            } else {
                                nameEl.innerHTML = data.results[0].formatted_address.split(',').slice(-3).join(',').trim();
                            }
                        } else {
                            nameEl.innerHTML = 'Location found';
                        }
                    })
                    .catch(error => {
                        console.error('Error fetching location:', error);
                        nameEl.innerHTML = 'Error loading name';
                    });
            } else {
                nameEl.innerHTML = 'API Key missing';
            }
        });
    });
</script>
@endsection
