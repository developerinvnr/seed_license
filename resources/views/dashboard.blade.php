{{-- @extends('layouts.app')
@section('content')
    @push('styles')
        <style>
            .material-shadow {
                box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            }

            .bg-warning-subtle {
                background-color: #fff3cd !important;
            }

            .bg-info-subtle {
                background-color: #e7f1ff !important;
            }

            .nav-tabs .nav-link.active {
                background-color: #fff;
                border-bottom: 2px solid #0d6efd;
                color: #0d6efd;
            }

            .nav-tabs .nav-link {
                color: #495057;
            }

            .date-filter-section {
                display: flex;
                align-items: center;
                gap: 15px;
                flex-wrap: wrap;
            }

            .date-filter-section label {
                margin-bottom: 0;
                font-size: 0.9rem;
            }

            .date-filter-section .input-group {
                max-width: 200px;
            }

            .alert-error {
                background-color: #f8d7da;
                color: #721c24;
            }

            .tab-hidden {
                display: none;
            }
        </style>
    @endpush

    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between bg-galaxy-transparent">
                        <h4 class="mb-sm-0">{{ ucwords(str_replace('-', ' ', Request::path())) }}</h4>
                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="javascript: void(0);">Pages</a></li>
                                <li class="breadcrumb-item active">{{ ucwords(str_replace('-', ' ', Request::path())) }}</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            <div class="page-content py-4">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="card shadow-sm">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm flex-shrink-0">
                                            <span class="avatar-title bg-light text-primary rounded-circle fs-3 material-shadow">
                                                <i class="ri-file-list-3-fill align-middle"></i>
                                            </span>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <p class="text-uppercase fw-semibold fs-12 text-muted mb-1">Total Licenses</p>
                                            <h4 class="mb-0">
                                                <span class="counter-value" data-target="{{ $totalLicenses }}">0</span>
                                            </h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="card shadow-sm">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm flex-shrink-0">
                                            <span class="avatar-title bg-light text-success rounded-circle fs-3 material-shadow">
                                                <i class="ri-checkbox-circle-fill align-middle"></i>
                                            </span>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <p class="text-uppercase fw-semibold fs-12 text-muted mb-1">Active Licenses</p>
                                            <h4 class="mb-0">
                                                <span class="counter-value" data-target="{{ $activeLicenses }}">0</span>
                                            </h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="card shadow-sm">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm flex-shrink-0">
                                            <span class="avatar-title bg-light text-danger rounded-circle fs-3 material-shadow">
                                                <i class="ri-close-circle-fill align-middle"></i>
                                            </span>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <p class="text-uppercase fw-semibold fs-12 text-muted mb-1">Deactive Licenses</p>
                                            <h4 class="mb-0">
                                                <span class="counter-value" data-target="{{ $deactiveLicenses }}">0</span>
                                            </h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        @can('view-company Responsible Person')
                            <div class="col-md-4">
                                <div class="card shadow-sm h-100">
                                    <div class="card-header align-items-center d-flex bg-warning-subtle">
                                        <h6 class="card-title mb-0 flex-grow-1 text-uppercase fw-semibold fs-12 text-danger">
                                            Authorized License expiry
                                        </h6>
                                        <div class="flex-shrink-0">
                                            <div class="dropdown card-header-dropdown">
                                                <a class="text-reset dropdown-btn" href="#" data-bs-toggle="dropdown"
                                                aria-haspopup="true" aria-expanded="false">
                                                    <span class="text-muted" id="filter-label">Expired Only <i
                                                            class="mdi mdi-chevron-down ms-1"></i></span>
                                                </a>
                                                <div class="dropdown-menu dropdown-menu-end">
                                                    <a class="dropdown-item" href="#" data-days="null">Expired Only</a>
                                                    @foreach ([90, 45, 30, 20, 10, 5, 1] as $day)
                                                        <a class="dropdown-item" href="#" data-days="{{ $day }}">{{ $day }} Days</a>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-body p-0">
                                        <div data-simplebar style="max-height: 200px; min-height: 120px;">
                                            <div class="p-3" id="license-alerts">
                                                @forelse($licenseAlerts as $alert)
                                                    <div class="d-flex align-items-center mb-3">
                                                        <div class="avatar-xs flex-shrink-0">
                                                            <span class="avatar-title bg-light rounded-circle material-shadow">
                                                                <i class="ri-error-warning-line {{ $alert['status'] === 'Expiring' ? 'text-warning' : 'text-danger' }} fs-18"></i>
                                                            </span>
                                                        </div>
                                                        <div class="flex-grow-1 ms-3">
                                                            <h6 class="fs-14 mb-1">{{ $alert['emp_name'] }}</h6>
                                                            <p class="text-muted fs-12 mb-0">
                                                                {{ $alert['company_name'] }} — {{ $alert['license_type'] }} — {{ $alert['license_name'] }}
                                                                @if($alert['certificate_no'] && $alert['certificate_no'] !== 'N/A') (Certificate: {{ $alert['certificate_no'] }}) @endif
                                                            </p>
                                                        </div>
                                                        <div class="flex-shrink-0 text-end">
                                                            <h6 class="mb-1 text-danger">{{ $alert['status'] }}</h6>
                                                            <p class="text-muted fs-13 mb-0">
                                                                {{ $alert['status'] === 'Expiring' ? $alert['diff_for_humans'] : $alert['valid_up_to'] }}
                                                            </p>
                                                            @if($alert['status'] === 'Expired')
                                                                <a href="{{ route('responsible') }}" class="btn btn-sm btn-primary mt-1">Assign New Person</a>
                                                            @endif
                                                        </div>
                                                    </div>
                                                @empty
                                                    <p class="text-center text-muted fs-12">No license alerts at this time.</p>
                                                @endforelse
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endcan

                        <div class="col-md-6">
                            <div class="card shadow-sm h-100">
                                <div class="card-header align-items-center d-flex bg-info-subtle">
                                    <h6 class="card-title mb-0 flex-grow-1 text-uppercase fw-semibold fs-12 text-dark">
                                        Recent Updates
                                    </h6>
                                    <div class="flex-shrink-0">
                                        <div class="dropdown card-header-dropdown">
                                            <a class="text-reset dropdown-btn" href="#" data-bs-toggle="dropdown"
                                            aria-haspopup="true" aria-expanded="false">
                                                <span class="text-muted" id="filter-label">All <i class="mdi mdi-chevron-down ms-1"></i></span>
                                            </a>
                                            <div class="dropdown-menu dropdown-menu-end">
                                                <a class="dropdown-item" href="#">Added</a>
                                                <a class="dropdown-item" href="#">Modified</a>
                                                <a class="dropdown-item" href="#">Approved</a>
                                                <a class="dropdown-item" href="#">Rejected</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="card-body p-0">
                                    <div data-simplebar style="max-height: 300px; min-height: 150px;">
                                        <div class="p-3">
                                    
                                            <ul class="nav nav-tabs nav-tabs-modern mb-3" id="recentUpdatesTabs" role="tablist">
                                                <li class="nav-item">
                                                    <a class="nav-link active" id="recent-added-tab" data-bs-toggle="tab" href="#recent-added" role="tab" aria-controls="recent-added" aria-selected="true">
                                                        Added <span class="badge bg-primary ms-1">{{ count($recentAdded) }}</span>
                                                    </a>
                                                </li>
                                                <li class="nav-item">
                                                    <a class="nav-link" id="modifications-tab" data-bs-toggle="tab" href="#modifications" role="tab" aria-controls="modifications" aria-selected="false">
                                                        Modified <span class="badge bg-info ms-1">{{ count($recentModified) }}</span>
                                                    </a>
                                                </li>
                                                <li class="nav-item">
                                                    <a class="nav-link" id="approval-tab" data-bs-toggle="tab" href="#approval" role="tab" aria-controls="approval" aria-selected="false">
                                                        Approved <span class="badge bg-success ms-1">{{ count($approvedLicenses) }}</span>
                                                    </a>
                                                </li>
                                                <li class="nav-item">
                                                    <a class="nav-link" id="rejection-tab" data-bs-toggle="tab" href="#rejection" role="tab" aria-controls="rejection" aria-selected="false">
                                                        Rejected <span class="badge bg-danger ms-1">{{ count($rejectedLicenses) }}</span>
                                                    </a>
                                                </li>
                                            </ul>


                                            <div class="tab-content tab-content-modern" id="recentUpdatesTabContent">
                                                <div class="tab-pane fade show active" id="recent-added" role="tabpanel" aria-labelledby="recent-added-tab">
                                                    @forelse($recentAdded as $update)
                                                        <div class="d-flex align-items-start mb-3">
                                                            <!-- Left details -->
                                                            <div class="flex-grow-1">
                                                                <h6 class="fs-14 mb-1">{{ $update['license_name'] }}</h6>
                                                                <p class="text-muted fs-12 mb-0">{{ $update['company_name'] }} — {{ $update['groupcom_name'] }} — {{ $update['license_type'] }}</p>
                                                            </div>

                                                            <!-- Right meta -->
                                                            <div class="text-end ms-3">
                                                                <h6 class="mb-1 text-success fs-13"><strong>{{ $update['activity_type'] }}</strong></h6>
                                                                <p class="text-muted fs-12 mb-1">{{ $update['activity_date'] }}</p>
                                                                <a href="{{ route('license-list') }}?filter_id={{ $update['id'] }}" class="btn btn-sm btn-primary">View</a>
                                                            </div>
                                                        </div>
                                                    @empty
                                                        <p class="text-center text-muted fs-12">No recent additions.</p>
                                                    @endforelse
                                                </div>

                                                <div class="tab-pane fade" id="modifications" role="tabpanel" aria-labelledby="modifications-tab">
                                                    @forelse($recentModified as $update)
                                                        <div class="d-flex align-items-start mb-3">
                                                            <div class="flex-grow-1">
                                                                <h6 class="fs-14 mb-1">{{ $update['license_name'] }}</h6>
                                                                <p class="text-muted fs-12 mb-0">{{ $update['company_name'] }} — {{ $update['groupcom_name'] }} — {{ $update['license_type'] }}</p>
                                                            </div>
                                                            <div class="text-end ms-3">
                                                                <h6 class="mb-1 text-info fs-13">{{ $update['activity_type'] }}</h6>
                                                                <p class="text-muted fs-12 mb-1">{{ $update['activity_date'] }}</p>
                                                                <a href="{{ route('license-list') }}?filter_id={{ $update['id'] }}" class="btn btn-sm btn-primary">View</a>
                                                            </div>
                                                        </div>
                                                    @empty
                                                        <p class="text-center text-muted fs-12">No recent modifications.</p>
                                                    @endforelse
                                                </div>

                                                <div class="tab-pane fade" id="approval" role="tabpanel" aria-labelledby="approval-tab">
                                                    @forelse($approvedLicenses as $update)
                                                        <div class="d-flex align-items-start mb-3">
                                                            <div class="flex-grow-1">
                                                                <h6 class="fs-14 mb-1">{{ $update['license_name'] }}</h6>
                                                                <p class="text-muted fs-12 mb-0">{{ $update['company_name'] }} — {{ $update['groupcom_name'] }} — {{ $update['license_type'] }}</p>
                                                            </div>
                                                            <div class="text-end ms-3">
                                                                <h6 class="mb-1 text-primary fs-13">{{ $update['activity_type'] }}</h6>
                                                                <p class="text-muted fs-12 mb-1">{{ $update['activity_date'] }}</p>
                                                                <a href="{{ route('license-list') }}?filter_id={{ $update['id'] }}" class="btn btn-sm btn-primary">View</a>
                                                            </div>
                                                        </div>
                                                    @empty
                                                        <p class="text-center text-muted fs-12">No approved licenses.</p>
                                                    @endforelse
                                                </div>

                                                <div class="tab-pane fade" id="rejection" role="tabpanel" aria-labelledby="rejection-tab">
                                                    @forelse($rejectedLicenses as $update)
                                                        <div class="d-flex align-items-start mb-3">
                                                            <div class="flex-grow-1">
                                                                <h6 class="fs-14 mb-1">{{ $update['license_name'] }}</h6>
                                                                <p class="text-muted fs-12 mb-0">{{ $update['company_name'] }} — {{ $update['groupcom_name'] }} — {{ $update['license_type'] }}</p>
                                                            </div>
                                                            <div class="text-end ms-3">
                                                                <h6 class="mb-1 text-danger fs-13">{{ $update['activity_type'] }}</h6>
                                                                <p class="text-muted fs-12 mb-1">{{ $update['activity_date'] }}</p>
                                                                <a href="{{ route('license-list') }}?filter_id={{ $update['id'] }}" class="btn btn-sm btn-primary">View</a>
                                                            </div>
                                                        </div>
                                                    @empty
                                                        <p class="text-center text-muted fs-12">No rejected licenses.</p>
                                                    @endforelse
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener("DOMContentLoaded", () => {
                // Counter animation for Total, Active, Deactive Licenses
                const counters = document.querySelectorAll(".counter-value");
                counters.forEach(counter => {
                    const target = +counter.getAttribute("data-target");
                    let count = 0;
                    const duration = 2000;
                    const frameRate = 60;
                    const totalSteps = duration / (1000 / frameRate);
                    const increment = Math.ceil(target / totalSteps);

                    const updateCount = () => {
                        count += increment;
                        if (count < target) {
                            counter.innerText = count;
                            requestAnimationFrame(updateCount);
                        } else {
                            counter.innerText = target;
                        }
                    };
                    requestAnimationFrame(updateCount);
                });

                // Function to update recent updates
                const updateRecentUpdates = async () => {
                    const startDate = document.getElementById('start_date_filter').value;
                    const endDate = document.getElementById('end_date_filter').value;
                    const tabFilter = document.getElementById('tab-filter-label').dataset.tab || 'recent-added';

                    if (!startDate || !endDate) {
                        console.log('Waiting for both start and end dates to be selected');
                        return;
                    }

                    // Validate date range
                    if (new Date(startDate) > new Date(endDate)) {
                        const alertContainer = document.querySelector('.alert-info');
                        alertContainer.classList.remove('alert-info');
                        alertContainer.classList.add('alert-error');
                        alertContainer.querySelector('.alert-message').textContent = 'Start date cannot be after end date';
                        return;
                    }

                    console.log('Fetching recent updates for:', { startDate, endDate, tabFilter });

                    try {
                        const response = await fetch(`/dashboard?start_date_filter=${startDate}&end_date_filter=${endDate}&tab_filter=${tabFilter}`, {
                            method: 'GET',
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json'
                            }
                        });

                        if (!response.ok) {
                            throw new Error(`HTTP error! Status: ${response.status}`);
                        }

                        const data = await response.json();
                        console.log('Received data:', data);

                        // Show only the selected tab
                        const tabs = ['recent-added', 'modifications', 'approval', 'rejection'];
                        tabs.forEach(tab => {
                            const nav = document.getElementById(`${tab}-nav`);
                            const pane = document.getElementById(tab);
                            const tabLink = document.getElementById(`${tab}-tab`);
                            if (tab === tabFilter) {
                                nav.classList.remove('tab-hidden');
                                pane.classList.add('active', 'show');
                                tabLink.classList.add('active');
                            } else {
                                nav.classList.add('tab-hidden');
                                pane.classList.remove('active', 'show');
                                tabLink.classList.remove('active');
                            }
                        });

                        // Update Recent Added
                        const addedContainer = document.getElementById('recent-added-content');
                        addedContainer.innerHTML = '';
                        if (data.recentAdded.length === 0) {
                            addedContainer.innerHTML = '<p class="text-center text-muted fs-12">No recent additions in this date range.</p>';
                        } else {
                            data.recentAdded.forEach(update => {
                                const html = `
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="avatar-xs flex-shrink-0">
                                            <span class="avatar-title bg-light rounded-circle material-shadow">
                                                <i class="ri-file-text-line text-success fs-18"></i>
                                            </span>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <h6 class="fs-14 mb-1">${update.license_name || 'N/A'}</h6>
                                            <p class="text-muted fs-12 mb-0">
                                                ${update.company_name || 'N/A'} — ${update.groupcom_name || 'N/A'} — ${update.license_type || 'N/A'}
                                            </p>
                                        </div>
                                        <div class="flex-shrink-0 text-end">
                                            <h6 class="mb-1 text-success">${update.activity_type}</h6>
                                            <p class="text-muted fs-13 mb-0">${update.activity_date}</p>
                                            <a href="${window.location.origin}/license-list?filter_id=${update.id}" class="btn btn-sm btn-primary mt-1">View Details</a>
                                        </div>
                                    </div>`;
                                addedContainer.insertAdjacentHTML('beforeend', html);
                            });
                        }

                        // Update Recent Modified
                        const modifiedContainer = document.getElementById('modifications-content');
                        modifiedContainer.innerHTML = '';
                        if (data.recentModified.length === 0) {
                            modifiedContainer.innerHTML = '<p class="text-center text-muted fs-12">No recent modifications in this date range.</p>';
                        } else {
                            data.recentModified.forEach(update => {
                                const html = `
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="avatar-xs flex-shrink-0">
                                            <span class="avatar-title bg-light rounded-circle material-shadow">
                                                <i class="ri-file-text-line text-info fs-18"></i>
                                            </span>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <h6 class="fs-14 mb-1">${update.license_name || 'N/A'}</h6>
                                            <p class="text-muted fs-12 mb-0">
                                                ${update.company_name || 'N/A'} — ${update.groupcom_name || 'N/A'} — ${update.license_type || 'N/A'}
                                            </p>
                                        </div>
                                        <div class="flex-shrink-0 text-end">
                                            <h6 class="mb-1 text-info">${update.activity_type}</h6>
                                            <p class="text-muted fs-13 mb-0">${update.activity_date}</p>
                                            <a href="${window.location.origin}/license-list?filter_id=${update.id}" class="btn btn-sm btn-primary mt-1">View Details</a>
                                        </div>
                                    </div>`;
                                modifiedContainer.insertAdjacentHTML('beforeend', html);
                            });
                        }

                        // Update Approval
                        const approvalContainer = document.getElementById('approval-content');
                        approvalContainer.innerHTML = '';
                        if (data.approvedLicenses.length === 0) {
                            approvalContainer.innerHTML = '<p class="text-center text-muted fs-12">No approved licenses in this date range.</p>';
                        } else {
                            data.approvedLicenses.forEach(update => {
                                const html = `
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="avatar-xs flex-shrink-0">
                                            <span class="avatar-title bg-light rounded-circle material-shadow">
                                                <i class="ri-check-line text-primary fs-18"></i>
                                            </span>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <h6 class="fs-14 mb-1">${update.license_name || 'N/A'}</h6>
                                            <p class="text-muted fs-12 mb-0">
                                                ${update.company_name || 'N/A'} — ${update.groupcom_name || 'N/A'} — ${update.license_type || 'N/A'}
                                            </p>
                                        </div>
                                        <div class="flex-shrink-0 text-end">
                                            <h6 class="mb-1 text-primary">${update.activity_type}</h6>
                                            <p class="text-muted fs-13 mb-0">${update.activity_date}</p>
                                            <a href="${window.location.origin}/license-list?filter_id=${update.id}" class="btn btn-sm btn-primary mt-1">View Details</a>
                                        </div>
                                    </div>`;
                                approvalContainer.insertAdjacentHTML('beforeend', html);
                            });
                        }

                        // Update Rejection
                        const rejectionContainer = document.getElementById('rejection-content');
                        rejectionContainer.innerHTML = '';
                        if (data.rejectedLicenses.length === 0) {
                            rejectionContainer.innerHTML = '<p class="text-center text-muted fs-12">No rejected licenses in this date range.</p>';
                        } else {
                            data.rejectedLicenses.forEach(update => {
                                const html = `
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="avatar-xs flex-shrink-0">
                                            <span class="avatar-title bg-light rounded-circle material-shadow">
                                                <i class="ri-close-line text-danger fs-18"></i>
                                            </span>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <h6 class="fs-14 mb-1">${update.license_name || 'N/A'}</h6>
                                            <p class="text-muted fs-12 mb-0">
                                                ${update.company_name || 'N/A'} — ${update.groupcom_name || 'N/A'} — ${update.license_type || 'N/A'}
                                            </p>
                                        </div>
                                        <div class="flex-shrink-0 text-end">
                                            <h6 class="mb-1 text-danger">${update.activity_type}</h6>
                                            <p class="text-muted fs-13 mb-0">${update.activity_date}</p>
                                            <a href="${window.location.origin}/license-list?filter_id=${update.id}" class="btn btn-sm btn-primary mt-1">View Details</a>
                                        </div>
                                    </div>`;
                                rejectionContainer.insertAdjacentHTML('beforeend', html);
                            });
                        }

                        // Update tab badges
                        const addedTabBadge = document.querySelector('#recent-added-tab .badge');
                        const modifiedTabBadge = document.querySelector('#modifications-tab .badge');
                        const approvalTabBadge = document.querySelector('#approval-tab .badge');
                        const rejectionTabBadge = document.querySelector('#rejection-tab .badge');
                        if (addedTabBadge) addedTabBadge.textContent = data.recentAdded.length;
                        if (modifiedTabBadge) modifiedTabBadge.textContent = data.recentModified.length;
                        if (approvalTabBadge) approvalTabBadge.textContent = data.approvedLicenses.length;
                        if (rejectionTabBadge) rejectionTabBadge.textContent = data.rejectedLicenses.length;

                        // Update summary alert
                        const alertContainer = document.querySelector('.alert-info');
                        alertContainer.classList.remove('alert-error');
                        alertContainer.classList.add('alert-info');
                        alertContainer.querySelector('.alert-message').textContent = 
                            `Showing ${data.recentAdded.length} Created | ${data.recentModified.length} Modified | ${data.approvedLicenses.length} Approved | ${data.rejectedLicenses.length} Rejected`;

                    } catch (error) {
                        console.error('Error fetching recent updates:', error);
                        document.getElementById('recent-added-content').innerHTML = '<p class="text-center text-muted fs-12">Error loading recent additions. Please try again.</p>';
                        document.getElementById('modifications-content').innerHTML = '<p class="text-center text-muted fs-12">Error loading recent modifications. Please try again.</p>';
                        document.getElementById('approval-content').innerHTML = '<p class="text-center text-muted fs-12">Error loading approved licenses. Please try again.</p>';
                        document.getElementById('rejection-content').innerHTML = '<p class="text-center text-muted fs-12">Error loading rejected licenses. Please try again.</p>';
                        const alertContainer = document.querySelector('.alert-info');
                        alertContainer.classList.remove('alert-info');
                        alertContainer.classList.add('alert-error');
                        alertContainer.querySelector('.alert-message').textContent = 'Error loading data. Please try again.';
                    }
                };

                // Event listeners for date inputs
                const startDateInput = document.getElementById('start_date_filter');
                const endDateInput = document.getElementById('end_date_filter');

                startDateInput.addEventListener('change', () => {
                    console.log('Start date changed:', startDateInput.value);
                    updateRecentUpdates();
                });

                endDateInput.addEventListener('change', () => {
                    console.log('End date changed:', endDateInput.value);
                    updateRecentUpdates();
                });

                // Event listener for tab filter dropdown
                document.querySelectorAll('.dropdown-item[data-tab]').forEach(item => {
                    item.addEventListener('click', function(e) {
                        e.preventDefault();
                        const tab = this.getAttribute('data-tab');
                        const label = this.textContent;
                        console.log('Tab filter selected:', tab);
                        document.getElementById('tab-filter-label').textContent = label + ' ';
                        document.getElementById('tab-filter-label').dataset.tab = tab;
                        updateRecentUpdates();
                    });
                });

                // Dropdown filter for License Status Alerts
                document.querySelectorAll('.dropdown-item[data-days]').forEach(item => {
                    item.addEventListener('click', async function(e) {
                        e.preventDefault();
                        const days = this.getAttribute('data-days');
                        const label = this.textContent;
                        console.log('Fetching alerts for days:', days);
                        document.getElementById('filter-label').textContent = label + ' ';

                        try {
                            const response = await fetch(`/dashboard?days=${days}`, {
                                method: 'GET',
                                headers: {
                                    'X-Requested-With': 'XMLHttpRequest',
                                    'Accept': 'application/json'
                                }
                            });

                            if (!response.ok) {
                                throw new Error(`HTTP error! Status: ${response.status}`);
                            }

                            const data = await response.json();
                            console.log('Received alerts data:', data);

                            // Update License Alerts only
                            const alertContainer = document.getElementById('license-alerts');
                            alertContainer.innerHTML = '';

                            if (data.licenseAlerts.length === 0) {
                                alertContainer.innerHTML = '<p class="text-center text-muted fs-12">No license alerts at this time.</p>';
                            } else {
                                data.licenseAlerts.forEach(alert => {
                                    const html = `
                                        <div class="d-flex align-items-center mb-3">
                                            <div class="avatar-xs flex-shrink-0">
                                                <span class="avatar-title bg-light rounded-circle material-shadow">
                                                    <i class="ri-error-warning-line ${alert.status === 'Expiring' ? 'text-warning' : 'text-danger'} fs-18"></i>
                                                </span>
                                            </div>
                                            <div class="flex-grow-1 ms-3">
                                                <h6 class="fs-14 mb-1">${alert.emp_name || 'N/A'}</h6>
                                                <p class="text-muted fs-12 mb-0">
                                                    ${alert.company_name || 'N/A'} — ${alert.license_type || 'N/A'} — ${alert.license_name || 'N/A'}
                                                    ${alert.certificate_no ? `(Certificate: ${alert.certificate_no})` : ''}
                                                </p>
                                            </div>
                                            <div class="flex-shrink-0 text-end">
                                                <h6 class="mb-1 text-danger">${alert.status}</h6>
                                                <p class="text-muted fs-13 mb-0">
                                                    ${alert.status === 'Expiring' ? alert.diff_for_humans : alert.valid_up_to}
                                                </p>
                                                ${alert.status === 'Expired' ? `<a href="${window.location.origin}/responsible" class="btn btn-sm btn-primary mt-1">Assign New Person</a>` : ''}
                                            </div>
                                        </div>`;
                                    alertContainer.insertAdjacentHTML('beforeend', html);
                                });
                            }
                        } if (!response.ok) {
                            throw new Error(`HTTP error! Status: ${response.status}`);
                        } catch (error) {
                            console.error('Error fetching alerts:', error);
                            document.getElementById('license-alerts').innerHTML = '<p class="text-center text-muted fs-12">Error loading alerts. Please try again.</p>';
                        }
                    });
                });

                // Initial load of recent updates
                updateRecentUpdates();

                const triggerTabList = document.querySelectorAll('#recentUpdatesTabs .nav-link');
                triggerTabList.forEach(triggerEl => {
                    triggerEl.addEventListener('click', () => {
                        console.log(`Tab switched to: ${triggerEl.textContent}`);
                    });
                });
            });
        </script>
    @endpush
@endsection --}}



@extends('layouts.app')
@section('content')
    @push('styles')
        <style>
            .material-shadow {
                box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            }

            .bg-warning-subtle {
                background-color: #fff3cd !important;
            }

            .bg-info-subtle {
                background-color: #e7f1ff !important;
            }

            .bg-success-subtle {
                background-color: #d4edda !important;
            }

            .nav-tabs .nav-link.active {
                background-color: #fff;
                border-bottom: 2px solid #0d6efd;
                color: #0d6efd;
            }

            .nav-tabs .nav-link {
                color: #495057;
            }

            .date-filter-section {
                display: flex;
                align-items: center;
                gap: 15px;
                flex-wrap: wrap;
            }

            .date-filter-section label {
                margin-bottom: 0;
                font-size: 0.9rem;
            }

            .date-filter-section .input-group {
                max-width: 200px;
            }

            .alert-error {
                background-color: #f8d7da;
                color: #721c24;
            }

            .tab-hidden {
                display: none;
            }
        </style>
    @endpush

    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between bg-galaxy-transparent">
                        <h4 class="mb-sm-0">{{ ucwords(str_replace('-', ' ', Request::path())) }}</h4>
                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="javascript: void(0);">Pages</a></li>
                                <li class="breadcrumb-item active">{{ ucwords(str_replace('-', ' ', Request::path())) }}</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            <div class="page-content py-4">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="card shadow-sm">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm flex-shrink-0">
                                            <span class="avatar-title bg-light text-primary rounded-circle fs-3 material-shadow">
                                                <i class="ri-file-list-3-fill align-middle"></i>
                                            </span>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <p class="text-uppercase fw-semibold fs-12 text-muted mb-1">Total Licenses</p>
                                            <h4 class="mb-0">
                                                <span class="counter-value" data-target="{{ $totalLicenses }}">0</span>
                                            </h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="card shadow-sm">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm flex-shrink-0">
                                            <span class="avatar-title bg-light text-success rounded-circle fs-3 material-shadow">
                                                <i class="ri-checkbox-circle-fill align-middle"></i>
                                            </span>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <p class="text-uppercase fw-semibold fs-12 text-muted mb-1">Active Licenses</p>
                                            <h4 class="mb-0">
                                                <span class="counter-value" data-target="{{ $activeLicenses }}">0</span>
                                            </h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="card shadow-sm">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm flex-shrink-0">
                                            <span class="avatar-title bg-light text-danger rounded-circle fs-3 material-shadow">
                                                <i class="ri-close-circle-fill align-middle"></i>
                                            </span>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <p class="text-uppercase fw-semibold fs-12 text-muted mb-1">Deactive Licenses</p>
                                            <h4 class="mb-0">
                                                <span class="counter-value" data-target="{{ $deactiveLicenses }}">0</span>
                                            </h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="card shadow-sm h-100">
                                <div class="card-header align-items-center d-flex bg-success-subtle">
                                    <h6 class="card-title mb-0 flex-grow-1 text-uppercase fw-semibold fs-12 text-success">
                                        Upcoming License Expiry Alerts
                                    </h6>
                                    <div class="flex-shrink-0">
                                        <div class="dropdown card-header-dropdown">
                                            <a class="text-reset dropdown-btn" href="#" data-bs-toggle="dropdown"
                                               aria-haspopup="true" aria-expanded="false">
                                                <span class="text-muted" id="expiry-filter-label">30 Days <i class="mdi mdi-chevron-down ms-1"></i></span>
                                            </a>
                                            <div class="dropdown-menu dropdown-menu-end">
                                                @foreach ([30, 60, 90] as $day)
                                                    <a class="dropdown-item" href="#" data-expiry-days="{{ $day }}">{{ $day }} Days</a>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body p-0">
                                    <div class="p-3">
                                        <div class="row g-2 mb-3">
                                            <div class="col-md-4">
                                                <select id="state_filter" class="form-select form-select-sm">
                                                    <option value="">All States</option>
                                                    @foreach ($states as $state)
                                                        <option value="{{ $state->id }}">{{ $state->state_name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-4">
                                                <select id="license_type_filter" class="form-select form-select-sm">
                                                    <option value="">All License Types</option>
                                                    @foreach ($licenseTypes as $licenseType)
                                                        <option value="{{ $licenseType->id }}">{{ $licenseType->license_type }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-4">
                                                <select id="responsible_person_filter" class="form-select form-select-sm">
                                                    <option value="">All Responsible Persons</option>
                                                    @foreach ($responsiblePersons as $person)
                                                        <option value="{{ $person->id }}">{{ $person->emp_name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div data-simplebar style="max-height: 200px; min-height: 120px;">
                                            <div id="upcoming-expiry-alerts">
                                                @forelse($upcomingExpiries as $expiry)
                                                    <div class="d-flex align-items-center mb-3">
                                                        <div class="avatar-xs flex-shrink-0">
                                                            <span class="avatar-title bg-light rounded-circle material-shadow">
                                                                <i class="ri-time-line text-warning fs-18"></i>
                                                            </span>
                                                        </div>
                                                        <div class="flex-grow-1 ms-3">
                                                            <h6 class="fs-14 mb-1">{{ $expiry['license_name'] }}</h6>
                                                            <p class="text-muted fs-12 mb-0">
                                                                {{ $expiry['company_name'] }} — {{ $expiry['license_type'] }} — {{ $expiry['state_name'] }}
                                                            </p>
                                                            <p class="text-muted fs-12 mb-0">
                                                                Responsible: {{ $expiry['responsible_person_name'] }}
                                                            </p>
                                                        </div>
                                                        <div class="flex-shrink-0 text-end">
                                                            <h6 class="mb-1 text-warning">{{ $expiry['expiry_category'] }}</h6>
                                                            <p class="text-muted fs-13 mb-0">{{ $expiry['valid_upto'] }}</p>
                                                            <p class="text-muted fs-13 mb-0">{{ $expiry['diff_for_humans'] }}</p>
                                                            <a href="{{ route('license-list') }}?filter_id={{ $expiry['id'] }}" class="btn btn-sm btn-primary mt-1">View</a>
                                                        </div>
                                                    </div>
                                                @empty
                                                    <p class="text-center text-muted fs-12">No upcoming expiries at this time.</p>
                                                @endforelse
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="card shadow-sm h-100">
                                <div class="card-header align-items-center d-flex bg-info-subtle">
                                    <h6 class="card-title mb-0 flex-grow-1 text-uppercase fw-semibold fs-12 text-dark">
                                        Recent Updates
                                    </h6>
                                    <div class="flex-shrink-0">
                                        <div class="dropdown card-header-dropdown">
                                            <a class="text-reset dropdown-btn" href="#" data-bs-toggle="dropdown"
                                            aria-haspopup="true" aria-expanded="false">
                                                <span class="text-muted" id="filter-label">All <i class="mdi mdi-chevron-down ms-1"></i></span>
                                            </a>
                                            <div class="dropdown-menu dropdown-menu-end">
                                                <a class="dropdown-item" href="#" data-tab="recent-added">Added</a>
                                                <a class="dropdown-item" href="#" data-tab="modifications">Modified</a>
                                                <a class="dropdown-item" href="#" data-tab="approval">Approved</a>
                                                <a class="dropdown-item" href="#" data-tab="rejection">Rejected</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="card-body p-0">                                    
                                    <div class="p-3">                                        
                                        <ul class="nav nav-tabs nav-tabs-modern mb-3" id="recentUpdatesTabs" role="tablist">
                                            <li class="nav-item" id="recent-added-nav">
                                                <a class="nav-link {{ $tabFilter == 'recent-added' ? 'active' : '' }}" id="recent-added-tab" data-bs-toggle="tab" href="#recent-added" role="tab" aria-controls="recent-added" aria-selected="{{ $tabFilter == 'recent-added' ? 'true' : 'false' }}">
                                                    Added <span class="badge bg-primary ms-1">{{ count($recentAdded) }}</span>
                                                </a>
                                            </li>
                                            <li class="nav-item" id="modifications-nav">
                                                <a class="nav-link {{ $tabFilter == 'modifications' ? 'active' : '' }}" id="modifications-tab" data-bs-toggle="tab" href="#modifications" role="tab" aria-controls="modifications" aria-selected="{{ $tabFilter == 'modifications' ? 'true' : 'false' }}">
                                                    Modified <span class="badge bg-info ms-1">{{ count($recentModified) }}</span>
                                                </a>
                                            </li>
                                            <li class="nav-item" id="approval-nav">
                                                <a class="nav-link {{ $tabFilter == 'approval' ? 'active' : '' }}" id="approval-tab" data-bs-toggle="tab" href="#approval" role="tab" aria-controls="approval" aria-selected="{{ $tabFilter == 'approval' ? 'true' : 'false' }}">
                                                    Approved <span class="badge bg-success ms-1">{{ count($approvedLicenses) }}</span>
                                                </a>
                                            </li>
                                            <li class="nav-item" id="rejection-nav">
                                                <a class="nav-link {{ $tabFilter == 'rejection' ? 'active' : '' }}" id="rejection-tab" data-bs-toggle="tab" href="#rejection" role="tab" aria-controls="rejection" aria-selected="{{ $tabFilter == 'rejection' ? 'true' : 'false' }}">
                                                    Rejected <span class="badge bg-danger ms-1">{{ count($rejectedLicenses) }}</span>
                                                </a>
                                            </li>
                                        </ul>

                                        <div class="tab-content tab-content-modern" id="recentUpdatesTabContent">
                                            <div class="tab-pane fade {{ $tabFilter == 'recent-added' ? 'show active' : '' }}" id="recent-added" role="tabpanel" aria-labelledby="recent-added-tab">
                                                <div id="recent-added-content">
                                                    @forelse($recentAdded as $update)
                                                        <div class="d-flex align-items-start mb-3">
                                                            <div class="flex-grow-1">
                                                                <h6 class="fs-14 mb-1">{{ $update['license_name'] }}</h6>
                                                                <p class="text-muted fs-12 mb-0">{{ $update['company_name'] }} — {{ $update['groupcom_name'] }} — {{ $update['license_type'] }}</p>
                                                            </div>
                                                            <div class="text-end ms-3">
                                                                <h6 class="mb-1 text-success fs-13"><strong>{{ $update['activity_type'] }}</strong></h6>
                                                                <p class="text-muted fs-12 mb-1">{{ $update['activity_date'] }}</p>
                                                                <a href="{{ route('license-list') }}?filter_id={{ $update['id'] }}" class="btn btn-sm btn-primary">View</a>
                                                            </div>
                                                        </div>
                                                    @empty
                                                        <p class="text-center text-muted fs-12">No recent additions.</p>
                                                    @endforelse
                                                </div>
                                            </div>

                                            <div class="tab-pane fade {{ $tabFilter == 'modifications' ? 'show active' : '' }}" id="modifications" role="tabpanel" aria-labelledby="modifications-tab">
                                                <div id="modifications-content">
                                                    @forelse($recentModified as $update)
                                                        <div class="d-flex align-items-start mb-3">
                                                            <div class="flex-grow-1">
                                                                <h6 class="fs-14 mb-1">{{ $update['license_name'] }}</h6>
                                                                <p class="text-muted fs-12 mb-0">{{ $update['company_name'] }} — {{ $update['groupcom_name'] }} — {{ $update['license_type'] }}</p>
                                                            </div>
                                                            <div class="text-end ms-3">
                                                                <h6 class="mb-1 text-info fs-13">{{ $update['activity_type'] }}</h6>
                                                                <p class="text-muted fs-12 mb-1">{{ $update['activity_date'] }}</p>
                                                                <a href="{{ route('license-list') }}?filter_id={{ $update['id'] }}" class="btn btn-sm btn-primary">View</a>
                                                            </div>
                                                        </div>
                                                    @empty
                                                        <p class="text-center text-muted fs-12">No recent modifications.</p>
                                                    @endforelse
                                                </div>
                                            </div>

                                            <div class="tab-pane fade {{ $tabFilter == 'approval' ? 'show active' : '' }}" id="approval" role="tabpanel" aria-labelledby="approval-tab">
                                                <div id="approval-content">
                                                    @forelse($approvedLicenses as $update)
                                                        <div class="d-flex align-items-start mb-3">
                                                            <div class="flex-grow-1">
                                                                <h6 class="fs-14 mb-1">{{ $update['license_name'] }}</h6>
                                                                <p class="text-muted fs-12 mb-0">{{ $update['company_name'] }} — {{ $update['groupcom_name'] }} — {{ $update['license_type'] }}</p>
                                                            </div>
                                                            <div class="text-end ms-3">
                                                                <h6 class="mb-1 text-success fs-13">{{ $update['activity_type'] }}</h6>
                                                                <p class="text-muted fs-12 mb-1">{{ $update['activity_date'] }}</p>
                                                                <a href="{{ route('license-list') }}?filter_id={{ $update['id'] }}" class="btn btn-sm btn-primary">View</a>
                                                            </div>
                                                        </div>
                                                    @empty
                                                        <p class="text-center text-muted fs-12">No approved licenses.</p>
                                                    @endforelse
                                                </div>
                                            </div>

                                            <div class="tab-pane fade {{ $tabFilter == 'rejection' ? 'show active' : '' }}" id="rejection" role="tabpanel" aria-labelledby="rejection-tab">
                                                <div id="rejection-content">
                                                    @forelse($rejectedLicenses as $update)
                                                        <div class="d-flex align-items-start mb-3">
                                                            <div class="flex-grow-1">
                                                                <h6 class="fs-14 mb-1">{{ $update['license_name'] }}</h6>
                                                                <p class="text-muted fs-12 mb-0">{{ $update['company_name'] }} — {{ $update['groupcom_name'] }} — {{ $update['license_type'] }}</p>
                                                            </div>
                                                            <div class="text-end ms-3">
                                                                <h6 class="mb-1 text-danger fs-13">{{ $update['activity_type'] }}</h6>
                                                                <p class="text-muted fs-12 mb-1">{{ $update['activity_date'] }}</p>
                                                                <a href="{{ route('license-list') }}?filter_id={{ $update['id'] }}" class="btn btn-sm btn-primary">View</a>
                                                            </div>
                                                        </div>
                                                    @empty
                                                        <p class="text-center text-muted fs-12">No rejected licenses.</p>
                                                    @endforelse
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        @can('view-company Responsible Person')
                            <div class="col-md-4 mt-4">
                                <div class="card shadow-sm h-100">
                                    <div class="card-header align-items-center d-flex bg-warning-subtle">
                                        <h6 class="card-title mb-0 flex-grow-1 text-uppercase fw-semibold fs-12 text-danger">
                                            Authorized License Expiry
                                        </h6>
                                        <div class="flex-shrink-0">
                                            <div class="dropdown card-header-dropdown">
                                                <a class="text-reset dropdown-btn" href="#" data-bs-toggle="dropdown"
                                                aria-haspopup="true" aria-expanded="false">
                                                    <span class="text-muted" id="filter-label">Expired Only <i
                                                            class="mdi mdi-chevron-down ms-1"></i></span>
                                                </a>
                                                <div class="dropdown-menu dropdown-menu-end">
                                                    <a class="dropdown-item" href="#" data-days="null">Expired Only</a>
                                                    @foreach ([90, 45, 30, 20, 10, 5, 1] as $day)
                                                        <a class="dropdown-item" href="#" data-days="{{ $day }}">{{ $day }} Days</a>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-body p-0">
                                        <div data-simplebar style="max-height: 200px; min-height: 120px;">
                                            <div class="p-3" id="license-alerts">
                                                @forelse($licenseAlerts as $alert)
                                                    <div class="d-flex align-items-center mb-3">
                                                        <div class="avatar-xs flex-shrink-0">
                                                            <span class="avatar-title bg-light rounded-circle material-shadow">
                                                                <i class="ri-error-warning-line {{ $alert['status'] === 'Expiring' ? 'text-warning' : 'text-danger' }} fs-18"></i>
                                                            </span>
                                                        </div>
                                                        <div class="flex-grow-1 ms-3">
                                                            <h6 class="fs-14 mb-1">{{ $alert['emp_name'] }}</h6>
                                                            <p class="text-muted fs-12 mb-0">
                                                                {{ $alert['company_name'] }} — {{ $alert['license_type'] }} — {{ $alert['license_name'] }}
                                                                @if($alert['certificate_no'] && $alert['certificate_no'] !== 'N/A') (Certificate: {{ $alert['certificate_no'] }}) @endif
                                                            </p>
                                                        </div>
                                                        <div class="flex-shrink-0 text-end">
                                                            <h6 class="mb-1 text-danger">{{ $alert['status'] }}</h6>
                                                            <p class="text-muted fs-13 mb-0">
                                                                {{ $alert['status'] === 'Expiring' ? $alert['diff_for_humans'] : $alert['valid_up_to'] }}
                                                            </p>
                                                            @if($alert['status'] === 'Expired')
                                                                <a href="{{ route('responsible') }}" class="btn btn-sm btn-primary mt-1">Assign New Person</a>
                                                            @endif
                                                        </div>
                                                    </div>
                                                @empty
                                                    <p class="text-center text-muted fs-12">No license alerts at this time.</p>
                                                @endforelse
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endcan

                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Initialize Flatpickr for date inputs
                flatpickr("#start_date_filter", {
                    dateFormat: "Y-m-d",
                    maxDate: "today"
                });
                flatpickr("#end_date_filter", {
                    dateFormat: "Y-m-d",
                    maxDate: "today"
                });

                // Function to update recent updates and upcoming expiries
                const updateDashboard = async () => {
                    const startDate = document.getElementById('start_date_filter').value;
                    const endDate = document.getElementById('end_date_filter').value;
                    const tabFilter = document.getElementById('tab-filter-label')?.dataset.tab || 'recent-added';
                    const expiryDays = document.getElementById('expiry-filter-label')?.dataset.expiryDays || '30';
                    const stateId = document.getElementById('state_filter').value;
                    const licenseTypeId = document.getElementById('license_type_filter').value;
                    const responsiblePersonId = document.getElementById('responsible_person_filter').value;

                    try {
                        const response = await fetch(`/dashboard?start_date_filter=${startDate}&end_date_filter=${endDate}&tab_filter=${tabFilter}&expiry_days=${expiryDays}&state_id=${stateId}&license_type_id=${licenseTypeId}&responsible_person_id=${responsiblePersonId}`, {
                            method: 'GET',
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json'
                            }
                        });

                        if (!response.ok) {
                            throw new Error(`HTTP error! Status: ${response.status}`);
                        }

                        const data = await response.json();
                        console.log('Received data:', data);

                        // Update Recent Updates
                        const tabs = ['recent-added', 'modifications', 'approval', 'rejection'];
                        tabs.forEach(tab => {
                            const nav = document.getElementById(`${tab}-nav`);
                            const pane = document.getElementById(tab);
                            const tabLink = document.getElementById(`${tab}-tab`);
                            if (tab === tabFilter) {  
                                nav.classList.remove('tab-hidden');
                                pane.classList.add('active', 'show');
                                tabLink.classList.add('active');
                            } else {
                                nav.classList.add('tab-hidden');
                                pane.classList.remove('active', 'show');
                                tabLink.classList.remove('active');
                            }
                        });

                        // Update Recent Added
                        const addedContainer = document.getElementById('recent-added-content');
                        addedContainer.innerHTML = '';   
                        if (data.recentAdded.length === 0) {
                            addedContainer.innerHTML = '<p class="text-center text-muted fs-12">No recent additions in this date range.</p>';
                        } else {
                            data.recentAdded.forEach(update => {
                                const html = `
                                    <div class="d-flex align-items-start mb-3">
                                        <div class="flex-grow-1">
                                            <h6 class="fs-14 mb-1">${update.license_name || 'N/A'}</h6>
                                            <p class="text-muted fs-12 mb-0">${update.company_name || 'N/A'} — ${update.groupcom_name || 'N/A'} — ${update.license_type || 'N/A'}</p>
                                        </div>
                                        <div class="text-end ms-3">
                                            <h6 class="mb-1 text-success fs-13"><strong>${update.activity_type}</strong></h6>
                                            <p class="text-muted fs-12 mb-1">${update.activity_date}</p>
                                            <a href="${window.location.origin}/license-list?filter_id=${update.id}" class="btn btn-sm btn-primary">View</a>
                                        </div>
                                    </div>`;
                                addedContainer.insertAdjacentHTML('beforeend', html);
                            });
                        }

                        // Update Recent Modified
                        const modifiedContainer = document.getElementById('modifications-content');
                        modifiedContainer.innerHTML = '';
                        if (data.recentModified.length === 0) {
                            modifiedContainer.innerHTML = '<p class="text-center text-muted fs-12">No recent modifications in this date range.</p>';
                        } else {
                            data.recentModified.forEach(update => {
                                const html = `
                                    <div class="d-flex align-items-start mb-3">
                                        <div class="flex-grow-1">
                                            <h6 class="fs-14 mb-1">${update.license_name || 'N/A'}</h6>
                                            <p class="text-muted fs-12 mb-0">${update.company_name || 'N/A'} — ${update.groupcom_name || 'N/A'} — ${update.license_type || 'N/A'}</p>
                                        </div>
                                        <div class="text-end ms-3">
                                            <h6 class="mb-1 text-info fs-13">${update.activity_type}</h6>
                                            <p class="text-muted fs-12 mb-1">${update.activity_date}</p>
                                            <a href="${window.location.origin}/license-list?filter_id=${update.id}" class="btn btn-sm btn-primary">View</a>
                                        </div>
                                    </div>`;
                                modifiedContainer.insertAdjacentHTML('beforeend', html);
                            });
                        }

                        // Update Approval
                        const approvalContainer = document.getElementById('approval-content');
                        approvalContainer.innerHTML = '';
                        if (data.approvedLicenses.length === 0) {
                            approvalContainer.innerHTML = '<p class="text-center text-muted fs-12">No approved licenses in this date range.</p>';
                        } else {
                            data.approvedLicenses.forEach(update => {
                                const html = `
                                    <div class="d-flex align-items-start mb-3">
                                        <div class="flex-grow-1">
                                            <h6 class="fs-14 mb-1">${update.license_name || 'N/A'}</h6>
                                            <p class="text-muted fs-12 mb-0">${update.company_name || 'N/A'} — ${update.groupcom_name || 'N/A'} — ${update.license_type || 'N/A'}</p>
                                        </div>
                                        <div class="text-end ms-3">
                                            <h6 class="mb-1 text-success fs-13">${update.activity_type}</h6>
                                            <p class="text-muted fs-12 mb-1">${update.activity_date}</p>
                                            <a href="${window.location.origin}/license-list?filter_id=${update.id}" class="btn btn-sm btn-primary">View</a>
                                        </div>
                                    </div>`;
                                approvalContainer.insertAdjacentHTML('beforeend', html);
                            });
                        }

                        // Update Rejection
                        const rejectionContainer = document.getElementById('rejection-content');
                        rejectionContainer.innerHTML = '';
                        if (data.rejectedLicenses.length === 0) {
                            rejectionContainer.innerHTML = '<p class="text-center text-muted fs-12">No rejected licenses in this date range.</p>';
                        } else {
                            data.rejectedLicenses.forEach(update => {
                                const html = `
                                    <div class="d-flex align-items-start mb-3">
                                        <div class="flex-grow-1">
                                            <h6 class="fs-14 mb-1">${update.license_name || 'N/A'}</h6>
                                            <p class="text-muted fs-12 mb-0">${update.company_name || 'N/A'} — ${update.groupcom_name || 'N/A'} — ${update.license_type || 'N/A'}</p>
                                        </div>
                                        <div class="text-end ms-3">
                                            <h6 class="mb-1 text-danger fs-13">${update.activity_type}</h6>
                                            <p class="text-muted fs-12 mb-1">${update.activity_date}</p>
                                            <a href="${window.location.origin}/license-list?filter_id=${update.id}" class="btn btn-sm btn-primary">View</a>
                                        </div>
                                    </div>`;
                                rejectionContainer.insertAdjacentHTML('beforeend', html);
                            });
                        }

                        // Update Upcoming Expiry Alerts
                        const expiryContainer = document.getElementById('upcoming-expiry-alerts');
                        expiryContainer.innerHTML = '';
                        if (data.upcomingExpiries.length === 0) {
                            expiryContainer.innerHTML = '<p class="text-center text-muted fs-12">No upcoming expiries at this time.</p>';
                        } else {
                            data.upcomingExpiries.forEach(expiry => {
                                const html = `
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="avatar-xs flex-shrink-0">
                                            <span class="avatar-title bg-light rounded-circle material-shadow">
                                                <i class="ri-time-line text-warning fs-18"></i>
                                            </span>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <h6 class="fs-14 mb-1">${expiry.license_name || 'N/A'}</h6>
                                            <p class="text-muted fs-12 mb-0">${expiry.company_name || 'N/A'} — ${expiry.license_type || 'N/A'} — ${expiry.state_name || 'N/A'}</p>
                                            <p class="text-muted fs-12 mb-0">Responsible: ${expiry.responsible_person_name || 'N/A'}</p>
                                        </div>
                                        <div class="flex-shrink-0 text-end">
                                            <h6 class="mb-1 text-warning">${expiry.expiry_category}</h6>
                                            <p class="text-muted fs-13 mb-0">${expiry.valid_upto}</p>
                                            <p class="text-muted fs-13 mb-0">${expiry.diff_for_humans}</p>
                                            <a href="${window.location.origin}/license-list?filter_id=${expiry.id}" class="btn btn-sm btn-primary mt-1">View</a>
                                        </div>
                                    </div>`;
                                expiryContainer.insertAdjacentHTML('beforeend', html);
                            });
                        }

                        // Update tab badges
                        const addedTabBadge = document.querySelector('#recent-added-tab .badge');
                        const modifiedTabBadge = document.querySelector('#modifications-tab .badge');
                        const approvalTabBadge = document.querySelector('#approval-tab .badge');
                        const rejectionTabBadge = document.querySelector('#rejection-tab .badge');
                        if (addedTabBadge) addedTabBadge.textContent = data.recentAdded.length;
                        if (modifiedTabBadge) modifiedTabBadge.textContent = data.recentModified.length;
                        if (approvalTabBadge) approvalTabBadge.textContent = data.approvedLicenses.length;
                        if (rejectionTabBadge) rejectionTabBadge.textContent = data.rejectedLicenses.length;

                        // Update summary alert
                        const alertContainer = document.querySelector('.alert-info');
                        alertContainer.classList.remove('alert-error');
                        alertContainer.classList.add('alert-info');
                        alertContainer.querySelector('.alert-message').textContent = 
                            `Showing ${data.recentAdded.length} Created | ${data.recentModified.length} Modified | ${data.approvedLicenses.length} Approved | ${data.rejectedLicenses.length} Rejected`;

                    } catch (error) {
                        console.error('Error fetching dashboard data:', error);
                        document.getElementById('recent-added-content').innerHTML = '<p class="text-center text-muted fs-12">Error loading recent additions. Please try again.</p>';
                        document.getElementById('modifications-content').innerHTML = '<p class="text-center text-muted fs-12">Error loading recent modifications. Please try again.</p>';
                        document.getElementById('approval-content').innerHTML = '<p class="text-center text-muted fs-12">Error loading approved licenses. Please try again.</p>';
                        document.getElementById('rejection-content').innerHTML = '<p class="text-center text-muted fs-12">Error loading rejected licenses. Please try again.</p>';
                        document.getElementById('upcoming-expiry-alerts').innerHTML = '<p class="text-center text-muted fs-12">Error loading upcoming expiries. Please try again.</p>';
                        const alertContainer = document.querySelector('.alert-info');
                        alertContainer.classList.remove('alert-info');
                        alertContainer.classList.add('alert-error');
                        alertContainer.querySelector('.alert-message').textContent = 'Error loading data. Please try again.';
                    }
                };

                // Event listeners for date inputs
                const startDateInput = document.getElementById('start_date_filter');
                const endDateInput = document.getElementById('end_date_filter');
                startDateInput.addEventListener('change', () => {
                    console.log('Start date changed:', startDateInput.value);
                    updateDashboard();
                });
                endDateInput.addEventListener('change', () => {
                    console.log('End date changed:', endDateInput.value);
                    updateDashboard();
                });

                // Event listener for tab filter dropdown
                document.querySelectorAll('.dropdown-item[data-tab]').forEach(item => {
                    item.addEventListener('click', function(e) {
                        e.preventDefault();
                        const tab = this.getAttribute('data-tab');
                        const label = this.textContent;
                        console.log('Tab filter selected:', tab);
                        document.getElementById('tab-filter-label').textContent = label + ' ';
                        document.getElementById('tab-filter-label').dataset.tab = tab;
                        updateDashboard();
                    });
                });

                // Event listener for expiry days filter
                document.querySelectorAll('.dropdown-item[data-expiry-days]').forEach(item => {
                    item.addEventListener('click', function(e) {
                        e.preventDefault();
                        const days = this.getAttribute('data-expiry-days');
                        const label = this.textContent;
                        console.log('Expiry days selected:', days);
                        document.getElementById('expiry-filter-label').textContent = label + ' ';
                        document.getElementById('expiry-filter-label').dataset.expiryDays = days;
                        updateDashboard();
                    });
                });

                // Event listeners for filter dropdowns
                document.getElementById('state_filter').addEventListener('change', () => {
                    console.log('State filter changed:', document.getElementById('state_filter').value);
                    updateDashboard();
                });
                document.getElementById('license_type_filter').addEventListener('change', () => {
                    console.log('License type filter changed:', document.getElementById('license_type_filter').value);
                    updateDashboard();
                });
                document.getElementById('responsible_person_filter').addEventListener('change', () => {
                    console.log('Responsible person filter changed:', document.getElementById('responsible_person_filter').value);
                    updateDashboard();
                });

                // Dropdown filter for License Status Alerts
                document.querySelectorAll('.dropdown-item[data-days]').forEach(item => {
                    item.addEventListener('click', async function(e) {
                        e.preventDefault();
                        const days = this.getAttribute('data-days');
                        const label = this.textContent;
                        console.log('Fetching alerts for days:', days);
                        document.getElementById('filter-label').textContent = label + ' ';

                        try {
                            const response = await fetch(`/dashboard?days=${days}`, {
                                method: 'GET',
                                headers: {
                                    'X-Requested-With': 'XMLHttpRequest',
                                    'Accept': 'application/json'
                                }
                            });

                            if (!response.ok) {
                                throw new Error(`HTTP error! Status: ${response.status}`);
                            }

                            const data = await response.json();
                            console.log('Received alerts data:', data);

                            // Update License Alerts only
                            const alertContainer = document.getElementById('license-alerts');
                            alertContainer.innerHTML = '';

                            if (data.licenseAlerts.length === 0) {
                                alertContainer.innerHTML = '<p class="text-center text-muted fs-12">No license alerts at this time.</p>';
                            } else {
                                data.licenseAlerts.forEach(alert => {
                                    const html = `
                                        <div class="d-flex align-items-center mb-3">
                                            <div class="avatar-xs flex-shrink-0">
                                                <span class="avatar-title bg-light rounded-circle material-shadow">
                                                    <i class="ri-error-warning-line ${alert.status === 'Expiring' ? 'text-warning' : 'text-danger'} fs-18"></i>
                                                </span>
                                            </div>
                                            <div class="flex-grow-1 ms-3">
                                                <h6 class="fs-14 mb-1">${alert.emp_name || 'N/A'}</h6>
                                                <p class="text-muted fs-12 mb-0">
                                                    ${alert.company_name || 'N/A'} — ${alert.license_type || 'N/A'} — ${alert.license_name || 'N/A'}
                                                    ${alert.certificate_no && alert.certificate_no !== 'N/A' ? `(Certificate: ${alert.certificate_no})` : ''}
                                                </p>
                                            </div>
                                            <div class="flex-shrink-0 text-end">
                                                <h6 class="mb-1 text-danger">${alert.status}</h6>
                                                <p class="text-muted fs-13 mb-0">
                                                    ${alert.status === 'Expiring' ? alert.diff_for_humans : alert.valid_up_to}
                                                </p>
                                                ${alert.status === 'Expired' ? `<a href="${window.location.origin}/responsible" class="btn btn-sm btn-primary mt-1">Assign New Person</a>` : ''}
                                            </div>
                                        </div>`;
                                    alertContainer.insertAdjacentHTML('beforeend', html);
                                });
                            }
                        } catch (error) {
                            console.error('Error fetching alerts:', error);
                            document.getElementById('license-alerts').innerHTML = '<p class="text-center text-muted fs-12">Error loading alerts. Please try again.</p>';
                        }
                    });
                });

                // Initial load of dashboard data
                updateDashboard();

                const triggerTabList = document.querySelectorAll('#recentUpdatesTabs .nav-link');
                triggerTabList.forEach(triggerEl => {
                    triggerEl.addEventListener('click', () => {
                        console.log(`Tab switched to: ${triggerEl.textContent}`);
                    });
                });
            });
        </script>
    @endpush
@endsection