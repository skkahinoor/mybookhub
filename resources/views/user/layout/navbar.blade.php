<nav class="navbar col-lg-12 col-12 p-0 fixed-top d-flex flex-row">
    <div class="text-center navbar-brand-wrapper d-flex align-items-center justify-content-center">
        <a class="navbar-brand brand-logo mr-5" href="{{ url('/') }}"><img
                src="{{ asset('uploads/logos/' . $logos->first()->logo) }}" class="mr-2" alt="logo" /></a>
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
                            <i class="icon-search"></i>
                        </span>
                    </div>
                    <input type="text" class="form-control" id="sidebarSearchInput" placeholder="Search Sidebar Menu"
                        aria-label="search" aria-describedby="search">
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
            <li class="nav-item">
                <a class="nav-link" href="{{ route('user.wallet') }}"
                    style="display: flex; align-items: center; gap: 8px; color: #ff9900; font-weight: 700; background: #fff5e6; padding: 8px 16px; border-radius: 20px; border: 1px solid #ffe3b3;">
                    <i class="ti-wallet" style="font-size: 1.1rem;"></i>
                    <span>₹{{ Auth::user()->wallet_balance }}</span>
                </a>
            </li>
            <li class="nav-item nav-profile dropdown">
                <a class="nav-link dropdown-toggle" href="#" data-toggle="dropdown" id="profileDropdown">
                    @php
                        $avatar = optional(Auth::user())->profile_image ?? null;
                        // Check if avatar exists and is in the new location (asset/user) or old location (storage)
                        if ($avatar) {
                            // If it's already in asset/user format, use it directly
    if (strpos($avatar, 'asset/user/') !== false) {
        $avatarSrc = asset($avatar);
    } else {
        // If it's in old storage format, still support it for backward compatibility
                                $avatarSrc = asset($avatar);
                            }
                        } else {
                            $avatarSrc = asset('user/images/faces/face28.jpg');
                        }
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
    // Sidebar menu search functionality
    (function() {
        var searchInput = document.getElementById('sidebarSearchInput');
        if (!searchInput) return;

        searchInput.addEventListener('input', function(e) {
            var searchTerm = e.target.value.toLowerCase().trim();
            filterSidebarMenu(searchTerm);
        });

        function filterSidebarMenu(searchTerm) {
            var sidebar = document.getElementById('sidebar');
            if (!sidebar) return;

            var menuItems = sidebar.querySelectorAll('.nav-item');

            if (!searchTerm) {
                // Show all items if search is empty
                menuItems.forEach(function(item) {
                    item.style.display = '';
                });
                return;
            }

            menuItems.forEach(function(item) {
                var menuTitle = item.querySelector('.menu-title');
                if (menuTitle) {
                    var titleText = menuTitle.textContent.toLowerCase();
                    if (titleText.includes(searchTerm)) {
                        item.style.display = '';
                    } else {
                        item.style.display = 'none';
                    }
                } else {
                    // For items without menu-title, check the link text
                    var link = item.querySelector('a.nav-link');
                    if (link) {
                        var linkText = link.textContent.toLowerCase();
                        if (linkText.includes(searchTerm)) {
                            item.style.display = '';
                        } else {
                            item.style.display = 'none';
                        }
                    }
                }
            });
        }

        // Clear search on escape key
        searchInput.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                e.target.value = '';
                filterSidebarMenu('');
            }
        });
    })();
</script>
