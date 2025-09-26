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
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header align-items-center d-flex">  

                        <!-- Date Filter Inputs -->
                        <div class="flex-grow-1 me-3">
                            <div class="row g-2 align-items-end">
                                <div class="col-md-3">
                                    <label for="start_date" class="form-label text-muted">Start Date</label>
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text bg-soft-primary text-primary">
                                            <i class="ri-calendar-2-line"></i>
                                        </span>
                                        <input type="text" 
                                            class="form-control form-control-sm" 
                                            id="start_date" 
                                            name="start_date" 
                                            placeholder="Select start date">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <label for="end_date" class="form-label text-muted">End Date</label>
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text bg-soft-primary text-primary">
                                            <i class="ri-calendar-2-line"></i>
                                        </span>
                                        <input type="text" 
                                            class="form-control form-control-sm" 
                                            id="end_date" 
                                            name="end_date" 
                                            placeholder="Select end date">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <button type="button" class="btn btn-primary btn-sm waves-effect waves-light rounded-pill me-1" id="apply_date_filter">
                                        <i class="ri-filter-3-line me-1"></i> Apply
                                    </button>      
                                    <button type="button" class="btn btn-secondary btn-sm waves-effect waves-light rounded-pill" data-bs-toggle="offcanvas" data-bs-target="#filterOffcanvas" aria-controls="filterOffcanvas">
                                        <i class="ri-filter-line me-1"></i> More Filters
                                    </button>                            
                                </div>
                            </div>
                        </div>

                        <div class="flex-shrink-0">
                            <button type="button" class="btn btn-primary btn-label waves-effect waves-light rounded-pill"
                                data-bs-toggle="modal" data-bs-target="#addLicenseModal">
                                <i class="ri-add-circle-fill label-icon align-middle rounded-pill fs-16 me-2"></i>Add License
                            </button>
                        </div>
                    </div>

                    <div class="card-body">
                        @if (session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <strong>Success!</strong> {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        @if (session('error'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                {{ session('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        <div class="table-responsive">
                            <table class="table nowrap dt-responsive align-middle table-hover table-bordered" id="licenseTable" style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Company Name</th>
                                        <th>License Holder Name</th>
                                        <th>License Type</th>
                                        <th>License Name</th>
                                        <th>Valid Upto</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($licenses as $index => $license)
                                        <tr data-license-type-id="{{ $license->license_type_id }}"
                                            data-license-name-id="{{ $license->license_name_id }}">
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $license->company->company_name ?? 'N/A' }}</td>
                                            <td>{{ $license->groupcom->name ?? ($license->company->company_name ?? 'N/A') }}</td>
                                            <td>{{ $license->licenseType->license_type ?? 'N/A' }}</td>
                                            <td>{{ $license->licenseName->license_name ?? 'N/A' }}</td>
                                            <td>{{ $license->valid_upto }}</td>
                                            <td>{{ $license->lis_status }}</td>
                                            <td>
                                                <div class="dropdown">
                                                    <button class="btn btn-sm btn-icon btn-light" type="button"
                                                        data-bs-toggle="dropdown" aria-expanded="false">
                                                        <i class="ri-more-fill fs-18"></i>
                                                    </button>
                                                    <ul class="dropdown-menu dropdown-menu-end shadow">
                                                        <li>
                                                            <a class="dropdown-item d-flex align-items-center viewLicenseBtn"
                                                                data-id="{{ $license->id }}" data-bs-toggle="modal"
                                                                data-bs-target="#viewLicenseModal">
                                                                <i class="ri-eye-line text-primary me-2"></i> View
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a class="dropdown-item d-flex align-items-center editLicenseBtn"
                                                                data-id="{{ $license->id }}" data-bs-toggle="modal"
                                                                data-bs-target="#editLicenseModal">
                                                                <i class="ri-pencil-line text-warning me-2"></i> Edit
                                                            </a>
                                                        </li>
                                                        @if (Carbon\Carbon::parse($license->valid_upto)->lte(now()))
                                                            <li>
                                                                <a class="dropdown-item d-flex align-items-center renewLicenseBtn"
                                                                    data-id="{{ $license->id }}" data-bs-toggle="modal"
                                                                    data-bs-target="#renewLicenseModal">
                                                                    <i class="ri-refresh-line text-success me-2"></i> Renew
                                                                </a>
                                                            </li>
                                                        @endif
                                                        <li>
                                                            <a class="dropdown-item d-flex align-items-center history-btn"
                                                                href="#" data-license-type-id="{{ $license->license_type_id }}"
                                                                data-license-name-id="{{ $license->license_name_id }}"
                                                                data-bs-toggle="modal" data-bs-target="#historyLicenseModal">
                                                                <i class="ri-file-list-3-line text-info me-2"></i> History
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a class="dropdown-item d-flex align-items-center"
                                                                href="{{ route('license.activity-log') }}">
                                                                <i class="ri-history-line text-muted me-2"></i> Activity Log
                                                            </a>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="text-center">No licenses found.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Add License Modal -->
        <div class="modal fade" id="addLicenseModal" tabindex="-1" aria-labelledby="addLicenseModalLabel"
            aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog modal-lg modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addLicenseModalLabel">Add License</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="addLicenseForm" action="{{ route('license.store') }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            <h6 class="fw-bold mb-3 text-primary border-bottom pb-2">Add License Basic Details</h6>
                                <div class="row g-4 mb-4">
                                    <div class="col-md-3">
                                        <label for="company_id" class="form-label">Company Name</label>
                                        <select class="form-select form-select-sm" id="company_id" name="company_id" required>
                                            <option value="">Select Company</option>
                                            @foreach ($companies as $company)
                                                <option value="{{ $company->id }}">{{ $company->company_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-md-3">
                                        <label for="groupcom_id" class="form-label">License Holder Name</label>
                                        <select class="form-select form-select-sm" id="groupcom_id" name="groupcom_id">
                                            <option value="">Select License Holder</option>
                                        </select>
                                    </div>

                                    <div class="col-md-3">
                                        <label for="license_type_id" class="form-label">License Type</label>
                                        <select class="form-select form-select-sm" id="license_type_id" name="license_type_id" required>
                                            <option value="">Select License Type</option>
                                            @foreach ($licenseTypes as $type)
                                                <option value="{{ $type->id }}">{{ $type->license_type }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-md-3">
                                        <label for="license_name_id" class="form-label">License Name</label>
                                        <select class="form-select form-select-sm" id="license_name_id" name="license_name_id" required>
                                            <option value="">Select License Name</option>
                                        </select>
                                    </div>

                                    <div class="col-md-3">
                                        <label for="state_id" class="form-label">State</label>
                                        <select class="form-select form-select-sm" id="state_id" name="state_id" required>
                                            <option value="">Select State</option>
                                            @foreach ($states as $state)
                                                <option value="{{ $state->id }}">{{ $state->state_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-md-3">
                                        <label for="district_id" class="form-label">District</label>
                                        <select class="form-select form-select-sm" id="district_id" name="district_id" required>
                                            <option value="">Select District</option>
                                        </select>
                                    </div>

                                    <div class="col-md-3">
                                        <label for="city_village_id" class="form-label">City/Village</label>
                                        <select class="form-select form-select-sm" id="city_village_id" name="city_village_id" required>
                                            <option value="">Select City/Village</option>
                                        </select>
                                    </div>

                                    <div class="col-md-3">
                                        <label for="pincode" class="form-label">Pincode</label>
                                        <input type="text" class="form-control form-control-sm" id="pincode" name="pincode" required
                                            readonly>
                                    </div>
                                </div>

                                <h6 class="fw-bold mb-3 text-primary border-bottom pb-2">Authority Person Details</h6>
                                <div class="row g-4 mb-4">
                                    <div class="col-md-3">
                                        <label for="responsible_person" class="form-label">Authority Name</label>
                                        <input type="text" id="responsible_person_name" name="responsible_person_name"
                                            class="form-control form-control-sm" required readonly>
                                        <input type="hidden" name="responsible_person" id="responsible_person">
                                    </div>

                                    <div class="col-md-3">
                                        <label for="res_email" class="form-label">Email</label>
                                        <input type="email" id="res_email" name="res_email" class="form-control form-control-sm" required
                                            readonly>
                                    </div>

                                    <div class="col-md-3">
                                        <label for="res_contact" class="form-label">Contact</label>
                                        <input type="text" id="res_contact" name="res_contact" class="form-control form-control-sm"
                                            required readonly>
                                    </div>

                                    <div class="col-md-3">
                                        <label for="res_department" class="form-label">Department</label>
                                        <input type="text" id="res_department" name="res_department" class="form-control form-control-sm"
                                            required readonly>
                                    </div>

                                    <div class="col-md-3">
                                        <label for="res_designation" class="form-label">Designation</label>
                                        <input type="text" id="res_designation" name="res_designation"
                                            class="form-control form-control-sm" required readonly>
                                    </div>
                                </div>

                                <h6 class="fw-bold mb-3 text-primary border-bottom pb-2">Other Details</h6>
                                <div class="row g-4 mb-4">
                                    <div class="col-md-3">
                                        <label class="form-label d-block">License Creation</label>
                                        
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="license_creation" id="license_creation_new" value="new" checked>
                                            <label class="form-check-label" for="license_creation_new">New</label>
                                        </div>

                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="license_creation" id="license_creation_modification" value="modification">
                                            <label class="form-check-label" for="license_creation_modification">Modification</label>
                                        </div>
                                    </div>

                                    <div class="col-md-3" id="license_creation_remark_section" style="display: none;">
                                        <label for="license_creation_remark" class="form-label">Remark</label>
                                        <textarea class="form-control form-control-sm" id="license_creation_remark" name="license_creation_remark" rows="3" placeholder="Enter remark for modification"></textarea>
                                    </div>

                                    <div class="col-md-3">
                                        <label for="application_number" class="form-label">Application Number</label>
                                        <input type="text" class="form-control form-control-sm" id="application_number" name="application_number"
                                            required>
                                    </div>
                                   

                                    <div class="col-md-3">
                                        <label for="letter_date" class="form-label">Date of Application</label>
                                        <input type="date" class="form-control form-control-sm" id="letter_date" name="letter_date">
                                    </div>

                                    <div class="col-md-3">
                                        <label for="application_document" class="form-label">Application Document</label>
                                        <div id="application-document-list">
                                           <div class="document-row input-group mb-2">
                                                <input type="text" name="document_name[]" class="form-control form-control-sm" placeholder="Enter document name">
                                                <input type="file" name="application_document[]" class="form-control form-control-sm">

                                                <!-- Action Icons -->
                                                <span class="input-group-text bg-white border-0">
                                                    <i class="ri-add-circle-line text-muted fs-5 add-document" role="button" title="Add"></i>
                                                </span>
                                                <span class="input-group-text bg-white border-0" style="display: none;">
                                                    <i class="ri-delete-bin-line text-danger fs-5 remove-document" role="button" title="Remove"></i>
                                                </span>
                                            </div>
                                        </div>
                                    </div>

                                    
                                    <div class="col-md-3">
                                        <label for="application_status" class="form-label">Application Status</label>
                                        <select class="form-select form-select-sm" id="application_status_select" name="application_status" required>
                                            <option value="">Select</option>
                                            <option value="Submitted">Submitted</option>
                                            <option value="Under review">Under review</option>
                                            <option value="Withdrawn">Withdrawn</option>
                                            <option value="Rejected">Rejected</option>
                                        </select>
                                        <input type="text" class="form-control form-control-sm" id="application_status_input" name="application_status" value="Approved" readonly style="display: none;">
                                    </div>


                                    <div class="col-md-3">
                                        <label for="date_of_issue" class="form-label">Date of Registration</label>
                                        <input type="date" class="form-control form-control-sm" id="date_of_issue" name="date_of_issue">
                                    </div>

                                    <div class="col-md-3">
                                        <label for="registration_number" class="form-label">Registration Number</label>
                                        <input type="text" class="form-control form-control-sm" id="registration_number" name="registration_number">
                                    </div>

                                    <div class="col-md-3">
                                        <label for="certificate_number" class="form-label">Certificate Number</label>
                                        <input type="text" class="form-control form-control-sm" id="certificate_number" name="certificate_number">
                                    </div>  

                                    <div class="col-md-3">
                                        <label for="valid_upto" class="form-label">Valid Upto</label>
                                        <input type="date" class="form-control form-control-sm" id="valid_upto" name="valid_upto"
                                            required>
                                    </div>

                                    <div class="col-md-3">
                                        <label for="lis_status" class="form-label">License Status</label>
                                        <select class="form-select form-select-sm" id="lis_status" name="lis_status" required>
                                            <option value="">Select</option>
                                            <option value="Active">Active</option>
                                            <option value="Deactive">Expired</option>
                                        </select>
                                    </div>

                                    <div class="col-md-3">
                                        <label class="form-label">Add CC Email</label>
                                        <select class="form-select form-select-sm" id="reminder_option" name="reminder_option" required>
                                            <option value="N">No</option>
                                            <option value="Y">Yes</option>
                                        </select>
                                    </div>

                                    <div class="col-md-3" id="reminder-email-section" style="display: none;">
                                        <label class="form-label">Reminder Emails</label>
                                        <div id="reminder-email-list">
                                            <div class="input-group mb-2">
                                                <input type="email" name="reminder_emails[]" class="form-control form-control-sm"
                                                    placeholder="Enter email">
                                                <button type="button"
                                                    class="btn btn-outline-secondary add-email">Add</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <h6 class="fw-bold mb-3 text-primary border-bottom pb-2">Requirement Details</h6>
                                <div id="mapped_fields" style="margin-top: 10px;">
                                    <!-- Mapped fields will be dynamically inserted here -->
                                </div>
                            <input type="hidden" name="license_performance" value="new">
                            <button type="submit" class="btn btn-success">Save</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Edit License Modal -->
        <div class="modal fade" id="editLicenseModal" tabindex="-1" aria-labelledby="editLicenseModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog modal-lg modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editLicenseModalLabel">Edit License</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="editLicenseForm" action="{{ route('license.update') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="license_id" id="edit_license_id">
                            <input type="hidden" name="license_performance" id="edit_license_performance">
                            <h6 class="fw-bold mb-3 text-primary border-bottom pb-2">Edit License Basic Details</h6>
                            <div class="row g-4 mb-4">
                                <div class="col-md-3">
                                    <label for="edit_company_id" class="form-label">Company Name</label>
                                    <select class="form-select form-select-sm" id="edit_company_id" name="company_id" required>
                                        <option value="">Select Company</option>
                                        @foreach ($companies as $company)
                                            <option value="{{ $company->id }}">{{ $company->company_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label for="edit_groupcom_id" class="form-label">License Holder Name</label>
                                    <select class="form-select form-select-sm" id="edit_groupcom_id" name="groupcom_id">
                                        <option value="">Select License Holder</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label for="edit_license_type_id" class="form-label">License Type</label>
                                    <select class="form-select form-select-sm" id="edit_license_type_id" name="license_type_id" required>
                                        <option value="">Select License Type</option>
                                        @foreach ($licenseTypes as $type)
                                            <option value="{{ $type->id }}">{{ $type->license_type }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label for="edit_license_name_id" class="form-label">License Name</label>
                                    <select class="form-select form-select-sm" id="edit_license_name_id" name="license_name_id" required>
                                        <option value="">Select License Name</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label for="edit_state_id" class="form-label">State</label>
                                    <select class="form-select form-select-sm" id="edit_state_id" name="state_id" required>
                                        <option value="">Select State</option>
                                        @foreach ($states as $state)
                                            <option value="{{ $state->id }}">{{ $state->state_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label for="edit_district_id" class="form-label">District</label>
                                    <select class="form-select form-select-sm" id="edit_district_id" name="district_id" required>
                                        <option value="">Select District</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label for="edit_city_village_id" class="form-label">City/Village</label>
                                    <select class="form-select form-select-sm" id="edit_city_village_id" name="city_village_id" required>
                                        <option value="">Select City/Village</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label for="edit_pincode" class="form-label">Pincode</label>
                                    <input type="text" class="form-control form-control-sm" id="edit_pincode" name="pincode" required readonly>
                                </div>
                            </div>

                            <h6 class="fw-bold mb-3 text-primary border-bottom pb-2">Authority Person Details</h6>
                            <div class="row g-3 mb-4">
                                <div class="col-md-3">
                                    <label for="edit_responsible_person_name" class="form-label">Authority Name</label>
                                    <input type="text" id="edit_responsible_person_name" name="responsible_person_name" class="form-control form-control-sm" required readonly>
                                    <input type="hidden" name="responsible_person" id="edit_responsible_person">
                                </div>
                                <div class="col-md-3">
                                    <label for="edit_res_email" class="form-label">Email</label>
                                    <input type="email" id="edit_res_email" name="res_email" class="form-control form-control-sm" required readonly>
                                </div>
                                <div class="col-md-3">
                                    <label for="edit_res_contact" class="form-label">Contact</label>
                                    <input type="text" id="edit_res_contact" name="res_contact" class="form-control form-control-sm" required readonly>
                                </div>
                                <div class="col-md-3">
                                    <label for="edit_res_department" class="form-label">Department</label>
                                    <input type="text" id="edit_res_department" name="res_department" class="form-control form-control-sm" required readonly>
                                </div>
                                <div class="col-md-3">
                                    <label for="edit_res_designation" class="form-label">Designation</label>
                                    <input type="text" id="edit_res_designation" name="res_designation" class="form-control form-control-sm" required readonly>
                                </div>
                            </div>

                            <h6 class="fw-bold mb-3 text-primary border-bottom pb-2">Other Details</h6>
                            <div class="row g-3 mb-4">
                                <div class="col-md-3">
                                    <label class="form-label d-block">License Creation</label>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="license_creation" id="edit_license_creation_new" value="new" checked>
                                        <label class="form-check-label" for="edit_license_creation_new">New</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="license_creation" id="edit_license_creation_modification" value="modification">
                                        <label class="form-check-label" for="edit_license_creation_modification">Modification</label>
                                    </div>
                                </div>
                                <div class="col-md-3" id="edit_license_creation_remark_section" style="display: none;">
                                    <label for="edit_license_creation_remark" class="form-label">Remark</label>
                                    <textarea class="form-control form-control-sm" id="edit_license_creation_remark" name="license_creation_remark" rows="3" placeholder="Enter remark for modification"></textarea>
                                </div>
                                <div class="col-md-3">
                                    <label for="edit_application_number" class="form-label">Application Number</label>
                                    <input type="text" class="form-control form-control-sm" id="edit_application_number" name="application_number" required>
                                </div>
                                <div class="col-md-3">
                                    <label for="edit_letter_date" class="form-label">Date of Application</label>
                                    <input type="date" class="form-control form-control-sm" id="edit_letter_date" name="letter_date">
                                </div>
                                <div class="col-md-3">
                                    <label for="edit_application_document" class="form-label">Application Document</label>
                                    <div id="edit_application-document-list">
                                        <div class="document-row input-group mb-2">
                                            <input type="text" name="document_name[]" class="form-control form-control-sm" placeholder="Enter document name">
                                            <input type="file" name="application_document[]" class="form-control form-control-sm">
                                            <span class="input-group-text bg-white border-0">
                                                <i class="ri-add-circle-line text-muted fs-5 add-document" role="button" title="Add"></i>
                                            </span>
                                            <span class="input-group-text bg-white border-0" style="display: none;">
                                                <i class="ri-delete-bin-line text-danger fs-5 remove-document" role="button" title="Remove"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <label for="edit_application_status" class="form-label">Application Status</label>
                                    <select class="form-select form-select-sm" id="edit_application_status_select" name="application_status" required>
                                        <option value="">Select</option>
                                        <option value="Submitted">Submitted</option>
                                        <option value="Under review">Under review</option>
                                        <option value="Withdrawn">Withdrawn</option>
                                        <option value="Rejected">Rejected</option>
                                    </select>
                                    <input type="text" class="form-control form-control-sm" id="edit_application_status_input" name="application_status" value="Approved" readonly style="display: none;">
                                </div>

                                <div class="col-md-3">
                                    <label for="edit_date_of_issue" class="form-label">Date of Registration</label>
                                    <input type="date" class="form-control form-control-sm" id="edit_date_of_issue" name="date_of_issue">
                                </div>
                                <div class="col-md-3">
                                    <label for="edit_registration_number" class="form-label">Registration Number</label>
                                    <input type="text" class="form-control form-control-sm" id="edit_registration_number" name="registration_number">
                                </div>
                                <div class="col-md-3">
                                    <label for="edit_certificate_number" class="form-label">Certificate Number</label>
                                    <input type="text" class="form-control form-control-sm" id="edit_certificate_number" name="certificate_number">
                                </div>
                                <div class="col-md-3">
                                    <label for="edit_valid_upto" class="form-label">Valid Upto</label>
                                    <input type="date" class="form-control form-control-sm" id="edit_valid_upto" name="valid_upto" required>
                                </div>
                                <div class="col-md-3">
                                    <label for="edit_lis_status" class="form-label">License Status</label>
                                    <select class="form-select form-select-sm" id="edit_lis_status" name="lis_status" required>
                                        <option value="">Select</option>
                                        <option value="Active">Active</option>
                                        <option value="Deactive">Expired</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Add CC Email</label>
                                    <select class="form-select form-select-sm" id="edit_reminder_option" name="reminder_option" required>
                                        <option value="N">No</option>
                                        <option value="Y">Yes</option>
                                    </select>
                                </div>
                                <div class="col-md-3" id="edit_reminder-email-section" style="display: none;">
                                    <label class="form-label">Reminder Emails</label>
                                    <div id="edit_reminder-email-list">
                                        <div class="input-group mb-2">
                                            <input type="email" name="reminder_emails[]" class="form-control form-control-sm" placeholder="Enter email">
                                            <button type="button" class="btn btn-outline-secondary add-email">Add</button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <h6 class="fw-bold mb-3 text-primary border-bottom pb-2">Requirement Details</h6>
                            <div id="edit_mapped_fields" style="margin-top: 10px;">
                                <!-- Mapped fields will be dynamically inserted here -->
                            </div>

                            <button type="submit" class="btn btn-success">Update</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- View License Modal -->
        <div class="modal fade" id="viewLicenseModal" tabindex="-1" aria-labelledby="viewLicenseModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog modal-lg modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="viewLicenseModalLabel">View License Details</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <h6 class="fw-bold mb-3 text-primary border-bottom pb-2">License Basic Details</h6>
                        <div class="row g-4 mb-4">
                            <div class="col-md-3">
                                <label class="form-label fw-bold">Company Name</label>
                                <p id="view_company_name" class="form-control-static"></p>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold">License Holder Name</label>
                                <p id="view_groupcom_name" class="form-control-static"></p>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold">License Type</label>
                                <p id="view_license_type" class="form-control-static"></p>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold">License Name</label>
                                <p id="view_license_name" class="form-control-static"></p>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold">State</label>
                                <p id="view_state" class="form-control-static"></p>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold">District</label>
                                <p id="view_district" class="form-control-static"></p>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold">City/Village</label>
                                <p id="view_city_village" class="form-control-static"></p>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold">Pincode</label>
                                <p id="view_pincode" class="form-control-static"></p>
                            </div>
                        </div>

                        <h6 class="fw-bold mb-3 text-primary border-bottom pb-2">Authority Person Details</h6>
                        <div class="row g-3 mb-4">
                            <div class="col-md-3">
                                <label class="form-label fw-bold">Authority Name</label>
                                <p id="view_responsible_person_name" class="form-control-static"></p>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold">Email</label>
                                <p id="view_res_email" class="form-control-static"></p>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold">Contact</label>
                                <p id="view_res_contact" class="form-control-static"></p>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold">Department</label>
                                <p id="view_res_department" class="form-control-static"></p>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold">Designation</label>
                                <p id="view_res_designation" class="form-control-static"></p>
                            </div>
                        </div>

                        <h6 class="fw-bold mb-3 text-primary border-bottom pb-2">Other Details</h6>
                        <div class="row g-3 mb-4">
                            <div class="col-md-3">
                                <label class="form-label fw-bold">License Creation</label>
                                <p id="view_license_creation" class="form-control-static"></p>
                            </div>
                            <div class="col-md-3" id="view_license_creation_remark_section" style="display: none;">
                                <label class="form-label fw-bold">Remark</label>
                                <p id="view_license_creation_remark" class="form-control-static"></p>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold">Application Number</label>
                                <p id="view_application_number" class="form-control-static"></p>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold">Date of Application</label>
                                <p id="view_letter_date" class="form-control-static"></p>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold">Application Document</label>
                                <div id="view_application_document" class="form-control-static"></div>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold">Application Status</label>
                                <p id="view_application_status" class="form-control-static"></p>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold">Date of Registration</label>
                                <p id="view_date_of_issue" class="form-control-static"></p>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold">Registration Number</label>
                                <p id="view_registration_number" class="form-control-static"></p>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold">Certificate Number</label>
                                <p id="view_certificate_number" class="form-control-static"></p>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold">Valid Upto</label>
                                <p id="view_valid_upto" class="form-control-static"></p>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold">License Status</label>
                                <p id="view_lis_status" class="form-control-static"></p>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold">Add CC Email</label>
                                <p id="view_reminder_option" class="form-control-static"></p>
                            </div>
                            <div class="col-md-3" id="view_reminder-email-section" style="display: none;">
                                <label class="form-label fw-bold">Reminder Emails</label>
                                <p id="view_reminder_emails" class="form-control-static"></p>
                            </div>
                        </div>

                        <h6 class="fw-bold mb-3 text-primary border-bottom pb-2">Requirement Details</h6>
                        <div id="view_mapped_fields" style="margin-top: 10px;">
                            <!-- Dynamic fields will be inserted here -->
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Renew License Modal -->
        <div class="modal fade" id="renewLicenseModal" tabindex="-1" aria-labelledby="renewLicenseModalLabel"
            aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog modal-xl modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="renewLicenseModalLabel">Renew License</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="renewLicenseForm" action="{{ route('license.renew') }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="license_id" id="renew_license_id">
                            <h6 class="fw-bold mb-3 text-primary border-bottom pb-2">License Basic Details</h6>
                            <div class="row g-3 mb-4">
                                <div class="col-md-4">
                                    <label for="renew_company_id" class="form-label">Company Name</label>
                                    <select class="form-select form-select-sm" id="renew_company_id" name="company_id" required>
                                        <option value="">Select Company</option>
                                        @foreach ($companies as $company)
                                            <option value="{{ $company->id }}">{{ $company->company_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label for="renew_groupcom_id" class="form-label">Company Name/License Holder Name</label>
                                    <select class="form-select form-select-sm" id="renew_groupcom_id" name="groupcom_id">
                                        <option value="">Select License Holder</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label for="renew_license_type_id" class="form-label">License Type</label>
                                    <select class="form-select form-select-sm" id="renew_license_type_id" name="license_type_id" required>
                                        <option value="">Select License Type</option>
                                        @foreach ($licenseTypes as $type)
                                            <option value="{{ $type->id }}">{{ $type->license_type }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label for="renew_license_name_id" class="form-label">License Name</label>
                                    <select class="form-select form-select-sm" id="renew_license_name_id" name="license_name_id" required>
                                        <option value="">Select License Name</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label for="renew_state_id" class="form-label">State</label>
                                    <select class="form-select form-select-sm" id="renew_state_id" name="state_id" required>
                                        <option value="">Select State</option>
                                        @foreach ($states as $state)
                                            <option value="{{ $state->id }}">{{ $state->state_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label for="renew_district_id" class="form-label">District</label>
                                    <select class="form-select form-select-sm" id="renew_district_id" name="district_id" required>
                                        <option value="">Select District</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label for="renew_city_village_id" class="form-label">City/Village</label>
                                    <select class="form-select form-select-sm" id="renew_city_village_id" name="city_village_id" required>
                                        <option value="">Select City/Village</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label for="renew_pincode" class="form-label">Pincode</label>
                                    <input type="text" class="form-control form-control-sm" id="renew_pincode" name="pincode" required readonly>
                                </div>
                            </div>

                            <h6 class="fw-bold mb-3 text-primary border-bottom pb-2">Authority Person Details</h6>
                            <div class="row g-3 mb-4">
                                <div class="col-md-4">
                                    <label for="renew_responsible_person_name" class="form-label">Authority Name</label>
                                    <input type="text" class="form-control form-control-sm" id="renew_responsible_person_name" name="responsible_person_name" required readonly>
                                    <input type="hidden" name="responsible_person" id="renew_responsible_person">
                                </div>
                                <div class="col-md-4">
                                    <label for="renew_res_email" class="form-label">Email</label>
                                    <input type="email" class="form-control form-control-sm" id="renew_res_email" name="res_email" required readonly>
                                </div>
                                <div class="col-md-4">
                                    <label for="renew_res_contact" class="form-label">Contact</label>
                                    <input type="text" class="form-control form-control-sm" id="renew_res_contact" name="res_contact" required readonly>
                                </div>
                                <div class="col-md-4">
                                    <label for="renew_res_department" class="form-label">Department</label>
                                    <input type="text" class="form-control form-control-sm" id="renew_res_department" name="res_department" required readonly>
                                </div>
                                <div class="col-md-4">
                                    <label for="renew_res_designation" class="form-label">Designation</label>
                                    <input type="text" class="form-control form-control-sm" id="renew_res_designation" name="res_designation" required readonly>
                                </div>
                            </div>

                            <h6 class="fw-bold mb-3 text-primary border-bottom pb-2">Other Details</h6>
                            <div class="row g-3 mb-4">
                                <div class="col-md-4">
                                    <label for="renew_application_number" class="form-label">Application Number</label>
                                    <input type="text" class="form-control form-control-sm" id="renew_application_number" name="application_number" required>
                                </div>
                                <div class="col-md-4">
                                    <label for="renew_letter_date" class="form-label">Date of Application</label>
                                    <input type="date" class="form-control form-control-sm" id="renew_letter_date" name="letter_date" required>
                                </div>
                                <div class="col-md-4">
                                    <label for="renew_application_document" class="form-label">Application Document</label>
                                    <div id="renew_application_document_list">
                                        <div class="document-row input-group mb-2">
                                            <input type="text" name="document_name[]" class="form-control form-control-sm" placeholder="Enter document name">
                                            <input type="file" name="application_document[]" class="form-control form-control-sm">
                                            <span class="input-group-text bg-white border-0">
                                                <i class="ri-add-circle-line text-muted fs-5 add-document" role="button" title="Add"></i>
                                            </span>
                                            <span class="input-group-text bg-white border-0" style="display: none;">
                                                <i class="ri-delete-bin-line text-danger fs-5 remove-document" role="button" title="Remove"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label for="renew_date_of_issue" class="form-label">Date of Registration</label>
                                    <input type="date" class="form-control form-control-sm" id="renew_date_of_issue" name="date_of_issue" required>
                                </div>
                                <div class="col-md-4">
                                    <label for="renew_registration_number" class="form-label">Registration Number</label>
                                    <input type="text" class="form-control form-control-sm" id="renew_registration_number" name="registration_number">
                                </div>
                                <div class="col-md-4">
                                    <label for="renew_certificate_number" class="form-label">Certificate Number</label>
                                    <input type="text" class="form-control form-control-sm" id="renew_certificate_number" name="certificate_number">
                                </div>
                                <div class="col-md-4">
                                    <label for="renew_valid_upto" class="form-label">Valid Upto</label>
                                    <input type="date" class="form-control form-control-sm" id="renew_valid_upto" name="valid_upto" required>
                                </div>
                                <div class="col-md-4">
                                    <label for="renew_lis_status" class="form-label">License Status</label>
                                    <select class="form-select form-select-sm" id="renew_lis_status" name="lis_status" required>
                                        <option value="">Select</option>
                                        <option value="Active">Active</option>
                                        <option value="Deactive">Expired</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Add CC Email</label>
                                    <select class="form-select form-select-sm" id="renew_reminder_option" name="reminder_option" required>
                                        <option value="N">No</option>
                                        <option value="Y">Yes</option>
                                    </select>
                                </div>

                                 <div class="col-md-6 mb-3" id="renew_reminder-email-section" style="display: none;">
                                    <label class="form-label">Reminder Emails</label>
                                    <div id="renew_reminder-email-list">
                                        <div class="input-group mb-2">
                                            <input type="email" name="reminder_emails[]" class="form-control form-control-sm"
                                                placeholder="Enter email">
                                            <button type="button"
                                                class="btn btn-outline-secondary add-email">Add</button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <h6 class="fw-bold mb-3 text-primary border-bottom pb-2">Requirement Details</h6>
                            <div id="renew_mapped_fields" style="margin-top: 10px;">
                                <!-- Mapped fields will be dynamically inserted here -->
                            </div>
                            <input type="hidden" name="license_performance" value="renewed">
                            <button type="submit" class="btn btn-success">Renew License</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

       <!-- History License Modal -->
        <div class="modal fade" id="historyLicenseModal" tabindex="-1" aria-labelledby="historyLicenseModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog modal-lg modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="historyLicenseModalLabel">License History</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="table-responsive">
                            <table class="table nowrap dt-responsive align-middle table-hover table-bordered" id="historyLicenseTable" style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Company Name</th>
                                        <th>License Holder Name</th>
                                        <th>License Type</th>
                                        <th>License Name</th>
                                        <th>Valid Upto</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Off-Canvas Panel for More Filters -->
        <div class="offcanvas offcanvas-end" tabindex="-1" id="filterOffcanvas" aria-labelledby="filterOffcanvasLabel">
            <div class="offcanvas-header">
                <h5 class="offcanvas-title" id="filterOffcanvasLabel">More Filters</h5>
                <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body">
                <form id="moreFiltersForm">
                    <div class="mb-3">
                        <label for="company_filter" class="form-label">Filter by Company</label>
                        <select class="form-select form-select-sm" id="company_filter" name="company_filter">
                            <option value="">Select Company</option>
                            @foreach ($companies as $company)
                                <option value="{{ $company->id }}">{{ $company->company_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="license_type_filter" class="form-label">Filter by License Type</label>
                        <select class="form-select form-select-sm" id="license_type_filter" name="license_type_filter">
                            <option value="">Select License Type</option>
                            @foreach ($licenses->groupBy('license_type_id')->sortBy(function($group) {
                                return $group->first()->licenseType->license_type ?? 'N/A';
                            }) as $license)
                                @if ($license->first()->licenseType && $license->first()->license_type_id !== null)
                                    <option value="{{ $license->first()->license_type_id }}">
                                        {{ $license->first()->licenseType->license_type }}
                                    </option>
                                @endif
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="license_name_filter" class="form-label">Filter by License Name</label>
                        <select class="form-select form-select-sm" id="license_name_filter" name="license_name_filter">
                            <option value="">Select License Name</option>
                            @foreach ($licenses->groupBy('license_name_id')->sortBy(function($group) {
                                return $group->first()->licenseName->license_name ?? 'N/A';
                            }) as $license)
                                @if ($license->first()->licenseName)
                                    <option value="{{ $license->first()->license_name_id }}">
                                        {{ $license->first()->licenseName->license_name }}
                                    </option>
                                @endif
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                    <label for="status_filter" class="form-label">Filter by License Status</label>
                    <select class="form-select form-select-sm" id="status_filter" name="status_filter">
                        <option value="">All Statuses</option>
                        <option value="Active">Active</option>
                        <option value="Deactive">Expired</option>
                    </select>
                </div>
                    
                    <div class="d-flex justify-content-end">
                        <button type="button" class="btn btn-primary btn-sm me-2" id="apply_more_filters">
                            <i class="ri-filter-3-line me-1"></i> Apply Filter
                        </button>
                        <button type="button" class="btn btn-secondary btn-sm" id="reset_filters">
                            <i class="ri-restart-line me-1"></i> Reset
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@push('custom-js')
<script>
    $(document).ready(function() {

        flatpickr("#start_date", {
            dateFormat: "Y-m-d",       
            altInput: true,
            altFormat: "d M, Y",       
            onChange: function(selectedDates, dateStr) {
                flatpickr("#end_date", {
                    dateFormat: "Y-m-d",
                    altInput: true,
                    altFormat: "d M, Y",
                });
            }
        });

        flatpickr("#end_date", {
            dateFormat: "Y-m-d",
            altInput: true,
            altFormat: "d M, Y",
        });

        // Initialize DataTable
        let table = $("#licenseTable").DataTable({
            ordering: false,
            searching: true,
            paging: true,
            info: true,
            lengthChange: true,
            pageLength: 10,
            lengthMenu: [10, 25, 50, 100]
        });

        // Set up CSRF token for AJAX requests
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // Custom date range filter
        $.fn.dataTable.ext.search.push(
            function(settings, data, dataIndex) {
                let startDate = $('#start_date').val();
                let endDate = $('#end_date').val();
                let validUpto = data[5]; 
                let companyId = $('#company_filter').val();
                let licenseTypeId = $('#license_type_filter').val();
                let licenseNameId = $('#license_name_filter').val();
                let statusFilter = $('#status_filter').val();

                // Date filter logic
                let dateFilterPass = true;
                if (startDate || endDate) {
                    let validUptoDate = new Date(validUpto);
                    let start = startDate ? new Date(startDate) : null;
                    let end = endDate ? new Date(endDate) : null;
                    if (start && end) {
                        dateFilterPass = validUptoDate >= start && validUptoDate <= end;
                    } else if (start) {
                        dateFilterPass = validUptoDate >= start;
                    } else if (end) {
                        dateFilterPass = validUptoDate <= end;
                    }
                }

                // Company filter logic
                let companyFilterPass = true;
                if (companyId) {
                    let selectedCompanyName = $('#company_filter option[value="' + companyId + '"]').text().trim();
                    let rowCompanyName = data[1].trim(); 
                    companyFilterPass = rowCompanyName === selectedCompanyName;
                }

                // License type filter logic
                let licenseTypeFilterPass = true;
                if (licenseTypeId) {
                    let row = table.row(dataIndex).node();
                    let rowLicenseTypeId = $(row).attr('data-license-type-id') || '';
                    licenseTypeFilterPass = String(rowLicenseTypeId) === String(licenseTypeId);
                }

                // License name filter logic
                let licenseNameFilterPass  = true;
                if (licenseNameId) {
                    let row = table.row(dataIndex).node();
                    let rowLicenseNameId = $(row).attr('data-license-name-id') || '';
                    licenseNameFilterPass = String(rowLicenseNameId) === String(licenseNameId);
                }

                // License status filter logic
                let statusFilterPass = true;
                if (statusFilter) {
                    let rowStatus = data[6].trim(); 
                    statusFilterPass = rowStatus === statusFilter;
                }

                let result = dateFilterPass && companyFilterPass && licenseNameFilterPass && licenseTypeFilterPass && statusFilterPass;
                return result;
            }
        );

        // Apply date filter
        $('#apply_date_filter').on('click', function() {
            let startDate = $('#start_date').val();
            let endDate = $('#end_date').val();

            if (startDate && endDate && new Date(startDate) > new Date(endDate)) {
                alert('Start date cannot be greater than end date.');
                return;
            }
            table.draw();
        });

        // Apply more filters
        $('#apply_more_filters').on('click', function() {
            let companyId = $('#company_filter').val();
            let licenseTypeId = $('#license_type_filter').val();
            let licenseNameId = $('#license_name_filter').val();
            let statusId = $('#status_filter').val();            
            table.draw();
            $('#filterOffcanvas').offcanvas('hide'); 
        });

        // Reset filters
        $('#reset_filters').on('click', function() {
            $('#start_date').val('');
            $('#end_date').val('');
            $('#company_filter').val('');
            $('#license_type_filter').val('');
            $('#license_name_filter').val('');
            $('#status_filter').val('');
            table.draw();
            $('#filterOffcanvas').offcanvas('hide'); 
        });

        // Handle history button click to show modal with DataTable
        $(document).on('click', '.history-btn', function(e) {
            e.preventDefault();
            let licenseTypeId = $(this).data('license-type-id');
            let licenseNameId = $(this).data('license-name-id');

            // Open the history modal
            $('#historyLicenseModal').modal('show');

            // Destroy any existing DataTable instance to avoid conflicts
            if ($.fn.DataTable.isDataTable('#historyLicenseTable')) {
                $('#historyLicenseTable').DataTable().destroy();
            }

            // Fetch history data and populate DataTable
            $.ajax({
                url: '/get-license-history/' + licenseTypeId + '/' + licenseNameId,
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.licenses && response.licenses.length > 0) {
                        // Initialize DataTable with fetched data
                        let historyTable = $('#historyLicenseTable').DataTable({
                            ordering: false,
                            searching: true,
                            paging: true,
                            info: true,
                            lengthChange: true,
                            pageLength: 10,
                            lengthMenu: [5, 10, 25, 50],
                            data: response.licenses,
                            columns: [
                                {
                                    data: null,
                                    render: function(data, type, row, meta) {
                                        return meta.row + 1;
                                    }
                                },
                                {
                                    data: null,
                                    render: function(data, type, row) {
                                        return row.company?.company_name || 'N/A';
                                    }
                                },
                                {
                                    data: null,
                                    render: function(data, type, row) {
                                        return row.groupcom?.name || row.company?.company_name || 'N/A';
                                    }
                                },
                                {
                                    data: null,
                                    render: function(data, type, row) {
                                        return row.licenseType?.license_type || 'N/A';
                                    }
                                },
                                {
                                    data: null,
                                    render: function(data, type, row) {
                                        return row.licenseName?.license_name || 'N/A';
                                    }
                                },
                                { data: 'valid_upto' },
                                { data: 'lis_status' },
                                {
                                    data: null,
                                    render: function(data, type, row) {
                                        return `
                                            <button class="btn btn-sm btn-icon btn-light viewLicenseBtn" data-id="${row.id}" data-bs-toggle="modal" data-bs-target="#viewLicenseModal">
                                                <i class="ri-eye-line text-primary"></i>
                                            </button>`;
                                    }
                                }
                            ],
                            language: {
                                emptyTable: "No history found."
                            }
                        });
                    } else {
                        $('#historyLicenseTable tbody').html('<tr><td colspan="8" class="text-center">No history found.</td></tr>');
                        if ($.fn.DataTable.isDataTable('#historyLicenseTable')) {
                            $('#historyLicenseTable').DataTable().destroy();
                        }
                        $('#historyLicenseTable').DataTable({
                            ordering: false,
                            searching: false,
                            paging: false,
                            info: false,
                            language: {
                                emptyTable: "No history found."
                            }
                        });
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching history:', xhr.responseText);
                    alert('Failed to load license history.');
                    $('#historyLicenseTable tbody').html('<tr><td colspan="8" class="text-center">Failed to load history.</td></tr>');
                    if ($.fn.DataTable.isDataTable('#historyLicenseTable')) {
                        $('#historyLicenseTable').DataTable().destroy();
                    }
                    $('#historyLicenseTable').DataTable({
                        ordering: false,
                        searching: false,
                        paging: false,
                        info: false,
                        language: {
                            emptyTable: "Failed to load history."
                        }
                    });
                }
            });
        });

        // Add document row
        $(document).on('click', '.add-document', function() {
            let documentList = $('#application-document-list');
            let newRow = `
                <div class="document-row input-group mb-2">
                    <input type="text" name="document_name[]" class="form-control form-control-sm" placeholder="Enter document name">
                    <input type="file" name="application_document[]" class="form-control form-control-sm">
                    <span class="input-group-text bg-white border-0">
                        <i class="ri-add-circle-line text-muted fs-5 add-document" role="button" title="Add"></i>
                    </span>
                    <span class="input-group-text bg-white border-0">
                        <i class="ri-delete-bin-line text-danger fs-5 remove-document" role="button" title="Remove"></i>
                    </span>
                </div>`;
            $(this).closest('.document-row').after(newRow);
            updateButtonStates();
        });

        // Remove document row
        $(document).on('click', '.remove-document', function() {
            if ($('#application-document-list .document-row').length > 1) {
                $(this).closest('.document-row').remove();
                updateButtonStates();
            }
        });

        // Update button states for document rows
        function updateButtonStates() {
            let rows = $('#application-document-list .document-row');
            rows.each(function(index) {
                let addButton = $(this).find('.add-document');
                let removeButton = $(this).find('.remove-document');
                if (index === rows.length - 1) {
                    addButton.show();
                    removeButton.show();
                } else {
                    addButton.hide();
                    removeButton.show();
                }
            });
        }
        updateButtonStates();

        // Check if documents are uploaded
        function checkDocumentUploaded() {
            let filesSelected = false;
            $('#application-document-list input[type="file"]').each(function() {
                if ($(this).val()) {
                    filesSelected = true;
                    return false;
                }
            });
            return filesSelected;
        }

        // Toggle application status field
        function toggleApplicationStatus() {
            if (checkDocumentUploaded()) {
                $('#application_status_select').hide().prop('required', false);
                $('#application_status_input').show().prop('required', true);
            } else {
                $('#application_status_select').show().prop('required', true);
                $('#application_status_input').hide().prop('required', false);
            }
        }

        // Initial check on page load
        toggleApplicationStatus();

        // Check on file input change
        $(document).on('change', '#application-document-list input[type="file"]', function() {
            toggleApplicationStatus();
        });

        // Handle form submission
        $('#addLicenseForm').on('submit', function() {
            if (checkDocumentUploaded()) {
                $('#application_status_select').prop('disabled', true);
                $('#application_status_input').prop('disabled', false);
            } else {
                $('#application_status_select').prop('disabled', false);
                $('#application_status_input').prop('disabled', true);
            }
        });

        let fieldGroups = [];

        // Populate license names
        function populateLicenseNames(licenseTypeId, licenseNameSelectId, callback = null) {
            if (!licenseTypeId) {
                $(`#${licenseNameSelectId}`).html('<option value="">Select License Name</option>');
                if (callback) callback();
                return;
            }

            $.ajax({
                url: '/license-names/' + licenseTypeId,
                type: 'GET',
                dataType: 'json',
                beforeSend: function() {
                    $(`#${licenseNameSelectId}`).prop('disabled', true).html('<option value="">Loading...</option>');
                },
                success: function(response) {
                    let licenseNameSelect = $(`#${licenseNameSelectId}`);
                    licenseNameSelect.html('<option value="">Select License Name</option>');
                    if (response.license_names && response.license_names.length > 0) {
                        let hasValidLicense = false;
                        $.each(response.license_names, function(index, name) {
                            $.ajax({
                                url: '/check-license-name-responsible/' + name.id,
                                type: 'GET',
                                async: false,
                                dataType: 'json',
                                success: function(res) {
                                    if (res.exists) {
                                        licenseNameSelect.append('<option value="' + name.id + '">' + name.license_name + '</option>');
                                        hasValidLicense = true;
                                    }
                                }
                            });
                        });
                        if (!hasValidLicense) {
                            alert('License name is not available for authorized person.');
                        }
                    } else {
                        alert('License name is not available for authorized person.');
                    }
                    licenseNameSelect.prop('disabled', false);
                    if (callback) callback();
                },
                error: function() {
                    alert('Failed to load license names.');
                    $(`#${licenseNameSelectId}`).prop('disabled', false).html('<option value="">Select License Name</option>');
                    if (callback) callback();
                }
            });
        }

        // Populate group companies
        function populateGroupCompanies(companyId, selectId, callback, selectedId = null) {
            if (!companyId) {
                $('#' + selectId).html('<option value="">Select License Holder</option>');
                if (callback) callback();
                return;
            }
            $.ajax({
                url: '/license-group-companies?company_id=' + companyId,
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    let options = '<option value="">Select License Holder</option>';
                    $.each(response.groupcom, function(index, groupcom) {
                        let value = groupcom.id || '';
                        options += `<option value="${value}">${groupcom.name}</option>`;
                    });
                    $('#' + selectId).html(options);
                    if (selectedId !== null) {
                        $('#' + selectId).val(selectedId);
                    }
                    if (callback) callback();
                },
                error: function() {
                    $('#' + selectId).html('<option value="">Select License Holder</option>');
                    if (callback) callback();
                }
            });
        }

        // Populate districts
        function populateDistricts(stateId, districtSelectId, cityVillageSelectId, pincodeInputId, selectedDistrictId = null, callback = null) {
            if (!stateId) {
                $(`#${districtSelectId}`).html('<option value="">Select District</option>');
                $(`#${cityVillageSelectId}`).html('<option value="">Select City/Village</option>');
                $(`#${pincodeInputId}`).val('');
                if (callback) callback();
                return;
            }

            $.ajax({
                url: '/get-districts/' + stateId,
                type: 'GET',
                dataType: 'json',
                beforeSend: function() {
                    $(`#${districtSelectId}`).prop('disabled', true).html('<option value="">Loading...</option>');
                    $(`#${cityVillageSelectId}`).prop('disabled', true).html('<option value="">Select City/Village</option>');
                    $(`#${pincodeInputId}`).val('');
                },
                success: function(response) {
                    let districtSelect = $(`#${districtSelectId}`);
                    districtSelect.html('<option value="">Select District</option>');
                    if (response.districts && response.districts.length > 0) {
                        $.each(response.districts, function(index, district) {
                            districtSelect.append('<option value="' + district.id + '">' + district.district_name + '</option>');
                        });
                        if (selectedDistrictId) {
                            districtSelect.val(selectedDistrictId);
                            populateCityVillages(selectedDistrictId, cityVillageSelectId, pincodeInputId, null, callback);
                        } else if (callback) {
                            callback();
                        }
                    } else {
                        alert('No districts available for this state.');
                    }
                    districtSelect.prop('disabled', false);
                },
                error: function(xhr, status, error) {
                    console.log('Error fetching districts:', xhr.status, xhr.responseText, error);
                    alert('Failed to load districts.');
                    $(`#${districtSelectId}`).prop('disabled', false).html('<option value="">Select District</option>');
                    if (callback) callback();
                }
            });
        }

        // Populate city/villages
        function populateCityVillages(districtId, cityVillageSelectId, pincodeInputId, selectedCityVillageId = null, callback = null) {
            if (!districtId) {
                $(`#${cityVillageSelectId}`).html('<option value="">Select City/Village</option>');
                $(`#${pincodeInputId}`).val('');
                if (callback) callback();
                return;
            }

            $.ajax({
                url: '/get-city-villages/' + districtId,
                type: 'GET',
                dataType: 'json',
                beforeSend: function() {
                    $(`#${cityVillageSelectId}`).prop('disabled', true).html('<option value="">Loading...</option>');
                    $(`#${pincodeInputId}`).val('');
                },
                success: function(response) {
                    let cityVillageSelect = $(`#${cityVillageSelectId}`);
                    cityVillageSelect.html('<option value="">Select City/Village</option>');
                    if (response.cityVillages && response.cityVillages.length > 0) {
                        $.each(response.cityVillages, function(index, cityVillage) {
                            cityVillageSelect.append('<option value="' + cityVillage.id + '" data-pincode="' + cityVillage.pincode + '">' + cityVillage.city_village_name + '</option>');
                        });
                        if (selectedCityVillageId) {
                            cityVillageSelect.val(selectedCityVillageId);
                            let selectedOption = cityVillageSelect.find('option:selected');
                            $(`#${pincodeInputId}`).val(selectedOption.data('pincode') || '');
                        }
                    } else {
                        alert('No city/villages available for this district.');
                    }
                    cityVillageSelect.prop('disabled', false);
                    if (callback) callback();
                },
                error: function(xhr, status, error) {
                    console.log('Error fetching city/villages:', xhr.status, xhr.responseText, error);
                    alert('Failed to load city/villages.');
                    $(`#${cityVillageSelectId}`).prop('disabled', false).html('<option value="">Select City/Village</option>');
                    if (callback) callback();
                }
            });
        }

        // Populate responsible details
        function populateResponsibleDetails(licenseTypeId, licenseNameId, modalPrefix = '') {
            $('#' + modalPrefix + 'responsible_person').val('');
            $('#' + modalPrefix + 'responsible_person_name').val('');
            $('#' + modalPrefix + 'res_email').val('');
            $('#' + modalPrefix + 'res_contact').val('');
            $('#' + modalPrefix + 'res_department').val('');
            $('#' + modalPrefix + 'res_designation').val('');

            if (licenseTypeId && licenseNameId) {
                $.ajax({
                    url: '/get-responsible-details',
                    type: 'POST',
                    data: {
                        license_type_id: licenseTypeId,
                        license_name_id: licenseNameId
                    },
                    success: function(response) {
                        if (response.status === 'success' && response.data) {
                            $('#' + modalPrefix + 'responsible_person').val(response.data.emp_id || '');
                            $('#' + modalPrefix + 'responsible_person_name').val(response.data.emp_name || '');
                            $('#' + modalPrefix + 'res_email').val(response.data.emp_email || '');
                            $('#' + modalPrefix + 'res_contact').val(response.data.emp_contact || '');
                            $('#' + modalPrefix + 'res_department').val(response.data.emp_department || '');
                            $('#' + modalPrefix + 'res_designation').val(response.data.emp_designation || '');
                        } else {
                            alert('No authorized person found for this license.');
                        }
                    },
                    error: function() {
                        alert('Failed to load responsible details.');
                    }
                });
            }
        }

        // Load dynamic license fields
        function loadLicenseFields(containerId, fieldGroups, existingData = {}, prefix = '') {
            console.log('loadLicenseFields called with:', { containerId, fieldGroups, existingData, prefix });
            $(`#${containerId}`).html('');
            if (!fieldGroups || fieldGroups.length === 0) {
                $(`#${containerId}`).html('<p>No dynamic fields available.</p>');
                return;
            }

            const isViewMode = prefix === 'view';
            let allHtml = `<div class="accordion" id="${prefix}AccordionFields">`;
            fieldGroups.forEach((group, groupIndex) => {
                const labelKey = `label_${group.label_id}`;
                const rows = (existingData[labelKey] && existingData[labelKey].length > 0) ? existingData[labelKey] : [{}];
                let rowCounter = 0;

                let html = `<div class="accordion-item mb-3">
                    <h2 class="accordion-header" id="${prefix}-heading-${groupIndex}">
                        <button class="accordion-button ${groupIndex === 0 ? '' : 'collapsed'}" type="button"
                            data-bs-toggle="collapse" data-bs-target="#${prefix}-collapse-${groupIndex}"
                            aria-expanded="${groupIndex === 0 ? 'true' : 'false'}" aria-controls="${prefix}-collapse-${groupIndex}">
                            <strong>${group.label_name || 'Unnamed Label'}</strong>
                        </button>
                    </h2>
                    <div id="${prefix}-collapse-${groupIndex}" class="accordion-collapse collapse ${groupIndex === 0 ? 'show' : ''}"
                        aria-labelledby="${prefix}-heading-${groupIndex}" data-bs-parent="#${prefix}AccordionFields">
                        <div class="accordion-body">
                            <div class="field-group" data-group-index="${groupIndex}">`;

                rows.forEach((rowData, index) => {
                    html += `<div class="row field-row" data-row-index="${rowCounter}">`;
                    group.fields.forEach(field => {
                        const inputName = `mapped_fields[label_${group.label_id}][${rowCounter}][field_name_${field.sub_field_id}]`;
                        const fieldValue = rowData[`field_name_${field.sub_field_id}`] || '';

                        let fieldHtml = `<div class="col-md-6 mb-3">
                            <label class="form-label">${field.field_name || 'Unnamed Field'}</label>`;

                        if (isViewMode) {
                            if (field.input_type === 'select' && field.options && field.options.length > 0) {
                                const selectedOption = field.options.find(option => option.value === fieldValue);
                                const displayValue = selectedOption ? selectedOption.label : fieldValue || 'N/A';
                                fieldHtml += `<p class="form-control-static">${displayValue}</p>`;
                            } else if (field.input_type === 'upload') {
                                const displayValue = fieldValue
                                    ? `<a href="/storage/${fieldValue}" target="_blank">${fieldValue.split('/').pop()}</a>`
                                    : 'N/A';
                                fieldHtml += `<p class="form-control-static">${displayValue}</p>`;
                            } else {
                                fieldHtml += `<p class="form-control-static">${fieldValue || 'N/A'}</p>`;
                            }
                        } else {
                            if (field.input_type === 'select' && field.options && field.options.length > 0) {
                                fieldHtml += `<select name="${inputName}" class="form-select form-select-sm" required>
                                    <option value="">Select ${field.field_name || 'Option'}</option>`;
                                field.options.forEach(option => {
                                    const selected = option.value === fieldValue ? 'selected' : '';
                                    fieldHtml += `<option value="${option.value}" ${selected}>${option.label}</option>`;
                                });
                                fieldHtml += `</select>`;
                            } else if (field.input_type === 'date') {
                                fieldHtml += `<input type="date" name="${inputName}" class="form-control form-control-sm" value="${fieldValue}" required>`;
                            } else if (field.input_type === 'upload') {
                                fieldHtml += `<input type="file" name="${inputName}" class="form-control form-control-sm">`;
                                if (fieldValue) {
                                    fieldHtml += `<small class="form-text text-muted">Current file: <a href="/storage/${fieldValue}" target="_blank">${fieldValue.split('/').pop()}</a></small>`;
                                }
                            } else {
                                fieldHtml += `<input type="text" name="${inputName}" class="form-control form-control-sm" value="${fieldValue}" required>`;
                            }
                        }
                        fieldHtml += `</div>`;
                        html += fieldHtml;
                    });

                    if (!isViewMode) {
                        html += `<div class="col-md-12 mb-3 d-flex justify-content-end">
                            <i class="ri-delete-bin-line remove-row text-danger ms-2 cursor-pointer" title="Remove Row"></i>
                        </div>`;
                    }
                    html += `</div>`;
                    rowCounter++;
                });

                html += `</div>`;
                if (!isViewMode) {
                    html += `<div class="d-flex justify-content-end mt-2">
                        <i class="ri-add-line add-row mt-2 text-primary ms-2 cursor-pointer" data-group-index="${groupIndex}" title="Add Row"></i>
                    </div>`;
                }
                html += `</div></div>`;
                allHtml += html;
            });

            allHtml += `</div>`;
            $(`#${containerId}`).html(allHtml);
        }

        // Handle license type change
        $('#license_type_id, #edit_license_type_id, #renew_license_type_id').change(function() {
            let licenseTypeId = $(this).val();
            let modalPrefix = $(this).attr('id').split('_')[0];
            let licenseNameSelectId = modalPrefix === 'license' ? 'license_name_id' : `${modalPrefix}_license_name_id`;
            populateResponsibleDetails('', '', modalPrefix);
            populateLicenseNames(licenseTypeId, licenseNameSelectId, function() {
                let containerId = modalPrefix === 'license' ? 'mapped_fields' : `${modalPrefix}_mapped_fields`;
                $(`#${containerId}`).html('');
                fieldGroups = [];
            });
        });

        // Handle license name change
        $('#license_name_id').change(function() {
            let licenseNameId = $(this).val();
            let licenseTypeId = $('#license_type_id').val();
            let containerId = 'mapped_fields';

            $(`#${containerId}`).html('');
            fieldGroups = [];
            populateResponsibleDetails(licenseTypeId, licenseNameId, '');
            if (licenseNameId) {
                $.ajax({
                    url: '/license-address/' + licenseNameId,
                    type: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        $('#state_id').val(response.state_id || '');
                        populateDistricts(response.state_id, 'district_id', 'city_village_id', 'pincode', response.district_id, function() {
                            populateCityVillages(response.district_id, 'city_village_id', 'pincode', response.city_village_id, function() {
                                $('#pincode').val(response.pincode || '');
                            });
                        });
                    },
                    error: function() {
                        alert('Failed to load address details.');
                        $('#state_id, #district_id, #city_village_id, #pincode').val('');
                    }
                });

                $.ajax({
                    url: '/license-mapped-fields/' + licenseNameId,
                    type: 'GET',
                    dataType: 'json',
                    beforeSend: function() {
                        $(`#${containerId}`).html('<p>Loading fields...</p>');
                    },
                    success: function(response) {
                        fieldGroups = response.fields || [];
                        loadLicenseFields(containerId, fieldGroups, {}, '');
                    },
                    error: function() {
                        alert('Failed to load dynamic fields.');
                        $(`#${containerId}`).html('');
                        fieldGroups = [];
                    }
                });
            } else {
                $('#state_id, #district_id, #city_village_id, #pincode').val('');
                $(`#${containerId}`).html('');
                fieldGroups = [];
            }
        });

        // Handle state change
        $('#state_id, #edit_state_id, #renew_state_id').change(function() {
            let stateId = $(this).val();
            let modalPrefix = $(this).attr('id').split('_')[0];
            let districtSelectId = modalPrefix === 'state' ? 'district_id' : modalPrefix + '_district_id';
            let cityVillageSelectId = modalPrefix === 'state' ? 'city_village_id' : modalPrefix + '_city_village_id';
            let pincodeInputId = modalPrefix === 'state' ? 'pincode' : modalPrefix + '_pincode';
            populateDistricts(stateId, districtSelectId, cityVillageSelectId, pincodeInputId);
        });

        // Handle district change
        $('#district_id, #edit_district_id, #renew_district_id').change(function() {
            let districtId = $(this).val();
            let modalPrefix = $(this).attr('id').split('_')[0];
            let cityVillageSelectId = modalPrefix === 'district' ? 'city_village_id' : modalPrefix + '_city_village_id';
            let pincodeInputId = modalPrefix === 'district' ? 'pincode' : modalPrefix + '_pincode';
            populateCityVillages(districtId, cityVillageSelectId, pincodeInputId);
        });

        // Handle city/village change
        $('#city_village_id, #edit_city_village_id, #renew_city_village_id').change(function() {
            let modalPrefix = $(this).attr('id').split('_')[0];
            let pincodeInputId = modalPrefix === 'city' ? 'pincode' : modalPrefix + '_pincode';
            let selectedOption = $(this).find('option:selected');
            $(`#${pincodeInputId}`).val(selectedOption.data('pincode') || '');
        });

        // Handle reminder option change
        $('#reminder_option, #edit_reminder_option, #renew_reminder_option').change(function() {
            let value = $(this).val();
            let modalPrefix = $(this).attr('id').split('_')[0];
            let sectionId = modalPrefix === 'reminder' ? 'reminder-email-section' : modalPrefix + '_reminder-email-section';
            if (value === 'Y') {
                $(`#${sectionId}`).show();
            } else {
                $(`#${sectionId}`).hide().find('input').val('');
            }
        });

        // Add email input
        $(document).on('click', '.add-email', function() {
            let emailList = $(this).closest('.input-group').parent();
            let newEmailInput = `
                <div class="input-group mb-2">
                    <input type="email" name="reminder_emails[]" class="form-control form-control-sm" placeholder="Enter email">
                    <button type="button" class="btn btn-outline-danger remove-email">Remove</button>
                </div>`;
            emailList.append(newEmailInput);
        });

        // Remove email input
        $(document).on('click', '.remove-email', function() {
            if ($(this).closest('.input-group').parent().find('.input-group').length > 1) {
                $(this).closest('.input-group').remove();
            }
        });

        // Add row for dynamic fields
        $(document).on('click', '.add-row', function() {
            let groupIndex = $(this).data('group-index');
            let group = fieldGroups[groupIndex];
            let container = $(this).closest('.accordion-body').find('.field-group');
            let rowIndex = container.find('.field-row').length;

            let rowHtml = `<div class="row field-row" data-row-index="${rowIndex}">`;
            group.fields.forEach(function(field) {
                const inputName = `mapped_fields[label_${group.label_id}][${rowIndex}][field_name_${field.sub_field_id}]`;
                let fieldHtml = `<div class="col-md-6 mb-3">
                    <label class="form-label">${field.field_name}</label>`;
                if (field.input_type === 'select' && field.options && field.options.length > 0) {
                    fieldHtml += `<select name="${inputName}" class="form-select form-select-sm" required>
                        <option value="">Select ${field.field_name}</option>`;
                    field.options.forEach(function(option) {
                        fieldHtml += `<option value="${option.value}">${option.label}</option>`;
                    });
                    fieldHtml += `</select>`;
                } else if (field.input_type === 'date') {
                    fieldHtml += `<input type="date" name="${inputName}" class="form-control form-control-sm" required>`;
                } else if (field.input_type === 'upload') {
                    fieldHtml += `<input type="file" name="${inputName}" class="form-control form-control-sm">`;
                } else {
                    fieldHtml += `<input type="text" name="${inputName}" class="form-control form-control-sm" required>`;
                }
                fieldHtml += `</div>`;
                rowHtml += fieldHtml;
            });
            rowHtml += `<div class="col-md-12 mb-3 d-flex justify-content-end">
                <i class="ri-delete-bin-line remove-row text-danger ms-2 cursor-pointer" title="Remove Row"></i>
            </div></div>`;
            container.append(rowHtml);
        });

        // Remove row for dynamic fields
        $(document).on('click', '.remove-row', function() {
            if ($(this).closest('.field-group').find('.field-row').length > 1) {
                $(this).closest('.field-row').remove();
            }
        });

        // View License
        $(document).on('click', '.viewLicenseBtn', function() {
            let licenseId = $(this).data('id');
            $.ajax({
                url: '/get-license-details/' + licenseId,
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    let license = response.license;
                    $('#view_company_name').text(license.company?.company_name || 'N/A');
                    $('#view_groupcom_name').text(license.groupcom?.name || license.company?.company_name || 'N/A');
                    $('#view_license_type').text(license.licenseType?.license_type || 'N/A');
                    $('#view_license_name').text(license.licenseName?.license_name || 'N/A');
                    $('#view_state').text(license.state?.state_name || 'N/A');
                    $('#view_district').text(license.district?.district_name || 'N/A');
                    $('#view_city_village').text(license.cityVillage?.city_village_name || 'N/A');
                    $('#view_pincode').text(license.pincode || 'N/A');
                    $('#view_responsible_person').text(license.responsible_person || 'N/A');
                    $('#view_res_email').text(license.res_email || 'N/A');
                    $('#view_res_contact').text(license.res_contact || 'N/A');
                    $('#view_res_department').text(license.res_department || 'N/A');
                    $('#view_res_designation').text(license.res_designation || 'N/A');
                    $('#view_letter_date').text(license.letter_date || 'N/A');
                    $('#view_date_of_issue').text(license.date_of_issue || 'N/A');
                    $('#view_valid_upto').text(license.valid_upto || 'N/A');
                    $('#view_reminder_option').text(license.reminder_option === 'Y' ? 'Yes' : 'No');
                    if (license.reminder_option === 'Y' && license.reminder_emails) {
                        $('#view_reminder_emails').text(license.reminder_emails);
                        $('#view_reminder-email-section').show();
                    } else {
                        $('#view_reminder_emails').text('');
                        $('#view_reminder-email-section').hide();
                    }
                    $('#view_application_number').text(license.application_number || 'N/A');
                    $('#view_application_status').text(license.application_status || 'N/A');
                    $('#view_registration_number').text(license.registration_number || 'N/A');
                    $('#view_certificate_number').text(license.certificate_number || 'N/A');
                    $('#view_lis_status').text(license.lis_status || 'N/A');
                    $('#view_license_creation').remove();
                    $('#view_license_creation_remark').remove();
                    let licenseCreationHtml = `
                        <div class="col-md-6 mb-3">
                            <label class="form-label">License Creation</label>
                            <p id="view_license_creation" class="form-control-static">${license.license_performance || 'New'}</p>
                        </div>`;
                    let remarkHtml = license.license_performance === 'modification' ? `
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Remark</label>
                            <p id="view_license_creation_remark" class="form-control-static">${license.license_creation_remark || 'N/A'}</p>
                        </div>` : '';
                    $('#view_mapped_fields').before(licenseCreationHtml + remarkHtml);

                    $.ajax({
                        url: '/license-mapped-fields/' + license.license_name_id,
                        type: 'GET',
                        dataType: 'json',
                        success: function(response) {
                            fieldGroups = response.fields || [];
                            let existingData = {};

                            let promises = fieldGroups.map(function(group) {
                                return new Promise(function(resolve) {
                                    $.ajax({
                                        url: '/get-label-data/' + license.id + '/label_' + group.label_id,
                                        type: 'GET',
                                        dataType: 'json',
                                        success: function(dataResponse) {
                                            existingData['label_' + group.label_id] = dataResponse.rows || [{}];
                                            resolve();
                                        },
                                        error: function() {
                                            existingData['label_' + group.label_id] = [{}];
                                            resolve();
                                        }
                                    });
                                });
                            });

                            Promise.all(promises).then(function() {
                                loadLicenseFields('view_mapped_fields', fieldGroups, existingData, 'view');
                                $('#view_mapped_fields').find('input, select').prop('disabled', true);
                            });
                        },
                        error: function() {
                            $('#view_mapped_fields').html('');
                            fieldGroups = [];
                        }
                    });
                },
                error: function() {
                    alert('Failed to load license details.');
                }
            });
        });

        // Edit License
        $(document).on('click', '.editLicenseBtn', function() {
            let licenseId = $(this).data('id');
            $('#edit_license_id').val(licenseId);
            $.ajax({
                url: '/get-license-details/' + licenseId,
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    let license = response.license;
                    if (!license) {
                        alert('No license data found.');
                        return;
                    }

                    $('#edit_company_id').val(license.company_id || '');
                    populateGroupCompanies(license.company_id, 'edit_groupcom_id', null, license.groupcom_id || '');
                    $('#edit_license_type_id').val(license.license_type_id || '');
                    populateLicenseNames(license.license_type_id, 'edit_license_name_id', function() {
                        $('#edit_license_name_id').val(license.license_name_id || '');
                    });
                    $('#edit_state_id').val(license.state_id || '');
                    populateDistricts(license.state_id, 'edit_district_id', 'edit_city_village_id', 'edit_pincode', license.district_id, function() {
                        populateCityVillages(license.district_id, 'edit_city_village_id', 'edit_pincode', license.city_village_id);
                    });
                    $('#edit_pincode').val(license.pincode || '');
                    $('#edit_responsible_person').val(license.responsible_person || '');
                    $('#edit_responsible_person_name').val(license.responsible_person || '');
                    $('#edit_res_email').val(license.res_email || '');
                    $('#edit_res_contact').val(license.res_contact || '');
                    $('#edit_res_department').val(license.res_department || '');
                    $('#edit_res_designation').val(license.res_designation || '');
                    $('#edit_application_number').val(license.application_number || '');
                    $('#edit_letter_date').val(license.letter_date || '');
                    $('#edit_date_of_issue').val(license.date_of_issue || '');
                    $('#edit_registration_number').val(license.registration_number || '');
                    $('#edit_certificate_number').val(license.certificate_number || '');
                    $('#edit_valid_upto').val(license.valid_upto || '');
                    $('#edit_lis_status').val(license.lis_status || '');
                    $('#edit_reminder_option').val(license.reminder_option || 'N');

                    let licenseCreation = license.license_performance || 'new';
                    $(`#edit_license_creation_${licenseCreation}`).prop('checked', true);
                    $('#edit_license_performance').val(licenseCreation);
                    if (licenseCreation === 'modification') {
                        $('#edit_license_creation_remark_section').show();
                        $('#edit_license_creation_remark').prop('required', true).val(license.license_creation_remark || '');
                    } else {
                        $('#edit_license_creation_remark_section').hide();
                        $('#edit_license_creation_remark').prop('required', false).val('');
                    }

                    if (license.reminder_option === 'Y' && license.reminder_emails) {
                        $('#edit_reminder-email-section').show();
                        $('#edit_reminder-email-list').html('');
                        let emails = license.reminder_emails.split(',');
                        emails.forEach(function(email, index) {
                            let emailInput = `
                                <div class="input-group mb-2">
                                    <input type="email" name="reminder_emails[]" class="form-control form-control-sm" value="${email.trim()}" placeholder="Enter email">
                                    <button type="button" class="btn btn-outline-${index === 0 ? 'secondary add-email' : 'danger remove-email'}">${index === 0 ? 'Add' : 'Remove'}</button>
                                </div>`;
                            $('#edit_reminder-email-list').append(emailInput);
                        });
                    } else {
                        $('#edit_reminder-email-section').hide();
                        $('#edit_reminder-email-list').html(`
                            <div class="input-group mb-2">
                                <input type="email" name="reminder_emails[]" class="form-control form-control-sm" placeholder="Enter email">
                                <button type="button" class="btn btn-outline-secondary add-email">Add</button>
                            </div>`);
                    }

                    if (license.documents && license.documents.length > 0) {
                        let documentList = $('#edit_application-document-list');
                        documentList.html('');
                        license.documents.forEach(function(doc, index) {
                            let docRow = `
                                <div class="document-row input-group mb-2">
                                    <input type="text" name="document_name[]" class="form-control form-control-sm" value="${doc.document_name || ''}" placeholder="Enter document name">
                                    <input type="file" name="application_document[]" class="form-control form-control-sm">
                                    <span class="input-group-text bg-white border-0">
                                        <i class="ri-add-circle-line text-muted fs-5 add-document" role="button" title="Add"></i>
                                    </span>
                                    <span class="input-group-text bg-white border-0" ${index === 0 ? 'style="display: none;"' : ''}>
                                        <i class="ri-delete-bin-line text-danger fs-5 remove-document" role="button" title="Remove"></i>
                                    </span>
                                    <small class="form-text text-muted">Current file: <a href="/storage/${doc.file_path}" target="_blank">${doc.file_name || 'None'}</a></small>
                                </div>`;
                            documentList.append(docRow);
                        });
                    }

                    $.ajax({
                        url: '/license-mapped-fields/' + license.license_name_id,
                        type: 'GET',
                        dataType: 'json',
                        success: function(response) {
                            fieldGroups = response.fields || [];
                            let existingData = {};

                            let promises = fieldGroups.map(function(group) {
                                return new Promise(function(resolve) {
                                    $.ajax({
                                        url: '/get-label-data/' + license.id + '/label_' + group.label_id,
                                        type: 'GET',
                                        dataType: 'json',
                                        success: function(dataResponse) {
                                            existingData['label_' + group.label_id] = dataResponse.rows || [{}];
                                            resolve();
                                        },
                                        error: function() {
                                            existingData['label_' + group.label_id] = [{}];
                                            resolve();
                                        }
                                    });
                                });
                            });

                            Promise.all(promises).then(function() {
                                loadLicenseFields('edit_mapped_fields', fieldGroups, existingData, 'edit');
                            });
                        },
                        error: function() {
                            $('#edit_mapped_fields').html('<p>Failed to load dynamic fields.</p>');
                            fieldGroups = [];
                            alert('Failed to load dynamic fields.');
                        }
                    });
                },
                error: function() {
                    alert('Failed to load license details.');
                }
            });
        });

        // Renew License
        $(document).on('click', '.renewLicenseBtn', function() {
            let licenseId = $(this).data('id');
            $('#renew_license_id').val(licenseId);
            $.ajax({
                url: '/get-license-details/' + licenseId,
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    let license = response.license;
                    $('#renew_company_id').val(license.company_id || '');
                    populateGroupCompanies(license.company_id, 'renew_groupcom_id', null, license.groupcom_id || '');
                    $('#renew_license_type_id').val(license.license_type_id || '');
                    populateLicenseNames(license.license_type_id, 'renew_license_name_id', function() {
                        $('#renew_license_name_id').val(license.license_name_id || '');
                    });
                    $('#renew_state_id').val(license.state_id || '');
                    populateDistricts(license.state_id, 'renew_district_id', 'renew_city_village_id', 'renew_pincode', license.district_id, function() {
                        populateCityVillages(license.district_id, 'renew_city_village_id', 'renew_pincode', license.city_village_id);
                    });
                    $('#renew_pincode').val(license.pincode || '');
                    $('#renew_responsible_person').val(license.responsible_person || '');
                    $('#renew_responsible_person_name').val(license.responsible_person || '');
                    $('#renew_res_email').val(license.res_email || '');
                    $('#renew_res_contact').val(license.res_contact || '');
                    $('#renew_res_department').val(license.res_department || '');
                    $('#renew_res_designation').val(license.res_designation || '');
                    $('#renew_letter_date').val('');
                    $('#renew_date_of_issue').val('');
                    $('#renew_valid_upto').val('');
                    $('#renew_lis_status').val('Active');
                    $('#renew_reminder_option').val(license.reminder_option || 'N');
                    if (license.reminder_option === 'Y' && license.reminder_emails) {
                        $('#renew_reminder-email-section').show();
                        $('#renew_reminder-email-list').html('');
                        let emails = license.reminder_emails.split(',');
                        emails.forEach(function(email, index) {
                            let emailInput = `
                                <div class="input-group mb-2">
                                    <input type="email" name="reminder_emails[]" class="form-control form-control-sm" value="${email.trim()}" placeholder="Enter email">
                                    <button type="button" class="btn btn-outline-${index === 0 ? 'secondary add-email' : 'danger remove-email'}">${index === 0 ? 'Add' : 'Remove'}</button>
                                </div>`;
                            $('#renew_reminder-email-list').append(emailInput);
                        });
                    } else {
                        $('#renew_reminder-email-section').hide();
                        $('#renew_reminder-email-list').html(`
                            <div class="input-group mb-2">
                                <input type="email" name="reminder_emails[]" class="form-control form-control-sm" placeholder="Enter email">
                                <button type="button" class="btn btn-outline-secondary add-email">Add</button>
                            </div>`);
                    }

                    $.ajax({
                        url: '/license-mapped-fields/' + license.license_name_id,
                        type: 'GET',
                        dataType: 'json',
                        success: function(response) {
                            fieldGroups = response.fields || [];
                            let existingData = {};

                            let promises = fieldGroups.map(function(group) {
                                return new Promise(function(resolve) {
                                    $.ajax({
                                        url: '/get-label-data/' + license.id + '/label_' + group.label_id,
                                        type: 'GET',
                                        dataType: 'json',
                                        success: function(dataResponse) {
                                            existingData['label_' + group.label_id] = dataResponse.rows || [{}];
                                            resolve();
                                        },
                                        error: function() {
                                            existingData['label_' + group.label_id] = [{}];
                                            resolve();
                                        }
                                    });
                                });
                            });

                            Promise.all(promises).then(function() {
                                loadLicenseFields('renew_mapped_fields', fieldGroups, existingData, 'renew');
                            });
                        },
                        error: function() {
                            $('#renew_mapped_fields').html('');
                            fieldGroups = [];
                        }
                    });
                },
                error: function() {
                    alert('Failed to load license details.');
                }
            });
        });

        // Handle company change
        $(document).on("change", "#company_id", function() {
            let companyId = $(this).val();
            populateGroupCompanies(companyId, 'groupcom_id');
        });

        $('#edit_company_id').change(function() {
            let companyId = $(this).val();
            populateGroupCompanies(companyId, 'edit_groupcom_id');
        });

        $('#renew_company_id').change(function() {
            let companyId = $(this).val();
            populateGroupCompanies(companyId, 'renew_groupcom_id');
        });

        // Handle License Creation radio button change for Add License Modal
        $('input[name="license_creation"]').change(function() {
            let value = $(this).val();
            $('#license_performance').val(value);
            if (value === 'modification') {
                $('#license_creation_remark_section').show();
                $('#license_creation_remark').prop('required', true);
            } else {
                $('#license_creation_remark_section').hide();
                $('#license_creation_remark').prop('required', false).val('');
            }
        });

        // Handle License Creation radio button change for Edit License Modal
        $('input[name="license_creation"][id^="edit_"]').change(function() {
            let value = $(this).val();
            $('#edit_license_performance').val(value);
            if (value === 'modification') {
                $('#edit_license_creation_remark_section').show();
                $('#edit_license_creation_remark').prop('required', true);
            } else {
                $('#edit_license_creation_remark_section').hide();
                $('#edit_license_creation_remark').prop('required', false).val('');
            }
        });
    });
</script>
@endpush