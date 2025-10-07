@extends('layouts.app')
@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between bg-galaxy-transparent">
                        <div class="page-title-right ms-auto">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="javascript: void(0);">Masters</a></li>
                                <li class="breadcrumb-item active">{{ ucwords(str_replace('-', ' ', Request::path())) }}</li>
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
                        <h4 class="card-title mb-0 flex-grow-1"><i class="ri-list-unordered"></i> Company List</h4>
                        @can('add-Company')
                            <div class="flex-shrink-0">
                                <button type="button" class="btn btn-primary btn-label waves-effect waves-light rounded-pill"
                                    data-bs-toggle="modal" data-bs-target="#addCompanyModal">
                                    <i class="ri-add-circle-fill label-icon align-middle rounded-pill fs-16 me-2"></i> Add New
                                </button>
                            </div>
                        @endcan
                    </div>
                    <div class="card-body p-4">
                        @if (session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif
                        @if (session('errors'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                @foreach ($errors->all() as $error)
                                    {{ $error }}<br>
                                @endforeach
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif
                        <table class="table nowrap dt-responsive align-middle table-hover table-bordered" style="width: 100%;">
                            <thead>
                                <tr>
                                    <th>Sno</th>
                                    <th>Company Name</th>
                                    <th>Company Code</th>
                                    <th>Registration Number</th>
                                    <th>GST Number</th>
                                    @canany(['view-Company', 'edit-Company', 'delete-Company'])
                                        <th>Actions</th>
                                    @endcanany
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($companies as $index => $company)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $company->company_name }}</td>
                                        <td>{{ $company->company_code }}</td>
                                        <td>{{ $company->registration_number }}</td>
                                        <td>{{ $company->gst_number }}</td>
                                        @canany(['view-Company', 'edit-Company', 'delete-Company'])
                                            <td>
                                                @can('view-Company')
                                                    <button class="btn btn-sm btn-info view-btn" title="View Company"
                                                        data-id="{{ $company->id }}" data-bs-toggle="modal"
                                                        data-bs-target="#viewCompanyModal">
                                                        <i class="ri-eye-line"></i>
                                                    </button>
                                                @endcan
                                                @can('edit-Company')
                                                    <button class="btn btn-sm btn-warning edit-btn" title="Edit Company"
                                                        data-id="{{ $company->id }}" data-bs-toggle="modal"
                                                        data-bs-target="#editCompanyModal">
                                                        <i class="ri-edit-2-line"></i>
                                                    </button>
                                                @endcan
                                                @can('delete-Company')
                                                    <form action="{{ route('company.destroy', $company->id) }}" method="POST"
                                                        style="display:inline;">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger" title="Delete Company"
                                                            onclick="return confirm('Are you sure you want to delete this company?')">
                                                            <i class="ri-delete-bin-line"></i>
                                                        </button>
                                                    </form>
                                                @endcan
                                            </td>
                                        @endcanany
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Add Company Modal -->
        @can('add-Company')
            <div class="modal fade" id="addCompanyModal" tabindex="-1" aria-labelledby="addCompanyModalLabel"
                aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
                <div class="modal-dialog modal-lg modal-dialog-scrollable">
                    <form method="POST" action="{{ route('company.store') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="addCompanyModalLabel"><i class="ri-building-4-line me-2"></i>Add New Company</h5>
                                <button type="button" class="btn-close btn-close-black" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body" style="max-height: 70vh; overflow-y: auto;">
                                <div class="card mb-4">
                                    <div id="companyInfoHeading">
                                        <h5 class="mb-0">
                                            <button class="btn btn-link text-primary text-decoration-none" type="button" data-bs-toggle="collapse"
                                                data-bs-target="#companyInfoCollapse" aria-expanded="true" aria-controls="companyInfoCollapse">
                                                <i class="ri-information-line me-2"></i>Company Information and Documents
                                            </button>
                                        </h5>
                                    </div>
                                    <div id="companyInfoCollapse" class="collapse show" aria-labelledby="companyInfoHeading">
                                        <div class="card-body">
                                            <div class="row g-6">
                                                <div class="col-md-3">
                                                    <label for="company_name" class="form-label fw-bold">Company Name <span class="text-danger">*</span></label>
                                                    <select class="form-select form-select-sm @error('company_name') is-invalid @enderror"
                                                        id="company_name" name="company_name" required>
                                                        <option value="">Select Company</option>
                                                    </select>
                                                    @error('company_name')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <div class="col-md-3">
                                                    <label for="company_code" class="form-label fw-bold">Company Code</label>
                                                    <input type="text" class="form-control form-control-sm @error('company_code') is-invalid @enderror"
                                                        id="company_code" name="company_code" readonly>
                                                    @error('company_code')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <div class="col-md-3">
                                                    <label for="registration_number" class="form-label fw-bold">Registration Number</label>
                                                    <input type="text" class="form-control form-control-sm @error('registration_number') is-invalid @enderror"
                                                        id="registration_number" name="registration_number" readonly>
                                                    @error('registration_number')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <div class="col-md-3">
                                                    <label for="gst_number" class="form-label fw-bold">GST Number</label>
                                                    <input type="text" class="form-control form-control-sm @error('gst_number') is-invalid @enderror"
                                                        id="gst_number" name="gst_number" readonly>
                                                    @error('gst_number')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <div class="col-md-3 mt-3">
                                                    <label for="tin" class="form-label fw-bold">TIN (if applicable)</label>
                                                    <input type="text" class="form-control form-control-sm">
                                                </div>
                                                <div class="col-md-3 mt-3">
                                                    <label for="date_of_incorporation" class="form-label fw-bold">Date of Incorporation</label>
                                                    <input type="date" class="form-control form-control-sm">
                                                </div>
                                                <div class="col-12 mt-3">
                                                    <h6 class="fw-bold">Company Documents</h6>
                                                    <hr class="mt-1 mb-3">
                                                </div>
                                                <div class="col-md-3">
                                                    <label for="certificate_incorporation" class="form-label">Certificate of Incorporation</label>
                                                    <input type="file" class="form-control form-control-sm @error('documents.certificate_incorporation') is-invalid @enderror"
                                                        id="certificate_incorporation" name="documents[certificate_incorporation]" accept=".pdf,.jpg,.png">
                                                    @error('documents.certificate_incorporation')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <div class="col-md-3">
                                                    <label for="company_pan_card" class="form-label">PAN Card</label>
                                                    <input type="file" class="form-control form-control-sm" id="company_pan_card"
                                                        name="documents[company_pan_card]" accept=".pdf,.jpg,.png">
                                                </div>
                                                <div class="col-md-3">
                                                    <label for="aoa" class="form-label" title="Articles of Association">AOA</label>
                                                    <input type="file" class="form-control form-control-sm" id="aoa" name="documents[aoa]"
                                                        accept=".pdf,.jpg,.png">
                                                </div>
                                                <div class="col-md-3">
                                                    <label for="moa" class="form-label" title="Memorandum of Association">MOA</label>
                                                    <input type="file" class="form-control form-control-sm" id="moa" name="documents[moa]"
                                                        accept=".pdf,.jpg,.png">
                                                </div>
                                                <div class="col-md-3 mt-3">
                                                    <label for="gst_certificate" class="form-label">GST Certificate</label>
                                                    <input type="file" class="form-control form-control-sm" id="gst_certificate"
                                                        name="documents[gst_certificate]" accept=".pdf,.jpg,.png">
                                                </div>
                                                <div class="col-md-3 mt-3">
                                                    <label for="board_resolution" class="form-label" title="Board Resolution for Authorized Signatory">Board Resolution</label>
                                                    <input type="file" class="form-control form-control-sm" id="board_resolution"
                                                        name="documents[board_resolution]" accept=".pdf,.jpg,.png">
                                                </div>
                                                <div class="col-md-3 mt-3">
                                                    <label for="signature_specimen" class="form-label">Signature Specimen</label>
                                                    <input type="file" class="form-control form-control-sm" id="signature_specimen"
                                                        name="documents[signature_specimen]" accept=".pdf,.jpg,.png">
                                                </div>
                                                <div class="col-md-3 mt-3">
                                                    <label for="other_docs" class="form-label">Any Other Relevant Docs</label>
                                                    <div class="side-by-side-inputs">
                                                        <input type="text" class="form-control form-control-sm" id="other_docs_name"
                                                            name="documents[other_docs_name]" placeholder="Document Name" title="Write Document Name">
                                                        <input type="file" class="form-control form-control-sm" id="other_docs"
                                                            name="documents[other_docs]" accept=".pdf,.jpg,.png">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="card mb-4">
                                        <div class="card-header" id="directorInfoHeading">
                                            <h5 class="mb-0">
                                                <button class="btn btn-link text-primary text-decoration-none" type="button" data-bs-toggle="collapse"
                                                    data-bs-target="#directorInfoCollapse" aria-expanded="true" aria-controls="directorInfoCollapse">
                                                    <i class="ri-user-3-line me-2"></i>Director Details and Documents
                                                </button>
                                            </h5>
                                        </div>
                                        <div id="directorInfoCollapse" class="collapse show" aria-labelledby="directorInfoHeading">
                                            <div class="card-body">
                                                <div class="director-details">
                                                    <div class="row g-3 director-row border-bottom pb-3 mb-3" data-index="0">
                                                        <div class="col-12">
                                                            <h6 class="fw-bold">Director 1</h6>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <label for="director_name_0" class="form-label fw-bold">Name of Director <span class="text-danger">*</span></label>
                                                            <input type="text" name="directors[0][name]" class="form-control form-control-sm @error('directors.0.name') is-invalid @enderror"
                                                                id="director_name_0" required>
                                                            @error('directors.0.name')
                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                        <div class="col-md-3">
                                                            <label for="director_designation_0" class="form-label fw-bold">Designation / Role <span class="text-danger">*</span></label>
                                                            <input type="text" name="directors[0][designation]" class="form-control form-control-sm @error('directors.0.designation') is-invalid @enderror"
                                                                id="director_designation_0" required>
                                                            @error('directors.0.designation')
                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                        <div class="col-md-3">
                                                            <label for="director_din_0" class="form-label fw-bold">DIN</label>
                                                            <input type="text" name="directors[0][din]" class="form-control form-control-sm @error('directors.0.din') is-invalid @enderror"
                                                                id="director_din_0">
                                                            @error('directors.0.din')
                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                        <div class="col-md-3">
                                                            <label for="director_pan_0" class="form-label fw-bold">PAN</label>
                                                            <input type="text" name="directors[0][pan]" class="form-control form-control-sm @error('directors.0.pan') is-invalid @enderror"
                                                                id="director_pan_0">
                                                            @error('directors.0.pan')
                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                        <div class="col-md-3">
                                                            <label for="director_aadhaar_0" class="form-label fw-bold">Aadhaar (if applicable)</label>
                                                            <input type="text" name="directors[0][aadhaar]" class="form-control form-control-sm @error('directors.0.aadhaar') is-invalid @enderror"
                                                                id="director_aadhaar_0">
                                                            @error('directors.0.aadhaar')
                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                        <div class="col-md-3">
                                                            <label for="director_contact_0" class="form-label fw-bold">Contact Number</label>
                                                            <input type="text" name="directors[0][contact_number]" class="form-control form-control-sm @error('directors.0.contact_number') is-invalid @enderror"
                                                                id="director_contact_0">
                                                            @error('directors.0.contact_number')
                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                        <div class="col-md-3">
                                                            <label for="director_email_0" class="form-label fw-bold">Email ID</label>
                                                            <input type="email" name="directors[0][email]" class="form-control form-control-sm @error('directors.0.email') is-invalid @enderror"
                                                                id="director_email_0">
                                                            @error('directors.0.email')
                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                        <div class="col-md-3">
                                                            <label for="director_appointment_date_0" class="form-label fw-bold">Date of Appointment</label>
                                                            <input type="date" name="directors[0][appointment_date]" class="form-control form-control-sm @error('directors.0.appointment_date') is-invalid @enderror"
                                                                id="director_appointment_date_0">
                                                            @error('directors.0.appointment_date')
                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                        <div class="col-md-3">
                                                            <label for="director_resignation_date_0" class="form-label fw-bold">Resignation Date</label>
                                                            <input type="date" name="directors[0][resignation_date]" class="form-control form-control-sm @error('directors.0.resignation_date') is-invalid @enderror"
                                                                id="director_resignation_date_0">
                                                            @error('directors.0.resignation_date')
                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                        <div class="col-12 mt-3">
                                                            <h6 class="fw-bold">Director Documents</h6>
                                                            <hr class="mt-1 mb-4">
                                                        </div>
                                                        <div class="col-md-3">
                                                            <label for="director_aadhar_doc_0" class="form-label">Aadhar Document</label>
                                                            <input type="file" class="form-control form-control-sm @error('directors.0.documents.aadhar_doc') is-invalid @enderror"
                                                                id="director_aadhar_doc_0" name="directors[0][documents][aadhar_doc]" accept=".pdf,.jpg,.png">
                                                            @error('directors.0.documents.aadhar_doc')
                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                        <div class="col-md-3">
                                                            <label for="director_pan_doc_0" class="form-label">PAN Document</label>
                                                            <input type="file" class="form-control form-control-sm @error('directors.0.documents.pan_doc') is-invalid @enderror"
                                                                id="director_pan_doc_0" name="directors[0][documents][pan_doc]" accept=".pdf,.jpg,.png">
                                                            @error('directors.0.documents.pan_doc')
                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                        <div class="col-md-3">
                                                            <label for="director_passport_doc_0" class="form-label">Passport</label>
                                                            <input type="file" class="form-control form-control-sm @error('directors.0.documents.passport_doc') is-invalid @enderror"
                                                                id="director_passport_doc_0" name="directors[0][documents][passport_doc]" accept=".pdf,.jpg,.png">
                                                            @error('directors.0.documents.passport_doc')
                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                        <div class="col-md-3">
                                                            <label for="director_driving_license_doc_0" class="form-label">Driving License</label>
                                                            <input type="file" class="form-control form-control-sm @error('directors.0.documents.driving_license_doc') is-invalid @enderror"
                                                                id="director_driving_license_doc_0" name="directors[0][documents][driving_license_doc]" accept=".pdf,.jpg,.png">
                                                            @error('directors.0.documents.driving_license_doc')
                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                        <div class="col-md-3">
                                                            <label for="director_bank_passbook_doc_0" class="form-label">Bank Passbook</label>
                                                            <input type="file" class="form-control form-control-sm @error('directors.0.documents.bank_passbook_doc') is-invalid @enderror"
                                                                id="director_bank_passbook_doc_0" name="directors[0][documents][bank_passbook_doc]" accept=".pdf,.jpg,.png">
                                                            @error('directors.0.documents.bank_passbook_doc')
                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                        <div class="col-md-3 d-flex align-items-end">
                                                            <a href="javascript:void(0)" class="remove-director-row w-100 text-danger" title="Remove Director">
                                                                <i class="ri-delete-bin-line me-1"></i> Remove Director
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                                <a href="javascript:void(0)" id="addDirectorRow" class="text-primary mt-3">
                                                    <i class="ri-add-circle-fill me-1"></i> Add Another Director
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                    <button type="submit" class="btn btn-primary">Save Company</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endcan

        <!-- View Company Modal -->
        @can('view-Company')
            <div class="modal fade" id="viewCompanyModal" tabindex="-1" aria-labelledby="viewCompanyModalLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-scrollable">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="viewCompanyModalLabel"><i class="ri-building-4-line me-2"></i>View Company Details</h5>
                            <button type="button" class="btn-close btn-close-black" data-bs-dismiss=    "modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="card mb-4">
                                <div class="card-header bg-light">
                                    <h7 class="mb-0"><i class="ri-information-line me-2"></i>Company Information and Documents</h7>
                                </div>
                                <div class="card-body">
                                    <div class="row g-6">
                                        <div class="col-md-3">
                                            <label class="form-label fw-bold">Company Name</label>
                                            <p id="view_company_name" class="form-control-static"></p>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label fw-bold">Company Code</label>
                                            <p id="view_company_code" class="form-control-static"></p>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label fw-bold">Registration Number</label>
                                            <p id="view_registration_number" class="form-control-static"></p>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label fw-bold">GST Number</label>
                                            <p id="view_gst_number" class="form-control-static"></p>
                                        </div>
                                        <div class="col-12 mt-3">
                                            <h6 class="fw-bold">Company Documents</h6>
                                            <hr class="mt-1 mb-4">
                                        </div>
                                        <div id="view_company_documents" class="row g-3"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="card mb-4">
                                <div class="card-header bg-light">
                                    <h7 class="mb-0"><i class="ri-user-3-line me-2"></i>Director Details and Documents</h7>
                                </div>
                                <div class="card-body">
                                    <div id="view_director_details"></div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>
        @endcan

        <!-- Edit Company Modal -->
        @can('edit-Company')
            <div class="modal fade" id="editCompanyModal" tabindex="-1" aria-labelledby="editCompanyModalLabel"
                aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
                <div class="modal-dialog modal-lg modal-dialog-scrollable">
                    <form method="POST" action="" id="editCompanyForm" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="editCompanyModalLabel"><i class="ri-edit-2-line me-2"></i>Edit Company</h5>
                                <button type="button" class="btn-close btn-close-black" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body" style="max-height: 70vh; overflow-y: auto;">
                                <div class="card mb-4">
                                    <div id="editCompanyInfoHeading">
                                        <h5 class="mb-0">
                                            <button class="btn btn-link text-primary text-decoration-none" type="button" data-bs-toggle="collapse"
                                                    data-bs-target="#editCompanyInfoCollapse" aria-expanded="true" aria-controls="editCompanyInfoCollapse">
                                                <i class="ri-information-line me-2"></i>Company Information and Documents
                                            </button>
                                        </h5>
                                    </div>
                                    <div id="editCompanyInfoCollapse" class="collapse show" aria-labelledby="editCompanyInfoHeading">
                                        <div class="card-body">
                                            <div class="row g-6">
                                                <div class="col-md-3">
                                                    <label for="edit_company_name" class="form-label fw-bold">Company Name <span class="text-danger">*</span></label>
                                                    <input type="text" class="form-control form-control-sm @error('company_name') is-invalid @enderror"
                                                        id="edit_company_name" name="company_name" required>
                                                    @error('company_name')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <div class="col-md-3">
                                                    <label for="edit_company_code" class="form-label fw-bold">Company Code</label>
                                                    <input type="text" class="form-control form-control-sm @error('company_code') is-invalid @enderror"
                                                        id="edit_company_code" name="company_code" readonly>
                                                    @error('company_code')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <div class="col-md-3">
                                                    <label for="edit_registration_number" class="form-label fw-bold">Registration Number</label>
                                                    <input type="text" class="form-control form-control-sm @error('registration_number') is-invalid @enderror"
                                                        id="edit_registration_number" name="registration_number" readonly>
                                                    @error('registration_number')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <div class="col-md-3">
                                                    <label for="edit_gst_number" class="form-label fw-bold">GST Number</label>
                                                    <input type="text" class="form-control form-control-sm @error('gst_number') is-invalid @enderror"
                                                        id="edit_gst_number" name="gst_number" readonly>
                                                    @error('gst_number')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <div class="col-md-3 mt-3">
                                                    <label for="edit_tin" class="form-label fw-bold">TIN (if applicable)</label>
                                                    <input type="text" class="form-control form-control-sm" id="edit_tin" name="tin">
                                                </div>
                                                <div class="col-md-3 mt-3">
                                                    <label for="edit_date_of_incorporation" class="form-label fw-bold">Date of Incorporation</label>
                                                    <input type="date" class="form-control form-control-sm" id="edit_date_of_incorporation" name="date_of_incorporation">
                                                </div>
                                                <div class="col-12 mt-3">
                                                    <h6 class="fw-bold">Company Documents</h6>
                                                    <hr class="mt-1 mb-3">
                                                </div>
                                                <div class="col-md-3">
                                                    <label for="edit_certificate_incorporation" class="form-label">Certificate of Incorporation</label>
                                                    <input type="file" class="form-control form-control-sm @error('documents.certificate_incorporation') is-invalid @enderror"
                                                        id="edit_certificate_incorporation" name="documents[certificate_incorporation]" accept=".pdf,.jpg,.png">
                                                    @error('documents.certificate_incorporation')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                    <div id="existing_certificate_incorporation" class="existing-document mt-2"></div>
                                                </div>
                                                <div class="col-md-3">
                                                    <label for="edit_company_pan_card" class="form-label">PAN Card</label>
                                                    <input type="file" class="form-control form-control-sm" id="edit_company_pan_card"
                                                        name="documents[company_pan_card]" accept=".pdf,.jpg,.png">
                                                    <div id="existing_company_pan_card" class="existing-document mt-2"></div>
                                                </div>
                                                <div class="col-md-3">
                                                    <label for="edit_aoa" class="form-label" title="Articles of Association">AOA</label>
                                                    <input type="file" class="form-control form-control-sm" id="edit_aoa" name="documents[aoa]"
                                                        accept=".pdf,.jpg,.png">
                                                    <div id="existing_aoa" class="existing-document mt-2"></div>
                                                </div>
                                                <div class="col-md-3">
                                                    <label for="edit_moa" class="form-label" title="Memorandum of Association">MOA</label>
                                                    <input type="file" class="form-control form-control-sm" id="edit_moa" name="documents[moa]"
                                                        accept=".pdf,.jpg,.png">
                                                    <div id="existing_moa" class="existing-document mt-2"></div>
                                                </div>
                                                <div class="col-md-3 mt-3">
                                                    <label for="edit_gst_certificate" class="form-label">GST Certificate</label>
                                                    <input type="file" class="form-control form-control-sm" id="edit_gst_certificate"
                                                        name="documents[gst_certificate]" accept=".pdf,.jpg,.png">
                                                    <div id="existing_gst_certificate" class="existing-document mt-2"></div>
                                                </div>
                                                <div class="col-md-3 mt-3">
                                                    <label for="edit_board_resolution" class="form-label" title="Board Resolution for Authorized Signatory">Board Resolution</label>
                                                    <input type="file" class="form-control form-control-sm" id="edit_board_resolution"
                                                        name="documents[board_resolution]" accept=".pdf,.jpg,.png">
                                                    <div id="existing_board_resolution" class="existing-document mt-2"></div>
                                                </div>
                                                <div class="col-md-3 mt-3">
                                                    <label for="edit_signature_specimen" class="form-label">Signature Specimen</label>
                                                    <input type="file" class="form-control form-control-sm" id="edit_signature_specimen"
                                                        name="documents[signature_specimen]" accept=".pdf,.jpg,.png">
                                                    <div id="existing_signature_specimen" class="existing-document mt-2"></div>
                                                </div>
                                                <div class="col-md-3 mt-3">
                                                    <label for="edit_other_docs" class="form-label">Any Other Relevant Docs</label>
                                                    <div class="side-by-side-inputs">
                                                        <input type="text" class="form-control form-control-sm" id="edit_other_docs_name"
                                                            name="documents[other_docs_name]" placeholder="Document Name" title="Write Document Name">
                                                        <input type="file" class="form-control form-control-sm" id="edit_other_docs"
                                                            name="documents[other_docs]" accept=".pdf,.jpg,.png">
                                                    </div>
                                                    <div id="existing_other_docs" class="existing-document mt-2"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="card mb-4">
                                    <div class="card-header" id="editDirectorInfoHeading">
                                        <h5 class="mb-0">
                                            <button class="btn btn-link text-primary text-decoration-none" type="button" data-bs-toggle="collapse"
                                                    data-bs-target="#editDirectorInfoCollapse" aria-expanded="true" aria-controls="editDirectorInfoCollapse">
                                                <i class="ri-user-3-line me-2"></i>Director Details and Documents
                                            </button>
                                        </h5>
                                    </div>
                                    <div id="editDirectorInfoCollapse" class="collapse show" aria-labelledby="editDirectorInfoHeading">
                                        <div class="card-body">
                                            <div class="director-details">
                                                <div class="row g-6 director-row border-bottom pb-3 mb-3" data-index="0">
                                                    <div class="col-12">
                                                        <h6 class="fw-bold">Director 1</h6>
                                                    </div>
                                                    <div class="col-md-3 mt-3">
                                                        <label for="edit_director_name_0" class="form-label fw-bold">Name of Director <span class="text-danger">*</span></label>
                                                        <input type="text" name="directors[0][name]" class="form-control form-control-sm @error('directors.0.name') is-invalid @enderror"
                                                            id="edit_director_name_0" required>
                                                        @error('directors.0.name')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                    <div class="col-md-3 mt-3">
                                                        <label for="edit_director_designation_0" class="form-label fw-bold">Designation / Role <span class="text-danger">*</span></label>
                                                        <input type="text" name="directors[0][designation]" class="form-control form-control-sm @error('directors.0.designation') is-invalid @enderror"
                                                            id="edit_director_designation_0" required>
                                                        @error('directors.0.designation')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                    <div class="col-md-3 mt-3">
                                                        <label for="edit_director_din_0" class="form-label fw-bold">DIN</label>
                                                        <input type="text" name="directors[0][din]" class="form-control form-control-sm @error('directors.0.din') is-invalid @enderror"
                                                            id="edit_director_din_0">
                                                        @error('directors.0.din')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                    <div class="col-md-3 mt-3">
                                                        <label for="edit_director_pan_0" class="form-label fw-bold">PAN</label>
                                                        <input type="text" name="directors[0][pan]" class="form-control form-control-sm @error('directors.0.pan') is-invalid @enderror"
                                                            id="edit_director_pan_0">
                                                        @error('directors.0.pan')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                    <div class="col-md-3 mt-3">
                                                        <label for="edit_director_aadhaar_0" class="form-label fw-bold">Aadhaar (if applicable)</label>
                                                        <input type="text" name="directors[0][aadhaar]" class="form-control form-control-sm @error('directors.0.aadhaar') is-invalid @enderror"
                                                            id="edit_director_aadhaar_0">
                                                        @error('directors.0.aadhaar')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                    <div class="col-md-3 mt-3">
                                                        <label for="edit_director_contact_0" class="form-label fw-bold">Contact Number</label>
                                                        <input type="text" name="directors[0][contact_number]" class="form-control form-control-sm @error('directors.0.contact_number') is-invalid @enderror"
                                                            id="edit_director_contact_0">
                                                        @error('directors.0.contact_number')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                    <div class="col-md-3 mt-3">
                                                        <label for="edit_director_email_0" class="form-label fw-bold">Email ID</label>
                                                        <input type="email" name="directors[0][email]" class="form-control form-control-sm @error('directors.0.email') is-invalid @enderror"
                                                            id="edit_director_email_0">
                                                        @error('directors.0.email')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                    <div class="col-md-3 mt-3">
                                                        <label for="edit_director_appointment_date_0" class="form-label fw-bold">Date of Appointment</label>
                                                        <input type="date" name="directors[0][appointment_date]" class="form-control form-control-sm @error('directors.0.appointment_date') is-invalid @enderror"
                                                            id="edit_director_appointment_date_0">
                                                        @error('directors.0.appointment_date')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                    <div class="col-md-3 mt-3">
                                                        <label for="edit_director_resignation_date_0" class="form-label fw-bold">Resignation Date</label>
                                                        <input type="date" name="directors[0][resignation_date]" class="form-control form-control-sm @error('directors.0.resignation_date') is-invalid @enderror"
                                                            id="edit_director_resignation_date_0">
                                                        @error('directors.0.resignation_date')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                    <div class="col-12 mt-3">
                                                        <h6 class="fw-bold">Director Documents</h6>
                                                        <hr class="mt-1 mb-4">
                                                    </div>
                                                    <div class="col-md-3 mt-3">
                                                        <label for="edit_director_aadhar_doc_0" class="form-label">Aadhar Document</label>
                                                        <input type="file" class="form-control form-control-sm @error('directors.0.documents.aadhar_doc') is-invalid @enderror"
                                                            id="edit_director_aadhar_doc_0" name="directors[0][documents][aadhar_doc]" accept=".pdf,.jpg,.png">
                                                        @error('directors.0.documents.aadhar_doc')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                        <div id="existing_director_aadhar_doc_0" class="existing-document mt-2"></div>
                                                    </div>
                                                    <div class="col-md-3 mt-3">
                                                        <label for="edit_director_pan_doc_0" class="form-label">PAN Document</label>
                                                        <input type="file" class="form-control form-control-sm @error('directors.0.documents.pan_doc') is-invalid @enderror"
                                                            id="edit_director_pan_doc_0" name="directors[0][documents][pan_doc]" accept=".pdf,.jpg,.png">
                                                        @error('directors.0.documents.pan_doc')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                        <div id="existing_director_pan_doc_0" class="existing-document mt-2"></div>
                                                    </div>
                                                    <div class="col-md-3 mt-3">
                                                        <label for="edit_director_passport_doc_0" class="form-label">Passport</label>
                                                        <input type="file" class="form-control form-control-sm @error('directors.0.documents.passport_doc') is-invalid @enderror"
                                                            id="edit_director_passport_doc_0" name="directors[0][documents][passport_doc]" accept=".pdf,.jpg,.png">
                                                        @error('directors.0.documents.passport_doc')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                        <div id="existing_director_passport_doc_0" class="existing-document mt-2"></div>
                                                    </div>
                                                    <div class="col-md-3 mt-3">
                                                        <label for="edit_director_driving_license_doc_0" class="form-label">Driving License</label>
                                                        <input type="file" class="form-control form-control-sm @error('directors.0.documents.driving_license_doc') is-invalid @enderror"
                                                            id="edit_director_driving_license_doc_0" name="directors[0][documents][driving_license_doc]" accept=".pdf,.jpg,.png">
                                                        @error('directors.0.documents.driving_license_doc')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                        <div id="existing_director_driving_license_doc_0" class="existing-document mt-2"></div>
                                                    </div>
                                                    <div class="col-md-3 mt-3">
                                                        <label for="edit_director_bank_passbook_doc_0" class="form-label">Bank Passbook</label>
                                                        <input type="file" class="form-control form-control-sm @error('directors.0.documents.bank_passbook_doc') is-invalid @enderror"
                                                            id="edit_director_bank_passbook_doc_0" name="directors[0][documents][bank_passbook_doc]" accept=".pdf,.jpg,.png">
                                                        @error('directors.0.documents.bank_passbook_doc')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                        <div id="existing_director_bank_passbook_doc_0" class="existing-document mt-2"></div>
                                                    </div>
                                                    <div class="col-md-3 d-flex align-items-end">
                                                        <a href="javascript:void(0)" class="remove-director-row w-100 text-danger" title="Remove Director">
                                                            <i class="ri-delete-bin-line me-1"></i> Remove Director
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                            <a href="javascript:void(0)" id="editAddDirectorRow" class="text-primary mt-3">
                                                <i class="ri-add-circle-fill me-1"></i> Add Another Director
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-primary">Update Company</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        @endcan
 
    </div>
@endsection

@push('custom-js')
    <script>
        $(document).ready(function() {
            // Initialize DataTables
            $('.table').DataTable({
                responsive: true,
                pageLength: 10,
                order: [[0, 'asc']],
                language: {
                    search: "_INPUT_",
                    searchPlaceholder: "Search companies..."
                }
            });

            // Prevent page scroll when modal is open
            $('#addCompanyModal, #viewCompanyModal, #editCompanyModal').on('show.bs.modal', function() {
                $('body').addClass('modal-open');
            }).on('hide.bs.modal', function() {
                $('body').removeClass('modal-open');
            });

            // Fetch companies for dropdown
            $.ajax({
                url: '{{ route('company.fetch_companies') }}',
                method: 'GET',
                dataType: 'json',
                success: function(data) {
                    const select = $('#company_name');
                    if (data.error) {
                        alert('Error: ' + data.error);
                        return;
                    }
                    select.empty().append('<option value="">Select Company</option>');
                    data.forEach(company => {
                        if (company.company_name) {
                            select.append(
                                `<option value="${company.company_name}" 
                                         data-code="${company.company_code || ''}" 
                                         data-reg="${company.registration_number || ''}" 
                                         data-gst="${company.gst_number || ''}">
                                    ${company.company_name}
                                </option>`
                            );
                        }
                    });
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching companies:', error);
                    alert('Failed to load company data. Please try again.');
                }
            });

            // Populate company details on selection
            $('#company_name').change(function() {
                const selectedOption = $(this).find('option:selected');
                $('#company_code').val(selectedOption.data('code') || '');
                $('#registration_number').val(selectedOption.data('reg') || '');
                $('#gst_number').val(selectedOption.data('gst') || '');
            });

            // Add director row in Add Company Modal
            let addDirectorIndex = 1;
            $('#addDirectorRow').click(function(e) {
                e.preventDefault();
                $('.director-details').append(`
                    <div class="row g-6 director-row border-bottom pb-3 mb-3" data-index="${addDirectorIndex}">
                        <div class="col-12">
                            <h6 class="fw-bold">Director ${addDirectorIndex + 1}</h6>
                        </div>
                        <div class="col-md-3 mt-3">
                            <label for="director_name_${addDirectorIndex}" class="form-label fw-bold">Name of Director <span class="text-danger">*</span></label>
                            <input type="text" name="directors[${addDirectorIndex}][name]" class="form-control form-control-sm"
                                id="director_name_${addDirectorIndex}" required>
                        </div>
                        <div class="col-md-3 mt-3">
                            <label for="director_designation_${addDirectorIndex}" class="form-label fw-bold">Designation / Role <span class="text-danger">*</span></label>
                            <input type="text" name="directors[${addDirectorIndex}][designation]" class="form-control form-control-sm"
                                id="director_designation_${addDirectorIndex}" required>
                        </div>
                        <div class="col-md-3 mt-3">
                            <label for="director_din_${addDirectorIndex}" class="form-label fw-bold">DIN</label>
                            <input type="text" name="directors[${addDirectorIndex}][din]" class="form-control form-control-sm"
                                id="director_din_${addDirectorIndex}">
                        </div>
                        <div class="col-md-3 mt-3">
                            <label for="director_pan_${addDirectorIndex}" class="form-label fw-bold">PAN</label>
                            <input type="text" name="directors[${addDirectorIndex}][pan]" class="form-control form-control-sm"
                                id="director_pan_${addDirectorIndex}">
                        </div>
                        <div class="col-md-3 mt-3">
                            <label for="director_aadhaar_${addDirectorIndex}" class="form-label fw-bold">Aadhaar (if applicable)</label>
                            <input type="text" name="directors[${addDirectorIndex}][aadhaar]" class="form-control form-control-sm"
                                id="director_aadhaar_${addDirectorIndex}">
                        </div>
                        <div class="col-md-3 mt-3">
                            <label for="director_contact_${addDirectorIndex}" class="form-label fw-bold">Contact Number</label>
                            <input type="text" name="directors[${addDirectorIndex}][contact_number]" class="form-control form-control-sm"
                                id="director_contact_${addDirectorIndex}">
                        </div>
                        <div class="col-md-3 mt-3">
                            <label for="director_email_${addDirectorIndex}" class="form-label fw-bold">Email ID</label>
                            <input type="email" name="directors[${addDirectorIndex}][email]" class="form-control form-control-sm"
                                id="director_email_${addDirectorIndex}">
                        </div>
                        <div class="col-md-3 mt-3">
                            <label for="director_appointment_date_${addDirectorIndex}" class="form-label fw-bold">Date of Appointment</label>
                            <input type="date" name="directors[${addDirectorIndex}][appointment_date]" class="form-control form-control-sm"
                                id="director_appointment_date_${addDirectorIndex}">
                        </div>
                        <div class="col-md-3 mt-3">
                            <label for="director_resignation_date_${addDirectorIndex}" class="form-label fw-bold">Resignation Date</label>
                            <input type="date" name="directors[${addDirectorIndex}][resignation_date]" class="form-control form-control-sm"
                                id="director_resignation_date_${addDirectorIndex}">
                        </div>
                        <div class="col-12 mt-3">
                            <h6 class="fw-bold">Director Documents</h6>
                            <hr class="mt-1 mb-4">
                        </div>
                        <div class="col-md-3 mt-3">
                            <label for="director_aadhar_doc_${addDirectorIndex}" class="form-label">Aadhar Document</label>
                            <input type="file" class="form-control form-control-sm" id="director_aadhar_doc_${addDirectorIndex}"
                                name="directors[${addDirectorIndex}][documents][aadhar_doc]" accept=".pdf,.jpg,.png">
                            <div id="existing_director_aadhar_doc_${addDirectorIndex}" class="existing-document mt-2"></div>
                        </div>
                        <div class="col-md-3 mt-3">
                            <label for="director_pan_doc_${addDirectorIndex}" class="form-label">PAN Document</label>
                            <input type="file" class="form-control form-control-sm" id="director_pan_doc_${addDirectorIndex}"
                                name="directors[${addDirectorIndex}][documents][pan_doc]" accept=".pdf,.jpg,.png">
                            <div id="existing_director_pan_doc_${addDirectorIndex}" class="existing-document mt-2"></div>
                        </div>
                        <div class="col-md-3 mt-3">
                            <label for="director_passport_doc_${addDirectorIndex}" class="form-label">Passport</label>
                            <input type="file" class="form-control form-control-sm" id="director_passport_doc_${addDirectorIndex}"
                                name="directors[${addDirectorIndex}][documents][passport_doc]" accept=".pdf,.jpg,.png">
                            <div id="existing_director_passport_doc_${addDirectorIndex}" class="existing-document mt-2"></div>
                        </div>
                        <div class="col-md-3 mt-3">
                            <label for="director_driving_license_doc_${addDirectorIndex}" class="form-label">Driving License</label>
                            <input type="file" class="form-control form-control-sm" id="director_driving_license_doc_${addDirectorIndex}"
                                name="directors[${addDirectorIndex}][documents][driving_license_doc]" accept=".pdf,.jpg,.png">
                            <div id="existing_director_driving_license_doc_${addDirectorIndex}" class="existing-document mt-2"></div>
                        </div>
                        <div class="col-md-3 mt-3">
                            <label for="director_bank_passbook_doc_${addDirectorIndex}" class="form-label">Bank Passbook</label>
                            <input type="file" class="form-control form-control-sm" id="director_bank_passbook_doc_${addDirectorIndex}"
                                name="directors[${addDirectorIndex}][documents][bank_passbook_doc]" accept=".pdf,.jpg,.png">
                            <div id="existing_director_bank_passbook_doc_${addDirectorIndex}" class="existing-document mt-2"></div>
                        </div>
                        <div class="col-md-3 d-flex align-items-end">
                            <a href="javascript:void(0)" class="remove-director-row w-100 text-danger" title="Remove Director">
                                <i class="ri-delete-bin-line me-1"></i> Remove Director
                            </a>
                        </div>
                    </div>
                `);
                addDirectorIndex++;
            });

            // Remove director row in Add Company Modal
            $(document).on('click', '.remove-director-row', function(e) {
                e.preventDefault();
                if ($('.director-details .director-row').length > 1) {
                    $(this).closest('.director-row').remove();
                    $('.director-details .director-row').each(function(index) {
                        $(this).find('h6.fw-bold').text(`Director ${index + 1}`);
                        $(this).attr('data-index', index);
                        $(this).find('input, select').each(function() {
                            const nameAttr = $(this).attr('name');
                            if (nameAttr) {
                                $(this).attr('name', nameAttr.replace(/directors\[\d+\]/, `directors[${index}]`));
                            }
                            const idAttr = $(this).attr('id');
                            if (idAttr) {
                                $(this).attr('id', idAttr.replace(/director_(\w+)_(\d+)/, `director_$1_${index}`));
                            }
                        });
                    });
                } else {
                    alert('At least one director is required.');
                }
            });

            // View company details
            $(document).on('click', '.view-btn', function() {
                const id = $(this).data('id');
                $.get(`/company/edit/${id}`, function(data) {
                    $('#view_company_name').text(data.company.company_name || 'N/A');
                    $('#view_company_code').text(data.company.company_code || 'N/A');
                    $('#view_registration_number').text(data.company.registration_number || 'N/A');
                    $('#view_gst_number').text(data.company.gst_number || 'N/A');

                    let documentHtml = '';
                    const docs = data.company_documents;
                    if (docs.certificate_incorporation) documentHtml += `<div class="col-md-4"><label class="form-label fw-bold">CERTIFICATE OF INCORPORATION</label><p><a href="/storage/${docs.certificate_incorporation}" target="_blank" class="text-primary">View Document</a></p></div>`;
                    if (docs.company_pan_card) documentHtml += `<div class="col-md-4"><label class="form-label fw-bold">PAN CARD</label><p><a href="/storage/${docs.company_pan_card}" target="_blank" class="text-primary">View Document</a></p></div>`;
                    if (docs.aoa) documentHtml += `<div class="col-md-4"><label class="form-label fw-bold">AOA</label><p><a href="/storage/${docs.aoa}" target="_blank" class="text-primary">View Document</a></p></div>`;
                    if (docs.moa) documentHtml += `<div class="col-md-4"><label class="form-label fw-bold">MOA</label><p><a href="/storage/${docs.moa}" target="_blank" class="text-primary">View Document</a></p></div>`;
                    if (docs.gst_certificate) documentHtml += `<div class="col-md-4"><label class="form-label fw-bold">GST CERTIFICATE</label><p><a href="/storage/${docs.gst_certificate}" target="_blank" class="text-primary">View Document</a></p></div>`;
                    if (docs.board_resolution) documentHtml += `<div class="col-md-4"><label class="form-label fw-bold">BOARD RESOLUTION</label><p><a href="/storage/${docs.board_resolution}" target="_blank" class="text-primary">View Document</a></p></div>`;
                    if (docs.signature_specimen) documentHtml += `<div class="col-md-4"><label class="form-label fw-bold">SIGNATURE SPECIMEN</label><p><a href="/storage/${docs.signature_specimen}" target="_blank" class="text-primary">View Document</a></p></div>`;
                    if (docs.other_docs) documentHtml += `<div class="col-md-4"><label class="form-label fw-bold">OTHER DOCS (${data.company_documents.other_docs_name || 'N/A'})</label><p><a href="/storage/${docs.other_docs}" target="_blank" class="text-primary">View Document</a></p></div>`;
                    $('#view_company_documents').html(documentHtml || '<p class="text-muted">No documents available.</p>');

                    let directorHtml = '';
                    data.directors.forEach((director, index) => {
                        directorHtml += `
                            <div class="card mb-3 shadow-sm">
                                <div class="card-body">
                                    <h6 class="fw-bold">Director ${index + 1}</h6>
                                    <div class="row g-6">
                                        <div class="col-md-3 mt-3"><strong>Name:</strong> ${director.name || 'N/A'}</div>
                                        <div class="col-md-3 mt-3"><strong>Designation/Role:</strong> ${director.designation || 'N/A'}</div>
                                        <div class="col-md-3 mt-3"><strong>DIN:</strong> ${director.din || 'N/A'}</div>
                                        <div class="col-md-3 mt-3"><strong>PAN:</strong> ${director.pan || 'N/A'}</div>
                                        <div class="col-md-3 mt-3"><strong>Aadhaar:</strong> ${director.aadhaar || 'N/A'}</div>
                                        <div class="col-md-3 mt-3"><strong>Contact Number:</strong> ${director.contact_number || 'N/A'}</div>
                                        <div class="col-md-3 mt-3"><strong>Email:</strong> ${director.email || 'N/A'}</div>
                                        <div class="col-md-3 mt-3"><strong>Date of Appointment:</strong> ${director.appointment_date || 'N/A'}</div>
                                        <div class="col-md-3 mt-3"><strong>Resignation Date:</strong> ${director.resignation_date || 'N/A'}</div>
                                        <div class="col-12 mt-3">
                                            <h6 class="fw-bold">Documents</h6>
                                            <hr class="mt-1 mb-3">
                                        </div>
                                        ${director.documents.aadhar_doc ? `
                                            <div class="col-md-3">
                                                <strong>Aadhar Document:</strong>
                                                <a href="/storage/${director.documents.aadhar_doc}" target="_blank" class="text-primary">View Document</a>
                                            </div>
                                        ` : '<div class="col-md-3"><strong>Aadhar Document:</strong> N/A</div>'}
                                        ${director.documents.pan_doc ? `
                                            <div class="col-md-3">
                                                <strong>PAN Document:</strong>
                                                <a href="/storage/${director.documents.pan_doc}" target="_blank" class="text-primary">View Document</a>
                                            </div>
                                        ` : '<div class="col-md-3"><strong>PAN Document:</strong> N/A</div>'}
                                        ${director.documents.passport_doc ? `
                                            <div class="col-md-3">
                                                <strong>Passport:</strong>
                                                <a href="/storage/${director.documents.passport_doc}" target="_blank" class="text-primary">View Document</a>
                                            </div>
                                        ` : '<div class="col-md-3"><strong>Passport:</strong> N/A</div>'}
                                        ${director.documents.driving_license_doc ? `
                                            <div class="col-md-3">
                                                <strong>Driving License:</strong>
                                                <a href="/storage/${director.documents.driving_license_doc}" target="_blank" class="text-primary">View Document</a>
                                            </div>
                                        ` : '<div class="col-md-3"><strong>Driving License:</strong> N/A</div>'}
                                        ${director.documents.bank_passbook_doc ? `
                                            <div class="col-md-3">
                                                <strong>Bank Passbook:</strong>
                                                <a href="/storage/${director.documents.bank_passbook_doc}" target="_blank" class="text-primary">View Document</a>
                                            </div>
                                        ` : '<div class="col-md-3"><strong>Bank Passbook:</strong> N/A</div>'}
                                    </div>
                                </div>
                            </div>
                        `;
                    });
                    $('#view_director_details').html(directorHtml || '<p class="text-muted">No directors available.</p>');
                }).fail(function(xhr) {
                    alert('Failed to load company details: ' + xhr.responseJSON.error);
                });
            });

            // Edit company
            let editDirectorIndex = 0;
            $(document).on('click', '.edit-btn', function() {
                const id = $(this).data('id');
                $.get(`/company/edit/${id}`, function(data) {
                    $('#edit_company_name').val(data.company.company_name);
                    $('#edit_company_code').val(data.company.company_code);
                    $('#edit_registration_number').val(data.company.registration_number);
                    $('#edit_gst_number').val(data.company.gst_number);
                    $('#edit_tin').val(data.company.tin || '');
                    $('#edit_date_of_incorporation').val(data.company.date_of_incorporation || '');
                    $('#editCompanyForm').attr('action', `/company/update/${id}`);

                    // Handle existing company documents
                    $('#editCompanyForm .existing-document').empty();
                    const docs = data.company_documents;
                    if (docs.certificate_incorporation) {
                        $('#existing_certificate_incorporation').html(`<a href="/storage/${docs.certificate_incorporation}" target="_blank" class="text-primary">View Certificate of Incorporation</a> <a href="javascript:void(0)" class="delete-document text-danger ms-2" data-type="certificate_incorporation" title="Delete Document"><i class="ri-delete-bin-line"></i></a>`);
                        $('#edit_certificate_incorporation').hide();
                    } else {
                        $('#edit_certificate_incorporation').show();
                    }
                    if (docs.company_pan_card) {
                        $('#existing_company_pan_card').html(`<a href="/storage/${docs.company_pan_card}" target="_blank" class="text-primary">View PAN Card</a> <a href="javascript:void(0)" class="delete-document text-danger ms-2" data-type="company_pan_card" title="Delete Document"><i class="ri-delete-bin-line"></i></a>`);
                        $('#edit_company_pan_card').hide();
                    } else {
                        $('#edit_company_pan_card').show();
                    }
                    if (docs.aoa) {
                        $('#existing_aoa').html(`<a href="/storage/${docs.aoa}" target="_blank" class="text-primary">View AOA</a> <a href="javascript:void(0)" class="delete-document text-danger ms-2" data-type="aoa" title="Delete Document"><i class="ri-delete-bin-line"></i></a>`);
                        $('#edit_aoa').hide();
                    } else {
                        $('#edit_aoa').show();
                    }
                    if (docs.moa) {
                        $('#existing_moa').html(`<a href="/storage/${docs.moa}" target="_blank" class="text-primary">View MOA</a> <a href="javascript:void(0)" class="delete-document text-danger ms-2" data-type="moa" title="Delete Document"><i class="ri-delete-bin-line"></i></a>`);
                        $('#edit_moa').hide();
                    } else {
                        $('#edit_moa').show();
                    }
                    if (docs.gst_certificate) {
                        $('#existing_gst_certificate').html(`<a href="/storage/${docs.gst_certificate}" target="_blank" class="text-primary">View GST Certificate</a> <a href="javascript:void(0)" class="delete-document text-danger ms-2" data-type="gst_certificate" title="Delete Document"><i class="ri-delete-bin-line"></i></a>`);
                        $('#edit_gst_certificate').hide();
                    } else {
                        $('#edit_gst_certificate').show();
                    }
                    if (docs.board_resolution) {
                        $('#existing_board_resolution').html(`<a href="/storage/${docs.board_resolution}" target="_blank" class="text-primary">View Board Resolution</a> <a href="javascript:void(0)" class="delete-document text-danger ms-2" data-type="board_resolution" title="Delete Document"><i class="ri-delete-bin-line"></i></a>`);
                        $('#edit_board_resolution').hide();
                    } else {
                        $('#edit_board_resolution').show();
                    }
                    if (docs.signature_specimen) {
                        $('#existing_signature_specimen').html(`<a href="/storage/${docs.signature_specimen}" target="_blank" class="text-primary">View Signature Specimen</a> <a href="javascript:void(0)" class="delete-document text-danger ms-2" data-type="signature_specimen" title="Delete Document"><i class="ri-delete-bin-line"></i></a>`);
                        $('#edit_signature_specimen').hide();
                    } else {
                        $('#edit_signature_specimen').show();
                    }
                    if (docs.other_docs) {
                        $('#existing_other_docs').html(`<a href="/storage/${docs.other_docs}" target="_blank" class="text-primary">View Other Docs (${docs.other_docs_name || 'N/A'})</a> <a href="javascript:void(0)" class="delete-document text-danger ms-2" data-type="other_docs" title="Delete Document"><i class="ri-delete-bin-line"></i></a>`);
                        $('#edit_other_docs').hide();
                    } else {
                        $('#edit_other_docs').show();
                    }
                    $('#edit_other_docs_name').val(docs.other_docs_name || '');

                    $('#editCompanyForm .director-details').empty();
                    editDirectorIndex = 0;
                    data.directors.forEach((director, index) => {
                        $('#editCompanyForm .director-details').append(`
                            <div class="row g-6 director-row border-bottom pb-3 mb-3" data-index="${index}">
                                <div class="col-12">
                                    <h6 class="fw-bold">Director ${index + 1}</h6>
                                </div>
                                <input type="hidden" name="directors[${index}][id]" value="${director.id}">
                                <div class="col-md-3 mt-3">
                                    <label for="edit_director_name_${index}" class="form-label fw-bold">Name of Director<span class="text-danger">*</span></label>
                                    <input type="text" name="directors[${index}][name]" class="form-control form-control-sm"
                                        id="edit_director_name_${index}" value="${director.name || ''}" required>
                                </div>
                                <div class="col-md-3 mt-3">
                                    <label for="edit_director_designation_${index}" class="form-label fw-bold">Designation / Role <span class="text-danger">*</span></label>
                                    <input type="text" name="directors[${index}][designation]" class="form-control form-control-sm"
                                        id="edit_director_designation_${index}" value="${director.designation || ''}" required>
                                </div>
                                <div class="col-md-3 mt-3">
                                    <label for="edit_director_din_${index}" class="form-label fw-bold">DIN</label>
                                    <input type="text" name="directors[${index}][din]" class="form-control form-control-sm"
                                        id="edit_director_din_${index}" value="${director.din || ''}">
                                </div>
                                <div class="col-md-3 mt-3">
                                    <label for="edit_director_pan_${index}" class="form-label fw-bold">PAN</label>
                                    <input type="text" name="directors[${index}][pan]" class="form-control form-control-sm"
                                        id="edit_director_pan_${index}" value="${director.pan || ''}">
                                </div>
                                <div class="col-md-3 mt-3">
                                    <label for="edit_director_aadhaar_${index}" class="form-label fw-bold">Aadhaar (if applicable)</label>
                                    <input type="text" name="directors[${index}][aadhaar]" class="form-control form-control-sm"
                                        id="edit_director_aadhaar_${index}" value="${director.aadhaar || ''}">
                                </div>
                                <div class="col-md-3 mt-3">
                                    <label for="edit_director_contact_${index}" class="form-label fw-bold">Contact Number</label>
                                    <input type="text" name="directors[${index}][contact_number]" class="form-control form-control-sm"
                                        id="edit_director_contact_${index}" value="${director.contact_number || ''}">
                                </div>
                                <div class="col-md-3 mt-3">
                                    <label for="edit_director_email_${index}" class="form-label fw-bold">Email ID</label>
                                    <input type="email" name="directors[${index}][email]" class="form-control form-control-sm"
                                        id="edit_director_email_${index}" value="${director.email || ''}">
                                </div>
                                <div class="col-md-3 mt-3">
                                    <label for="edit_director_appointment_date_${index}" class="form-label fw-bold">Date of Appointment</label>
                                    <input type="date" name="directors[${index}][appointment_date]" class="form-control form-control-sm"
                                        id="edit_director_appointment_date_${index}" value="${director.appointment_date || ''}">
                                </div>
                                <div class="col-md-3 mt-3">
                                    <label for="edit_director_resignation_date_${index}" class="form-label fw-bold">Resignation Date</label>
                                    <input type="date" name="directors[${index}][resignation_date]" class="form-control form-control-sm"
                                        id="edit_director_resignation_date_${index}" value="${director.resignation_date || ''}">
                                </div>
                                <div class="col-12 mt-3">
                                    <h6 class="fw-bold">Director Documents</h6>
                                    <hr class="mt-1 mb-4">
                                </div>
                                <div class="col-md-3 mt-3">
                                    <label for="edit_director_aadhar_doc_${index}" class="form-label">Aadhar Document</label>
                                    <input type="file" class="form-control form-control-sm" id="edit_director_aadhar_doc_${index}"
                                        name="directors[${index}][documents][aadhar_doc]" accept=".pdf,.jpg,.png">
                                    <div id="existing_director_aadhar_doc_${index}" class="existing-document mt-2">
                                        ${director.documents.aadhar_doc ? `<a href="/storage/${director.documents.aadhar_doc}" target="_blank" class="text-primary">View Aadhar Document</a> <a href="javascript:void(0)" class="delete-director-document text-danger ms-2" data-director-id="${director.id}" data-type="aadhar_doc" title="Delete Document"><i class="ri-delete-bin-line"></i></a>` : ''}
                                    </div>
                                </div>
                                <div class="col-md-3 mt-3">
                                    <label for="edit_director_pan_doc_${index}" class="form-label">PAN Document</label>
                                    <input type="file" class="form-control form-control-sm" id="edit_director_pan_doc_${index}"
                                        name="directors[${index}][documents][pan_doc]" accept=".pdf,.jpg,.png">
                                    <div id="existing_director_pan_doc_${index}" class="existing-document mt-2">
                                        ${director.documents.pan_doc ? `<a href="/storage/${director.documents.pan_doc}" target="_blank" class="text-primary">View PAN Document</a> <a href="javascript:void(0)" class="delete-director-document text-danger ms-2" data-director-id="${director.id}" data-type="pan_doc" title="Delete Document"><i class="ri-delete-bin-line"></i></a>` : ''}
                                    </div>
                                </div>
                                <div class="col-md-3 mt-3">
                                    <label for="edit_director_passport_doc_${index}" class="form-label">Passport</label>
                                    <input type="file" class="form-control form-control-sm" id="edit_director_passport_doc_${index}"
                                        name="directors[${index}][documents][passport_doc]" accept=".pdf,.jpg,.png">
                                    <div id="existing_director_passport_doc_${index}" class="existing-document mt-2">
                                        ${director.documents.passport_doc ? `<a href="/storage/${director.documents.passport_doc}" target="_blank" class="text-primary">View Passport</a> <a href="javascript:void(0)" class="delete-director-document text-danger ms-2" data-director-id="${director.id}" data-type="passport_doc" title="Delete Document"><i class="ri-delete-bin-line"></i></a>` : ''}
                                    </div>
                                </div>
                                <div class="col-md-3 mt-3">
                                    <label for="edit_director_driving_license_doc_${index}" class="form-label">Driving License</label>
                                    <input type="file" class="form-control form-control-sm" id="edit_director_driving_license_doc_${index}"
                                        name="directors[${index}][documents][driving_license_doc]" accept=".pdf,.jpg,.png">
                                    <div id="existing_director_driving_license_doc_${index}" class="existing-document mt-2">
                                        ${director.documents.driving_license_doc ? `<a href="/storage/${director.documents.driving_license_doc}" target="_blank" class="text-primary">View Driving License</a> <a href="javascript:void(0)" class="delete-director-document text-danger ms-2" data-director-id="${director.id}" data-type="driving_license_doc" title="Delete Document"><i class="ri-delete-bin-line"></i></a>` : ''}
                                    </div>
                                </div>
                                <div class="col-md-3 mt-3 mt-3">
                                    <label for="edit_director_bank_passbook_doc_${index}" class="form-label">Bank Passbook</label>
                                    <input type="file" class="form-control form-control-sm" id="edit_director_bank_passbook_doc_${index}"
                                        name="directors[${index}][documents][bank_passbook_doc]" accept=".pdf,.jpg,.png">
                                    <div id="existing_director_bank_passbook_doc_${index}" class="existing-document mt-2">
                                        ${director.documents.bank_passbook_doc ? `<a href="/storage/${director.documents.bank_passbook_doc}" target="_blank" class="text-primary">View Bank Passbook</a> <a href="javascript:void(0)" class="delete-director-document text-danger ms-2" data-director-id="${director.id}" data-type="bank_passbook_doc" title="Delete Document"><i class="ri-delete-bin-line"></i></a>` : ''}
                                    </div>
                                </div>
                                <div class="col-md-3 d-flex align-items-end">
                                    <a href="javascript:void(0)" class="remove-director-row w-100 text-danger" title="Remove Director">
                                        <i class="ri-delete-bin-line me-1"></i> Remove Director
                                    </a>
                                </div>
                            </div>
                        `);
                        // Hide file inputs for existing documents
                        if (director.documents.aadhar_doc) $('#edit_director_aadhar_doc_' + index).hide();
                        if (director.documents.pan_doc) $('#edit_director_pan_doc_' + index).hide();
                        if (director.documents.passport_doc) $('#edit_director_passport_doc_' + index).hide();
                        if (director.documents.driving_license_doc) $('#edit_director_driving_license_doc_' + index).hide();
                        if (director.documents.bank_passbook_doc) $('#edit_director_bank_passbook_doc_' + index).hide();
                        editDirectorIndex++;
                    });

                    // Add new director row in Edit Company Modal
                    $('#editAddDirectorRow').click(function(e) {
                        e.preventDefault();
                        $('#editCompanyForm .director-details').append(`
                            <div class="row g-6 director-row border-bottom pb-3 mb-3" data-index="${editDirectorIndex}">
                                <div class="col-12">
                                    <h6 class="fw-bold">Director ${editDirectorIndex + 1}</h6>
                                </div>
                                <div class="col-md-3 mt-3">
                                    <label for="edit_director_name_${editDirectorIndex}" class="form-label fw-bold">Name of Director <span class="text-danger">*</span></label>
                                    <input type="text" name="directors[${editDirectorIndex}][name]" class="form-control form-control-sm"
                                        id="edit_director_name_${editDirectorIndex}" required>
                                </div>
                                <div class="col-md-3 mt-3">
                                    <label for="edit_director_designation_${editDirectorIndex}" class="form-label fw-bold">Designation / Role <span class="text-danger">*</span></label>
                                    <input type="text" name="directors[${editDirectorIndex}][designation]" class="form-control form-control-sm"
                                        id="edit_director_designation_${editDirectorIndex}" required>
                                </div>
                                <div class="col-md-3 mt-3">
                                    <label for="edit_director_din_${editDirectorIndex}" class="form-label fw-bold">DIN</label>
                                    <input type="text" name="directors[${editDirectorIndex}][din]" class="form-control form-control-sm"
                                        id="edit_director_din_${editDirectorIndex}">
                                </div>
                                <div class="col-md-3 mt-3">
                                    <label for="edit_director_pan_${editDirectorIndex}" class="form-label fw-bold">PAN</label>
                                    <input type="text" name="directors[${editDirectorIndex}][pan]" class="form-control form-control-sm"
                                        id="edit_director_pan_${editDirectorIndex}">
                                </div>
                                <div class="col-md-3 mt-3">
                                    <label for="edit_director_aadhaar_${editDirectorIndex}" class="form-label fw-bold">Aadhaar (if applicable)</label>
                                    <input type="text" name="directors[${editDirectorIndex}][aadhaar]" class="form-control form-control-sm"
                                        id="edit_director_aadhaar_${editDirectorIndex}">
                                </div>
                                <div class="col-md-3 mt-3">
                                    <label for="edit_director_contact_${editDirectorIndex}" class="form-label fw-bold">Contact Number</label>
                                    <input type="text" name="directors[${editDirectorIndex}][contact_number]" class="form-control form-control-sm"
                                        id="edit_director_contact_${editDirectorIndex}">
                                </div>
                                <div class="col-md-3 mt-3">
                                    <label for="edit_director_email_${editDirectorIndex}" class="form-label fw-bold">Email ID</label>
                                    <input type="email" name="directors[${editDirectorIndex}][email]" class="form-control form-control-sm"
                                        id="edit_director_email_${editDirectorIndex}">
                                </div>
                                <div class="col-md-3 mt-3">
                                    <label for="edit_director_appointment_date_${editDirectorIndex}" class="form-label fw-bold">Date of Appointment</label>
                                    <input type="date" name="directors[${editDirectorIndex}][appointment_date]" class="form-control form-control-sm"
                                        id="edit_director_appointment_date_${editDirectorIndex}">
                                </div>
                                <div class="col-md-3 mt-3">
                                    <label for="edit_director_resignation_date_${editDirectorIndex}" class="form-label fw-bold">Resignation Date</label>
                                    <input type="date" name="directors[${editDirectorIndex}][resignation_date]" class="form-control form-control-sm"
                                        id="edit_director_resignation_date_${editDirectorIndex}">
                                </div>
                                <div class="col-12 mt-3 mt-3">
                                    <h6 class="fw-bold">Director Documents</h6>
                                    <hr class="mt-1 mb-4">
                                </div>
                                <div class="col-md-3 mt-3">
                                    <label for="edit_director_aadhar_doc_${editDirectorIndex}" class="form-label">Aadhar Document</label>
                                    <input type="file" class="form-control form-control-sm" id="edit_director_aadhar_doc_${editDirectorIndex}"
                                        name="directors[${editDirectorIndex}][documents][aadhar_doc]" accept=".pdf,.jpg,.png">
                                    <div id="existing_director_aadhar_doc_${editDirectorIndex}" class="existing-document mt-2"></div>
                                </div>
                                <div class="col-md-3 mt-3">
                                    <label for="edit_director_pan_doc_${editDirectorIndex}" class="form-label">PAN Document</label>
                                    <input type="file" class="form-control form-control-sm" id="edit_director_pan_doc_${editDirectorIndex}"
                                        name="directors[${editDirectorIndex}][documents][pan_doc]" accept=".pdf,.jpg,.png">
                                    <div id="existing_director_pan_doc_${editDirectorIndex}" class="existing-document mt-2"></div>
                                </div>
                                <div class="col-md-3 mt-3">
                                    <label for="edit_director_passport_doc_${editDirectorIndex}" class="form-label">Passport</label>
                                    <input type="file" class="form-control form-control-sm" id="edit_director_passport_doc_${editDirectorIndex}"
                                        name="directors[${editDirectorIndex}][documents][passport_doc]" accept=".pdf,.jpg,.png">
                                    <div id="existing_director_passport_doc_${editDirectorIndex}" class="existing-document mt-2"></div>
                                </div>
                                <div class="col-md-3 mt-3">
                                    <label for="edit_director_driving_license_doc_${editDirectorIndex}" class="form-label">Driving License</label>
                                    <input type="file" class="form-control form-control-sm" id="edit_director_driving_license_doc_${editDirectorIndex}"
                                        name="directors[${editDirectorIndex}][documents][driving_license_doc]" accept=".pdf,.jpg,.png">
                                    <div id="existing_director_driving_license_doc_${editDirectorIndex}" class="existing-document mt-2"></div>
                                </div>
                                <div class="col-md-3 mt-3">
                                    <label for="edit_director_bank_passbook_doc_${editDirectorIndex}" class="form-label">Bank Passbook</label>
                                    <input type="file" class="form-control form-control-sm" id="edit_director_bank_passbook_doc_${editDirectorIndex}"
                                        name="directors[${editDirectorIndex}][documents][bank_passbook_doc]" accept=".pdf,.jpg,.png">
                                    <div id="existing_director_bank_passbook_doc_${editDirectorIndex}" class="existing-document mt-2"></div>
                                </div>
                                <div class="col-md-3 d-flex align-items-end">
                                    <a href="javascript:void(0)" class="remove-director-row w-100 text-danger" title="Remove Director">
                                        <i class="ri-delete-bin-line me-1"></i> Remove Director
                                    </a>
                                </div>
                            </div>
                        `);
                        editDirectorIndex++;
                    });

                    // Handle client-side director document deletion
                    $(document).on('click', '.delete-director-document', function() {
                        const $this = $(this);
                        const directorId = $this.data('director-id');
                        const docType = $this.data('type');
                        const index = $this.closest('.director-row').data('index');
                        const $existingDocDiv = $this.closest('.existing-document');
                        const $fileInput = $this.closest('.col-md-3').find(`input[name="directors[${index}][documents][${docType}]"]`);

                        // Clear the existing document div and show the file input
                        $existingDocDiv.empty();
                        $fileInput.show();

                        // Add a hidden input to mark the document for deletion
                        $this.closest('.director-row').append(
                            `<input type="hidden" name="directors[${index}][delete_documents][${docType}]" value="1">`
                        );
                    });

                    // Handle client-side company document deletion
                    $(document).on('click', '.delete-document', function() {
                        const $this = $(this);
                        const docType = $this.data('type');
                        const $existingDocDiv = $this.closest('.existing-document');
                        const $fileInput = $this.closest('.col-md-3').find(`input[name="documents[${docType}]"]`);

                        // Clear the existing document div and show the file input
                        $existingDocDiv.empty();
                        $fileInput.show();

                        // Add a hidden input to mark the document for deletion
                        $this.closest('.col-md-3').append(
                            `<input type="hidden" name="documents[delete_${docType}]" value="1">`
                        );
                    });
                }).fail(function(xhr) {
                    alert('Failed to load company details: ' + xhr.responseJSON.error);
                });
            });

            // Remove director row in Edit Company Modal
            $(document).on('click', '#editCompanyForm .remove-director-row', function(e) {
                e.preventDefault();
                if ($('#editCompanyForm .director-details .director-row').length > 1) {
                    $(this).closest('.director-row').remove();
                    $('#editCompanyForm .director-details .director-row').each(function(index) {
                        $(this).find('h6.fw-bold').text(`Director ${index + 1}`);
                        $(this).attr('data-index', index);
                        $(this).find('input, select').each(function() {
                            const nameAttr = $(this).attr('name');
                            if (nameAttr) {
                                $(this).attr('name', nameAttr.replace(/directors\[\d+\]/, `directors[${index}]`));
                            }
                            const idAttr = $(this).attr('id');
                            if (idAttr) {
                                $(this).attr('id', idAttr.replace(/edit_director_(\w+)_(\d+)/, `edit_director_$1_${index}`));
                            }
                        });
                    });
                } else {
                    alert('At least one director is required.');
                }
            });
        });
    </script>
@endpush