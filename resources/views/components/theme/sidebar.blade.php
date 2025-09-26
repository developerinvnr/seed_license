<div class="app-menu navbar-menu">
    <div class="navbar-brand-box">
        <a href="{{ URL::to('/dashboard') }}" class="logo logo-dark">
            <span class="logo-sm">
                <img src="{{ asset('') }}assets/images/license_logo_v.png" alt="" height="55" />
            </span>
            <span class="logo-lg">
                <img src="{{ asset('') }}assets/images/license_logo_v.png" alt="" height="17" />
            </span>
        </a>

        <span class="logo-sm">
            <img src="{{ asset('') }}assets/images/license_logo_v.png" alt="" height="55"
                style="margin-top: 10px;" />
        </span>

        <a href="{{ URL::to('/dashboard') }}" class="logo logo-light">
            <span class="logo-sm">
                <img src="{{ asset('') }}assets/images/license_logo_v.png" alt="" height="55" />
            </span>


        </a>
        <button type="button" class="btn btn-sm p-0 fs-20 header-item float-end btn-vertical-sm-hover"
            id="vertical-hover">
            <i class="ri-record-circle-line"></i>
        </button>
    </div>
    <div class="dropdown sidebar-user m-1 rounded">
        <button type="button" class="btn material-shadow-none" id="page-header-user-dropdown" data-bs-toggle="dropdown"
            aria-haspopup="true" aria-expanded="false">
            <span class="d-flex align-items-center gap-2">
                <img class="rounded header-profile-user" src="{{ asset('') }}assets/images/users/avatar-1.jpg"
                    alt="Header Avatar" />
                <span class="text-start">
                    <span class="d-block fw-medium sidebar-user-name-text">cccAnna Adame</span>
                    <span class="d-block fs-14 sidebar-user-name-sub-text"><i
                            class="ri ri-circle-fill fs-10 text-success align-baseline"></i> <span
                            class="align-middle">Online</span></span>
                </span>
            </span>
        </button>
        <div class="dropdown-menu dropdown-menu-end">
            <h6 class="dropdown-header">Welcome Anna!</h6>
            <a class="dropdown-item" href="auth-logout-basic.html"><i
                    class="mdi mdi-logout text-muted fs-16 align-middle me-1"></i> <span class="align-middle"
                    data-key="t-logout">Logout</span></a>
        </div>
    </div>
    <div id="scrollbar">
        <div class="container-fluid">
            <div id="two-column-menu"></div>
            <ul class="navbar-nav" id="navbar-nav">
                <!-- Dashboard -->
                @can('view-dashboard')
                    <li class="nav-item">
                        <a class="nav-link menu-link {{ Request::is('dashboard') ? 'active' : '' }}"
                            href="{{ route('dashboard') }}">
                            <i class="ri-dashboard-2-line"></i> <span data-key="t-dashboards">Dashboards</span>
                        </a>
                    </li>
                @endcan

                <!-- Masters Menu -->
                @php
                    $mastersPermissions = [
                        'view-License Type',
                        'view-License Name',
                        'view-company Responsible Person',
                        'view-Company',
                        'view-Core API Data',
                        'view-Auth Draft Master',
                        'view-Crop Master',
                    ];
                    $hasMastersPermission = Auth::user()->hasAnyPermission($mastersPermissions);
                    $mastersActive =
                        Request::routeIs('license-type') ||
                        Request::routeIs('license-name') ||
                        Request::routeIs('responsible') ||
                        Request::routeIs('core') ||
                        Request::routeIs('auth-draft-master') ||
                        Request::routeIs('crop-master');
                @endphp
                @if ($hasMastersPermission)
                    <li class="nav-item">
                        <a class="nav-link menu-link {{ $mastersActive ? '' : 'collapsed' }}" href="#sidebarMasters"
                            data-bs-toggle="collapse" role="button"
                            aria-expanded="{{ $mastersActive ? 'true' : 'false' }}" aria-controls="sidebarMasters">
                            <i class="ri-dashboard-fill"></i><span data-key="t-dashboards">Masters</span>
                        </a>
                        <div class="collapse menu-dropdown {{ $mastersActive ? 'show' : '' }}" id="sidebarMasters">
                            <ul class="nav nav-sm flex-column">

                                @can('view-Company')
                                    <li class="nav-item">
                                        <a href="{{ route('company') }}"
                                            class="nav-link {{ Request::routeIs('company') ? 'active' : '' }}"
                                            data-key="t-company">
                                            Company Details
                                        </a>
                                    </li>
                                @endcan

                                @can('view-License Name')
                                    <li class="nav-item">
                                        <a href="{{ route('license-name') }}"
                                            class="nav-link {{ Request::routeIs('license-name') ? 'active' : '' }}"
                                            data-key="t-license-name">
                                            License Name
                                        </a>
                                    </li>
                                @endcan

                                @can('view-License Type')
                                    <li class="nav-item">
                                        <a href="{{ route('license-type') }}"
                                            class="nav-link {{ Request::routeIs('license-type') ? 'active' : '' }}"
                                            data-key="t-license-type">
                                            License Category
                                        </a>
                                    </li>
                                @endcan
                                
                                @can('view-company Responsible Person')
                                    <li class="nav-item">
                                        <a href="{{ route('responsible') }}"
                                            class="nav-link {{ Request::routeIs('responsible') ? 'active' : '' }}"
                                            data-key="t-responsible">
                                            Authorization Person
                                        </a>
                                    </li>
                                @endcan

                                @can('view-Auth Draft Master')
                                    <li class="nav-item">
                                        <a href="{{ route('auth-draft-master') }}"
                                            class="nav-link {{ Request::routeIs('auth-draft-master') ? 'active' : '' }}"
                                            data-key="t-auth-draft-master">
                                            Auth Draft Master
                                        </a>
                                    </li>
                                @endcan

                                @can('view-Core API Data')
                                    <li class="nav-item">
                                        <a href="{{ route('core') }}"
                                            class="nav-link {{ Request::routeIs('core') ? 'active' : '' }}"
                                            data-key="t-core_data">
                                            Core API Data
                                        </a>
                                    </li>
                                @endcan

                                @can('view-Crop Master')
                                    <li class="nav-item">
                                        <a href="{{ route('crop-master') }}"
                                            class="nav-link {{ Request::routeIs('crop-master') ? 'active' : '' }}"
                                            data-key="t-crop-master">
                                            Crop Master
                                        </a>
                                    </li>
                                @endcan
                            </ul>
                        </div>
                    </li>
                @endif

                <!-- Mapping Menu -->
                @php
                    $mappingPermissions = ['view-License Label', 'view-Lable sub field'];
                    $hasMappingPermission = Auth::user()->hasAnyPermission($mappingPermissions);
                    $mappingActive = Request::routeIs('license_label') || Request::routeIs('license_label_sub_field');
                @endphp
                @if ($hasMappingPermission)
                    <li class="nav-item">
                        <a class="nav-link menu-link {{ $mappingActive ? '' : 'collapsed' }}" href="#sidebarMapping"
                            data-bs-toggle="collapse" role="button"
                            aria-expanded="{{ $mappingActive ? 'true' : 'false' }}" aria-controls="sidebarMapping">
                            <i class="ri-share-line"></i><span data-key="t-mapping">Mapping</span>
                        </a>
                        <div class="collapse menu-dropdown {{ $mappingActive ? 'show' : '' }}" id="sidebarMapping">
                            <ul class="nav nav-sm flex-column">
                                @can('view-License Label')
                                    <li class="nav-item">
                                        <a href="{{ route('license_label') }}"
                                            class="nav-link {{ Request::routeIs('license_label') ? 'active' : '' }}"
                                            data-key="t-license_label">
                                            License Label
                                        </a>
                                    </li>
                                @endcan
                                @can('view-Lable sub field')
                                    <li class="nav-item">
                                        <a href="{{ route('license_label_sub_field') }}"
                                            class="nav-link {{ Request::routeIs('license_label_sub_field') ? 'active' : '' }}"
                                            data-key="t-license_label_sub_field">
                                            Label Sub Field
                                        </a>
                                    </li>
                                @endcan
                            </ul>
                        </div>
                    </li>
                @endif

                <!-- Users Menu -->
                @php
                    $usersPermissions = ['view-user', 'view-permission', 'view-role'];
                    $hasUsersPermission = Auth::user()->hasAnyPermission($usersPermissions);
                    $usersActive = Request::is('users*');
                @endphp
                @if ($hasUsersPermission)
                    <li class="nav-item">
                        <a class="nav-link menu-link {{ $usersActive ? '' : 'collapsed' }}" data-bs-toggle="collapse"
                            href="#usersMenu" role="button" aria-expanded="{{ $usersActive ? 'true' : 'false' }}"
                            aria-controls="usersMenu">
                            <i class="ri-file-user-line"></i>
                            <span data-key="t-users">Users</span>
                        </a>
                        <div class="collapse menu-dropdown {{ $usersActive ? 'show' : '' }}" id="usersMenu">
                            <ul class="nav nav-sm flex-column">
                                @can('view-user')
                                    <li class="nav-item">
                                        <a href="{{ route('users.index') }}"
                                            class="nav-link {{ Request::routeIs('users.index') ? 'active' : '' }}">
                                            Users List
                                        </a>
                                    </li>
                                @endcan
                                @can('view-permission')
                                    <li class="nav-item">
                                        <a href="{{ route('users.permission') }}"
                                            class="nav-link {{ Request::routeIs('users.permission') ? 'active' : '' }}">
                                            Permission
                                        </a>
                                    </li>
                                @endcan
                                @can('view-role')
                                    <li class="nav-item">
                                        <a href="{{ route('users.role') }}"
                                            class="nav-link {{ Request::routeIs('users.role') ? 'active' : '' }}">
                                            Role
                                        </a>
                                    </li>
                                @endcan
                            </ul>
                        </div>
                    </li>
                @endif

                <!-- All Licenses -->
                @php
                    $licenseActive = Request::routeIs('license-list');
                @endphp
                @can('view-Add License')
                    <li class="nav-item">
                        <a class="nav-link menu-link {{ $licenseActive ? '' : 'collapsed' }}" href="#all_license"
                            data-bs-toggle="collapse" role="button"
                            aria-expanded="{{ $licenseActive ? 'true' : 'false' }}" aria-controls="all_license">
                            <i class="ri-list-check"></i><span data-key="t-dashboards">All Licenses</span>
                        </a>
                        <div class="collapse menu-dropdown {{ $licenseActive ? 'show' : '' }}" id="all_license">
                            <ul class="nav nav-sm flex-column">
                                <li class="nav-item">
                                    <a href="{{ route('license-list') }}"
                                        class="nav-link {{ Request::routeIs('license-list') ? 'active' : '' }}"
                                        data-key="t-license-list">
                                        Add License
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </li>
                @endcan
            </ul>
        </div>
    </div>
    <div class="sidebar-background"></div>
    <div id="removeNotificationModal"></div>
</div>
