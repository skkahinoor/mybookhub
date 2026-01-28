@extends('admin.layout.layout')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Website Mode Settings</h4>
                </div>
                <div class="card-body">
                    @if(session('success_message'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success_message') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    <form method="POST" action="{{ url('/admin/coming-soon-settings') }}">
                        @csrf
                        
                        <!-- Maintenance Mode Section -->
                        <div class="card mb-4" style="border-left: 4px solid #f5576c;">
                            <div class="card-header bg-light">
                                <h5 class="mb-0"><i class="fas fa-tools"></i> Maintenance Mode</h5>
                            </div>
                            <div class="card-body">
                                <div class="form-group">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="maintenance_mode_enabled" 
                                               id="maintenance_mode_enabled" value="1" 
                                               {{ $maintenanceModeEnabled == 1 ? 'checked' : '' }}>
                                        <label class="form-check-label" for="maintenance_mode_enabled">
                                            <strong>Enable Maintenance Mode</strong>
                                        </label>
                                    </div>
                                    <small class="form-text text-muted">
                                        When enabled, visitors will see a "Maintenance Mode" page. 
                                        This takes priority over Coming Soon mode. Admin, vendor, user, and sales executive routes will still be accessible.
                                    </small>
                                </div>
                            </div>
                        </div>

                        <!-- Coming Soon Mode Section -->
                        <div class="card mb-4" style="border-left: 4px solid #667eea;">
                            <div class="card-header bg-light">
                                <h5 class="mb-0"><i class="fas fa-clock"></i> Coming Soon Mode</h5>
                            </div>
                            <div class="card-body">
                                <div class="form-group">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="coming_soon_enabled" 
                                               id="coming_soon_enabled" value="1" 
                                               {{ $comingSoonEnabled == 1 ? 'checked' : '' }}>
                                        <label class="form-check-label" for="coming_soon_enabled">
                                            <strong>Enable Coming Soon Mode</strong>
                                        </label>
                                    </div>
                                    <small class="form-text text-muted">
                                        When enabled, visitors will see a "Coming Soon" page instead of the regular website. 
                                        Admin, vendor, user, and sales executive routes will still be accessible.
                                    </small>
                                </div>

                                <!-- Countdown Settings -->
                                <div class="form-group mt-4">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="show_countdown" 
                                               id="show_countdown" value="1" 
                                               {{ $showCountdown == 1 ? 'checked' : '' }}>
                                        <label class="form-check-label" for="show_countdown">
                                            <strong>Show Countdown Timer</strong>
                                        </label>
                                    </div>
                                    <small class="form-text text-muted">
                                        Display a countdown timer on the Coming Soon page.
                                    </small>
                                </div>

                                <div id="countdown-settings" style="{{ $showCountdown == 1 ? '' : 'display: none;' }}">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="countdown_date"><strong>Countdown Date</strong></label>
                                                <input type="date" 
                                                       class="form-control" 
                                                       id="countdown_date" 
                                                       name="countdown_date" 
                                                       value="{{ $countdownDate }}">
                                                <small class="form-text text-muted">
                                                    Select the target date for the countdown.
                                                </small>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="countdown_time"><strong>Countdown Time</strong></label>
                                                <input type="time" 
                                                       class="form-control" 
                                                       id="countdown_time" 
                                                       name="countdown_time" 
                                                       value="{{ $countdownTime }}">
                                                <small class="form-text text-muted">
                                                    Select the target time for the countdown.
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Social Media Links Section -->
                        <div class="card mb-4" style="border-left: 4px solid #17a2b8;">
                            <div class="card-header bg-light">
                                <h5 class="mb-0"><i class="fas fa-share-alt"></i> Social Media Links</h5>
                            </div>
                            <div class="card-body">
                                <p class="text-muted mb-3">Add your social media URLs. These will be displayed on both Coming Soon and Maintenance Mode pages.</p>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="social_facebook"><i class="fab fa-facebook-f text-primary"></i> Facebook URL</label>
                                            <input type="url" 
                                                   class="form-control" 
                                                   id="social_facebook" 
                                                   name="social_facebook" 
                                                   value="{{ $socialFacebook ?? '' }}"
                                                   placeholder="https://facebook.com/yourpage">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="social_twitter"><i class="fab fa-twitter text-info"></i> Twitter URL</label>
                                            <input type="url" 
                                                   class="form-control" 
                                                   id="social_twitter" 
                                                   name="social_twitter" 
                                                   value="{{ $socialTwitter ?? '' }}"
                                                   placeholder="https://twitter.com/yourhandle">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="social_instagram"><i class="fab fa-instagram text-danger"></i> Instagram URL</label>
                                            <input type="url" 
                                                   class="form-control" 
                                                   id="social_instagram" 
                                                   name="social_instagram" 
                                                   value="{{ $socialInstagram ?? '' }}"
                                                   placeholder="https://instagram.com/yourprofile">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="social_linkedin"><i class="fab fa-linkedin-in text-primary"></i> LinkedIn URL</label>
                                            <input type="url" 
                                                   class="form-control" 
                                                   id="social_linkedin" 
                                                   name="social_linkedin" 
                                                   value="{{ $socialLinkedin ?? '' }}"
                                                   placeholder="https://linkedin.com/company/yourcompany">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="social_youtube"><i class="fab fa-youtube text-danger"></i> YouTube URL</label>
                                            <input type="url" 
                                                   class="form-control" 
                                                   id="social_youtube" 
                                                   name="social_youtube" 
                                                   value="{{ $socialYoutube ?? '' }}"
                                                   placeholder="https://youtube.com/channel/yourchannel">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="social_pinterest"><i class="fab fa-pinterest text-danger"></i> Pinterest URL</label>
                                            <input type="url" 
                                                   class="form-control" 
                                                   id="social_pinterest" 
                                                   name="social_pinterest" 
                                                   value="{{ $socialPinterest ?? '' }}"
                                                   placeholder="https://pinterest.com/yourprofile">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="social_whatsapp"><i class="fab fa-whatsapp text-success"></i> WhatsApp URL</label>
                                            <input type="url" 
                                                   class="form-control" 
                                                   id="social_whatsapp" 
                                                   name="social_whatsapp" 
                                                   value="{{ $socialWhatsapp ?? '' }}"
                                                   placeholder="https://wa.me/1234567890">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="social_telegram"><i class="fab fa-telegram text-info"></i> Telegram URL</label>
                                            <input type="url" 
                                                   class="form-control" 
                                                   id="social_telegram" 
                                                   name="social_telegram" 
                                                   value="{{ $socialTelegram ?? '' }}"
                                                   placeholder="https://t.me/yourchannel">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle"></i> 
                            <strong>Important:</strong> 
                            <ul class="mb-0 mt-2">
                                <li>Maintenance Mode takes priority over Coming Soon Mode</li>
                                <li>When either mode is enabled, all front-end pages will display the respective page</li>
                                <li>Only authenticated admin, vendor, user, and sales executive routes will remain accessible</li>
                            </ul>
                        </div>

                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Save Settings
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Toggle countdown settings visibility
    document.getElementById('show_countdown').addEventListener('change', function() {
        const countdownSettings = document.getElementById('countdown-settings');
        if (this.checked) {
            countdownSettings.style.display = '';
        } else {
            countdownSettings.style.display = 'none';
        }
    });

    // Add confirmation dialogs
    document.getElementById('maintenance_mode_enabled').addEventListener('change', function() {
        if (this.checked) {
            if (!confirm('Are you sure you want to enable Maintenance Mode? This will hide your website from visitors.')) {
                this.checked = false;
            }
        }
    });

    document.getElementById('coming_soon_enabled').addEventListener('change', function() {
        if (this.checked) {
            if (!confirm('Are you sure you want to enable Coming Soon mode? This will hide your website from visitors.')) {
                this.checked = false;
            }
        }
    });

    // Prevent both modes from being enabled at the same time
    document.getElementById('maintenance_mode_enabled').addEventListener('change', function() {
        if (this.checked) {
            document.getElementById('coming_soon_enabled').checked = false;
        }
    });

    document.getElementById('coming_soon_enabled').addEventListener('change', function() {
        if (this.checked) {
            document.getElementById('maintenance_mode_enabled').checked = false;
        }
    });
</script>
@endsection
