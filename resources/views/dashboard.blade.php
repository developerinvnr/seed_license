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
                                            License Status Alerts
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

                // Dropdown filter for License Status Alerts
                document.querySelectorAll('.dropdown-item').forEach(item => {
                    item.addEventListener('click', async function(e) {
                        e.preventDefault();
                        const days = this.getAttribute('data-days');
                        const label = this.textContent;
                        console.log('Fetching alerts for days:', days); // Debug
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
                            console.log('Response data:', data); // Debug
                            const container = document.getElementById('license-alerts');
                            container.innerHTML = '';

                            if (data.licenseAlerts.length === 0) {
                                container.innerHTML = '<p class="text-center text-muted fs-12">No license alerts at this time.</p>';
                                return;
                            }

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
                                container.insertAdjacentHTML('beforeend', html);
                            });
                        } catch (error) {
                            console.error('Error fetching alerts:', error);
                            document.getElementById('license-alerts').innerHTML = '<p class="text-center text-muted fs-12">Error loading alerts. Please try again.</p>';
                        }
                    });
                });
            });
        </script>
    @endpush
@endsection