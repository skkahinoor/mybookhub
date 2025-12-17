<nav class="navbar navbar-expand-lg bg-white border-bottom shadow-sm sticky-top">
    <div class="container">

        <img
        src="{{ asset('uploads/logos/' . $logos->first()->logo) }}"
        alt="" height="50" width="150">
        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar"
                aria-controls="mainNavbar" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="mainNavbar">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item" style="padding-top: 0.2rem; !important;">
                    @if(auth('sales')->check())
                        <x-nav-link :href="route('sales.dashboard')" :active="request()->routeIs('sales.*')" class="nav-link">
                            <i class="bi bi-speedometer2 me-1"></i>{{ __('Dashboard') }}
                        </x-nav-link>
                    @else
                        <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" class="nav-link">
                            <i class="bi bi-speedometer2 me-1"></i>{{ __('Dashboard') }}
                        </x-nav-link>
                    @endif
                </li>
                @if(auth('sales')->check())
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="salesInstitutionsDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-building me-1"></i> Institutions
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="salesInstitutionsDropdown">
                        <li>
                            <a class="dropdown-item" href="{{ route('sales.institution_managements.index') }}">All Institutions</a>
                        </li>
                    </ul>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="salesStudentsDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-person-lines-fill me-1"></i> Students
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="salesStudentsDropdown">
                        <li>
                            <a class="dropdown-item" href="{{ route('sales.students.index') }}">All Students</a>
                        </li>
                    </ul>
                </li>
                <li class="nav-item" style="padding-top: 4px;">
                    <x-nav-link :href="route('sales.reports.index')" :active="request()->routeIs('sales.reports.*')" class="nav-link">
                        <i class="bi bi-file-earmark-text me-1"></i>{{ __('Reports') }}
                    </x-nav-link>
                </li>
                <li class="nav-item" style="padding-top: 4px;">
                    <x-nav-link :href="route('sales.withdrawals.index')" :active="request()->routeIs('sales.withdrawals.*')" class="nav-link">
                        <i class="bi bi-wallet2 me-1"></i>{{ __('Withdrawals') }}
                    </x-nav-link>
                </li>
                {{-- <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="salesBlocksDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-person-lines-fill me-1"></i> Blocks
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="salesBlocksDropdown">
                        <li>
                            <a class="dropdown-item" href="{{ route('sales.blocks.index') }}">All Blocks</a>
                        </li>
                    </ul>
                </li> --}}
                @endif
                <!-- Add more nav-items here -->
            </ul>
            <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                @if(auth('sales')->check())
                    <li class="nav-item dropdown">
                        @php
                            $salesUser = auth('sales')->user();
                            $profilePicture = $salesUser?->profile_picture
                                ? asset($salesUser->profile_picture)
                                : asset('assets/images/avatar.png');
                        @endphp
                        <a class="nav-link dropdown-toggle d-flex align-items-center gap-2" href="#" id="userDropdown"
                           role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <span class="rounded-circle border overflow-hidden" style="width: 36px; height: 36px;">
                                <img src="{{ $profilePicture }}" alt="Profile photo" class="w-100 h-100 object-fit-cover">
                            </span>
                            {{ $salesUser->name ?? 'Sales' }}
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="userDropdown">
                            <li>
                                <a class="dropdown-item text-danger" href="{{ route('sales.profile.edit') }}">
                                    <i class="bi bi-person-fill me-2"></i>{{ __('Profile') }}
                                </a>
                            </li>
                            <li>
                                <form method="POST" action="{{ route('sales.logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-danger">
                                        <i class="bi bi-box-arrow-right me-2"></i>{{ __('Log Out') }}
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </li>
                @else
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('sales.login') }}">Sales Login</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('sales.register') }}">Sales Register</a>
                    </li>
                @endif
            </ul>
        </div>
    </div>
</nav>

<style>
body {
    font-family: 'Nunito', Arial, sans-serif;
    background: #f8f9fa;
}
.navbar {
    box-shadow: 0 4px 24px rgba(13, 110, 253, 0.04), 0 1.5px 4px rgba(0,0,0,0.03);
}
.navbar-nav .nav-link.active {
    color: #0d6efd !important;
    background: rgba(13,110,253,0.1);
    border-radius: 0.375rem;
    transition: background .2s, color .2s;
}
.navbar-nav .nav-link {
    padding: .55rem 1.1rem;
    transition: background .2s, color .2s;
    font-weight: 500;
}
.navbar-nav .nav-link:hover {
    color: #0d6efd;
    background: rgba(13,110,253,0.06);
}
.dropdown-menu {
    border-radius: .5rem;
    border: none;
    box-shadow: 0 12px 24px rgba(13, 110, 253, 0.10), 0 2px 6px rgba(0,0,0,0.08);
}
</style>
