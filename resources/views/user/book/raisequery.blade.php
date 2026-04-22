@include('user.layout.header')

<style>
    .raise-query-page .card {
        border: 1px solid #e8edf5;
        border-radius: 14px;
        box-shadow: 0 6px 20px rgba(21, 38, 67, 0.06);
    }
    .raise-query-page .hero {
        border-radius: 12px;
        padding: 18px 20px;
        background: linear-gradient(135deg, #fff8ef 0%, #f6f9ff 100%);
        border: 1px solid #f0e5d6;
        margin-bottom: 18px;
    }
    .raise-query-page .hero h4 {
        font-size: 22px;
        font-weight: 700;
        color: #1f2d3d;
        margin-bottom: 5px;
    }
    .raise-query-page .hero p {
        margin: 0;
        color: #64748b;
    }
    .raise-query-page .submit-btn {
        background: #cf8938;
        border: none;
        color: #fff;
        border-radius: 8px;
        font-weight: 600;
        padding: 10px 20px;
    }
</style>

<div class="container-fluid page-body-wrapper">
    @include('user.layout.sidebar')

    <div class="main-panel raise-query-page">
        <div class="content-wrapper">
            <div class="row">
                <div class="col-lg-12 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <div class="hero d-flex justify-content-between align-items-center flex-wrap" style="gap:12px;">
                                <div>
                                    <h4>Raise Your Query</h4>
                                    <p>Fill details and your request will be shared with vendors in your default address district.</p>
                                </div>
                                <a href="{{ route('student.query.index') }}" class="btn btn-outline-primary" style="border-radius:8px;">
                                    View My Queries
                                </a>
                            </div>

                            @if (session('success'))
                                <div class="alert alert-success">{{ session('success') }}</div>
                            @endif
                            @if (session('error'))
                                <div class="alert alert-danger">{{ session('error') }}</div>
                            @endif

                            <form action="{{ route('student.book.request.store') }}" method="POST">
                                @csrf
                                <input type="hidden" name="user_location" id="user_location" value="{{ old('user_location') }}">
                                <input type="hidden" name="user_location_name" id="user_location_name" value="{{ old('user_location_name') }}">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Book Title <span class="text-danger">*</span></label>
                                            <input type="text" name="book_title" class="form-control" value="{{ old('book_title') }}" required>
                                            @error('book_title')
                                                <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Author Name</label>
                                            <input type="text" name="author_name" class="form-control" value="{{ old('author_name') }}">
                                            @error('author_name')
                                                <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Publisher Name</label>
                                            <input type="text" name="publisher_name" class="form-control" value="{{ old('publisher_name') }}">
                                            @error('publisher_name')
                                                <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>District Matching <span class="text-danger">*</span></label>
                                            @if (empty($districtId))
                                                <div class="alert alert-warning mb-0">Please set a default address with district to raise a query.</div>
                                            @elseif (isset($matchingVendors) && $matchingVendors->count() > 0)
                                                <div class="alert alert-success mb-0">
                                                    Your request will be sent to {{ $matchingVendors->count() }} vendor(s) in your district.
                                                </div>
                                            @else
                                                <div class="alert alert-danger mb-0">No vendors found for your district.</div>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label>Your Message <span class="text-danger">*</span></label>
                                    <textarea name="message" rows="5" class="form-control" required minlength="10"
                                        placeholder="Type your message here... Provide details about the book you're looking for.">{{ old('message') }}</textarea>
                                    <small class="text-muted">Minimum 10 characters required</small>
                                    @error('message')
                                        <small class="text-danger d-block">{{ $message }}</small>
                                    @enderror
                                </div>

                                @php
                                    $canSubmitQuery = !empty($districtId) && isset($matchingVendors) && $matchingVendors->count() > 0;
                                @endphp
                                <div class="d-flex align-items-center flex-wrap" style="gap: 15px;">
                                    <button type="submit" class="btn submit-btn" {{ $canSubmitQuery ? '' : 'disabled' }} style="{{ $canSubmitQuery ? '' : 'opacity:0.65;cursor:not-allowed;' }}">
                                        📤 Submit Query
                                    </button>
                                    <div id="location-status" class="text-muted" style="font-size: 13px; font-style: italic;">
                                        <i class="fas fa-map-marker-alt"></i> Detecting location...
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@include('user.layout.footer')

<!-- plugins:js -->
<script src="{{ asset('user/vendors/js/vendor.bundle.base.js') }}"></script>
<!-- endinject -->
<!-- inject:js -->
<script src="{{ asset('user/js/off-canvas.js') }}"></script>
<script src="{{ asset('user/js/hoverable-collapse.js') }}"></script>
<script src="{{ asset('user/js/template.js') }}"></script>
<script src="{{ asset('user/js/settings.js') }}"></script>
<!-- endinject -->

<script>
    (function () {
        const locEl = document.getElementById('user_location');
        const locNameEl = document.getElementById('user_location_name');
        const statusEl = document.getElementById('location-status');
        if (!locEl || !locNameEl) return;

        function setLocation(lat, lng) {
            locEl.value = `${lat},${lng}`;
        }

        function setLocationName(name) {
            if (!locNameEl.value) locNameEl.value = name || '';
            if (statusEl) {
                statusEl.innerHTML = '<i class="fas fa-check-circle text-success"></i> Location detected';
                statusEl.classList.remove('text-muted');
                statusEl.classList.add('text-success');
            }
        }

        if (!('geolocation' in navigator)) {
            if (statusEl) statusEl.innerHTML = '<i class="fas fa-exclamation-triangle text-warning"></i> Geolocation not supported';
            return;
        }

        navigator.geolocation.getCurrentPosition(async function (pos) {
            const lat = pos.coords.latitude;
            const lng = pos.coords.longitude;
            setLocation(lat, lng);

            try {
                const url = `https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=${encodeURIComponent(lat)}&lon=${encodeURIComponent(lng)}`;
                const res = await fetch(url, { headers: { 'Accept': 'application/json' } });
                if (!res.ok) throw new Error('Fetch failed');
                const data = await res.json();
                if (data && data.display_name) {
                    setLocationName(data.display_name);
                } else {
                    if (statusEl) statusEl.innerHTML = '<i class="fas fa-check-circle text-success"></i> Coordinates captured';
                }
            } catch (e) {
                if (statusEl) statusEl.innerHTML = '<i class="fas fa-check-circle text-success"></i> Coordinates captured';
            }
        }, function (err) {
            if (statusEl) {
                statusEl.innerHTML = '<i class="fas fa-times-circle text-danger"></i> Location access denied';
                statusEl.classList.replace('text-muted', 'text-danger');
            }
        }, { enableHighAccuracy: true, timeout: 8000, maximumAge: 60000 });
    })();
</script>
