<nav class="navbar col-lg-12 col-12 p-0 fixed-top d-flex flex-row">
    <div class="text-center navbar-brand-wrapper d-flex align-items-center justify-content-center">
        @php
            $navbarLogo = null;
            if (isset($logos)) {
                if ($logos instanceof \Illuminate\Database\Eloquent\Model) {
                    $navbarLogo = $logos->logo;
                } else {
                    $navbarLogo = optional($logos->first())->logo;
                }
            }
            if (! filled($navbarLogo) && isset($headerLogo) && filled($headerLogo->logo)) {
                $navbarLogo = $headerLogo->logo;
            }
        @endphp
        <a class="navbar-brand brand-logo mr-5" href="{{ url('/') }}">
            @if(filled($navbarLogo))
                <img src="{{ asset('uploads/logos/' . $navbarLogo) }}" class="mr-2" alt="logo" />
            @else
                <span class="font-weight-bold text-primary mr-2">BookHub</span>
            @endif
        </a>
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
                    @php
                        $unread = (int) ($navUnreadCount ?? 0);
                    @endphp
                    @if($unread > 0)
                      <span class="count"></span>
                    @endif
                </a>
                <div class="dropdown-menu dropdown-menu-right navbar-dropdown preview-list"
                     aria-labelledby="notificationDropdown" style="min-width:320px; max-width:380px;">
                    <div class="d-flex justify-content-between align-items-center px-3 py-2 border-bottom">
                        <p class="mb-0 font-weight-normal">Notifications</p>
                        @if(($unread ?? 0) > 0)
                            <span class="badge badge-primary badge-pill">{{ $unread }} new</span>
                        @endif
                    </div>
                    <div style="max-height:320px; overflow-y:auto;">
                    @forelse(($navNotifications ?? []) as $n)
                      @php
                        $isUnread = !((bool) ($n->is_read ?? false));
                        $iconBg = $isUnread ? 'bg-primary' : 'bg-light';
                        $iconClass = $isUnread ? 'ti-bell' : 'ti-check';
                      @endphp
                      <a href="#" class="dropdown-item preview-item js-student-notification"
                         data-id="{{ $n->id }}"
                         data-read="{{ (int) ((bool) ($n->is_read ?? false)) }}"
                         style="white-space: normal;">
                        <div class="preview-thumbnail">
                          <div class="preview-icon {{ $iconBg }}">
                            <i class="{{ $iconClass }} mx-0"></i>
                          </div>
                        </div>
                        <div class="preview-item-content">
                          <h6 class="preview-subject font-weight-normal mb-1">
                              {{ $n->title }}
                              @if($isUnread)
                                  <span class="badge badge-pill badge-primary ml-1">New</span>
                              @endif
                          </h6>
                          <p class="font-weight-light small-text mb-0 text-muted" style="white-space: normal;">
                              {{ \Illuminate\Support\Str::limit($n->message, 60) }}
                          </p>
                          <small class="text-muted d-block mt-1">
                              {{ $n->created_at?->diffForHumans() }}
                          </small>
                        </div>
                      </a>
                    @empty
                      <div class="dropdown-item preview-item" style="white-space: normal;">
                        <div class="preview-item-content">
                          <p class="mb-0 text-muted">No notifications yet.</p>
                        </div>
                      </div>
                    @endforelse
                    </div>
                    <div class="border-top text-center py-2">
                        <a href="{{ route('student.notifications.index') }}" class="text-primary font-weight-bold" style="font-size: 0.85rem;">
                            See all notifications &rarr;
                        </a>
                    </div>
                </div>
            </li>
            <li class="nav-item">
                <a class="nav-link wallet-pill" href="{{ route('student.wallet') }}">
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
                    <a class="dropdown-item" href="{{ route('student.account') }}">
                        <i class="ti-user text-primary"></i>
                        My Profile
                    </a>
                    <a class="dropdown-item" href="#"
                        onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <i class="ti-power-off text-primary"></i>
                        Logout
                    </a>

                    <form id="logout-form" action="{{ route('student.logout') }}" method="POST" class="d-none">
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

    // Student notifications: mark as read when clicked
    (function () {
        var items = document.querySelectorAll('.js-student-notification');
        if (!items.length) return;

        items.forEach(function (el) {
            el.addEventListener('click', function (e) {
                e.preventDefault();
                var id = el.getAttribute('data-id');
                var isRead = el.getAttribute('data-read') === '1';
                if (isRead || !id) return;

                fetch("{{ url('/student/notifications') }}/" + id + "/read", {
                    method: "POST",
                    headers: {
                        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        "Accept": "application/json"
                    }
                }).then(function () {
                    // Simple UI update: hide New badge & decrement count if present
                    var badge = el.querySelector('.badge.badge-primary');
                    if (badge) badge.remove();
                    el.setAttribute('data-read', '1');
                    var countEl = document.querySelector('#notificationDropdown .count');
                    if (countEl) {
                        var n = parseInt(countEl.textContent || '0', 10);
                        n = isNaN(n) ? 0 : Math.max(0, n - 1);
                        if (n === 0) countEl.remove();
                        else countEl.textContent = String(n);
                    }
                }).catch(function () {});
            });
        });
    })();
</script>
