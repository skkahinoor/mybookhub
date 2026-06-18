@include('user.layout.header')

<div class="container-fluid page-body-wrapper">
    @include('user.layout.sidebar')

    <div class="main-panel">
        <div class="content-wrapper d-flex align-items-center justify-content-center" style="min-height: calc(100vh - 120px);">
            <div class="row w-100 justify-content-center">
                <div class="col-md-8 col-lg-6">
                    <div class="card border-0 shadow-lg" style="border-radius: 20px; overflow: hidden; background: #ffffff;">
                        <div class="card-body p-5 text-center">
                            
                            <!-- Premium Illustration/Icon -->
                            <div class="mb-4 d-inline-flex align-items-center justify-content-center" style="width: 100px; height: 100px; border-radius: 50%; background: #fff3e0; color: #ff9800; box-shadow: 0 10px 20px rgba(255, 152, 0, 0.15);">
                                <i class="mdi mdi-cellphone-link-off" style="font-size: 3rem;"></i>
                            </div>

                            <!-- Title -->
                            <h3 class="font-weight-bold text-dark mb-3" style="font-size: 1.8rem; letter-spacing: -0.5px;">Sell Old Book</h3>
                            
                            <!-- Description -->
                            <p class="text-muted mb-4 px-3" style="font-size: 1.05rem; line-height: 1.6;">
                                Currently, we don't support the Sell Book concept through the website.
                                Please download our mobile app to list and sell your old books.
                            </p>

                            <!-- Play Store Badge / Button -->
                            <div class="mt-4 pt-2">
                                <a href="https://play.google.com/store/apps/details?id=com.bookhub.user" target="_blank" rel="noopener noreferrer" class="playstore-btn d-inline-flex align-items-center" style="text-decoration: none;">
                                    <div class="d-flex align-items-center justify-content-center bg-dark text-white py-2 px-4" style="border-radius: 12px; border: 1px solid #333; transition: all 0.3s ease; gap: 12px; box-shadow: 0 6px 20px rgba(0,0,0,0.15);">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 512 512">
                                            <path fill="#00c6ff" d="M12.28 12.18C8.98 15.65 7 21 7 28.18v455.64c0 7.18 1.98 12.53 5.28 16L12.9 505L270.5 247.3v-5.6L12.9 7l-.62 5.18z"/>
                                            <path fill="#00e676" d="M356.42 333.18L270.5 247.3v-5.6l85.92-85.88l5.22 3.01L463.3 216.5c28.92 16.5 28.92 43.46 0 59.96l-101.66 57.81l-5.22-1.09z"/>
                                            <path fill="#ffd600" d="M270.5 241.7L12.9 7c-3.3 3.47-5.28 8.82-5.28 16v1.4c0-7.18 1.98-12.53 5.28-16L270.5 241.7z"/>
                                            <path fill="#ff3d00" d="M270.5 252.9L12.9 505c-3.3-3.47-5.28-8.82-5.28-16v-1.4c0 7.18 1.98 12.53 5.28 16L270.5 252.9z"/>
                                            <path fill="#ff1744" d="M356.42 161.72L12.9 7l257.6 257.6l85.92-85.88z"/>
                                            <path fill="#00b0ff" d="M356.42 333.18L270.5 247.3L12.9 505l343.52-171.82z"/>
                                        </svg>
                                        <div class="text-left">
                                            <div style="font-size: 0.75rem; text-transform: uppercase; font-weight: 500; opacity: 0.8; letter-spacing: 0.5px; text-align: left;">Get it on</div>
                                            <div style="font-size: 1.15rem; font-weight: 700; line-height: 1.2; text-align: left;">Google Play</div>
                                        </div>
                                    </div>
                                </a>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@include('user.layout.footer')

<style>
    .playstore-btn div:hover {
        background-color: #111 !important;
        transform: translateY(-2px);
        box-shadow: 0 10px 25px rgba(0,0,0,0.25) !important;
    }
</style>

<!-- plugins:js -->
<script src="{{ asset('user/vendors/js/vendor.bundle.base.js') }}"></script>
<!-- endinject -->
<!-- inject:js -->
<script src="{{ asset('user/js/off-canvas.js') }}"></script>
<script src="{{ asset('user/js/hoverable-collapse.js') }}"></script>
<script src="{{ asset('user/js/template.js') }}"></script>
<script src="{{ asset('user/js/settings.js') }}"></script>
<!-- endinject -->
