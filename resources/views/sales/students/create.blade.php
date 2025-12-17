@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Custom CSS for enhanced styling -->
    <style>
        .form-container {
    background: #ffffff;
    border-radius: 12px;
    box-shadow: 0 4px 20px rgba(34, 51, 84, 0.10);
    padding: 30px;
    margin-bottom: 30px;
}

.page-header {
    background: #393e6e;           /* deep indigo */
    color: #f6f8fb;
    padding: 30px;
    border-radius: 12px;
    margin-bottom: 30px;
}

.page-title {
    font-size: 2rem;
    font-weight: 700;
    margin: 0;
}

.page-subtitle {
    font-size: 1rem;
    opacity: 0.9;
    margin: 10px 0 0 0;
}

.form-group {
    margin-bottom: 25px;
}

.form-label {
    font-weight: 600;
    color: #283046;                /* dark slate */
    margin-bottom: 8px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.form-control {
    border: 2px solid #e8eaf6;     /* pale indigo */
    border-radius: 8px;
    padding: 12px 16px;
    font-size: 0.95rem;
    transition: all 0.3s ease;
    background: #f5f6fa;           /* lightest background */
}

.form-control:focus {
    border-color: #0b0c0e;         /* strong blue */
    box-shadow: 0 0 0 0.2rem rgba(80, 143, 243, 0.20);
}

.btn-submit {
    background: #565381;           /* vibrant blue */
    border: none;
    color: #f4f8fa;
    padding: 12px 30px;
    border-radius: 8px;
    font-weight: 600;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    gap: 8px;
}

.btn-submit:hover {
    background: #2952a3;
    transform: translateY(-2px);
    box-shadow: 0 8px 24px rgba(80, 143, 243, 0.35);
}

.btn-cancel {
    background: #abb1c7;           /* muted lavender gray */
    border: none;
    color: #232425;
    padding: 12px 30px;
    border-radius: 8px;
    font-weight: 600;
    transition: all 0.3s ease;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    margin-right: 15px;
}

.btn-cancel:hover {
    background: #8287a6;
    color: #f6f8fb;
    text-decoration: none;
}

.required {
    color: #ee6c6c;                /* friendly red */
}

.form-row {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
}

.error-message {
    color: #ee6c6c;
    font-size: 0.875rem;
    margin-top: 5px;
}

.form-icon {
    color: #3b4877;
    width: 16px;
}

    </style>

    <div class="row">
        <div class="col-12">
            <div class="page-header">
                <h1 class="page-title">
                    <i class="fas fa-user-plus"></i>
                    Add New Student
                </h1>
                <p class="page-subtitle">Create a new student record in the system</p>
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
                <form method="post" action="{{ url('sales/students') }}">
                    @csrf

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">
                                <i class="fas fa-school form-icon"></i>
                                Institution <span class="required">*</span>
                            </label>
                            <select name="institution_id" class="form-control">
                                <option value="">Select Institution</option>
                                @foreach($institutions as $institution)
                                    <option value="{{ $institution->id }}" {{ old('institution_id') == $institution->id ? 'selected' : '' }}>
                                        {{ $institution->name }} ({{ ucfirst($institution->type) }})
                                    </option>
                                @endforeach
                            </select>
                            @error('institution_id')
                                <div class="error-message">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label class="form-label">
                                <i class="fas fa-user form-icon"></i>
                                Student Name <span class="required">*</span>
                            </label>
                            <input type="text" name="name" class="form-control" value="{{ old('name') }}" placeholder="Enter student name" required>
                            @error('name')
                                <div class="error-message">{{ $message }}</div>
                            @enderror
                        </div>

                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">
                                <i class="fas fa-phone form-icon"></i>
                                Phone Number <span class="required">*</span>
                            </label>
                            <input type="tel" name="phone" class="form-control" value="{{ old('phone') }}" placeholder="Enter 10-digit phone number" required>
                            @error('phone')
                                <div class="error-message">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label class="form-label">
                                <i class="fas fa-envelope form-icon"></i>
                                Father's name
                            </label>
                            <input type="text" name="father_names" class="form-control" value="{{ old('father_names') }}" placeholder="Enter father's name">
                            @error('father_names')
                                <div class="error-message">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">
                                <i class="fas fa-layer-group form-icon"></i>
                                Class <span class="required">*</span>
                            </label>
                            <input type="text" name="class" class="form-control" value="{{ old('class') }}" placeholder="Enter class/grade" required>
                            @error('class')
                                <div class="error-message">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label class="form-label">
                                <i class="fas fa-id-card form-icon"></i>
                                Roll Number
                            </label>
                            <input type="text" name="roll_number" class="form-control" value="{{ old('roll_number') }}" placeholder="Enter roll number">
                            @error('roll_number')
                                <div class="error-message">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">
                                <i class="fas fa-venus-mars form-icon"></i>
                                Gender <span class="required">*</span>
                            </label>
                            <select name="gender" class="form-control" required>
                                <option value="">Select Gender</option>
                                <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Male</option>
                                <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Female</option>
                                <option value="other" {{ old('gender') == 'other' ? 'selected' : '' }}>Other</option>
                            </select>
                            @error('gender')
                                <div class="error-message">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label class="form-label">
                                <i class="fas fa-birthday-cake form-icon"></i>
                                Date of Birth <span class="required">*</span>
                            </label>
                            <input type="date" name="dob" class="form-control" value="{{ old('dob') }}" required>
                            @error('dob')
                                <div class="error-message">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group text-center mt-4">
                        <a href="{{ url('sales/students') }}" class="btn-cancel">
                            <i class="fas fa-arrow-left"></i>
                            Cancel
                        </a>
                        <button type="submit" class="btn-submit">
                            <i class="fas fa-save"></i>
                            Create Student
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
{{-- <script>
    document.addEventListener('DOMContentLoaded', function() {
        const institutionSelect = document.querySelector('select[name="institution_id"]');
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';
        const addressTemplate = @json(route('sales.students.institution_address', ['institution' => '__INSTITUTION__']));
        const endpoints = {
            user: '{{ route('sales.students.store_user_location') }}',
            institution: '{{ route('sales.students.store_institution_location') }}',
        };

        // let userLocation = {
        //     latitude: {{ session('user_latitude') ?? 'null' }},
        //     longitude: {{ session('user_longitude') ?? 'null' }},
        // };

        function buildInstitutionAddressUrl(institutionId) {
            return addressTemplate.replace('__INSTITUTION__', institutionId);
        }

        function fetchInstitutionAddress(institutionId) {
            if (!institutionId) {
                return Promise.reject(new Error('Institution is required.'));
            }

            return fetch(buildInstitutionAddressUrl(institutionId), {
                headers: {
                    'Accept': 'application/json',
                },
            }).then(function(response) {
                if (!response.ok) {
                    throw new Error('Unable to fetch institution address.');
                }
                return response.json();
            }).then(function(payload) {
                if (!payload.address) {
                    throw new Error('Address not available for the selected institution.');
                }
                return payload.address;
            });
        }

        function geocodeAddress(address) {
            const searchParams = new URLSearchParams({
                format: 'json',
                limit: '1',
                q: address,
            });

            return fetch('https://nominatim.openstreetmap.org/search?' + searchParams.toString(), {
                headers: {
                    'Accept': 'application/json',
                },
            }).then(function(response) {
                if (!response.ok) {
                    throw new Error('Unable to query the geocoding service.');
                }
                return response.json();
            }).then(function(results) {
                if (!Array.isArray(results) || results.length === 0) {
                    throw new Error('Coordinates could not be determined for the selected institution.');
                }
                return {
                    latitude: parseFloat(results[0].lat),
                    longitude: parseFloat(results[0].lon),
                };
            });
        }

        function postJson(url, payload) {
            if (!url) {
                return Promise.resolve();
            }

            return fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                },
                body: JSON.stringify(payload),
            }).catch(function(error) {
                console.error('Session store error:', error);
            });
        }

        function storeUserLocationSession(latitude, longitude) {
            return postJson(endpoints.user, { latitude, longitude });
        }

        function storeInstitutionLocationSession(institutionId, latitude, longitude) {
            return postJson(endpoints.institution, {
                institution_id: institutionId,
                latitude: latitude,
                longitude: longitude,
            });
        }

        function toRadians(degrees) {
            return degrees * (Math.PI / 180);
        }

        function getDistanceKm(lat1, lon1, lat2, lon2) {
            const earthRadius = 6371;
            const dLat = toRadians(lat2 - lat1);
            const dLon = toRadians(lon2 - lon1);

            const a = Math.sin(dLat / 2) * Math.sin(dLat / 2) +
                Math.cos(toRadians(lat1)) * Math.cos(toRadians(lat2)) *
                Math.sin(dLon / 2) * Math.sin(dLon / 2);

            const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
            return earthRadius * c;
        }

        function showDistanceAlert(distanceKm) {
            const formattedDistance = distanceKm.toFixed(2);
            if (distanceKm <= 1) {
                Swal.fire({
                    icon: 'success',
                    title: 'Within 1 km radius',
                    text: `You are in the selected institution radius (${formattedDistance} km).`,
                    confirmButtonColor: '#565381',
                });
            } else {
                Swal.fire({
                    icon: 'info',
                    title: 'Outside radius',
                    html: `You are far from the selected institution.<br>Distance: <strong>${formattedDistance} km</strong>`,
                    confirmButtonColor: '#565381',
                });
            }
        }

        function requestUserLocation(callback) {
            if (!navigator.geolocation) {
                Swal.fire({
                    icon: 'error',
                    title: 'Geolocation unsupported',
                    text: 'Your browser does not support geolocation.',
                });
                return;
            }

            navigator.geolocation.getCurrentPosition(function(position) {
                userLocation = {
                    latitude: position.coords.latitude,
                    longitude: position.coords.longitude,
                };

                storeUserLocationSession(userLocation.latitude, userLocation.longitude)
                    .finally(function() {
                        if (typeof callback === 'function') {
                            callback(userLocation);
                        }
                    });
            }, function(error) {
                const message = error && error.message ? error.message : 'Permission denied or location unavailable.';
                Swal.fire({
                    icon: 'error',
                    title: 'Unable to fetch location',
                    text: message,
                });
            }, {
                enableHighAccuracy: true,
                timeout: 15000,
                maximumAge: 0,
            });
        }

        function compareDistanceAndNotify(targetCoords) {
            const proceed = function() {
                if (userLocation.latitude === null || userLocation.longitude === null) {
                    return;
                }
                const distanceKm = getDistanceKm(
                    userLocation.latitude,
                    userLocation.longitude,
                    targetCoords.latitude,
                    targetCoords.longitude
                );
                showDistanceAlert(distanceKm);
            };

            if (userLocation.latitude === null || userLocation.longitude === null) {
                requestUserLocation(proceed);
            } else {
                proceed();
            }
        }

        function handleInstitutionSelection(optionElement) {
            if (!optionElement || !optionElement.value) {
                return;
            }

            const institutionId = optionElement.value;

            fetchInstitutionAddress(institutionId)
                .then(function(address) {
                    return geocodeAddress(address);
                })
                .then(function(coords) {
                    return storeInstitutionLocationSession(institutionId, coords.latitude, coords.longitude)
                        .then(function() {
                            return coords;
                        });
                })
                .then(function(coords) {
                    compareDistanceAndNotify(coords);
                })
                .catch(function(error) {
                    const message = error && error.message ? error.message : 'Unable to determine the institution location.';
                    Swal.fire({
                        icon: 'error',
                        title: 'Location unavailable',
                        text: message,
                    });
                });
        }

        if (institutionSelect) {
            institutionSelect.addEventListener('change', function(event) {
                handleInstitutionSelection(event.target.selectedOptions[0]);
            });

            if (institutionSelect.value) {
                handleInstitutionSelection(institutionSelect.selectedOptions[0]);
            }
        }

        if (userLocation.latitude === null || userLocation.longitude === null) {
            requestUserLocation();
        }
    });
</script> --}}

@endsection
