@extends('layouts.app')
@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between bg-galaxy-transparent">
                        <h4 class="mb-sm-0">{{ ucwords(str_replace('-', ' ', Request::path())) }}</h4>
                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="javascript: void(0);">Masters</a></li>
                                <li class="breadcrumb-item active">{{ ucwords(str_replace('-', ' ', Request::path())) }}
                                </li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header align-items-center d-flex">
                        <h4 class="card-title mb-0 flex-grow-1"><i class="ri-list-unordered"></i> Authorization Person
                            List</h4>
                        <div class="flex-shrink-0">
                            <button type="button" class="btn btn-primary btn-label waves-effect waves-light rounded-pill"
                                data-bs-toggle="modal" data-bs-target="#addResponsibleModal">
                                <i class="ri-add-circle-fill label-icon align-middle rounded-pill fs-16 me-2"></i> Add New
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover align-middle nowrap dt-responsive w-100">
                                <thead class="table-light text-center align-middle">
                                    <tr>
                                        <th style="width: 60px;">Sr. No.</th>
                                        <th style="min-width: 120px;">Certificate No</th>
                                        <th style="min-width: 100px;">EC Code</th>
                                        <th style="min-width: 150px;">Full Name</th>
                                        {{-- <th style="min-width: 150px;">Department</th>
                                        <th style="min-width: 150px;">Designation</th> --}}
                                        {{-- <th style="min-width: 180px;">Company Name</th>
                                        <th style="min-width: 150px;">Authorised By</th> --}}
                                        {{-- <th style="min-width: 130px;">Effective From</th> --}}
                                        <th style="min-width: 130px;">Valid Up To</th>
                                        <th style="min-width: 100px;">Status</th>
                                        <th style="min-width: 120px;">Document</th>
                                        <th style="min-width: 130px;">Certificate</th>
                                        <th style="min-width: 120px;">Action</th>
                                    </tr>
                                </thead>
                            <tbody>
                                @forelse ($responsibles as $index => $responsible)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $responsible->certificate_no ?? '-' }}</td>
                                        <td>{{ $responsible->emp_code ?? 'N/A' }}</td>
                                        <td>{{ $responsible->employee->emp_name ?? 'N/A' }}</td>
                                        {{-- <td>{{ $responsible->employee->emp_department ?? 'N/A' }}</td>
                                        <td>{{ $responsible->employee->emp_designation ?? 'N/A' }}</td> --}}
                                        {{-- <td>{{ $responsible->company->company_name ?? 'N/A' }}</td> --}}
                                        {{-- <td>{{ $responsible->Authorisation_Issued_By ?? '-' }}</td> --}}
                                        {{-- <td>{{ $responsible->Effective_From ? \Carbon\Carbon::parse($responsible->Effective_From)->format('d-m-Y') : '-' }}</td> --}}
                                        <td>
                                            @if ($responsible->Valid_up_to === 'Lifetime')
                                                Lifetime
                                            @elseif($responsible->Valid_up_to)
                                                {{ \Carbon\Carbon::parse($responsible->Valid_up_to)->format('d-m-Y') }}
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td>
                                            @php
                                                $statusClass = match ($responsible->Authorization_status) {
                                                    'Active' => 'badge bg-success',
                                                    'Expired' => 'badge bg-danger',
                                                    'Revoked' => 'badge bg-warning',
                                                    default => 'badge bg-secondary',
                                                };
                                            @endphp
                                            <span
                                                class="{{ $statusClass }}">{{ $responsible->Authorization_status ?? '-' }}</span>
                                        </td>
                                        <td>
                                            @if ($responsible->auth_doc)
                                                <a href="{{ Storage::url($responsible->auth_doc) }}" target="_blank"
                                                    class="text-primary fs-8">
                                                    <i class="ri-file-pdf-line">View</i> 
                                                </a>
                                            @else
                                              <i class="ri-file-close-line text-muted fs-8" title="No File"></i>
                                            @endif
                                        </td>
                                        <td>
                                            @if($responsible->auth_certificate)
                                                <a href="#"  class="text-info edit-certificate fs-8" 
                                                    data-bs-toggle="modal" data-bs-target="#certificateModal"
                                                    data-id="{{ $responsible->id }}"
                                                    data-certificate="{{ $responsible->auth_certificate }}"  title="Update Certificate">
                                                    <i class="ri-edit-line"></i> Update
                                                </a>                                                
                                            @else
                                                <a href="#"  class="text-info edit-certificate fs-8"
                                                    data-bs-toggle="modal" data-bs-target="#certificateModal"
                                                    data-id="{{ $responsible->id }}"  title="Add Draft">
                                                    <i class="ri-file-text-line"></i> Draft
                                                </a>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="javascript:void(0);"  class="text-info view-responsible"
                                                data-bs-toggle="modal" data-bs-target="#viewResponsibleModal"  title="View"
                                                data-id="{{ $responsible->id }}"
                                                data-certificate_no="{{ $responsible->certificate_no }}"
                                                data-core_company_id="{{ $responsible->core_company_id }}"
                                                data-core_employee_id="{{ $responsible->core_employee_id }}"
                                                data-emp_code="{{ $responsible->emp_code }}"
                                                data-authorised_through="{{ $responsible->Authorised_Through }}"
                                                data-scope_of_authorisation="{{ $responsible->Scope_of_Authorisation }}"
                                                data-authorisation_issued_by="{{ $responsible->Authorisation_Issued_By }}"
                                                data-issue_date="{{ $responsible->Issue_Date }}"
                                                data-effective_from="{{ $responsible->Effective_From }}"
                                                data-valid_up_to="{{ $responsible->Valid_up_to }}"
                                                data-authorization_status="{{ $responsible->Authorization_status }}"
                                                data-revocation_date="{{ $responsible->Revocation_Date }}"
                                                data-revocation_doc="{{ $responsible->revocation_doc ? Storage::url($responsible->revocation_doc) : '' }}"
                                                data-auth_doc="{{ $responsible->auth_doc ? Storage::url($responsible->auth_doc) : '' }}"
                                                data-purpose_details="{{ json_encode($responsible->purpose_details ? explode(',', $responsible->purpose_details) : []) }}"
                                                data-licenses="{{ json_encode(
                                                    $responsible->licenseDetails->map(function ($license) {
                                                        return [
                                                            'license_type_id' => $license->license_type_id,
                                                            'license_name_id' => $license->license_name_id,
                                                            'license_type' => $license->licenseType->license_type ?? 'N/A',
                                                            'license_name' => $license->licenseName->license_name ?? 'N/A',
                                                        ];
                                                    })->toArray(),
                                                ) }}"
                                                data-history="{{ json_encode(
                                                    $responsible->activities->map(function ($activity) {
                                                        return [
                                                            'created_at' => \Carbon\Carbon::parse($activity->created_at)->format('d-m-Y H:i:s'),
                                                            'event' => $activity->event,
                                                            'description' => $activity->description,
                                                            'causer' => $activity->causer ? $activity->causer->name : 'System',
                                                        ];
                                                    })->toArray()
                                                ) }}"
                                                data-employee="{{ json_encode([
                                                    'emp_name' => $responsible->employee->emp_name ?? 'N/A',
                                                    'emp_status' => $responsible->employee->emp_status ?? 'N/A',
                                                    'emp_email' => $responsible->employee->emp_email ?? 'N/A',
                                                    'emp_contact' => $responsible->employee->emp_contact ?? 'N/A',
                                                    'emp_department' => $responsible->employee->emp_department ?? 'N/A',
                                                    'emp_designation' => $responsible->employee->emp_designation ?? 'N/A',
                                                    'emp_state' => $responsible->employee->emp_state ?? 'N/A',
                                                    'emp_city' => $responsible->employee->emp_city ?? 'N/A',
                                                    'emp_vertical' => $responsible->employee->emp_vertical ?? 'N/A',
                                                    'emp_region' => $responsible->employee->emp_region ?? 'N/A',
                                                    'emp_zone' => $responsible->employee->emp_zone ?? 'N/A',
                                                    'emp_bu' => $responsible->employee->emp_bu ?? 'N/A',
                                                    'emp_territory' => $responsible->employee->emp_territory ?? 'N/A',
                                                    'emp_doj' => $responsible->employee->emp_doj ?? 'N/A',
                                                    'last_date' => $responsible->employee->last_date ?? 'N/A',
                                                ]) }}"
                                                data-company_name="{{ $responsible->company->company_name ?? 'N/A' }}">
                                                <i class="ri-eye-line"></i>
                                            </a>

                                            <a href="javascript:void(0);"  class="text-warning edit-responsible ms-2"
                                                data-bs-toggle="modal" data-bs-target="#editResponsibleModal"   title="Edit" data-id="{{ $responsible->id }}"
                                                data-certificate_no="{{ $responsible->certificate_no }}"
                                                data-core_company_id="{{ $responsible->core_company_id }}"
                                                data-core_employee_id="{{ $responsible->core_employee_id }}"
                                                data-emp_code="{{ $responsible->emp_code }}"
                                                data-authorised_through="{{ $responsible->Authorised_Through }}"
                                                data-scope_of_authorisation="{{ $responsible->Scope_of_Authorisation }}"
                                                data-authorisation_issued_by="{{ $responsible->Authorisation_Issued_By }}"
                                                data-issue_date="{{ $responsible->Issue_Date }}"
                                                data-effective_from="{{ $responsible->Effective_From }}"
                                                data-valid_up_to="{{ $responsible->Valid_up_to }}"
                                                data-authorization_status="{{ $responsible->Authorization_status }}"
                                                data-Revocation_Date="{{ $responsible->Revocation_Date }}"
                                                data-revocation_doc="{{ $responsible->revocation_doc ? Storage::url($responsible->revocation_doc) : '' }}"
                                                data-auth_doc="{{ $responsible->auth_doc ? Storage::url($responsible->auth_doc) : '' }}"
                                                data-purpose_details="{{ json_encode($responsible->purpose_details ? explode(',', $responsible->purpose_details) : []) }}"
                                                data-licenses="{{ json_encode(
                                                    $responsible->licenseDetails->map(function ($license) {
                                                            return [
                                                                'license_type_id' => $license->license_type_id,
                                                                'license_name_id' => $license->license_name_id,
                                                            ];
                                                        })->toArray(),
                                                ) }}">
                                                <i class="ri-edit-line"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="12" class="text-center text-muted">No records found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <!-- Add Responsible Modal -->
        <div class="modal fade" id="addResponsibleModal" tabindex="-1" aria-labelledby="addResponsibleModalLabel"
            aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog modal-lg modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header  text-black">
                        <h5 class="modal-title" id="addResponsibleModalLabel">Add New Responsible Person</h5>
                        <button type="button" class="btn-close btn-close-black" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <form id="addResponsibleForm" method="POST" action="{{ route('responsible.store') }}"
                        enctype="multipart/form-data">
                        @csrf
                        <div class="modal-body" style="max-height: 60vh; overflow-y: auto;">
                            <div id="formErrors" class="alert alert-danger d-none" role="alert"></div>
                            <h6 class="fw-bold mb-3 text-primary border-bottom pb-2">Employee Details</h6>
                            <div class="row g-4 mb-4">
                                <div class="col-md-3">
                                    <label for="company_id" class="form-label fw-medium">Company<span
                                            class="text-danger">*</span></label>
                                    <select name="company_id" id="company_id" class="form-select form-select-sm" required>
                                        <option value="">Select Company</option>
                                        @foreach ($core_companies as $company)
                                            <option value="{{ $company->id }}"
                                                {{ old('company_id') == $company->id ? 'selected' : '' }}>
                                                {{ $company->company_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('company_id')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-3 groupcom-section" style="display: none;">
                                    <label for="groupcom_company_id" class="form-label fw-medium">Groupcom Company</label>
                                    <select name="groupcom_company_id" id="groupcom_company_id" class="form-select form-select-sm">
                                        <option value="">Select Groupcom Company</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label for="emp_name" class="form-label fw-medium">Name of Person <span
                                            class="text-danger">*</span></label>
                                    <select name="emp_name" id="emp_name" class="form-select" required>
                                        <option value="">Select Employee</option>
                                    </select>
                                    @error('emp_name')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-3">
                                    <label for="emp_code" class="form-label fw-medium">Employee Code</label>
                                    <input type="text" name="emp_code" id="emp_code" class="form-control form-control-sm"
                                        value="{{ old('emp_code') }}" readonly>
                                    @error('emp_code')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-3">
                                    <label for="emp_status" class="form-label fw-medium">Employee Status</label>
                                    <input type="text" name="emp_status" id="emp_status" class="form-control form-control-sm"
                                        value="{{ old('emp_status') }}" readonly>
                                </div>
                                <div class="col-md-3">
                                    <label for="emp_email" class="form-label fw-medium">Email</label>
                                    <input type="email" name="emp_email" id="emp_email" class="form-control form-control-sm"
                                        value="{{ old('emp_email') }}" readonly>
                                </div>
                                <div class="col-md-3">
                                    <label for="emp_contact" class="form-label fw-medium">Contact</label>
                                    <input type="text" name="emp_contact" id="emp_contact" class="form-control form-control-sm"
                                        value="{{ old('emp_contact') }}" readonly>
                                </div>
                                <div class="col-md-3">
                                    <label for="emp_department" class="form-label fw-medium">Department</label>
                                    <input type="text" name="emp_department" id="emp_department" class="form-control form-control-sm"
                                        value="{{ old('emp_department') }}" readonly>
                                </div>
                                <div class="col-md-3">
                                    <label for="emp_designation" class="form-label fw-medium">Designation</label>
                                    <input type="text" name="emp_designation" id="emp_designation"
                                        class="form-control form-control-sm" value="{{ old('emp_designation') }}" readonly>
                                </div>
                                <div class="col-md-3">
                                    <label for="emp_state" class="form-label fw-medium">State</label>
                                    <input type="text" name="emp_state" id="emp_state" class="form-control form-control-sm"
                                        value="{{ old('emp_state') }}" readonly>
                                </div>
                                <div class="col-md-3">
                                    <label for="emp_city" class="form-label fw-medium">City</label>
                                    <input type="text" name="emp_city" id="emp_city" class="form-control form-control-sm"
                                        value="{{ old('emp_city') }}" readonly>
                                </div>
                                <div class="col-md-3">
                                    <label for="emp_vertical" class="form-label fw-medium">Vertical</label>
                                    <input type="text" name="emp_vertical" id="emp_vertical" class="form-control form-control-sm"
                                        value="{{ old('emp_vertical') }}" readonly>
                                </div>
                                <div class="col-md-3">
                                    <label for="emp_region" class="form-label fw-medium">Region</label>
                                    <input type="text" name="emp_region" id="emp_region" class="form-control form-control-sm"
                                        value="{{ old('emp_region') }}" readonly>
                                </div>
                                <div class="col-md-3">
                                    <label for="emp_zone" class="form-label fw-medium">Zone</label>
                                    <input type="text" name="emp_zone" id="emp_zone" class="form-control form-control-sm"
                                        value="{{ old('emp_zone') }}" readonly>
                                </div>
                                <div class="col-md-3">
                                    <label for="emp_bu" class="form-label fw-medium">Business Unit</label>
                                    <input type="text" name="emp_bu" id="emp_bu" class="form-control form-control-sm"
                                        value="{{ old('emp_bu') }}" readonly>
                                </div>
                                <div class="col-md-3">
                                    <label for="emp_territory" class="form-label fw-medium">Territory</label>
                                    <input type="text" name="emp_territory" id="emp_territory" class="form-control form-control-sm"
                                        value="{{ old('emp_territory') }}" readonly>
                                </div>
                                <div class="col-md-3">
                                    <label for="emp_doj" class="form-label fw-medium">Join Date</label>
                                    <input type="date" name="emp_doj" id="emp_doj" class="form-control form-control-sm"
                                        value="{{ old('emp_doj') }}" readonly>
                                </div>
                                <div class="col-md-3">
                                    <label for="last_date" class="form-label fw-medium">Separation Date</label>
                                    <input type="date" name="last_date" id="last_date" class="form-control form-control-sm"
                                        value="{{ old('last_date') }}">
                                    @error('last_date')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <h6 class="fw-bold mb-3 text-primary border-bottom pb-2">Authorization Details</h6>
                            <div class="row g-3 mb-4">
                                <div class="col-md-3">
                                    <label for="Authorised_Through" class="form-label fw-medium">Authorized
                                        Through</label>
                                    <select name="Authorised_Through" id="Authorised_Through" class="form-select form-select-sm">
                                        <option value="">Select</option>
                                        <option value="BOR">Board Resolution</option>
                                        <option value="AUTHC">Authorisation Certificate</option>
                                        <option value="POA">Power of Attorney (POA)</option>
                                    </select>
                                    @error('Authorised_Through')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-3">
                                    <label for="Scope_of_Authorisation" class="form-label fw-medium">Scope of
                                        Authorization</label>
                                    <input type="text" name="Scope_of_Authorisation" id="Scope_of_Authorisation"
                                        class="form-control form-control-sm" value="{{ old('Scope_of_Authorisation') }}"
                                        placeholder="Enter scope of authorization">
                                    @error('Scope_of_Authorisation')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-3">
                                    <label for="Authorisation_Issued_By" class="form-label fw-medium">Authorization Issued
                                        By</label>
                                    <input type="text" name="Authorisation_Issued_By" id="Authorisation_Issued_By"
                                        class="form-control form-control-sm" value="{{ old('Authorisation_Issued_By') }}"
                                        placeholder="Enter issuing authority">
                                    @error('Authorisation_Issued_By')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-3">
                                    <label for="Authorised_Purpose" class="form-label fw-medium">Authorized
                                        Purpose</label>
                                    <select name="Authorised_Purpose[]" id="Authorised_Purpose"
                                        class="form-select form-select-sm select2-multi" multiple>
                                        <option value="">Select Purpose</option>
                                        @foreach ($purposes as $purpose)
                                            <option value="{{ $purpose->id }}">{{ $purpose->name }}</option>
                                        @endforeach
                                    </select>
                                    <small class="form-text text-muted">Select one or more purposes</small>
                                    @error('Authorised_Purpose')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row g-3 mb-4" id="dynamic_field_container">
                                <div class="col-md-12" id="add_license_row" style="display: none;">
                                    <a href="#" class="text-primary" id="addLicenseRowIcon">
                                        <i class="ri-add-line align-middle fs-5"></i>
                                    </a>
                                </div>
                            </div>

                            <h6 class="fw-bold mb-3 text-primary border-bottom pb-2">Authorization Certificate Details</h6>
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label for="Issue_Date" class="form-label fw-medium">Issue Date</label>
                                    <input type="date" name="Issue_Date" id="Issue_Date" class="form-control form-control-sm"
                                        value="{{ old('Issue_Date') }}">
                                    @error('Issue_Date')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4">
                                    <label for="Effective_From" class="form-label fw-medium">Effective From</label>
                                    <input type="date" name="Effective_From" id="Effective_From" class="form-control form-control-sm"
                                        value="{{ old('Effective_From') }}">
                                    @error('Effective_From')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4">
                                    <div class="d-flex align-items-center mb-1">
                                        <label class="form-label fw-medium me-3 mb-0">Valid Up to</label>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="valid_up_to_type"
                                                id="valid_up_to_date" value="date" checked>
                                            <label class="form-check-label" for="valid_up_to_date">Date</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="valid_up_to_type"
                                                id="valid_up_to_lifetime" value="lifetime">
                                            <label class="form-check-label" for="valid_up_to_lifetime">Lifetime</label>
                                        </div>
                                    </div>
                                    <div id="valid_up_to_date_container">
                                        <input type="date" name="valid_up_to_date" id="Valid_up_to"
                                            class="form-control form-control-sm">
                                    </div>
                                    <div id="valid_up_to_lifetime_container" style="display: none;">
                                        <input type="hidden" name="valid_up_to_lifetime" id="Valid_up_to_lifetime_input"
                                            value="Lifetime">
                                        <input type="text" class="form-control form-control-sm" value="Lifetime" readonly>
                                    </div>

                                    @error('Valid_up_to')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4">
                                    <label for="Authorization_status" class="form-label fw-medium">Authorization
                                        Status</label>
                                    <select name="Authorization_status" id="Authorization_status" class="form-select form-select-sm"
                                        disabled>
                                        <option value="">Select</option>
                                        <option value="Active">Active</option>
                                        <option value="Expired">Expired</option>
                                        <option value="Revoked">Revoked</option>
                                    </select>
                                    <small class="form-text text-muted">Automatically set based on Valid Up to date</small>
                                    @error('Authorization_status')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4">
                                    <label for="auth_doc" class="form-label fw-medium">Document View / Draft</label>
                                    <input type="file" name="auth_doc" id="auth_doc" class="form-control"
                                        accept=".pdf,.doc,.docx">
                                    @error('auth_doc')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Accepted formats: PDF, DOC, DOCX</small>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">
                                <i class="ri-check-double-line align-middle me-1"></i> Submit
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Edit Responsible Modal -->
        <div class="modal fade" id="editResponsibleModal" tabindex="-1" aria-labelledby="editResponsibleModalLabel"
            aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog modal-lg modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header text-black">
                        <h5 class="modal-title" id="editResponsibleModalLabel">Edit Responsible Person</h5>
                        <button type="button" class="btn-close btn-close-black" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <form id="editResponsibleForm" method="POST" action="{{ route('responsible.update', 0) }}"
                        enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="modal-body" style="max-height: 60vh; overflow-y: auto;">
                            <div id="editFormErrors" class="alert alert-danger d-none" role="alert"></div>
                            <input type="hidden" name="id" id="edit_id">
                            <h6 class="fw-bold mb-3 text-primary border-bottom pb-2">Employee Details</h6>
                            <div class="row g-4 mb-4">
                                <div class="col-md-3">
                                    <label for="edit_company_id" class="form-label fw-medium">Company <span
                                            class="text-danger">*</span></label>
                                    <select name="company_id" id="edit_company_id" class="form-select form-select-sm" required>
                                        <option value="">Select Company</option>
                                        @foreach ($core_companies as $company)
                                            <option value="{{ $company->id }}">{{ $company->company_name }}</option>
                                        @endforeach
                                    </select>
                                    @error('company_id')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="col-md-3 groupcom-section" style="display: none;">
                                    <label for="edit_groupcom_company_id" class="form-label fw-medium">Select Groupcom Company</label>
                                    <select name="groupcom_company_id" id="edit_groupcom_company_id" class="form-select form-select-sm">
                                        <option value="">Select Groupcom Company</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label for="edit_emp_name" class="form-label fw-medium">Name of Person <span
                                            class="text-danger">*</span></label>
                                    <select name="emp_name" id="edit_emp_name" class="form-select form-select-sm" required>
                                        <option value="">Select Employee</option>
                                    </select>
                                    @error('emp_name')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-3">
                                    <label for="edit_emp_code" class="form-label fw-medium">Employee Code</label>
                                    <input type="text" name="emp_code" id="edit_emp_code" class="form-control form-control-sm"
                                        readonly>
                                </div>
                                <div class="col-md-3">
                                    <label for="edit_emp_email" class="form-label fw-medium">Email</label>
                                    <input type="email" name="emp_email" id="edit_emp_email" class="form-control form-control-sm"
                                        readonly>
                                </div>
                                <div class="col-md-3">
                                    <label for="edit_emp_contact" class="form-label fw-medium">Contact</label>
                                    <input type="text" name="emp_contact" id="edit_emp_contact" class="form-control form-control-sm"
                                        readonly>
                                </div>
                                <div class="col-md-3">
                                    <label for="edit_emp_department" class="form-label fw-medium">Department</label>
                                    <input type="text" name="emp_department" id="edit_emp_department"
                                        class="form-control form-control-sm" readonly>
                                </div>
                                <div class="col-md-3">
                                    <label for="edit_emp_designation" class="form-label fw-medium">Designation</label>
                                    <input type="text" name="emp_designation" id="edit_emp_designation"
                                        class="form-control form-control-sm" readonly>
                                </div>
                                <div class="col-md-3">
                                    <label for="edit_emp_state" class="form-label fw-medium">State</label>
                                    <input type="text" name="emp_state" id="edit_emp_state" class="form-control form-control-sm"
                                        readonly>
                                </div>
                                <div class="col-md-3">
                                    <label for="edit_emp_city" class="form-label fw-medium">City</label>
                                    <input type="text" name="emp_city" id="edit_emp_city" class="form-control form-control-sm"
                                        readonly>
                                </div>
                                <div class="col-md-3">
                                    <label for="edit_emp_vertical" class="form-label fw-medium">Vertical</label>
                                    <input type="text" name="emp_vertical" id="edit_emp_vertical"
                                        class="form-control form-control-sm" readonly>
                                </div>
                                <div class="col-md-3">
                                    <label for="edit_emp_region" class="form-label fw-medium">Region</label>
                                    <input type="text" name="emp_region" id="edit_emp_region" class="form-control form-control-sm"
                                        readonly>
                                </div>
                                <div class="col-md-3">
                                    <label for="edit_emp_zone" class="form-label fw-medium">Zone</label>
                                    <input type="text" name="emp_zone" id="edit_emp_zone" class="form-control form-control-sm"
                                        readonly>
                                </div>
                                <div class="col-md-3">
                                    <label for="edit_emp_bu" class="form-label fw-medium">Business Unit</label>
                                    <input type="text" name="emp_bu" id="edit_emp_bu" class="form-control form-control-sm" readonly>
                                </div>
                                <div class="col-md-3">
                                    <label for="edit_emp_territory" class="form-label fw-medium">Territory</label>
                                    <input type="text" name="emp_territory" id="edit_emp_territory"
                                        class="form-control form-control-sm" readonly>
                                </div>
                                <div class="col-md-3">
                                    <label for="edit_emp_doj" class="form-label fw-medium">Join Date</label>
                                    <input type="date" name="emp_doj" id="edit_emp_doj" class="form-control form-control-sm"
                                        readonly>
                                </div>
                                <div class="col-md-3">
                                    <label for="edit_last_date" class="form-label fw-medium">Separation Date</label>
                                    <input type="date" name="last_date" id="edit_last_date" class="form-control form-control-sm">
                                    @error('last_date')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <h6 class="fw-bold mb-3 text-primary border-bottom pb-2">Authorization Details</h6>
                            <div class="row g-3 mb-4">
                                <div class="col-md-3">
                                    <label for="edit_Authorised_Through" class="form-label fw-medium">Authorized
                                        Through</label>
                                    <select name="Authorised_Through" id="edit_Authorised_Through" class="form-select form-select-sm">
                                        <option value="">Select</option>
                                        <option value="BOR">Board Resolution</option>
                                        <option value="AUTHC">Authorisation Certificate</option>
                                        <option value="POA">Power of Attorney (POA)</option>
                                    </select>
                                    @error('Authorised_Through')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-3">
                                    <label for="edit_Scope_of_Authorisation" class="form-label fw-medium">Scope of
                                        Authorization</label>
                                    <input type="text" name="Scope_of_Authorisation" id="edit_Scope_of_Authorisation"
                                        class="form-control form-control-sm" placeholder="Enter scope of authorization">
                                    @error('Scope_of_Authorisation')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-3">
                                    <label for="edit_Authorisation_Issued_By" class="form-label fw-medium">Authorization
                                        Issued By</label>
                                    <input type="text" name="Authorisation_Issued_By"
                                        id="edit_Authorisation_Issued_By" class="form-control form-control-sm"
                                        placeholder="Enter issuing authority">
                                    @error('Authorisation_Issued_By')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-3">
                                    <label for="edit_Authorised_Purpose" class="form-label fw-medium">Authorized
                                        Purpose</label>
                                    <select name="Authorised_Purpose[]" id="edit_Authorised_Purpose"
                                        class="form-select select2-multi" multiple>
                                        <option value="">Select Purpose</option>
                                        @foreach ($purposes as $purpose)
                                            <option value="{{ $purpose->id }}">{{ $purpose->name }}</option>
                                        @endforeach
                                    </select>
                                    <small class="form-text text-muted">Select one or more purposes</small>
                                </div>
                            </div>

                            <div class="row g-3 mb-4" id="edit_dynamic_field_container">
                                <div class="col-md-12" id="edit_add_license_row" style="display: none;">
                                    <a href="#" class="text-primary" id="editAddLicenseRowIcon">
                                        <i class="ri-add-line align-middle fs-5"></i>
                                    </a>
                                </div>
                            </div>

                            <h6 class="fw-bold mb-3 text-primary border-bottom pb-2">Authorization Certificate Details</h6>
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label for="edit_Issue_Date" class="form-label fw-medium">Issue Date</label>
                                    <input type="date" name="Issue_Date" id="edit_Issue_Date" class="form-control form-control-sm">
                                    @error('Issue_Date')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="col-md-4">
                                    <label for="edit_Effective_From" class="form-label fw-medium">Effective From</label>
                                    <input type="date" name="Effective_From" id="edit_Effective_From"
                                        class="form-control form-control-sm">
                                    @error('Effective_From')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-4">
                                    <div class="d-flex align-items-center mb-1">
                                        <label class="form-label fw-medium me-3 mb-0">Valid Up to</label>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="valid_up_to_type"
                                                id="edit_valid_up_to_date" value="date">
                                            <label class="form-check-label" for="edit_valid_up_to_date">Date</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="valid_up_to_type"
                                                id="edit_valid_up_to_lifetime" value="lifetime">
                                            <label class="form-check-label"
                                                for="edit_valid_up_to_lifetime">Lifetime</label>
                                        </div>
                                    </div>
                                    <div id="edit_valid_up_to_date_container">
                                        <input type="date" name="valid_up_to_date" id="edit_Valid_up_to"
                                            class="form-control form-control-sm">
                                    </div>
                                    <div id="edit_valid_up_to_lifetime_container" style="display: none;">
                                        <input type="hidden" name="valid_up_to_lifetime"
                                            id="edit_Valid_up_to_lifetime_input" value="Lifetime">
                                        <input type="text" class="form-control form-control-sm" value="Lifetime" readonly>
                                    </div>
                                    @error('Valid_up_to')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                              
                                <div class="col-md-4" >
                                    <div class="d-flex align-items-center mb-1">
                                        <label class="form-label fw-medium me-3 mb-0">Authorization Status</label>
                                        <div class="d-flex align-items-center">
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="authorization_status_type" id="edit_status_revoked" value="revoked">
                                                <label class="form-check-label" for="edit_status_revoked">Revoked</label>
                                            </div>
                                        </div>
                                    </div>
                                    <input type="text" name="Authorization_status" id="edit_Authorization_status" class="form-control form-control-sm" readonly>
                                    <small class="form-text text-muted">Automatically set based on Valid Up to date or Revoke selection</small>
                                    @error('Authorization_status')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-4" id="edit_revocation_date_container" style="display: none;">
                                    <label for="edit_Revocation_Date" class="form-label fw-medium">Revocation Date <span class="text-danger" id="edit_revocation_date_required" style="display: none;">*</span></label>
                                    <input type="date" name="Revocation_Date" id="edit_Revocation_Date" class="form-control form-control-sm">
                                    @error('Revocation_Date')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                              
                                <div class="col-md-4" id="edit_revocation_doc_container" style="display: none;">
                                        <label for="edit_revocation_doc" class="form-label fw-medium">Revocation Document <span class="text-danger" id="edit_revocation_doc_required" style="display: none;">*</span></label>
                                        <div id="edit_revocation_doc_input_container">
                                            <input type="file" name="revocation_doc" id="edit_revocation_doc" class="form-control form-control-sm" accept=".pdf,.doc,.docx">
                                        </div>
                                        <small class="form-text text-muted">Accepted formats: PDF, DOC, DOCX</small>
                                        <div id="existing_revocation_doc" class="mt-2"></div>
                                        <input type="hidden" name="delete_revocation_doc" id="delete_revocation_doc" value="0">
                                        @error('revocation_doc')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                </div>
                               
                                <div class="col-md-4">
                                        <label for="edit_auth_doc" class="form-label fw-medium">Document View / Draft</label>
                                        <div id="edit_auth_doc_input_container">
                                            <input type="file" name="auth_doc" id="edit_auth_doc" class="form-control form-control-sm" accept=".pdf,.doc,.docx">
                                        </div>
                                        <small class="form-text text-muted">Accepted formats: PDF, DOC, DOCX</small>
                                        <div id="existing_doc" class="mt-2"></div>
                                        <input type="hidden" name="delete_auth_doc" id="delete_auth_doc" value="0">
                                        @error('auth_doc')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">
                                <i class="ri-check-double-line align-middle me-1"></i> Update
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

       <!-- Certificate Modal -->
        <div class="modal fade" id="certificateModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header text-black">
                        <h5 class="modal-title">Certificate</h5>
                        <button type="button" class="btn-close btn-close-black" data-bs-dismiss="modal"></button>
                    </div>
                    <form id="certificateForm" method="POST" enctype="multipart/form-data">
                        @csrf @method('PUT')
                        <div class="modal-body">
                            <input type="hidden" name="id" id="certificate_id">
                            <textarea id="certificate_editor" name="auth_certificate"></textarea>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary"
                                data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary" id="certificateSaveBtn">
                                <i class="ri-save-line me-1"></i> Save
                            </button>
                            <button type="submit" class="btn btn-primary" id="certificateUpdateBtn" style="display:none;">
                                <i class="ri-save-line me-1"></i> Update
                            </button>
                          
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- <!-- View Responsible Modal -->
        <div class="modal fade" id="viewResponsibleModal" tabindex="-1" aria-labelledby="viewResponsibleModalLabel"
            aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog modal-xl modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header text-black">
                        <h5 class="modal-title" id="viewResponsibleModalLabel">Responsible Person Details</h5>
                        <button type="button" class="btn-close btn-close-black" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body" style="max-height: 60vh; overflow-y: auto;">
                        <h6 class="fw-bold mb-3 text-primary border-bottom pb-2">Employee Details</h6>
                        <div class="row g-3 mb-4">
                            <div class="col-md-4">
                                <label class="form-label fw-medium">Company</label>
                                <p id="view_company_name" class="mb-0"></p>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-medium">Name of Person</label>
                                <p id="view_emp_name" class="mb-0"></p>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-medium">Employee Code</label>
                                <p id="view_emp_code" class="mb-0"></p>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-medium">Employee Status</label>
                                <p id="view_emp_status" class="mb-0"></p>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-medium">Email</label>
                                <p id="view_emp_email" class="mb-0"></p>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-medium">Contact</label>
                                <p id="view_emp_contact" class="mb-0"></p>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-medium">Department</label>
                                <p id="view_emp_department" class="mb-0"></p>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-medium">Designation</label>
                                <p id="view_emp_designation" class="mb-0"></p>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-medium">State</label>
                                <p id="view_emp_state" class="mb-0"></p>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-medium">City</label>
                                <p id="view_emp_city" class="mb-0"></p>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-medium">Vertical</label>
                                <p id="view_emp_vertical" class="mb-0"></p>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-medium">Region</label>
                                <p id="view_emp_region" class="mb-0"></p>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-medium">Zone</label>
                                <p id="view_emp_zone" class="mb-0"></p>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-medium">Business Unit</label>
                                <p id="view_emp_bu" class="mb-0"></p>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-medium">Territory</label>
                                <p id="view_emp_territory" class="mb-0"></p>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-medium">Join Date</label>
                                <p id="view_emp_doj" class="mb-0"></p>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-medium">Separation Date</label>
                                <p id="view_last_date" class="mb-0"></p>
                            </div>
                        </div>

                        <h6 class="fw-bold mb-3 text-primary border-bottom pb-2">Authorization Details</h6>
                        <div class="row g-3 mb-4">
                            <div class="col-md-4">
                                <label class="form-label fw-medium">Authorized Through</label>
                                <p id="view_Authorised_Through" class="mb-0"></p>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-medium">Scope of Authorization</label>
                                <p id="view_Scope_of_Authorisation" class="mb-0"></p>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-medium">Authorization Issued By</label>
                                <p id="view_Authorisation_Issued_By" class="mb-0"></p>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-medium">Authorized Purpose</label>
                                <p id="view_Authorised_Purpose" class="mb-0"></p>
                            </div>
                            <div class="col-md-12" id="view_dynamic_field_container"></div>
                        </div>

                        <h6 class="fw-bold mb-3 text-primary border-bottom pb-2">Authorization Certificate Details</h6>
                        <div class="row g-3 mb-4">
                            <div class="col-md-4">
                                <label class="form-label fw-medium">Issue Date</label>
                                <p id="view_Issue_Date" class="mb-0"></p>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-medium">Effective From</label>
                                <p id="view_Effective_From" class="mb-0"></p>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-medium">Valid Up to</label>
                                <p id="view_Valid_up_to" class="mb-0"></p>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-medium">Authorization Status</label>
                                <p id="view_Authorization_status" class="mb-0"></p>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-medium">Document View / Draft</label>
                                <p id="view_auth_doc" class="mb-0"></p>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-medium">Revocation Date</label>
                                <p id="view_Revocation_Date" class="mb-0"></p>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-medium">Revocation Document</label>
                                <p id="view_revocation_doc" class="mb-0"></p>
                            </div>
                        </div>

                        <h6 class="fw-bold mb-3 text-primary border-bottom pb-2">Change History</h6>
                        <div class="row g-3">
                            <div class="col-md-12">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Event</th>
                                            <th>Changes</th>
                                            <th>User</th>
                                        </tr>
                                    </thead>
                                    <tbody id="view_change_history"></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div> --}}

        <!-- View Responsible Modal -->
        <div class="modal fade" id="viewResponsibleModal" tabindex="-1" aria-labelledby="viewResponsibleModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog modal-lg modal-dialog-scrollable">
                <div class="modal-content rounded-3 shadow-lg">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5 fw-bold" id="viewResponsibleModalLabel">Responsible Person Details</h1>
                        <button type="button" class="btn-close btn-close-black" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4" style="max-height: 70vh; overflow-y: auto;">
                        <!-- Employee Details Section -->
                        <div class="card mb-4 shadow-sm">
                            <div class="card-header bg-light">
                                <h5 class="card-title mb-0 text-primary fw-bold">Employee Details</h5>
                            </div>
                            <div class="card-body">
                                <div class="row g-4">
                                    <div class="col-md-3">
                                        <label class="form-label fw-semibold text-muted">Company</label>
                                        <p id="view_company_name" class="mb-0 fs-6"></p>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label fw-semibold text-muted">Name of Person</label>
                                        <p id="view_emp_name" class="mb-0 fs-6"></p>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label fw-semibold text-muted">Employee Code</label>
                                        <p id="view_emp_code" class="mb-0 fs-6"></p>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label fw-semibold text-muted">Email</label>
                                        <p id="view_emp_email" class="mb-0 fs-6"></p>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label fw-semibold text-muted">Contact</label>
                                        <p id="view_emp_contact" class="mb-0 fs-6"></p>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label fw-semibold text-muted">Department</label>
                                        <p id="view_emp_department" class="mb-0 fs-6"></p>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label fw-semibold text-muted">Designation</label>
                                        <p id="view_emp_designation" class="mb-0 fs-6"></p>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label fw-semibold text-muted">State</label>
                                        <p id="view_emp_state" class="mb-0 fs-6"></p>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label fw-semibold text-muted">City</label>
                                        <p id="view_emp_city" class="mb-0 fs-6"></p>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label fw-semibold text-muted">Vertical</label>
                                        <p id="view_emp_vertical" class="mb-0 fs-6"></p>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label fw-semibold text-muted">Region</label>
                                        <p id="view_emp_region" class="mb-0 fs-6"></p>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label fw-semibold text-muted">Zone</label>
                                        <p id="view_emp_zone" class="mb-0 fs-6"></p>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label fw-semibold text-muted">Business Unit</label>
                                        <p id="view_emp_bu" class="mb-0 fs-6"></p>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label fw-semibold text-muted">Territory</label>
                                        <p id="view_emp_territory" class="mb-0 fs-6"></p>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label fw-semibold text-muted">Join Date</label>
                                        <p id="view_emp_doj" class="mb-0 fs-6"></p>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label fw-semibold text-muted">Separation Date</label>
                                        <p id="view_last_date" class="mb-0 fs-6"></p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Authorization Details Section -->
                        <div class="card mb-4 shadow-sm">
                            <div class="card-header bg-light">
                                <h5 class="card-title mb-0 text-primary fw-bold">Authorization Details</h5>
                            </div>
                            <div class="card-body">
                                <div class="row g-4">
                                    <div class="col-md-3">
                                        <label class="form-label fw-semibold text-muted">Authorized Through</label>
                                        <p id="view_Authorised_Through" class="mb-0 fs-6"></p>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label fw-semibold text-muted">Scope of Authorization</label>
                                        <p id="view_Scope_of_Authorisation" class="mb-0 fs-6"></p>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label fw-semibold text-muted">Authorization Issued By</label>
                                        <p id="view_Authorisation_Issued_By" class="mb-0 fs-6"></p>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label fw-semibold text-muted">Authorized Purpose</label>
                                        <p id="view_Authorised_Purpose" class="mb-0 fs-6"></p>
                                    </div>
                                    <div class="col-md-12" id="view_dynamic_field_container"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Authorization Certificate Details Section -->
                        <div class="card mb-4 shadow-sm">
                            <div class="card-header bg-light">
                                <h5 class="card-title mb-0 text-primary fw-bold">Authorization Certificate Details</h5>
                            </div>
                            <div class="card-body">
                                <div class="row g-4">
                                    <div class="col-md-3">
                                        <label class="form-label fw-semibold text-muted">Issue Date</label>
                                        <p id="view_Issue_Date" class="mb-0 fs-6"></p>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label fw-semibold text-muted">Effective From</label>
                                        <p id="view_Effective_From" class="mb-0 fs-6"></p>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label fw-semibold text-muted">Valid Up to</label>
                                        <p id="view_Valid_up_to" class="mb-0 fs-6"></p>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label fw-semibold text-muted">Authorization Status</label>
                                        <p id="view_Authorization_status" class="mb-0 fs-6"></p>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label fw-semibold text-muted">Document View / Draft</label>
                                        <p id="view_auth_doc" class="mb-0 fs-6"></p>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label fw-semibold text-muted">Revocation Date</label>
                                        <p id="view_Revocation_Date" class="mb-0 fs-6"></p>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label fw-semibold text-muted">Revocation Document</label>
                                        <p id="view_revocation_doc" class="mb-0 fs-6"></p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Change History Section -->
                        <div class="card shadow-sm">
                            <div class="card-header bg-light">
                                <h5 class="card-title mb-0 text-primary fw-bold">Change History</h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover table-bordered">
                                        <thead class="table-light">
                                            <tr>
                                                <th scope="col">Date</th>
                                                <th scope="col">Event</th>
                                                <th scope="col">Changes</th>
                                                <th scope="col">User</th>
                                            </tr>
                                        </thead>
                                        <tbody id="view_change_history"></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('components.theme.footer')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/5.10.2/tinymce.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize Select2 for Add Modal
        $("#Authorised_Purpose").select2({
            dropdownParent: $("#addResponsibleModal"),
            placeholder: "Select Authorized Purpose",
            allowClear: true
        });

        tinymce.init({
            selector: '#certificate_editor',
            height: 400,
            plugins: 'advlist lists textcolor colorpicker codesample link image print table preview fullscreen charmap paste export',
            toolbar: 'bold italic underline | alignleft aligncenter alignright alignjustify | fontsizeselect | forecolor backcolor | numlist bullist | indent outdent | link image | increasefontsize decreasefontsize | print table | preview fullscreen | charmap | importword importdrive exportpdf exportword',

            menubar: false,
            setup: function(editor) {
                editor.on('init', function() {
                    document.querySelectorAll('.edit-certificate').forEach(btn => {
                        btn.addEventListener('click', function() {
                            const id = this.dataset.id;
                            const certificate = this.dataset.certificate || '';
                            document.getElementById('certificate_id').value = id;

                            const isUpdate = !!certificate;
                            document.getElementById('certificateSaveBtn').style.display = isUpdate ? 'none':'inline-block';
                            document.getElementById('certificateUpdateBtn').style.display = isUpdate ? 'inline-block':'none';
                            // document.getElementById('certificatePrintBtn').style.display = isUpdate ? 'inline-block':'none';

                            // Fetch prepared draft content with placeholders replaced
                            fetch(`/responsible/get-draft-content/${id}`)
                                .then(r=>r.json())
                                .then(data=>{
                                    let content = '';
                                    if(isUpdate && certificate){
                                        content = certificate; // show already saved cert
                                    } else if(data.content){
                                        content = data.content; // replaced dynamic content
                                    }
                                    editor.setContent(content);
                                })
                                .catch(err=>{
                                    console.error(err);
                                    editor.setContent('Error loading content.');
                                });
                        });
                    });
                });
            }
        });

        $("#emp_name").select2({
            width: "100%",
            placeholder: 'Select Employee',
            allowClear: true,
            dropdownParent: $("#addResponsibleModal")
        });

         $("#groupcom_company_id").select2({
            width: "100%",
            placeholder: 'Select Employee',
            allowClear: true,
            dropdownParent: $("#addResponsibleModal")
        });

        // Initialize Select2 for Edit Modal
        $("#edit_Authorised_Purpose").select2({
            dropdownParent: $("#editResponsibleModal"),
            placeholder: "Select Authorized Purpose",
            allowClear: true
        });

        $("#edit_emp_name").select2({
            width: "100%",
            placeholder: 'Select Employee',
            allowClear: true,
            dropdownParent: $("#editResponsibleModal")
        });

        // Auto-close success alert
        setTimeout(function() {
            let alert = document.querySelector('.alert-success');
            if (alert) {
                let bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            }
        }, 2000);

        const companySelect = document.getElementById('company_id');
        const employeeSelect = document.getElementById('emp_name');
        const editCompanySelect = document.getElementById('edit_company_id');
        const editEmployeeSelect = document.getElementById('edit_emp_name');
        const authorisedPurposeSelect = document.getElementById('Authorised_Purpose');
        const editAuthorisedPurposeSelect = document.getElementById('edit_Authorised_Purpose');
        const dynamicFieldContainer = document.getElementById('dynamic_field_container');
        const editDynamicFieldContainer = document.getElementById('edit_dynamic_field_container');
        const groupcomSection = document.querySelector('#addResponsibleModal .groupcom-section');
        const groupcomCompanySelect = document.getElementById('groupcom_company_id');
        const editGroupcomSection = document.querySelector('#editResponsibleModal .groupcom-section');
        const editGroupcomCompanySelect = document.getElementById('edit_groupcom_company_id');
        let licenseRowIndex = 0;
        let editLicenseRowIndex = 0;

        const fields = ['emp_code', 'emp_status', 'emp_email', 'emp_contact', 'emp_department',
            'emp_designation', 'emp_state', 'emp_city', 'emp_doj', 'emp_vertical', 'emp_region',
            'emp_zone', 'emp_bu', 'emp_territory'
        ];
        const editFields = fields.map(field => `edit_${field}`);

        // Company selection handler for Add Modal
        companySelect.addEventListener('change', function() {
            const companyId = this.value;
            employeeSelect.innerHTML = '<option value="">Select Employee</option>';
            groupcomSection.style.display = companyId === '2' ? 'block' : 'none';
            groupcomCompanySelect.innerHTML = '<option value="">Select Groupcom Company</option>'; 
            if (companyId === '2') {
                fetch(`/custom_api/groupcom-companies?company_id=${companyId}`)
                    .then(response => response.json())
                    .then(data => {
                       if (Array.isArray(data)) {
                            data.forEach(company => {
                                const option = document.createElement('option');
                                option.value = company.id;
                                option.textContent = company.name;
                                groupcomCompanySelect.appendChild(option);
                            });
                        } else {
                            console.error('Invalid data format:', data);
                        }
                    })
                    .catch(error => console.error('Error fetching groupcom companies:', error));
            }
            dynamicFieldContainer.innerHTML =
                '<div class="col-md-12" id="add_license_row" style="display: none;"><a href="#" class="text-primary" id="addLicenseRowIcon"><i class="ri-add-line align-middle fs-5"></i></a></div>';
            $('#Authorised_Purpose').val(null).trigger('change');
            if (companyId) {
                fetch(`/custom_api/employees?company_id=${companyId}`)
                    .then(response => response.json())
                    .then(data => {
                        console.log('Employees fetched:', data);
                        data.forEach(emp => {
                            const option = document.createElement('option');
                            option.value = emp.id;
                            option.textContent = emp.emp_name || 'Unknown';
                            option.dataset.code = emp.emp_code || '';
                            option.dataset.status = emp.emp_status || '';
                            option.dataset.email = emp.emp_email || '';
                            option.dataset.contact = emp.emp_contact || '';
                            option.dataset.department = emp.emp_department || '';
                            option.dataset.designation = emp.emp_designation || '';
                            option.dataset.state = emp.emp_state || '';
                            option.dataset.city = emp.emp_city || '';
                            option.dataset.doj = emp.emp_doj || '';
                            option.dataset.vertical = emp.emp_vertical || 'N/A';
                            option.dataset.region = emp.emp_region || 'N/A';
                            option.dataset.zone = emp.emp_zone || 'N/A';
                            option.dataset.bu = emp.emp_bu || 'N/A';
                            option.dataset.territory = emp.emp_territory || 'N/A';
                            employeeSelect.appendChild(option);
                        });
                        $('#emp_name').trigger('change.select2');
                    })
                    .catch(error => console.error('Error fetching employees:', error));
            }
            fields.forEach(field => {
                const input = document.getElementById(field);
                if (input) input.value = '';
            });
        });

        // Company selection handler for Edit Modal
        editCompanySelect.addEventListener('change', function() {
            const companyId = this.value;
            editEmployeeSelect.innerHTML = '<option value="">Select Employee</option>';
            editGroupcomSection.style.display = companyId === '2' ? 'block' : 'none';
            if (companyId === '2') {
                fetch('/custom_api/groupcom-companies')
                    .then(response => response.json())
                    .then(data => {
                        editGroupcomCompanySelect.innerHTML = '<option value="">Select Groupcom Company</option>';
                        data.forEach(company => {
                            const option = document.createElement('option');
                            option.value = company.id;
                            option.textContent = company.company_name;
                            editGroupcomCompanySelect.appendChild(option);
                        });
                    })
                    .catch(error => console.error('Error fetching groupcom companies:', error));
            }
            editDynamicFieldContainer.innerHTML =
                '<div class="col-md-12" id="edit_add_license_row" style="display: none;"><a href="#" class="text-primary" id="editAddLicenseRowIcon"><i class="ri-add-line align-middle fs-5"></i></a></div>';
            $('#edit_Authorised_Purpose').val(null).trigger('change');
            if (companyId && companyId !== '2') {
                fetch(`/custom_api/employees?company_id=${companyId}`)
                    .then(response => response.json())
                    .then(data => {
                        console.log('Edit Employees fetched:', data);
                        data.forEach(emp => {
                            const option = document.createElement('option');
                            option.value = emp.id;
                            option.textContent = emp.emp_name || 'Unknown';
                            option.dataset.code = emp.emp_code || '';
                            option.dataset.status = emp.emp_status || '';
                            option.dataset.email = emp.emp_email || '';
                            option.dataset.contact = emp.emp_contact || '';
                            option.dataset.department = emp.emp_department || '';
                            option.dataset.designation = emp.emp_designation || '';
                            option.dataset.state = emp.emp_state || '';
                            option.dataset.city = emp.emp_city || '';
                            option.dataset.doj = emp.emp_doj || '';
                            option.dataset.vertical = emp.emp_vertical || 'N/A';
                            option.dataset.region = emp.emp_region || 'N/A';
                            option.dataset.zone = emp.emp_zone || 'N/A';
                            option.dataset.bu = emp.emp_bu || 'N/A';
                            option.dataset.territory = emp.emp_territory || 'N/A';
                            editEmployeeSelect.appendChild(option);
                        });
                        $('#edit_emp_name').trigger('change.select2');
                    })
                    .catch(error => console.error('Error fetching employees:', error));
            }
            editFields.forEach(field => {
                const input = document.getElementById(field);
                if (input) input.value = '';
            });
        });

        // Employee selection handler for Add Modal
        $('#emp_name').on('select2:select', function(e) {
            const selectedOption = e.params.data.element || {};
            fields.forEach(field => {
                const key = field.replace('emp_', '');
                const input = document.getElementById(field);
                if (input) {
                    const value = selectedOption.dataset[key] || '';
                    input.value = value;
                }
            });
        });

        // Employee selection handler for Edit Modal
        $('#edit_emp_name').on('select2:select', function(e) {
            const selectedOption = e.params.data.element || {};
            editFields.forEach(field => {
                const key = field.replace('edit_emp_', '');
                const input = document.getElementById(field);
                if (input) {
                    const value = selectedOption.dataset[key] || '';
                    input.value = value;
                }
            });
        });

        // Function to add a license row for Add Modal
        function addLicenseRow(index) {
            const html = `
                <div class="col-md-12 license-row" data-index="${index}">
                    <div class="row g-3 align-items-end">
                        <div class="col-md-4">
                            <label for="license_category_${index}" class="form-label fw-medium">License Category</label>
                            <select name="license_category[${index}]" id="license_category_${index}" class="form-select form-select-sm">
                                <option value="">Select License Category</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="license_name_${index}" class="form-label fw-medium">License Name</label>
                            <select name="license_name[${index}]" id="license_name_${index}" class="form-select form-select-sm" disabled>
                                <option value="">Select License Name</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <a href="#" class="text-danger remove-license-row" data-index="${index}" title="Remove License">
                                <i class="ri-delete-bin-line align-middle fs-5"></i>
                            </a>
                        </div>
                    </div>
                </div>`;
            dynamicFieldContainer.insertAdjacentHTML('beforeend', html);
            initializeLicenseRow(index, 'license_category_', 'license_name_');
        }

        // Function to add a license row for Edit Modal
        function addEditLicenseRow(index, licenseTypeId = '', licenseNameId = '') {
            const html = `
                <div class="col-md-12 license-row" data-index="${index}">
                    <div class="row g-3 align-items-end">
                        <div class="col-md-4">
                            <label for="edit_license_category_${index}" class="form-label fw-medium">License Category</label>
                            <select name="license_category[${index}]" id="edit_license_category_${index}" class="form-select form-select-sm">
                                <option value="">Select License Category</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="edit_license_name_${index}" class="form-label fw-medium">License Name</label>
                            <select name="license_name[${index}]" id="edit_license_name_${index}" class="form-select form-select-sm" disabled>
                                <option value="">Select License Name</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <a href="#" class="text-danger remove-license-row" data-index="${index}" title="Remove License">
                                <i class="ri-delete-bin-line align-middle fs-5"></i>
                            </a>
                        </div>
                    </div>
                </div>`;
            editDynamicFieldContainer.insertAdjacentHTML('beforeend', html);
            initializeLicenseRow(index, 'edit_license_category_', 'edit_license_name_', licenseTypeId, licenseNameId);
        }

        // Function to initialize license row
        function initializeLicenseRow(index, categoryPrefix, namePrefix, licenseTypeId = '', licenseNameId = '') {
            const licenseCategorySelect = document.getElementById(`${categoryPrefix}${index}`);
            const licenseNameSelect = document.getElementById(`${namePrefix}${index}`);

            fetch('/custom_api/license-types')
                .then(response => response.json())
                .then(data => {
                    data.forEach(type => {
                        const option = document.createElement('option');
                        option.value = type.id;
                        option.textContent = type.license_type;
                        if (type.id == licenseTypeId) option.selected = true;
                        licenseCategorySelect.appendChild(option);
                    });
                    if (licenseTypeId) {
                        fetch(`/get-license-names/${licenseTypeId}`)
                            .then(response => response.json())
                            .then(data => {
                                licenseNameSelect.disabled = false;
                                data.license_names.forEach(name => {
                                    const option = document.createElement('option');
                                    option.value = name.id;
                                    option.textContent = name.license_name;
                                    if (name.id == licenseNameId) option.selected = true;
                                    licenseNameSelect.appendChild(option);
                                });
                            })
                            .catch(error => console.error('Error fetching license names:', error));
                    }
                })
                .catch(error => console.error('Error fetching license types:', error));

            licenseCategorySelect.addEventListener('change', function() {
                const licenseTypeId = this.value;
                licenseNameSelect.innerHTML = '<option value="">Select License Name</option>';
                licenseNameSelect.disabled = true;
                if (licenseTypeId) {
                    fetch(`/get-license-names/${licenseTypeId}`)
                        .then(response => response.json())
                        .then(data => {
                            licenseNameSelect.disabled = false;
                            data.license_names.forEach(name => {
                                const option = document.createElement('option');
                                option.value = name.id;
                                option.textContent = name.license_name;
                                licenseNameSelect.appendChild(option);
                            });
                        })
                        .catch(error => console.error('Error fetching license names:', error));
                }
            });

            document.querySelector(`.remove-license-row[data-index="${index}"]`).addEventListener('click', function(e) {
                e.preventDefault();
                document.querySelector(`.license-row[data-index="${index}"]`).remove();
                toggleAddLicenseIcon(categoryPrefix.includes('edit') ? 'edit' : 'add');
            });
        }

        // Function to toggle visibility of Add License icon
        function toggleAddLicenseIcon(modalType = 'add') {
            const addLicenseRow = document.getElementById(modalType === 'edit' ? 'edit_add_license_row' : 'add_license_row');
            const purposes = $(modalType === 'edit' ? '#edit_Authorised_Purpose' : '#Authorised_Purpose').val() || [];
            const licensesPurposeId = @json($purposes->where('name', 'Licenses')->first()->id ?? null);
            addLicenseRow.style.display = purposes.includes(licensesPurposeId.toString()) ? 'block' : 'none';
        }

        // Authorised Purpose selection handler for Add Modal
        $('#Authorised_Purpose').on('select2:select select2:unselect', function(e) {
            const purposes = $(this).val() || [];
            dynamicFieldContainer.innerHTML =
                '<div class="col-md-12" id="add_license_row" style="display: none;"><a href="#" class="text-primary" id="addLicenseRowIcon"><i class="ri-add-line align-middle fs-5"></i></a></div>';
            licenseRowIndex = 0;

            purposes.forEach((purpose, index) => {
                let html = '';
                @foreach ($purposes as $purpose)
                    if (purpose === '{{ $purpose->id }}') {
                        html = `
                            <div class="col-md-4">
                                <label for="purpose_details_${index}_${{{ $purpose->id }}}" class="form-label fw-medium">{{ $purpose->name }} Details</label>
                                <input type="text" name="purpose_details[{{ $purpose->id }}]" id="purpose_details_${index}_${{{ $purpose->id }}}" class="form-control" placeholder="Enter {{ $purpose->name }} details">
                            </div>`;
                    }
                @endforeach
                if (purpose === '@json($purposes->where('name', 'Licenses')->first()->id ?? null)') {
                    addLicenseRow(licenseRowIndex);
                    licenseRowIndex++;
                } else if (html) {
                    dynamicFieldContainer.insertAdjacentHTML('beforeend', html);
                }
            });

            toggleAddLicenseIcon('add');
            const addLicenseRowBtn = document.getElementById('addLicenseRowIcon');
            if (addLicenseRowBtn) {
                addLicenseRowBtn.removeEventListener('click', handleAddLicenseClick);
                addLicenseRowBtn.addEventListener('click', handleAddLicenseClick);
            }
        });

        // Authorised Purpose selection handler for Edit Modal
        $('#edit_Authorised_Purpose').on('select2:select select2:unselect', function(e) {
            const purposes = $(this).val() || [];
            editDynamicFieldContainer.innerHTML =
                '<div class="col-md-12" id="edit_add_license_row" style="display: none;"><a href="#" class="text-primary" id="editAddLicenseRowIcon"><i class="ri-add-line align-middle fs-5"></i></a></div>';
            editLicenseRowIndex = 0;

            purposes.forEach((purpose, index) => {
                let html = '';
                @foreach ($purposes as $purpose)
                    if (purpose === '{{ $purpose->id }}') {
                        html = `
                            <div class="col-md-4">
                                <label for="edit_purpose_details_${index}_${{{ $purpose->id }}}" class="form-label fw-medium">{{ $purpose->name }} Details</label>
                                <input type="text" name="purpose_details[{{ $purpose->id }}]" id="edit_purpose_details_${index}_${{{ $purpose->id }}}" class="form-control" placeholder="Enter {{ $purpose->name }} details">
                            </div>`;
                    }
                @endforeach
                if (purpose === '@json($purposes->where('name', 'Licenses')->first()->id ?? null)') {
                    addEditLicenseRow(editLicenseRowIndex);
                    editLicenseRowIndex++;
                } else if (html) {
                    editDynamicFieldContainer.insertAdjacentHTML('beforeend', html);
                }
            });

            toggleAddLicenseIcon('edit');
            const editAddLicenseRowBtn = document.getElementById('editAddLicenseRowIcon');
            if (editAddLicenseRowBtn) {
                editAddLicenseRowBtn.removeEventListener('click', handleEditAddLicenseClick);
                editAddLicenseRowBtn.addEventListener('click', handleEditAddLicenseClick);
            }
        });

        // Handle Add License icon click for Add Modal
        function handleAddLicenseClick(e) {
            e.preventDefault();
            addLicenseRow(licenseRowIndex);
            licenseRowIndex++;
        }

        // Handle Add License icon click for Edit Modal
        function handleEditAddLicenseClick(e) {
            e.preventDefault();
            addEditLicenseRow(editLicenseRowIndex);
            editLicenseRowIndex++;
        }

        // Function to toggle Valid Up to and Revocation inputs
        function toggleStatusInputs(modalType = 'add') {
            const dateContainer = document.getElementById(modalType === 'edit' ? 'edit_valid_up_to_date_container' : 'valid_up_to_date_container');
            const lifetimeContainer = document.getElementById(modalType === 'edit' ? 'edit_valid_up_to_lifetime_container' : 'valid_up_to_lifetime_container');
            const revocationDateContainer = document.getElementById(modalType === 'edit' ? 'edit_revocation_date_container' : 'revocation_date_container');
            const revocationDocContainer = document.getElementById(modalType === 'edit' ? 'edit_revocation_doc_container' : 'revocation_doc_container');
            const revocationDateRequired = document.getElementById(modalType === 'edit' ? 'edit_revocation_date_required' : 'revocation_date_required');
            const revocationDocRequired = document.getElementById(modalType === 'edit' ? 'edit_revocation_doc_required' : 'revocation_doc_required');
            const dateRadio = document.getElementById(modalType === 'edit' ? 'edit_valid_up_to_date' : 'valid_up_to_date');
            const lifetimeRadio = document.getElementById(modalType === 'edit' ? 'edit_valid_up_to_lifetime' : 'valid_up_to_lifetime');
            const revokedRadio = document.getElementById(modalType === 'edit' ? 'edit_status_revoked' : 'status_revoked');
            const statusInput = document.getElementById(modalType === 'edit' ? 'edit_Authorization_status' : 'Authorization_status');

            if (revokedRadio && revokedRadio.checked) {
                dateContainer.style.display = 'none';
                lifetimeContainer.style.display = 'none';
                revocationDateContainer.style.display = 'block';
                revocationDocContainer.style.display = 'block';
                revocationDateRequired.style.display = 'inline';
                revocationDocRequired.style.display = 'inline';
                document.getElementById(modalType === 'edit' ? 'edit_Revocation_Date' : 'Revocation_Date').required = true;
                document.getElementById(modalType === 'edit' ? 'edit_revocation_doc' : 'revocation_doc').required = true;
                statusInput.value = 'Revoked';
                statusInput.classList.remove('text-success', 'text-danger', 'text-muted');
                statusInput.classList.add('text-danger');
            } else {
                revocationDateContainer.style.display = 'none';
                revocationDocContainer.style.display = 'none';
                revocationDateRequired.style.display = 'none';
                revocationDocRequired.style.display = 'none';
                document.getElementById(modalType === 'edit' ? 'edit_Revocation_Date' : 'Revocation_Date').required = false;
                document.getElementById(modalType === 'edit' ? 'edit_revocation_doc' : 'revocation_doc').required = false;

                if (dateRadio.checked) {
                    dateContainer.style.display = 'block';
                    lifetimeContainer.style.display = 'none';
                } else if (lifetimeRadio.checked) {
                    dateContainer.style.display = 'none';
                    lifetimeContainer.style.display = 'block';
                }
                updateStatus(modalType);
            }
        }

        // Function to update Authorization_status based on Valid_up_to or Revoke
        function updateStatus(modalType = 'add') {
            const validUpToInput = document.getElementById(modalType === 'edit' ? 'edit_Valid_up_to' : 'Valid_up_to');
            const validUpToLifetimeInput = document.getElementById(modalType === 'edit' ? 'edit_Valid_up_to_lifetime_input' : 'Valid_up_to_lifetime_input');
            const statusInput = document.getElementById(modalType === 'edit' ? 'edit_Authorization_status' : 'Authorization_status');
            const dateRadio = document.getElementById(modalType === 'edit' ? 'edit_valid_up_to_date' : 'valid_up_to_date');
            const lifetimeRadio = document.getElementById(modalType === 'edit' ? 'edit_valid_up_to_lifetime' : 'valid_up_to_lifetime');
            const revokedRadio = document.getElementById(modalType === 'edit' ? 'edit_status_revoked' : 'status_revoked');

            if (!statusInput) return;

            statusInput.classList.remove('text-success', 'text-danger', 'text-muted');

            if (revokedRadio && revokedRadio.checked) {
                statusInput.value = 'Revoked';
                statusInput.classList.add('text-danger');
            } else if (lifetimeRadio.checked && validUpToLifetimeInput.value === 'Lifetime') {
                statusInput.value = 'Active';
                statusInput.classList.add('text-success');
                if (revokedRadio) revokedRadio.checked = false;
            } else if (dateRadio.checked && validUpToInput.value) {
                const validUpToDate = new Date(validUpToInput.value);
                const currentDate = new Date();
                validUpToDate.setHours(0, 0, 0, 0);
                currentDate.setHours(0, 0, 0, 0);

                if (!isNaN(validUpToDate)) {
                    statusInput.value = validUpToDate > currentDate ? 'Active' : 'Expired';
                    statusInput.classList.add(validUpToDate > currentDate ? 'text-success' : 'text-danger');
                    if (revokedRadio) revokedRadio.checked = false;
                } else {
                    statusInput.value = '';
                    statusInput.classList.add('text-muted');
                    if (revokedRadio) revokedRadio.checked = false;
                }
            } else {
                statusInput.value = '';
                statusInput.classList.add('text-muted');
                if (revokedRadio) revokedRadio.checked = false;
            }
        }

        // Initialize status update and radio button handlers for Add Modal
        document.getElementById('valid_up_to_date').addEventListener('change', () => toggleStatusInputs('add'));
        document.getElementById('valid_up_to_lifetime').addEventListener('change', () => toggleStatusInputs('add'));
        document.getElementById('Valid_up_to').addEventListener('change', () => updateStatus('add'));

        // Initialize status update and radio button handlers for Edit Modal
        document.getElementById('edit_valid_up_to_date').addEventListener('change', () => {
            document.getElementById('edit_status_revoked').checked = false; // Uncheck Revoked when Valid Up to changes
            toggleStatusInputs('edit');
        });

        document.getElementById('edit_valid_up_to_lifetime').addEventListener('change', () => {
            document.getElementById('edit_status_revoked').checked = false; // Uncheck Revoked when Valid Up to changes
            toggleStatusInputs('edit');
        });
        
        document.getElementById('edit_status_revoked').addEventListener('change', () => toggleStatusInputs('edit'));
        document.getElementById('edit_Valid_up_to').addEventListener('change', () => {
            document.getElementById('edit_status_revoked').checked = false; // Uncheck Revoked when Valid Up to date changes
            updateStatus('edit');
        });

        // Update the edit button click handler
        document.querySelectorAll('.edit-responsible').forEach(button => {
            button.addEventListener('click', function() {
                const data = this.dataset;
                console.log('Edit button data:', data); 
                document.getElementById('editResponsibleForm').action = `/responsible/${data.id}`;
                document.getElementById('edit_id').value = data.id;
                document.getElementById('edit_company_id').value = data.core_company_id || '';
                document.getElementById('edit_Authorised_Through').value = data.authorised_through || '';
                document.getElementById('edit_Scope_of_Authorisation').value = data.scope_of_authorisation || '';
                document.getElementById('edit_Authorisation_Issued_By').value = data.authorisation_issued_by || '';
                document.getElementById('edit_Issue_Date').value = data.issue_date || '';
                document.getElementById('edit_Effective_From').value = data.effective_from || '';
                document.getElementById('edit_Authorization_status').value = data.authorization_status || '';

                // Set Authorization Status radio
                const revokedRadio = document.getElementById('edit_status_revoked');
                revokedRadio.checked = data.authorization_status === 'Revoked';
                console.log('Authorization Status:', data.authorization_status, 'Revoked checked:', revokedRadio.checked); // Debug

                // Populate employee select
                fetch(`/custom_api/employees?company_id=${data.core_company_id}`)
                    .then(response => response.json())
                    .then(employees => {
                        console.log('Edit employees fetched:', employees);
                        editEmployeeSelect.innerHTML = '<option value="">Select Employee</option>';
                        employees.forEach(emp => {
                            const option = document.createElement('option');
                            option.value = emp.id;
                            option.textContent = emp.emp_name || 'Unknown';
                            option.dataset.code = emp.emp_code || '';
                            option.dataset.status = emp.emp_status || '';
                            option.dataset.email = emp.emp_email || '';
                            option.dataset.contact = emp.emp_contact || '';
                            option.dataset.department = emp.emp_department || '';
                            option.dataset.designation = emp.emp_designation || '';
                            option.dataset.state = emp.emp_state || '';
                            option.dataset.city = emp.emp_city || '';
                            option.dataset.doj = emp.emp_doj || '';
                            option.dataset.vertical = emp.emp_vertical || '';
                            option.dataset.region = emp.emp_region || '';
                            option.dataset.zone = emp.emp_zone || '';
                            option.dataset.bu = emp.emp_bu || '';
                            option.dataset.territory = emp.emp_territory || '';
                            if (emp.id == data.core_employee_id) option.selected = true;
                            editEmployeeSelect.appendChild(option);
                        });
                        $('#edit_emp_name').trigger('change.select2');
                        const selectedOption = editEmployeeSelect.querySelector(`option[value="${data.core_employee_id}"]`);
                        if (selectedOption) {
                            $('#edit_emp_name').val(data.core_employee_id).trigger({
                                type: 'select2:select',
                                params: { data: { element: selectedOption } }
                            });
                        }
                    })
                    .catch(error => console.error('Error fetching employees for edit:', error));

                // Populate Authorized Purpose
                const purposeDetails = JSON.parse(data.purpose_details || '[]');
                $('#edit_Authorised_Purpose').val(purposeDetails).trigger('change');

                // Populate Valid Up to
                const validUpToDateRadio = document.getElementById('edit_valid_up_to_date');
                const validUpToLifetimeRadio = document.getElementById('edit_valid_up_to_lifetime');
                const validUpToInput = document.getElementById('edit_Valid_up_to');
                if (data.valid_up_to === 'Lifetime') {
                    validUpToLifetimeRadio.checked = true;
                    validUpToDateRadio.checked = false;
                    validUpToInput.value = '';
                } else {
                    validUpToDateRadio.checked = true;
                    validUpToLifetimeRadio.checked = false;
                    validUpToInput.value = data.valid_up_to || '';
                }

                // Populate Revocation fields
                document.getElementById('edit_Revocation_Date').value = data.revocation_date || '';
                const existingRevocationDoc = document.getElementById('existing_revocation_doc');
                const revocationDocInputContainer = document.getElementById('edit_revocation_doc_input_container');
                const deleteRevocationDocInput = document.getElementById('delete_revocation_doc');
                if (data.revocation_doc) {
                    existingRevocationDoc.innerHTML = `
                        <a href="${data.revocation_doc}" target="_blank">View Existing Revocation Document</a>
                        <a href="javascript:void(0);" class="text-danger ms-3 delete-revocation-doc" title="Delete">
                            <i class="ri-delete-bin-line align-middle fs-5"></i>
                        </a>
                    `;
                    revocationDocInputContainer.style.display = 'none';
                    deleteRevocationDocInput.value = '0';
                } else {
                    existingRevocationDoc.innerHTML = '';
                    revocationDocInputContainer.style.display = 'block';
                    deleteRevocationDocInput.value = '0';
                }

                // Populate auth_doc
                const existingDoc = document.getElementById('existing_doc');
                const authDocInputContainer = document.getElementById('edit_auth_doc_input_container');
                const deleteAuthDocInput = document.getElementById('delete_auth_doc');
                if (data.auth_doc) {
                    existingDoc.innerHTML = `
                        <a href="${data.auth_doc}" target="_blank">View Existing Document</a>
                        <a href="javascript:void(0);" class="text-danger ms-3 delete-auth-doc" title="Delete"><i class="ri-delete-bin-line align-middle fs-5""></i></a>
                    `;
                    authDocInputContainer.style.display = 'none';
                    deleteAuthDocInput.value = '0';
                } else {
                    existingDoc.innerHTML = '';
                    authDocInputContainer.style.display = 'block';
                    deleteAuthDocInput.value = '0';
                }

                // Populate license rows
                editDynamicFieldContainer.innerHTML =
                    '<div class="col-md-12" id="edit_add_license_row" style="display: none;"><a href="#" class="text-primary" id="editAddLicenseRowIcon"><i class="ri-add-line align-middle fs-5"></i></a></div>';
                editLicenseRowIndex = 0;
                const licenses = JSON.parse(data.licenses || '[]');
                licenses.forEach(license => {
                    addEditLicenseRow(editLicenseRowIndex, license.license_type_id, license.license_name_id);
                    editLicenseRowIndex++;
                });

                // Initialize UI state
                toggleAddLicenseIcon('edit');
                toggleStatusInputs('edit');

                // Attach delete button event listeners
                const deleteRevocationDocBtn = existingRevocationDoc.querySelector('.delete-revocation-doc');
                if (deleteRevocationDocBtn) {
                    deleteRevocationDocBtn.addEventListener('click', function() {
                        existingRevocationDoc.innerHTML = '';
                        revocationDocInputContainer.style.display = 'block';
                        deleteRevocationDocInput.value = '1';
                    });
                }

                const deleteAuthDocBtn = existingDoc.querySelector('.delete-auth-doc');
                if (deleteAuthDocBtn) {
                    deleteAuthDocBtn.addEventListener('click', function() {
                        existingDoc.innerHTML = '';
                        authDocInputContainer.style.display = 'block';
                        deleteAuthDocInput.value = '1';
                    });
                }
            });
        });

        // View button handler
        document.querySelectorAll('.view-responsible').forEach(button => {
            button.addEventListener('click', function() {
                const data = this.dataset;
                console.log('View button data:', data); // Debug: Log dataset

                // Populate Employee Details
                const employee = JSON.parse(data.employee || '{}');
                document.getElementById('view_company_name').textContent = data.company_name || 'N/A';
                document.getElementById('view_emp_name').textContent = employee.emp_name || 'N/A';
                document.getElementById('view_emp_code').textContent = data.emp_code || 'N/A';
                document.getElementById('view_emp_email').textContent = employee.emp_email || 'N/A';
                document.getElementById('view_emp_contact').textContent = employee.emp_contact || 'N/A';
                document.getElementById('view_emp_department').textContent = employee.emp_department || 'N/A';
                document.getElementById('view_emp_designation').textContent = employee.emp_designation || 'N/A';
                document.getElementById('view_emp_state').textContent = employee.emp_state || 'N/A';
                document.getElementById('view_emp_city').textContent = employee.emp_city || 'N/A';
                document.getElementById('view_emp_vertical').textContent = employee.emp_vertical || 'N/A';
                document.getElementById('view_emp_region').textContent = employee.emp_region || 'N/A';
                document.getElementById('view_emp_zone').textContent = employee.emp_zone || 'N/A';
                document.getElementById('view_emp_bu').textContent = employee.emp_bu || 'N/A';
                document.getElementById('view_emp_territory').textContent = employee.emp_territory || 'N/A';
                document.getElementById('view_emp_doj').textContent = employee.emp_doj || 'N/A';
                document.getElementById('view_last_date').textContent = employee.last_date || 'N/A';

                // Populate Authorization Details
                document.getElementById('view_Authorised_Through').textContent = data.authorised_through || 'N/A';
                document.getElementById('view_Scope_of_Authorisation').textContent = data.scope_of_authorisation || 'N/A';
                document.getElementById('view_Authorisation_Issued_By').textContent = data.authorisation_issued_by || 'N/A';
                const purposeDetails = JSON.parse(data.purpose_details || '[]');
                const purposes = @json($purposes->pluck('name', 'id')->toArray());
                document.getElementById('view_Authorised_Purpose').textContent = purposeDetails.map(id => purposes[id] || 'Unknown').join(', ') || 'N/A';

                // Populate Licenses
                const licenses = JSON.parse(data.licenses || '[]');
                const dynamicFieldContainer = document.getElementById('view_dynamic_field_container');
                dynamicFieldContainer.innerHTML = '';
                if (licenses.length > 0) {
                    licenses.forEach((license, index) => {
                        const html = `
                        <div class="d-flex flex-wrap">
                            <div class="col-md-3">
                                <label class="form-label fw-medium"><b>License Category ${index + 1} </b></label>
                                <p class="mb-0">${license.license_type || 'N/A'}</p>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-medium"><b>License Name ${index + 1}</b></label>
                                <p class="mb-0">${license.license_name || 'N/A'}</p>
                            </div>
                        </div>`;
                        dynamicFieldContainer.insertAdjacentHTML('beforeend', html);
                    });
                }

                // Populate Authorization Certificate Details
                document.getElementById('view_Issue_Date').textContent = data.issue_date ? new Date(data.issue_date).toLocaleDateString('en-GB') : 'N/A';
                document.getElementById('view_Effective_From').textContent = data.effective_from ? new Date(data.effective_from).toLocaleDateString('en-GB') : 'N/A';
                document.getElementById('view_Valid_up_to').textContent = data.valid_up_to ? new Date(data.valid_up_to).toLocaleDateString('en-GB') : 'N/A';
                document.getElementById('view_Authorization_status').textContent = data.authorization_status || 'N/A';
                const authDoc = document.getElementById('view_auth_doc');
                authDoc.innerHTML = data.auth_doc ? `<a href="${data.auth_doc}" target="_blank">View Document</a>` : 'N/A';
                document.getElementById('view_Revocation_Date').textContent = data.revocation_date ? new Date(data.revocation_date).toLocaleDateString('en-GB') : 'N/A';
                const revocationDoc = document.getElementById('view_revocation_doc');
                revocationDoc.innerHTML = data.revocation_doc ? `<a href="${data.revocation_doc}" target="_blank">View Revocation Document</a>` : 'N/A';

                // Populate Change History
                const history = JSON.parse(data.history || '[]');
                const changeHistoryTable = document.getElementById('view_change_history');
                changeHistoryTable.innerHTML = '';
                const relevantFields = ['Issue Date', 'Effective From', 'Valid Up to', 'Authorization Status', 'Revocation Date', 'Authorization Document', 'Revocation Document'];
                const filteredHistory = history.filter(item => 
                    relevantFields.some(field => item.description.includes(field))
                );
                if (filteredHistory.length > 0) {
                    filteredHistory.forEach(item => {
                        const row = `
                            <tr>
                                <td>${item.created_at}</td>
                                <td>${item.event}</td>
                                <td>${item.description}</td>
                                <td>${item.causer}</td>
                            </tr>`;
                        changeHistoryTable.insertAdjacentHTML('beforeend', row);
                    });
                } else {
                    changeHistoryTable.innerHTML = '<tr><td colspan="4" class="text-center">No relevant change history available.</td></tr>';
                }
            });
        });

         document.querySelectorAll('.edit-certificate').forEach(button => {
            button.addEventListener('click', function() {
                const id = this.dataset.id;
                const certificate = this.dataset.certificate || '';
                document.getElementById('certificate_id').value = id;
                const isUpdate = !!certificate;
                document.getElementById('certificateSaveBtn').style.display = isUpdate ? 'none' : 'inline-block';
                document.getElementById('certificateUpdateBtn').style.display = isUpdate ? 'inline-block' : 'none';
                if (isUpdate) {
                    tinymce.get('certificate_editor').setContent(certificate);
                } else {
                    // Fetch draft content will be handled by TinyMCE init
                }
            });
        });
      
        document.getElementById('certificateForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const id = document.getElementById('certificate_id').value;
            const content = tinymce.get('certificate_editor').getContent();
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            $.ajax({
                url: `/responsible/${id}/certificate`, // Updated URL
                type: 'POST', // Use POST with _method=PUT
                data: {
                    _token: csrfToken,
                    _method: 'PUT', // Spoof PUT request
                    auth_certificate: content
                },
                success: function(response) {
                    alert('Certificate saved successfully.');
                    location.reload();
                },
                error: function(xhr) {
                    console.error('Error:', xhr.responseText);
                    alert('Error saving certificate: ' + (xhr.responseJSON?.message || 'Unknown error'));
                }
            });
        });
    });
</script>
@endsection
    