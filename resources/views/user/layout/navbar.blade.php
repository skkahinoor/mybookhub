<nav class="navbar col-lg-12 col-12 p-0 fixed-top d-flex flex-row">
    <div class="text-center navbar-brand-wrapper d-flex align-items-center justify-content-center">
        <a class="navbar-brand brand-logo mr-5" href="{{ url('/') }}"><img src="{{ asset('uploads/logos/' . $logos->first()->logo) }}" class="mr-2"
                alt="logo" /></a>
    </div>

    <div class="navbar-menu-wrapper d-flex align-items-center justify-content-end">
        <button class="navbar-toggler navbar-toggler align-self-center" type="button" data-toggle="minimize">
            <span class="icon-menu"></span>
        </button>
        <ul class="navbar-nav mr-lg-2">
            <li class="nav-item nav-search d-none d-lg-block">
                <div class="input-group">
                    <div class="input-group-prepend hover-cursor" id="navbar-search-icon">
                        <span class="input-group-text" id="search">
                            {{-- <i class="icon-search"></i> --}}
                        </span>
                    </div>
                    <input type="text" class="form-control" id="headerSearchInput" placeholder="Search Books Here"
                        aria-label="search" aria-describedby="search">
                        <button class="btn" id="headerSearchButton" type="button"><i class="icon-search"></i></button>


                       {{-- <input type="text" class="form-control" id="headerSearchInput"
                        aria-label="Text input with dropdown button" placeholder="Search Books Here"
                        style="border-top-left-radius:0px !important; border-bottom-left-radius:0px !important;">
                    <button class="btn" id="headerSearchButton" type="button"><i class="flaticon-loupe"></i></button> --}}
                </div>
            </li>
        </ul>
        <ul class="navbar-nav navbar-nav-right">
            <li class="nav-item dropdown">
                <a class="nav-link count-indicator dropdown-toggle" id="notificationDropdown" href="#"
                    data-toggle="dropdown">
                    <i class="icon-bell mx-0"></i>
                    <span class="count"></span>
                </a>
                <div class="dropdown-menu dropdown-menu-right navbar-dropdown preview-list"
                    aria-labelledby="notificationDropdown">
                    <p class="mb-0 font-weight-normal float-left dropdown-header">Notifications</p>
                    <a class="dropdown-item preview-item">
                        <div class="preview-thumbnail">
                            <div class="preview-icon bg-success">
                                <i class="ti-info-alt mx-0"></i>
                            </div>
                        </div>
                        <div class="preview-item-content">
                            <h6 class="preview-subject font-weight-normal">Application Error</h6>
                            <p class="font-weight-light small-text mb-0 text-muted">
                                Just now
                            </p>
                        </div>
                    </a>
                    <a class="dropdown-item preview-item">
                        <div class="preview-thumbnail">
                            <div class="preview-icon bg-warning">
                                <i class="ti-settings mx-0"></i>
                            </div>
                        </div>
                        <div class="preview-item-content">
                            <h6 class="preview-subject font-weight-normal">Settings</h6>
                            <p class="font-weight-light small-text mb-0 text-muted">
                                Private message
                            </p>
                        </div>
                    </a>
                    <a class="dropdown-item preview-item">
                        <div class="preview-thumbnail">
                            <div class="preview-icon bg-info">
                                <i class="ti-user mx-0"></i>
                            </div>
                        </div>
                        <div class="preview-item-content">
                            <h6 class="preview-subject font-weight-normal">New user registration</h6>
                            <p class="font-weight-light small-text mb-0 text-muted">
                                2 days ago
                            </p>
                        </div>
                    </a>
                </div>
            </li>
            <li class="nav-item nav-profile dropdown">
                <a class="nav-link dropdown-toggle" href="#" data-toggle="dropdown" id="profileDropdown">
                    @php
                        $avatar = optional(Auth::user())->profile_image ?? null;
                        $avatarSrc = $avatar ? asset($avatar) : asset('user/images/faces/face28.jpg');
                    @endphp
                    <img src="{{ $avatarSrc }}" alt="profile" id="nav-avatar" />
                </a>
                <div class="dropdown-menu dropdown-menu-right navbar-dropdown" aria-labelledby="profileDropdown">
                    <a class="dropdown-item" href="{{ route('user.account') }}">
                        <i class="ti-user text-primary"></i>
                        My Profile
                    </a>
                    <a class="dropdown-item" href="#"
                        onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <i class="ti-power-off text-primary"></i>
                        Logout
                    </a>

                    <form id="logout-form" action="{{ route('user.logout') }}" method="POST" class="d-none">
                        @csrf
                    </form>

                </div>
            </li>

        </ul>
        <button class="navbar-toggler navbar-toggler-right d-lg-none align-self-center" type="button"
            data-toggle="offcanvas">
            <span class="icon-menu"></span>
        </button>
    </div>
</nav>


<script>
    // Global search handlers for header and sticky inputs
    (function() {
        function goToSearch(term) {
            // User-side search route
            var url = '{{ route('user.query.index') }}';
            if (term && term.trim().length) {
                window.location.href = url + '?search=' + encodeURIComponent(term.trim());
            } else {
                window.location.href = url;
            }
        }

        function bindSearch(inputId, buttonId) {
            var input = document.getElementById(inputId);
            var button = document.getElementById(buttonId);
            if (!input || !button) return;

            button.addEventListener('click', function(e) {
                e.preventDefault();
                goToSearch(input.value);
            });
            input.addEventListener('keydown', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    goToSearch(input.value);
                }
            });
        }

        bindSearch('headerSearchInput', 'headerSearchButton');
        bindSearch('mobileSearchInput', 'mobileSearchButton');
    })();
</script>
