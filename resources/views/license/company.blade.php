{{-- @extends('layouts.app')
@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between bg-galaxy-transparent p-3 rounded">
                        <h4 class="mb-sm-0 text-white">{{ ucwords(str_replace('-', ' ', Request::path())) }}</h4>
                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="javascript: void(0);" class="text-white">Masters</a></li>
                                <li class="breadcrumb-item active text-white">{{ ucwords(str_replace('-', ' ', Request::path())) }}</li>
                            </ol>
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
                    <div class="modal-dialog modal-xl modal-dialog-scrollable">
                        <form method="POST" action="{{ route('company.store') }}" enctype="multipart/form-data">
                            @csrf
                            <div class="modal-content">
                                <div class="modal-header text-white">
                                    <h5 class="modal-title" id="addCompanyModalLabel"><i class="ri-building-4-line me-2"></i>Add New Company</h5>
                                    <button type="button" class="btn-close btn-close-black" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body" style="max-height: 70vh; overflow-y: auto;">
                                    <!-- Company Information and Documents Section -->
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
                                                <div class="row g-4">
                                                    <div class="col-md-4">
                                                        <label for="company_name" class="form-label fw-bold">Company Name <span class="text-danger">*</span></label>
                                                        <select class="form-select @error('company_name') is-invalid @enderror"
                                                            id="company_name" name="company_name" required>
                                                            <option value="">Select Company</option>
                                                        </select>
                                                        @error('company_name')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label for="company_code" class="form-label fw-bold">Company Code</label>
                                                        <input type="text" class="form-control @error('company_code') is-invalid @enderror"
                                                            id="company_code" name="company_code" readonly>
                                                        @error('company_code')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label for="registration_number" class="form-label fw-bold">Registration Number</label>
                                                        <input type="text" class="form-control @error('registration_number') is-invalid @enderror"
                                                            id="registration_number" name="registration_number" readonly>
                                                        @error('registration_number')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label for="gst_number" class="form-label fw-bold">GST Number</label>
                                                        <input type="text" class="form-control @error('gst_number') is-inva lid @enderror"
                                                            id="gst_number" name="gst_number" readonly>
                                                        @error('gst_number')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label for="tin" class="form-label fw-bold">TIN (if applicable)</label>
                                                        <input type="text" class="form-control">
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label for="date_of_incorporation" class="form-label fw-bold">Date of Incorporation</label>
                                                        <input type="date" class="form-control">
                                                    </div>
                                                    <div class="col-12 mt-3">
                                                        <h6 class="fw-bold">Company Documents</h6>
                                                        <hr class="mt-1 mb-4">
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label for="certificate_incorporation" class="form-label">Certificate of Incorporation</label>
                                                        <input type="file" class="form-control" id="certificate_incorporation"
                                                            name="documents[certificate_incorporation]" accept=".pdf,.jpg,.png">
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label for="company_pan_card" class="form-label">PAN Card</label>
                                                        <input type="file" class="form-control" id="company_pan_card"
                                                            name="documents[company_pan_card]" accept=".pdf,.jpg,.png">
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label for="aoa" class="form-label">AOA (Articles of Association)</label>
                                                        <input type="file" class="form-control" id="aoa" name="documents[aoa]"
                                                            accept=".pdf,.jpg,.png">
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label for="moa" class="form-label">MOA (Memorandum of Association)</label>
                                                        <input type="file" class="form-control" id="moa" name="documents[moa]"
                                                            accept=".pdf,.jpg,.png">
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label for="gst_certificate" class="form-label">GST Certificate</label>
                                                        <input type="file" class="form-control" id="gst_certificate"
                                                            name="documents[gst_certificate]" accept=".pdf,.jpg,.png">
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label for="board_resolution" class="form-label">Board Resolution for Authorized Signatory</label>
                                                        <input type="file" class="form-control" id="board_resolution"
                                                            name="documents[board_resolution]" accept=".pdf,.jpg,.png">
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label for="signature_specimen" class="form-label">Signature Specimen</label>
                                                        <input type="file" class="form-control" id="signature_specimen"
                                                            name="documents[signature_specimen]" accept=".pdf,.jpg,.png">
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label for="other_docs" class="form-label">Any Other Relevant Docs</label>
                                                        <div class="side-by-side-inputs">
                                                            <input type="text" class="form-control" id="other_docs_name"
                                                                name="documents[other_docs_name]" placeholder="Document Name">
                                                            <input type="file" class="form-control" id="other_docs"
                                                                name="documents[other_docs]" accept=".pdf,.jpg,.png">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Director Details and Documents Section -->
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
                                                        <div class="col-md-4">
                                                            <label for="director_name_0" class="form-label fw-bold">Name of Director <span class="text-danger">*</span></label>
                                                            <input type="text" name="directors[0][name]" class="form-control"
                                                                id="director_name_0" required>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <label for="director_designation_0" class="form-label fw-bold">Designation / Role <span class="text-danger">*</span></label>
                                                            <input type="text" name="directors[0][designation]" class="form-control"
                                                                id="director_designation_0" required>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <label for="director_din_0" class="form-label fw-bold">DIN</label>
                                                            <input type="text" name="directors[0][din]" class="form-control"
                                                                id="director_din_0">
                                                        </div>
                                                        <div class="col-md-4">
                                                            <label for="director_pan_0" class="form-label fw-bold">PAN</label>
                                                            <input type="text" name="directors[0][pan]" class="form-control"
                                                                id="director_pan_0">
                                                        </div>
                                                        <div class="col-md-4">
                                                            <label for="director_aadhaar_0" class="form-label fw-bold">Aadhaar (if applicable)</label>
                                                            <input type="text" name="directors[0][aadhaar]" class="form-control"
                                                                id="director_aadhaar_0">
                                                        </div>
                                                        <div class="col-md-4">
                                                            <label for="director_contact_0" class="form-label fw-bold">Contact Number</label>
                                                            <input type="text" name="directors[0][contact_number]" class="form-control"
                                                                id="director_contact_0">
                                                        </div>
                                                        <div class="col-md-4">
                                                            <label for="director_email_0" class="form-label fw-bold">Email ID</label>
                                                            <input type="email" name="directors[0][email]" class="form-control"
                                                                id="director_email_0">
                                                        </div>
                                                        <div class="col-md-4">
                                                            <label for="director_appointment_date_0" class="form-label fw-bold">Date of Appointment</label>
                                                            <input type="date" name="directors[0][appointment_date]" class="form-control"
                                                                id="director_appointment_date_0">
                                                        </div>
                                                        <div class="col-md-4">
                                                            <label for="director_resignation_date_0" class="form-label fw-bold">Resignation Date</label>
                                                            <input type="date" name="directors[0][resignation_date]" class="form-control"
                                                                id="director_resignation_date_0">
                                                        </div>
                                                        <div class="col-12 mt-3">
                                                            <h6 class="fw-bold">Director Documents</h6>
                                                            <hr class="mt-1 mb-4">
                                                        </div>
                                                        <div class="col-md-4">
                                                            <label for="director_aadhar_doc_0" class="form-label">Aadhar Document</label>
                                                            <input type="file" class="form-control" id="director_aadhar_doc_0"
                                                                name="directors[0][documents][aadhar_doc]" accept=".pdf,.jpg,.png">
                                                        </div>
                                                        <div class="col-md-4">
                                                            <label for="director_pan_doc_0" class="form-label">PAN Document</label>
                                                            <input type="file" class="form-control" id="director_pan_doc_0"
                                                                name="directors[0][documents][pan_doc]" accept=".pdf,.jpg,.png">
                                                        </div>
                                                        <div class="col-md-4">
                                                            <label for="director_passport_doc_0" class="form-label">Passport</label>
                                                            <input type="file" class="form-control" id="director_passport_doc_0"
                                                                name="directors[0][documents][passport_doc]" accept=".pdf,.jpg,.png">
                                                        </div>
                                                        <div class="col-md-4">
                                                            <label for="director_driving_license_doc_0" class="form-label">Driving License</label>
                                                            <input type="file" class="form-control" id="director_driving_license_doc_0"
                                                                name="directors[0][documents][driving_license_doc]" accept=".pdf,.jpg,.png">
                                                        </div>
                                                        <div class="col-md-4">
                                                            <label for="director_bank_passbook_doc_0" class="form-label">Bank Passbook</label>
                                                            <input type="file" class="form-control" id="director_bank_passbook_doc_0"
                                                                name="directors[0][documents][bank_passbook_doc]" accept=".pdf,.jpg,.png">
                                                        </div>
                                                      <div class="col-md-4 d-flex align-items-end">
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
            @endcan

            <!-- View Company Modal -->
            @can('view-Company')
                <div class="modal fade" id="viewCompanyModal" tabindex="-1" aria-labelledby="viewCompanyModalLabel"
                    aria-hidden="true">
                    <div class="modal-dialog modal-xl">
                        <div class="modal-content">
                            <div class="modal-header bg-primary text-white">
                                <h5 class="modal-title" id="viewCompanyModalLabel"><i class="ri-building-4-line me-2"></i>View Company Details</h5>
                                <button type="button" class="btn-close btn-close-black" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div class="card mb-4">
                                    <div class="card-header bg-light">
                                        <h5 class="mb-0"><i class="ri-information-line me-2"></i>Company Information and Documents</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row g-4">
                                            <div class="col-md-6">
                                                <label class="form-label fw-bold">Company Name</label>
                                                <p id="view_company_name" class="form-control-static"></p>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label fw-bold">Company Code</label>
                                                <p id="view_company_code" class="form-control-static"></p>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label fw-bold">Registration Number</label>
                                                <p id="view_registration_number" class="form-control-static"></p>
                                            </div>
                                            <div class="col-md-6">
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
                                        <h5 class="mb-0"><i class="ri-user-3-line me-2"></i>Director Details and Documents</h5>
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
                    <div class="modal-dialog modal-xl modal-dialog-scrollable">
                        <form method="POST" action="" id="editCompanyForm" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="editCompanyModalLabel"><i class="ri-edit-2-line me-2"></i>Edit Company</h5>
                                    <button type="button" class="btn-close btn-close-black" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body" style="max-height: 70vh; overflow-y: auto;">
                                    <!-- Company Information and Documents Section -->
                                    <div class="card mb-4">
                                        <div id="editCompanyInfoHeading">
                                            <h5 class="mb-0">
                                                <button class="btn btn-link text-dark text-decoration-none" type="button" data-bs-toggle="collapse"
                                                    data-bs-target="#editCompanyInfoCollapse" aria-expanded="true" aria-controls="editCompanyInfoCollapse">
                                                    <i class="ri-information-line me-2"></i>Company Information and Documents
                                                </button>
                                            </h5>
                                        </div>
                                        <div id="editCompanyInfoCollapse" class="collapse show" aria-labelledby="editCompanyInfoHeading">
                                            <div class="card-body">
                                                <div class="row g-4">
                                                    <div class="col-md-4">
                                                        <label for="edit_company_name" class="form-label fw-bold">Company Name <span class="text-danger">*</span></label>
                                                        <input type="text" class="form-control @error('company_name') is-invalid @enderror"
                                                            id="edit_company_name" name="company_name" required>
                                                        @error('company_name')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label for="edit_company_code" class="form-label fw-bold">Company Code</label>
                                                        <input type="text" class="form-control @error('company_code') is-invalid @enderror"
                                                            id="edit_company_code" name="company_code">
                                                        @error('company_code')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label for="edit_registration_number" class="form-label fw-bold">Registration Number</label>
                                                        <input type="text" class="form-control @error('registration_number') is-invalid @enderror"
                                                            id="edit_registration_number" name="registration_number">
                                                        @error('registration_number')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label for="edit_gst_number" class="form-label fw-bold">GST Number</label>
                                                        <input type="text" class="form-control @error('gst_number') is-invalid @enderror"
                                                            id="edit_gst_number" name="gst_number">
                                                        @error('gst_number')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                    <div class="col-12 mt-3">
                                                        <h6 class="fw-bold">Company Documents</h6>
                                                        <hr class="mt-1 mb-4">
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label for="edit_certificate_incorporation" class="form-label">Certificate of Incorporation</label>
                                                        <input type="file" class="form-control" id="edit_certificate_incorporation"
                                                            name="documents[certificate_incorporation]" accept=".pdf,.jpg,.png">
                                                        <div id="existing_certificate_incorporation" class="existing-document mt-2"></div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label for="edit_company_pan_card" class="form-label">PAN Card</label>
                                                        <input type="file" class="form-control" id="edit_company_pan_card"
                                                            name="documents[company_pan_card]" accept=".pdf,.jpg,.png">
                                                        <div id="existing_company_pan_card" class="existing-document mt-2"></div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label for="edit_aoa" class="form-label">AOA (Articles of Association)</label>
                                                        <input type="file" class="form-control" id="edit_aoa" name="documents[aoa]"
                                                            accept=".pdf,.jpg,.png">
                                                        <div id="existing_aoa" class="existing-document mt-2"></div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label for="edit_moa" class="form-label">MOA (Memorandum of Association)</label>
                                                        <input type="file" class="form-control" id="edit_moa" name="documents[moa]"
                                                            accept=".pdf,.jpg,.png">
                                                        <div id="existing_moa" class="existing-document mt-2"></div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label for="edit_gst_certificate" class="form-label">GST Certificate</label>
                                                        <input type="file" class="form-control" id="edit_gst_certificate"
                                                            name="documents[gst_certificate]" accept=".pdf,.jpg,.png">
                                                        <div id="existing_gst_certificate" class="existing-document mt-2"></div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label for="edit_board_resolution" class="form-label">Board Resolution for Authorized Signatory</label>
                                                        <input type="file" class="form-control" id="edit_board_resolution"
                                                            name="documents[board_resolution]" accept=".pdf,.jpg,.png">
                                                        <div id="existing_board_resolution" class="existing-document mt-2"></div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label for="edit_signature_specimen" class="form-label">Signature Specimen</label>
                                                        <input type="file" class="form-control" id="edit_signature_specimen"
                                                            name="documents[signature_specimen]" accept=".pdf,.jpg,.png">
                                                        <div id="existing_signature_specimen" class="existing-document mt-2"></div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label for="edit_other_docs" class="form-label">Any Other Relevant Docs</label>
                                                        <div class="side-by-side-inputs">
                                                            <input type="text" class="form-control" id="edit_other_docs_name"
                                                                name="documents[other_docs_name]" placeholder="Document Name">
                                                            <input type="file" class="form-control" id="edit_other_docs"
                                                                name="documents[other_docs]" accept=".pdf,.jpg,.png">
                                                        </div>
                                                        <div id="existing_other_docs" class="existing-document mt-2"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Director Details and Documents Section -->
                                    <div class="card mb-4">
                                        <div id="editDirectorInfoHeading">
                                            <h5 class="mb-0">
                                                <button class="btn btn-link text-dark text-decoration-none" type="button" data-bs-toggle="collapse"
                                                    data-bs-target="#editDirectorInfoCollapse" aria-expanded="true" aria-controls="editDirectorInfoCollapse">
                                                    <i class="ri-user-3-line me-2"></i>Director Details and Documents
                                                </button>
                                            </h5>
                                        </div>
                                        <div id="editDirectorInfoCollapse" class="collapse show" aria-labelledby="editDirectorInfoHeading">
                                            <div class="card-body">
                                                <div class="director-details"></div>
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
                        <div class="row g-3 director-row border-bottom pb-3 mb-3" data-index="${addDirectorIndex}">
                            <div class="col-12">
                                <h6 class="fw-bold">Director ${addDirectorIndex + 1}</h6>
                            </div>
                            <div class="col-md-4">
                                <label for="director_name_${addDirectorIndex}" class="form-label fw-bold">Name of Director <span class="text-danger">*</span></label>
                                <input type="text" name="directors[${addDirectorIndex}][name]" class="form-control"
                                    id="director_name_${addDirectorIndex}" required>
                            </div>
                            <div class="col-md-4">
                                <label for="director_designation_${addDirectorIndex}" class="form-label fw-bold">Designation / Role <span class="text-danger">*</span></label>
                                <input type="text" name="directors[${addDirectorIndex}][designation]" class="form-control"
                                    id="director_designation_${addDirectorIndex}" required>
                            </div>
                            <div class="col-md-4">
                                <label for="director_din_${addDirectorIndex}" class="form-label fw-bold">DIN</label>
                                <input type="text" name="directors[${addDirectorIndex}][din]" class="form-control"
                                    id="director_din_${addDirectorIndex}">
                            </div>
                            <div class="col-md-4">
                                <label for="director_pan_${addDirectorIndex}" class="form-label fw-bold">PAN</label>
                                <input type="text" name="directors[${addDirectorIndex}][pan]" class="form-control"
                                    id="director_pan_${addDirectorIndex}">
                            </div>
                            <div class="col-md-4">
                                <label for="director_aadhaar_${addDirectorIndex}" class="form-label fw-bold">Aadhaar (if applicable)</label>
                                <input type="text" name="directors[${addDirectorIndex}][aadhaar]" class="form-control"
                                    id="director_aadhaar_${addDirectorIndex}">
                            </div>
                            <div class="col-md-4">
                                <label for="director_contact_${addDirectorIndex}" class="form-label fw-bold">Contact Number</label>
                                <input type="text" name="directors[${addDirectorIndex}][contact_number]" class="form-control"
                                    id="director_contact_${addDirectorIndex}">
                            </div>
                            <div class="col-md-4">
                                <label for="director_email_${addDirectorIndex}" class="form-label fw-bold">Email ID</label>
                                <input type="email" name="directors[${addDirectorIndex}][email]" class="form-control"
                                    id="director_email_${addDirectorIndex}">
                            </div>
                            <div class="col-md-4">
                                <label for="director_appointment_date_${addDirectorIndex}" class="form-label fw-bold">Date of Appointment</label>
                                <input type="date" name="directors[${addDirectorIndex}][appointment_date]" class="form-control"
                                    id="director_appointment_date_${addDirectorIndex}">
                            </div>
                            <div class="col-md-4">
                                <label for="director_resignation_date_${addDirectorIndex}" class="form-label fw-bold">Resignation Date</label>
                                <input type="date" name="directors[${addDirectorIndex}][resignation_date]" class="form-control"
                                    id="director_resignation_date_${addDirectorIndex}">
                            </div>
                            <div class="col-12 mt-3">
                                <h6 class="fw-bold">Director Documents</h6>
                                <hr class="mt-1 mb-4">
                            </div>
                            <div class="col-md-4">
                                <label for="director_aadhar_doc_${addDirectorIndex}" class="form-label">Aadhar Document</label>
                                <input type="file" class="form-control" id="director_aadhar_doc_${addDirectorIndex}"
                                    name="directors[${addDirectorIndex}][documents][aadhar_doc]" accept=".pdf,.jpg,.png">
                            </div>
                            <div class="col-md-4">
                                <label for="director_pan_doc_${addDirectorIndex}" class="form-label">PAN Document</label>
                                <input type="file" class="form-control" id="director_pan_doc_${addDirectorIndex}"
                                    name="directors[${addDirectorIndex}][documents][pan_doc]" accept=".pdf,.jpg,.png">
                            </div>
                            <div class="col-md-4">
                                <label for="director_passport_doc_${addDirectorIndex}" class="form-label">Passport</label>
                                <input type="file" class="form-control" id="director_passport_doc_${addDirectorIndex}"
                                    name="directors[${addDirectorIndex}][documents][passport_doc]" accept=".pdf,.jpg,.png">
                            </div>
                            <div class="col-md-4">
                                <label for="director_driving_license_doc_${addDirectorIndex}" class="form-label">Driving License</label>
                                <input type="file" class="form-control" id="director_driving_license_doc_${addDirectorIndex}"
                                    name="directors[${addDirectorIndex}][documents][driving_license_doc]" accept=".pdf,.jpg,.png">
                            </div>
                            <div class="col-md-4">
                                <label for="director_bank_passbook_doc_${addDirectorIndex}" class="form-label">Bank Passbook</label>
                                <input type="file" class="form-control" id="director_bank_passbook_doc_${addDirectorIndex}"
                                    name="directors[${addDirectorIndex}][documents][bank_passbook_doc]" accept=".pdf,.jpg,.png">
                            </div>
                            <div class="col-md-4 d-flex align-items-end">
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
                        });
                    }
                });

                // Add director row in Edit Company Modal
                let editDirectorIndex = 1;
                $('#editAddDirectorRow').click(function(e) {
                    e.preventDefault();
                    $('#editCompanyForm .director-details').append(`
                        <div class="row g-3 director-row border-bottom pb-3 mb-3" data-index="${editDirectorIndex}">
                            <div class="col-12">
                                <h6 class="fw-bold">Director ${editDirectorIndex + 1}</h6>
                            </div>
                            <div class="col-md-4">
                                <label for="edit_director_name_${editDirectorIndex}" class="form-label fw-bold">Name of Director <span class="text-danger">*</span></label>
                                <input type="text" name="directors[${editDirectorIndex}][name]" class="form-control"
                                    id="edit_director_name_${editDirectorIndex}" required>
                            </div>
                            <div class="col-md-4">
                                <label for="edit_director_designation_${editDirectorIndex}" class="form-label fw-bold">Designation / Role <span class="text-danger">*</span></label>
                                <input type="text" name="directors[${editDirectorIndex}][designation]" class="form-control"
                                    id="edit_director_designation_${editDirectorIndex}" required>
                            </div>
                            <div class="col-md-4">
                                <label for="edit_director_din_${editDirectorIndex}" class="form-label fw-bold">DIN</label>
                                <input type="text" name="directors[${editDirectorIndex}][din]" class="form-control"
                                    id="edit_director_din_${editDirectorIndex}">
                            </div>
                            <div class="col-md-4">
                                <label for="edit_director_pan_${editDirectorIndex}" class="form-label fw-bold">PAN</label>
                                <input type="text" name="directors[${editDirectorIndex}][pan]" class="form-control"
                                    id="edit_director_pan_${editDirectorIndex}">
                            </div>
                            <div class="col-md-4">
                                <label for="edit_director_aadhaar_${editDirectorIndex}" class="form-label fw-bold">Aadhaar (if applicable)</label>
                                <input type="text" name="directors[${editDirectorIndex}][aadhaar]" class="form-control"
                                    id="edit_director_aadhaar_${editDirectorIndex}">
                            </div>
                            <div class="col-md-4">
                                <label for="edit_director_contact_${editDirectorIndex}" class="form-label fw-bold">Contact Number</label>
                                <input type="text" name="directors[${editDirectorIndex}][contact_number]" class="form-control"
                                    id="edit_director_contact_${editDirectorIndex}">
                            </div>
                            <div class="col-md-4">
                                <label for="edit_director_email_${editDirectorIndex}" class="form-label fw-bold">Email ID</label>
                                <input type="email" name="directors[${editDirectorIndex}][email]" class="form-control"
                                    id="edit_director_email_${editDirectorIndex}">
                            </div>
                            <div class="col-md-4">
                                <label for="edit_director_appointment_date_${editDirectorIndex}" class="form-label fw-bold">Date of Appointment</label>
                                <input type="date" name="directors[${editDirectorIndex}][appointment_date]" class="form-control"
                                    id="edit_director_appointment_date_${editDirectorIndex}">
                            </div>
                            <div class="col-md-4">
                                <label for="edit_director_resignation_date_${editDirectorIndex}" class="form-label fw-bold">Resignation Date</label>
                                <input type="date" name="directors[${editDirectorIndex}][resignation_date]" class="form-control"
                                    id="edit_director_resignation_date_${editDirectorIndex}">
                            </div>
                            <div class="col-12 mt-3">
                                <h6 class="fw-bold">Director Documents</h6>
                                <hr class="mt-1 mb-4">
                            </div>
                            <div class="col-md-4">
                                <label for="edit_director_aadhar_doc_${editDirectorIndex}" class="form-label">Aadhar Document</label>
                                <input type="file" class="form-control" id="edit_director_aadhar_doc_${editDirectorIndex}"
                                    name="directors[${editDirectorIndex}][documents][aadhar_doc]" accept=".pdf,.jpg,.png">
                                <div id="existing_director_aadhar_doc_${editDirectorIndex}" class="existing-document mt-2"></div>
                            </div>
                            <div class="col-md-4">
                                <label for="edit_director_pan_doc_${editDirectorIndex}" class="form-label">PAN Document</label>
                                <input type="file" class="form-control" id="edit_director_pan_doc_${editDirectorIndex}"
                                    name="directors[${editDirectorIndex}][documents][pan_doc]" accept=".pdf,.jpg,.png">
                                <div id="existing_director_pan_doc_${editDirectorIndex}" class="existing-document mt-2"></div>
                            </div>
                            <div class="col-md-4">
                                <label for="edit_director_passport_doc_${editDirectorIndex}" class="form-label">Passport</label>
                                <input type="file" class="form-control" id="edit_director_passport_doc_${editDirectorIndex}"
                                    name="directors[${editDirectorIndex}][documents][passport_doc]" accept=".pdf,.jpg,.png">
                                <div id="existing_director_passport_doc_${editDirectorIndex}" class="existing-document mt-2"></div>
                            </div>
                            <div class="col-md-4">
                                <label for="edit_director_driving_license_doc_${editDirectorIndex}" class="form-label">Driving License</label>
                                <input type="file" class="form-control" id="edit_director_driving_license_doc_${editDirectorIndex}"
                                    name="directors[${editDirectorIndex}][documents][driving_license_doc]" accept=".pdf,.jpg,.png">
                                <div id="existing_director_driving_license_doc_${editDirectorIndex}" class="existing-document mt-2"></div>
                            </div>
                            <div class="col-md-4">
                                <label for="edit_director_bank_passbook_doc_${editDirectorIndex}" class="form-label">Bank Passbook</label>
                                <input type="file" class="form-control" id="edit_director_bank_passbook_doc_${editDirectorIndex}"
                                    name="directors[${editDirectorIndex}][documents][bank_passbook_doc]" accept=".pdf,.jpg,.png">
                                <div id="existing_director_bank_passbook_doc_${editDirectorIndex}" class="existing-document mt-2"></div>
                            </div>
                            <div class="col-md-4 d-flex align-items-end">
                                <a href="javascript:void(0)" class="remove-director-row w-100 text-danger" title="Remove Director">
                                    <i class="ri-delete-bin-line me-1"></i> Remove Director
                                </a>
                            </div>
                        </div>
                    `);
                    editDirectorIndex++;
                });

                // Remove director row in Edit Company Modal
                $(document).on('click', '#editCompanyForm .remove-director-row', function(e) {
                    e.preventDefault();
                    if ($('#editCompanyForm .director-row').length > 1) {
                        $(this).closest('.director-row').remove();
                        $('#editCompanyForm .director-row').each(function(index) {
                            $(this).find('h6.fw-bold').text(`Director ${index + 1}`);
                        });
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
                        data.company_documents.forEach(doc => {
                            documentHtml += `
                                <div class="col-md-4">
                                    <label class="form-label fw-bold">${doc.document_type.replace(/_/g, ' ').toUpperCase()}</label>
                                    <p><a href="/storage/${doc.file_path}" target="_blank" class="text-primary">View Document</a></p>
                                </div>
                            `;
                        });
                        $('#view_company_documents').html(documentHtml || '<p class="text-muted">No documents available.</p>');

                        let directorHtml = '';
                        data.directors.forEach((director, index) => {
                            directorHtml += `
                                <div class="card mb-3 shadow-sm">
                                    <div class="card-body">
                                        <h6 class="fw-bold">Director ${index + 1}</h6>
                                        <div class="row g-3">
                                            <div class="col-md-4"><strong>Name:</strong> ${director.name || 'N/A'}</div>
                                            <div class="col-md-4"><strong>Designation/Role:</strong> ${director.designation || 'N/A'}</div>
                                            <div class="col-md-4"><strong>DIN:</strong> ${director.din || 'N/A'}</div>
                                            <div class="col-md-4"><strong>PAN:</strong> ${director.pan || 'N/A'}</div>
                                            <div class="col-md-4"><strong>Aadhaar:</strong> ${director.aadhaar || 'N/A'}</div>
                                            <div class="col-md-4"><strong>Contact Number:</strong> ${director.contact_number || 'N/A'}</div>
                                            <div class="col-md-4"><strong>Email:</strong> ${director.email || 'N/A'}</div>
                                            <div class="col-md-4"><strong>Date of Appointment:</strong> ${director.appointment_date || 'N/A'}</div>
                                            <div class="col-md-4"><strong>Resignation Date:</strong> ${director.resignation_date || 'N/A'}</div>
                                            <div class="col-12 mt-3">
                                                <h6 class="fw-bold">Documents</h6>
                                                <hr class="mt-1 mb-3">
                                            </div>
                                            ${director.documents ? Object.keys(director.documents).map(key => {
                                                return director.documents[key] ? `
                                                    <div class="col-md-4">
                                                        <strong>${key.replace(/_/g, ' ').toUpperCase()}:</strong>
                                                        <a href="/storage/${director.documents[key]}" target="_blank" class="text-primary">View Document</a>
                                                    </div>
                                                ` : '';
                                            }).join('') : '<p class="text-muted col-12">No documents available.</p>'}
                                        </div>
                                    </div>
                                </div>
                            `;
                        });
                        $('#view_director_details').html(directorHtml || '<p class="text-muted">No directors available.</p>');
                    });
                });

                // Edit company
                $(document).on('click', '.edit-btn', function() {
                    const id = $(this).data('id');
                    $.get(`/company/edit/${id}`, function(data) {
                        $('#edit_company_name').val(data.company.company_name);
                        $('#edit_company_code').val(data.company.company_code);
                        $('#edit_registration_number').val(data.company.registration_number);
                        $('#edit_gst_number').val(data.company.gst_number);
                        $('#editCompanyForm').attr('action', `/company/update/${id}`);

                        $('#editCompanyForm .existing-document').empty();
                        data.company_documents.forEach(doc => {
                            $(`#existing_${doc.document_type}`).html(`
                                <div class="d-flex align-items-center">
                                    <a href="/storage/${doc.file_path}" target="_blank" class="text-primary me-2">View ${doc.document_type.replace(/_/g, ' ').toUpperCase()}</a>
                                    <button type="button" class="btn btn-sm btn-danger delete-document" 
                                            data-id="${doc.id}" 
                                            data-type="${doc.document_type}"
                                            title="Delete Document">
                                        <i class="ri-delete-bin-line"></i>
                                    </button>
                                </div>
                            `);
                            $(`#edit_${doc.document_type}`).hide();
                        });

                        $('#editCompanyForm .director-details').empty();
                        editDirectorIndex = 0;
                        data.directors.forEach((director, index) => {
                            $('#editCompanyForm .director-details').append(`
                                <div class="row g-3 director-row border-bottom pb-3 mb-3" data-index="${index}">
                                    <div class="col-12">
                                        <h6 class="fw-bold">Director ${index + 1}</h6>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="edit_director_name_${index}" class="form-label fw-bold">Name of Director <span class="text-danger">*</span></label>
                                        <input type="text" name="directors[${index}][name]" class="form-control"
                                            id="edit_director_name_${index}" value="${director.name || ''}" required>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="edit_director_designation_${index}" class="form-label fw-bold">Designation / Role <span class="text-danger">*</span></label>
                                        <input type="text" name="directors[${index}][designation]" class="form-control"
                                            id="edit_director_designation_${index}" value="${director.designation || ''}" required>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="edit_director_din_${index}" class="form-label fw-bold">DIN</label>
                                        <input type="text" name="directors[${index}][din]" class="form-control"
                                            id="edit_director_din_${index}" value="${director.din || ''}">
                                    </div>
                                    <div class="col-md-4">
                                        <label for="edit_director_pan_${index}" class="form-label fw-bold">PAN</label>
                                        <input type="text" name="directors[${index}][pan]" class="form-control"
                                            id="edit_director_pan_${index}" value="${director.pan || ''}">
                                    </div>
                                    <div class="col-md-4">
                                        <label for="edit_director_aadhaar_${index}" class="form-label fw-bold">Aadhaar (if applicable)</label>
                                        <input type="text" name="directors[${index}][aadhaar]" class="form-control"
                                            id="edit_director_aadhaar_${index}" value="${director.aadhaar || ''}">
                                    </div>
                                    <div class="col-md-4">
                                        <label for="edit_director_contact_${index}" class="form-label fw-bold">Contact Number</label>
                                        <input type="text" name="directors[${index}][contact_number]" class="form-control"
                                            id="edit_director_contact_${index}" value="${director.contact_number || ''}">
                                    </div>
                                    <div class="col-md-4">
                                        <label for="edit_director_email_${index}" class="form-label fw-bold">Email ID</label>
                                        <input type="email" name="directors[${index}][email]" class="form-control"
                                            id="edit_director_email_${index}" value="${director.email || ''}">
                                    </div>
                                    <div class="col-md-4">
                                        <label for="edit_director_appointment_date_${index}" class="form-label fw-bold">Date of Appointment</label>
                                        <input type="date" name="directors[${index}][appointment_date]" class="form-control"
                                            id="edit_director_appointment_date_${index}" value="${director.appointment_date || ''}">
                                    </div>
                                    <div class="col-md-4">
                                        <label for="edit_director_resignation_date_${index}" class="form-label fw-bold">Resignation Date</label>
                                        <input type="date" name="directors[${index}][resignation_date]" class="form-control"
                                            id="edit_director_resignation_date_${index}" value="${director.resignation_date || ''}">
                                    </div>
                                    <div class="col-12 mt-3">
                                        <h6 class="fw-bold">Director Documents</h6>
                                        <hr class="mt-1 mb-4">
                                    </div>
                                    <div class="col-md-4">
                                        <label for="edit_director_aadhar_doc_${index}" class="form-label">Aadhar Document</label>
                                        <input type="file" class="form-control" id="edit_director_aadhar_doc_${index}"
                                            name="directors[${index}][documents][aadhar_doc]" accept=".pdf,.jpg,.png">
                                        <div id="existing_director_aadhar_doc_${index}" class="existing-document mt-2">
                                            ${director.documents && director.documents.aadhar_doc ? `
                                                <div class="d-flex align-items-center">
                                                    <a href="/storage/${director.documents.aadhar_doc}" target="_blank" class="text-primary me-2">View Aadhar Document</a>
                                                    <button type="button" class="btn btn-sm btn-danger delete-director-document"
                                                            data-id="${director.id}" 
                                                            data-type="aadhar_doc"
                                                            data-index="${index}"
                                                            title="Delete Document">
                                                        <i class="ri-delete-bin-line"></i>
                                                    </button>
                                                </div>
                                            ` : ''}
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="edit_director_pan_doc_${index}" class="form-label">PAN Document</label>
                                        <input type="file" class="form-control" id="edit_director_pan_doc_${index}"
                                            name="directors[${index}][documents][pan_doc]" accept=".pdf,.jpg,.png">
                                        <div id="existing_director_pan_doc_${index}" class="existing-document mt-2">
                                            ${director.documents && director.documents.pan_doc ? `
                                                <div class="d-flex align-items-center">
                                                    <a href="/storage/${director.documents.pan_doc}" target="_blank" class="text-primary me-2">View PAN Document</a>
                                                    <button type="button" class="btn btn-sm btn-danger delete-director-document"
                                                            data-id="${director.id}" 
                                                            data-type="pan_doc"
                                                            data-index="${index}"
                                                            title="Delete Document">
                                                        <i class="ri-delete-bin-line"></i>
                                                    </button>
                                                </div>
                                            ` : ''}
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="edit_director_passport_doc_${index}" class="form-label">Passport</label>
                                        <input type="file" class="form-control" id="edit_director_passport_doc_${index}"
                                            name="directors[${index}][documents][passport_doc]" accept=".pdf,.jpg,.png">
                                        <div id="existing_director_passport_doc_${index}" class="existing-document mt-2">
                                            ${director.documents && director.documents.passport_doc ? `
                                                <div class="d-flex align-items-center">
                                                    <a href="/storage/${director.documents.passport_doc}" target="_blank" class="text-primary me-2">View Passport</a>
                                                    <button type="button" class="btn btn-sm btn-danger delete-director-document"
                                                            data-id="${director.id}" 
                                                            data-type="passport_doc"
                                                            data-index="${index}"
                                                            title="Delete Document">
                                                        <i class="ri-delete-bin-line"></i>
                                                    </button>
                                                </div>
                                            ` : ''}
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="edit_director_driving_license_doc_${index}" class="form-label">Driving License</label>
                                        <input type="file" class="form-control" id="edit_director_driving_license_doc_${index}"
                                            name="directors[${index}][documents][driving_license_doc]" accept=".pdf,.jpg,.png">
                                        <div id="existing_director_driving_license_doc_${index}" class="existing-document mt-2">
                                            ${director.documents && director.documents.driving_license_doc ? `
                                                <div class="d-flex align-items-center">
                                                    <a href="/storage/${director.documents.driving_license_doc}" target="_blank" class="text-primary me-2">View Driving License</a>
                                                    <button type="button" class="btn btn-sm btn-danger delete-director-document"
                                                            data-id="${director.id}" 
                                                            data-type="driving_license_doc"
                                                            data-index="${index}"
                                                            title="Delete Document">
                                                        <i class="ri-delete-bin-line"></i>
                                                    </button>
                                                </div>
                                            ` : ''}
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="edit_director_bank_passbook_doc_${index}" class="form-label">Bank Passbook</label>
                                        <input type="file" class="form-control" id="edit_director_bank_passbook_doc_${index}"
                                            name="directors[${index}][documents][bank_passbook_doc]" accept=".pdf,.jpg,.png">
                                        <div id="existing_director_bank_passbook_doc_${index}" class="existing-document mt-2">
                                            ${director.documents && director.documents.bank_passbook_doc ? `
                                                <div class="d-flex align-items-center">
                                                    <a href="/storage/${director.documents.bank_passbook_doc}" target="_blank" class="text-primary me-2">View Bank Passbook</a>
                                                    <button type="button" class="btn btn-sm btn-danger delete-director-document"
                                                            data-id="${director.id}" 
                                                            data-type="bank_passbook_doc"
                                                            data-index="${index}"
                                                            title="Delete Document">
                                                        <i class="ri-delete-bin-line"></i>
                                                    </button>
                                                </div>
                                            ` : ''}
                                        </div>
                                    </div>
                                    <div class="col-md-4 d-flex align-items-end">
                                        <a href="javascript:void(0)" class="remove-director-row w-100 text-danger" title="Remove Director">
                                            <i class="ri-delete-bin-line me-1"></i> Remove Director
                                        </a>
                                    </div>
                                </div>
                            `);
                            editDirectorIndex = index + 1;
                        });
                    });
                });

                // Delete company document
                $(document).on('click', '.delete-document', function() {
                    const docId = $(this).data('id');
                    const docType = $(this).data('type');
                    if (confirm('Are you sure you want to delete this document?')) {
                        $.ajax({
                            url: `/company/document/${docId}`,
                            method: 'DELETE',
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                $(`#existing_${docType}`).empty();
                                $(`#edit_${docType}`).show();
                                alert('Document deleted successfully.');
                            },
                            error: function(xhr) {
                                alert('Failed to delete document. Please try again.');
                            }
                        });
                    }
                });

                // Delete director document
                $(document).on('click', '.delete-director-document', function() {
                    const docId = $(this).data('id');
                    const docType = $(this).data('type');
                    const index = $(this).data('index');
                    if (confirm('Are you sure you want to delete this director document?')) {
                        $.ajax({
                            url: `/company/directors/document/${docId}/${docType}`,
                            method: 'DELETE',
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                $(`#existing_director_${docType}_${index}`).empty();
                                $(`#edit_director_${docType}_${index}`).show();
                                alert('Director document deleted successfully.');
                            },
                            error: function(xhr) {
                                alert('Failed to delete director document. Please try again.');
                            }
                        });
                    }
                });

                // Fade out alerts
                setTimeout(function() {
                    let alert = document.querySelector('.alert');
                    if (alert) {
                        alert.classList.remove('show');
                        alert.classList.add('fade');
                        setTimeout(() => alert.remove(), 500);
                    }
                }, 2000);
            });
        </script>
    </div>
@endsection --}}
@extends('layouts.app')
@section('content')
    <div class="page-content">
        {{-- <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between bg-galaxy-transparent p-3 rounded">
                        <h4 class="mb-sm-0 text-white">{{ ucwords(str_replace('-', ' ', Request::path())) }}</h4>
                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="javascript: void(0);" class="text-white">Masters</a></li>
                                <li class="breadcrumb-item active text-white">{{ ucwords(str_replace('-', ' ', Request::path())) }}</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div> --}}
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
                    <div class="modal-dialog modal-xl modal-dialog-scrollable">
                        <form method="POST" action="{{ route('company.store') }}" enctype="multipart/form-data">
                            @csrf
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="addCompanyModalLabel"><i class="ri-building-4-line me-2"></i>Add New Company</h5>
                                    <button type="button" class="btn-close btn-close-black" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body" style="max-height: 70vh; overflow-y: auto;">
                                    <!-- Company Information and Documents Section -->
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
                                                <div class="row g-4">
                                                    <div class="col-md-4">
                                                        <label for="company_name" class="form-label fw-bold">Company Name <span class="text-danger">*</span></label>
                                                        <select class="form-select @error('company_name') is-invalid @enderror"
                                                            id="company_name" name="company_name" required>
                                                            <option value="">Select Company</option>
                                                        </select>
                                                        @error('company_name')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label for="company_code" class="form-label fw-bold">Company Code</label>
                                                        <input type="text" class="form-control @error('company_code') is-invalid @enderror"
                                                            id="company_code" name="company_code" readonly>
                                                        @error('company_code')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label for="registration_number" class="form-label fw-bold">Registration Number</label>
                                                        <input type="text" class="form-control @error('registration_number') is-invalid @enderror"
                                                            id="registration_number" name="registration_number" readonly>
                                                        @error('registration_number')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label for="gst_number" class="form-label fw-bold">GST Number</label>
                                                        <input type="text" class="form-control @error('gst_number') is-invalid @enderror"
                                                            id="gst_number" name="gst_number" readonly>
                                                        @error('gst_number')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label for="tin" class="form-label fw-bold">TIN (if applicable)</label>
                                                        <input type="text" class="form-control">
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label for="date_of_incorporation" class="form-label fw-bold">Date of Incorporation</label>
                                                        <input type="date" class="form-control">
                                                    </div>
                                                    <div class="col-12 mt-3">
                                                        <h6 class="fw-bold">Company Documents</h6>
                                                        <hr class="mt-1 mb-4">
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label for="certificate_incorporation" class="form-label">Certificate of Incorporation</label>
                                                        <input type="file" class="form-control" id="certificate_incorporation"
                                                            name="documents[certificate_incorporation]" accept=".pdf,.jpg,.png">
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label for="company_pan_card" class="form-label">PAN Card</label>
                                                        <input type="file" class="form-control" id="company_pan_card"
                                                            name="documents[company_pan_card]" accept=".pdf,.jpg,.png">
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label for="aoa" class="form-label">AOA (Articles of Association)</label>
                                                        <input type="file" class="form-control" id="aoa" name="documents[aoa]"
                                                            accept=".pdf,.jpg,.png">
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label for="moa" class="form-label">MOA (Memorandum of Association)</label>
                                                        <input type="file" class="form-control" id="moa" name="documents[moa]"
                                                            accept=".pdf,.jpg,.png">
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label for="gst_certificate" class="form-label">GST Certificate</label>
                                                        <input type="file" class="form-control" id="gst_certificate"
                                                            name="documents[gst_certificate]" accept=".pdf,.jpg,.png">
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label for="board_resolution" class="form-label">Board Resolution for Authorized Signatory</label>
                                                        <input type="file" class="form-control" id="board_resolution"
                                                            name="documents[board_resolution]" accept=".pdf,.jpg,.png">
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label for="signature_specimen" class="form-label">Signature Specimen</label>
                                                        <input type="file" class="form-control" id="signature_specimen"
                                                            name="documents[signature_specimen]" accept=".pdf,.jpg,.png">
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label for="other_docs" class="form-label">Any Other Relevant Docs</label>
                                                        <div class="side-by-side-inputs">
                                                            <input type="text" class="form-control" id="other_docs_name"
                                                                name="documents[other_docs_name]" placeholder="Document Name">
                                                            <input type="file" class="form-control" id="other_docs"
                                                                name="documents[other_docs]" accept=".pdf,.jpg,.png">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Director Details and Documents Section -->
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
                                                        <div class="col-md-4">
                                                            <label for="director_name_0" class="form-label fw-bold">Name of Director <span class="text-danger">*</span></label>
                                                            <input type="text" name="directors[0][name]" class="form-control @error('directors.0.name') is-invalid @enderror"
                                                                id="director_name_0" required>
                                                            @error('directors.0.name')
                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                        <div class="col-md-4">
                                                            <label for="director_designation_0" class="form-label fw-bold">Designation / Role <span class="text-danger">*</span></label>
                                                            <input type="text" name="directors[0][designation]" class="form-control @error('directors.0.designation') is-invalid @enderror"
                                                                id="director_designation_0" required>
                                                            @error('directors.0.designation')
                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                        <div class="col-md-4">
                                                            <label for="director_din_0" class="form-label fw-bold">DIN</label>
                                                            <input type="text" name="directors[0][din]" class="form-control @error('directors.0.din') is-invalid @enderror"
                                                                id="director_din_0">
                                                            @error('directors.0.din')
                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                        <div class="col-md-4">
                                                            <label for="director_pan_0" class="form-label fw-bold">PAN</label>
                                                            <input type="text" name="directors[0][pan]" class="form-control @error('directors.0.pan') is-invalid @enderror"
                                                                id="director_pan_0">
                                                            @error('directors.0.pan')
                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                        <div class="col-md-4">
                                                            <label for="director_aadhaar_0" class="form-label fw-bold">Aadhaar (if applicable)</label>
                                                            <input type="text" name="directors[0][aadhaar]" class="form-control @error('directors.0.aadhaar') is-invalid @enderror"
                                                                id="director_aadhaar_0">
                                                            @error('directors.0.aadhaar')
                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                        <div class="col-md-4">
                                                            <label for="director_contact_0" class="form-label fw-bold">Contact Number</label>
                                                            <input type="text" name="directors[0][contact_number]" class="form-control @error('directors.0.contact_number') is-invalid @enderror"
                                                                id="director_contact_0">
                                                            @error('directors.0.contact_number')
                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                        <div class="col-md-4">
                                                            <label for="director_email_0" class="form-label fw-bold">Email ID</label>
                                                            <input type="email" name="directors[0][email]" class="form-control @error('directors.0.email') is-invalid @enderror"
                                                                id="director_email_0">
                                                            @error('directors.0.email')
                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                        <div class="col-md-4">
                                                            <label for="director_appointment_date_0" class="form-label fw-bold">Date of Appointment</label>
                                                            <input type="date" name="directors[0][appointment_date]" class="form-control @error('directors.0.appointment_date') is-invalid @enderror"
                                                                id="director_appointment_date_0">
                                                            @error('directors.0.appointment_date')
                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                        <div class="col-md-4">
                                                            <label for="director_resignation_date_0" class="form-label fw-bold">Resignation Date</label>
                                                            <input type="date" name="directors[0][resignation_date]" class="form-control @error('directors.0.resignation_date') is-invalid @enderror"
                                                                id="director_resignation_date_0">
                                                            @error('directors.0.resignation_date')
                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                        <div class="col-12 mt-3">
                                                            <h6 class="fw-bold">Director Documents</h6>
                                                            <hr class="mt-1 mb-4">
                                                        </div>
                                                        <div class="col-md-4">
                                                            <label for="director_aadhar_doc_0" class="form-label">Aadhar Document</label>
                                                            <input type="file" class="form-control @error('directors.0.documents.aadhar_doc') is-invalid @enderror"
                                                                id="director_aadhar_doc_0" name="directors[0][documents][aadhar_doc]" accept=".pdf,.jpg,.png">
                                                            @error('directors.0.documents.aadhar_doc')
                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                        <div class="col-md-4">
                                                            <label for="director_pan_doc_0" class="form-label">PAN Document</label>
                                                            <input type="file" class="form-control @error('directors.0.documents.pan_doc') is-invalid @enderror"
                                                                id="director_pan_doc_0" name="directors[0][documents][pan_doc]" accept=".pdf,.jpg,.png">
                                                            @error('directors.0.documents.pan_doc')
                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                        <div class="col-md-4">
                                                            <label for="director_passport_doc_0" class="form-label">Passport</label>
                                                            <input type="file" class="form-control @error('directors.0.documents.passport_doc') is-invalid @enderror"
                                                                id="director_passport_doc_0" name="directors[0][documents][passport_doc]" accept=".pdf,.jpg,.png">
                                                            @error('directors.0.documents.passport_doc')
                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                        <div class="col-md-4">
                                                            <label for="director_driving_license_doc_0" class="form-label">Driving License</label>
                                                            <input type="file" class="form-control @error('directors.0.documents.driving_license_doc') is-invalid @enderror"
                                                                id="director_driving_license_doc_0" name="directors[0][documents][driving_license_doc]" accept=".pdf,.jpg,.png">
                                                            @error('directors.0.documents.driving_license_doc')
                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                        <div class="col-md-4">
                                                            <label for="director_bank_passbook_doc_0" class="form-label">Bank Passbook</label>
                                                            <input type="file" class="form-control @error('directors.0.documents.bank_passbook_doc') is-invalid @enderror"
                                                                id="director_bank_passbook_doc_0" name="directors[0][documents][bank_passbook_doc]" accept=".pdf,.jpg,.png">
                                                            @error('directors.0.documents.bank_passbook_doc')
                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                        <div class="col-md-4 d-flex align-items-end">
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
            @endcan

            <!-- View Company Modal -->
            @can('view-Company')
                <div class="modal fade" id="viewCompanyModal" tabindex="-1" aria-labelledby="viewCompanyModalLabel"
                    aria-hidden="true">
                    <div class="modal-dialog modal-xl">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="viewCompanyModalLabel"><i class="ri-building-4-line me-2"></i>View Company Details</h5>
                                <button type="button" class="btn-close btn-close-black" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div class="card mb-4">
                                    <div class="card-header bg-light">
                                        <h5 class="mb-0"><i class="ri-information-line me-2"></i>Company Information and Documents</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row g-4">
                                            <div class="col-md-6">
                                                <label class="form-label fw-bold">Company Name</label>
                                                <p id="view_company_name" class="form-control-static"></p>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label fw-bold">Company Code</label>
                                                <p id="view_company_code" class="form-control-static"></p>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label fw-bold">Registration Number</label>
                                                <p id="view_registration_number" class="form-control-static"></p>
                                            </div>
                                            <div class="col-md-6">
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
                                        <h5 class="mb-0"><i class="ri-user-3-line me-2"></i>Director Details and Documents</h5>
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
                    <div class="modal-dialog modal-xl modal-dialog-scrollable">
                        <form method="POST" action="" id="editCompanyForm" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="editCompanyModalLabel"><i class="ri-edit-2-line me-2"></i>Edit Company</h5>
                                    <button type="button" class="btn-close btn-close-black" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body" style="max-height: 70vh; overflow-y: auto;">
                                    <!-- Company Information and Documents Section -->
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
                                                <div class="row g-4">
                                                    <div class="col-md-4">
                                                        <label for="edit_company_name" class="form-label fw-bold">Company Name <span class="text-danger">*</span></label>
                                                        <input type="text" class="form-control @error('company_name') is-invalid @enderror"
                                                            id="edit_company_name" name="company_name" required>
                                                        @error('company_name')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label for="edit_company_code" class="form-label fw-bold">Company Code</label>
                                                        <input type="text" class="form-control @error('company_code') is-invalid @enderror"
                                                            id="edit_company_code" name="company_code">
                                                        @error('company_code')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label for="edit_registration_number" class="form-label fw-bold">Registration Number</label>
                                                        <input type="text" class="form-control @error('registration_number') is-invalid @enderror"
                                                            id="edit_registration_number" name="registration_number">
                                                        @error('registration_number')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label for="edit_gst_number" class="form-label fw-bold">GST Number</label>
                                                        <input type="text" class="form-control @error('gst_number') is-invalid @enderror"
                                                            id="edit_gst_number" name="gst_number">
                                                        @error('gst_number')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                    <div class="col-12 mt-3">
                                                        <h6 class="fw-bold">Company Documents</h6>
                                                        <hr class="mt-1 mb-4">
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label for="edit_certificate_incorporation" class="form-label">Certificate of Incorporation</label>
                                                        <input type="file" class="form-control" id="edit_certificate_incorporation"
                                                            name="documents[certificate_incorporation]" accept=".pdf,.jpg,.png">
                                                        <div id="existing_certificate_incorporation" class="existing-document mt-2"></div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label for="edit_company_pan_card" class="form-label">PAN Card</label>
                                                        <input type="file" class="form-control" id="edit_company_pan_card"
                                                            name="documents[company_pan_card]" accept=".pdf,.jpg,.png">
                                                        <div id="existing_company_pan_card" class="existing-document mt-2"></div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label for="edit_aoa" class="form-label">AOA (Articles of Association)</label>
                                                        <input type="file" class="form-control" id="edit_aoa" name="documents[aoa]"
                                                            accept=".pdf,.jpg,.png">
                                                        <div id="existing_aoa" class="existing-document mt-2"></div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label for="edit_moa" class="form-label">MOA (Memorandum of Association)</label>
                                                        <input type="file" class="form-control" id="edit_moa" name="documents[moa]"
                                                            accept=".pdf,.jpg,.png">
                                                        <div id="existing_moa" class="existing-document mt-2"></div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label for="edit_gst_certificate" class="form-label">GST Certificate</label>
                                                        <input type="file" class="form-control" id="edit_gst_certificate"
                                                            name="documents[gst_certificate]" accept=".pdf,.jpg,.png">
                                                        <div id="existing_gst_certificate" class="existing-document mt-2"></div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label for="edit_board_resolution" class="form-label">Board Resolution for Authorized Signatory</label>
                                                        <input type="file" class="form-control" id="edit_board_resolution"
                                                            name="documents[board_resolution]" accept=".pdf,.jpg,.png">
                                                        <div id="existing_board_resolution" class="existing-document mt-2"></div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label for="edit_signature_specimen" class="form-label">Signature Specimen</label>
                                                        <input type="file" class="form-control" id="edit_signature_specimen"
                                                            name="documents[signature_specimen]" accept=".pdf,.jpg,.png">
                                                        <div id="existing_signature_specimen" class="existing-document mt-2"></div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label for="edit_other_docs" class="form-label">Any Other Relevant Docs</label>
                                                        <div class="side-by-side-inputs">
                                                            <input type="text" class="form-control" id="edit_other_docs_name"
                                                                name="documents[other_docs_name]" placeholder="Document Name">
                                                            <input type="file" class="form-control" id="edit_other_docs"
                                                                name="documents[other_docs]" accept=".pdf,.jpg,.png">
                                                        </div>
                                                        <div id="existing_other_docs" class="existing-document mt-2"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Director Details and Documents Section -->
                                    <div class="card mb-4">
                                        <div id="editDirectorInfoHeading">
                                            <h5 class="mb-0">
                                                <button class="btn btn-link text-primary text-decoration-none" type="button" data-bs-toggle="collapse"
                                                    data-bs-target="#editDirectorInfoCollapse" aria-expanded="true" aria-controls="editDirectorInfoCollapse">
                                                    <i class="ri-user-3-line me-2"></i>Director Details and Documents
                                                </button>
                                            </h5>
                                        </div>
                                        <div id="editDirectorInfoCollapse" class="collapse show" aria-labelledby="editDirectorInfoHeading">
                                            <div class="card-body">
                                                <div class="director-details"></div>
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
                    <div class="row g-3 director-row border-bottom pb-3 mb-3" data-index="${addDirectorIndex}">
                        <div class="col-12">
                            <h6 class="fw-bold">Director ${addDirectorIndex + 1}</h6>
                        </div>
                        <div class="col-md-4">
                            <label for="director_name_${addDirectorIndex}" class="form-label fw-bold">Name of Director <span class="text-danger">*</span></label>
                            <input type="text" name="directors[${addDirectorIndex}][name]" class="form-control"
                                id="director_name_${addDirectorIndex}" required>
                        </div>
                        <div class="col-md-4">
                            <label for="director_designation_${addDirectorIndex}" class="form-label fw-bold">Designation / Role <span class="text-danger">*</span></label>
                            <input type="text" name="directors[${addDirectorIndex}][designation]" class="form-control"
                                id="director_designation_${addDirectorIndex}" required>
                        </div>
                        <div class="col-md-4">
                            <label for="director_din_${addDirectorIndex}" class="form-label fw-bold">DIN</label>
                            <input type="text" name="directors[${addDirectorIndex}][din]" class="form-control"
                                id="director_din_${addDirectorIndex}">
                        </div>
                        <div class="col-md-4">
                            <label for="director_pan_${addDirectorIndex}" class="form-label fw-bold">PAN</label>
                            <input type="text" name="directors[${addDirectorIndex}][pan]" class="form-control"
                                id="director_pan_${addDirectorIndex}">
                        </div>
                        <div class="col-md-4">
                            <label for="director_aadhaar_${addDirectorIndex}" class="form-label fw-bold">Aadhaar (if applicable)</label>
                            <input type="text" name="directors[${addDirectorIndex}][aadhaar]" class="form-control"
                                id="director_aadhaar_${addDirectorIndex}">
                        </div>
                        <div class="col-md-4">
                            <label for="director_contact_${addDirectorIndex}" class="form-label fw-bold">Contact Number</label>
                            <input type="text" name="directors[${addDirectorIndex}][contact_number]" class="form-control"
                                id="director_contact_${addDirectorIndex}">
                        </div>
                        <div class="col-md-4">
                            <label for="director_email_${addDirectorIndex}" class="form-label fw-bold">Email ID</label>
                            <input type="email" name="directors[${addDirectorIndex}][email]" class="form-control"
                                id="director_email_${addDirectorIndex}">
                        </div>
                        <div class="col-md-4">
                            <label for="director_appointment_date_${addDirectorIndex}" class="form-label fw-bold">Date of Appointment</label>
                            <input type="date" name="directors[${addDirectorIndex}][appointment_date]" class="form-control"
                                id="director_appointment_date_${addDirectorIndex}">
                        </div>
                        <div class="col-md-4">
                            <label for="director_resignation_date_${addDirectorIndex}" class="form-label fw-bold">Resignation Date</label>
                            <input type="date" name="directors[${addDirectorIndex}][resignation_date]" class="form-control"
                                id="director_resignation_date_${addDirectorIndex}">
                        </div>
                        <div class="col-12 mt-3">
                            <h6 class="fw-bold">Director Documents</h6>
                            <hr class="mt-1 mb-4">
                        </div>
                        <div class="col-md-4">
                            <label for="director_aadhar_doc_${addDirectorIndex}" class="form-label">Aadhar Document</label>
                            <input type="file" class="form-control" id="director_aadhar_doc_${addDirectorIndex}"
                                name="directors[${addDirectorIndex}][documents][aadhar_doc]" accept=".pdf,.jpg,.png">
                        </div>
                        <div class="col-md-4">
                            <label for="director_pan_doc_${addDirectorIndex}" class="form-label">PAN Document</label>
                            <input type="file" class="form-control" id="director_pan_doc_${addDirectorIndex}"
                                name="directors[${addDirectorIndex}][documents][pan_doc]" accept=".pdf,.jpg,.png">
                        </div>
                        <div class="col-md-4">
                            <label for="director_passport_doc_${addDirectorIndex}" class="form-label">Passport</label>
                            <input type="file" class="form-control" id="director_passport_doc_${addDirectorIndex}"
                                name="directors[${addDirectorIndex}][documents][passport_doc]" accept=".pdf,.jpg,.png">
                        </div>
                        <div class="col-md-4">
                            <label for="director_driving_license_doc_${addDirectorIndex}" class="form-label">Driving License</label>
                            <input type="file" class="form-control" id="director_driving_license_doc_${addDirectorIndex}"
                                name="directors[${addDirectorIndex}][documents][driving_license_doc]" accept=".pdf,.jpg,.png">
                        </div>
                        <div class="col-md-4">
                            <label for="director_bank_passbook_doc_${addDirectorIndex}" class="form-label">Bank Passbook</label>
                            <input type="file" class="form-control" id="director_bank_passbook_doc_${addDirectorIndex}"
                                name="directors[${addDirectorIndex}][documents][bank_passbook_doc]" accept=".pdf,.jpg,.png">
                        </div>
                        <div class="col-md-4 d-flex align-items-end">
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
                    data.company_documents.forEach(doc => {
                        documentHtml += `
                            <div class="col-md-4">
                                <label class="form-label fw-bold">${doc.document_type.replace(/_/g, ' ').toUpperCase()}</label>
                                <p><a href="/storage/${doc.file_path}" target="_blank" class="text-primary">View Document</a></p>
                            </div>
                        `;
                    });
                    $('#view_company_documents').html(documentHtml || '<p class="text-muted">No documents available.</p>');

                    let directorHtml = '';
                    data.directors.forEach((director, index) => {
                        directorHtml += `
                            <div class="card mb-3 shadow-sm">
                                <div class="card-body">
                                    <h6 class="fw-bold">Director ${index + 1}</h6>
                                    <div class="row g-3">
                                        <div class="col-md-4"><strong>Name:</strong> ${director.name || 'N/A'}</div>
                                        <div class="col-md-4"><strong>Designation/Role:</strong> ${director.designation || 'N/A'}</div>
                                        <div class="col-md-4"><strong>DIN:</strong> ${director.din || 'N/A'}</div>
                                        <div class="col-md-4"><strong>PAN:</strong> ${director.pan || 'N/A'}</div>
                                        <div class="col-md-4"><strong>Aadhaar:</strong> ${director.aadhaar || 'N/A'}</div>
                                        <div class="col-md-4"><strong>Contact Number:</strong> ${director.contact_number || 'N/A'}</div>
                                        <div class="col-md-4"><strong>Email:</strong> ${director.email || 'N/A'}</div>
                                        <div class="col-md-4"><strong>Date of Appointment:</strong> ${director.appointment_date || 'N/A'}</div>
                                        <div class="col-md-4"><strong>Resignation Date:</strong> ${director.resignation_date || 'N/A'}</div>
                                        <div class="col-12 mt-3">
                                            <h6 class="fw-bold">Documents</h6>
                                            <hr class="mt-1 mb-3">
                                        </div>
                                        ${director.documents.aadhar_doc ? `
                                            <div class="col-md-4">
                                                <strong>Aadhar Document:</strong>
                                                <a href="/storage/${director.documents.aadhar_doc}" target="_blank" class="text-primary">View Document</a>
                                            </div>
                                        ` : '<div class="col-md-4"><strong>Aadhar Document:</strong> N/A</div>'}
                                        ${director.documents.pan_doc ? `
                                            <div class="col-md-4">
                                                <strong>PAN Document:</strong>
                                                <a href="/storage/${director.documents.pan_doc}" target="_blank" class="text-primary">View Document</a>
                                            </div>
                                        ` : '<div class="col-md-4"><strong>PAN Document:</strong> N/A</div>'}
                                        ${director.documents.passport_doc ? `
                                            <div class="col-md-4">
                                                <strong>Passport:</strong>
                                                <a href="/storage/${director.documents.passport_doc}" target="_blank" class="text-primary">View Document</a>
                                            </div>
                                        ` : '<div class="col-md-4"><strong>Passport:</strong> N/A</div>'}
                                        ${director.documents.driving_license_doc ? `
                                            <div class="col-md-4">
                                                <strong>Driving License:</strong>
                                                <a href="/storage/${director.documents.driving_license_doc}" target="_blank" class="text-primary">View Document</a>
                                            </div>
                                        ` : '<div class="col-md-4"><strong>Driving License:</strong> N/A</div>'}
                                        ${director.documents.bank_passbook_doc ? `
                                            <div class="col-md-4">
                                                <strong>Bank Passbook:</strong>
                                                <a href="/storage/${director.documents.bank_passbook_doc}" target="_blank" class="text-primary">View Document</a>
                                            </div>
                                        ` : '<div class="col-md-4"><strong>Bank Passbook:</strong> N/A</div>'}
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
            let editDirectorIndex = 1;
            $(document).on('click', '.edit-btn', function() {
                const id = $(this).data('id');
                $.get(`/company/edit/${id}`, function(data) {
                    $('#edit_company_name').val(data.company.company_name);
                    $('#edit_company_code').val(data.company.company_code);
                    $('#edit_registration_number').val(data.company.registration_number);
                    $('#edit_gst_number').val(data.company.gst_number);
                    $('#editCompanyForm').attr('action', `/company/update/${id}`);

                    $('#editCompanyForm .existing-document').empty();
                    data.company_documents.forEach(doc => {
                        $(`#existing_${doc.document_type}`).html(`
                            <div class="d-flex align-items-center">
                                <a href="/storage/${doc.file_path}" target="_blank" class="text-primary me-2">View ${doc.document_type.replace(/_/g, ' ').toUpperCase()}</a>
                                <button type="button" class="btn btn-sm btn-danger delete-document"
                                        data-id="${doc.id}"
                                        data-type="${doc.document_type}"
                                        title="Delete Document">
                                    <i class="ri-delete-bin-line"></i>
                                </button>
                            </div>
                        `);
                        $(`#edit_${doc.document_type}`).hide();
                    });

                    $('#editCompanyForm .director-details').empty();
                    editDirectorIndex = 0;
                    data.directors.forEach((director, index) => {
                        $('#editCompanyForm .director-details').append(`
                            <div class="row g-3 director-row border-bottom pb-3 mb-3" data-index="${index}">
                                <div class="col-12">
                                    <h6 class="fw-bold">Director ${index + 1}</h6>
                                </div>
                                <div class="col-md-4">
                                    <label for="edit_director_name_${index}" class="form-label fw-bold">Name of Director <span class="text-danger">*</span></label>
                                    <input type="text" name="directors[${index}][name]" class="form-control"
                                        id="edit_director_name_${index}" value="${director.name || ''}" required>
                                </div>
                                <div class="col-md-4">
                                    <label for="edit_director_designation_${index}" class="form-label fw-bold">Designation / Role <span class="text-danger">*</span></label>
                                    <input type="text" name="directors[${index}][designation]" class="form-control"
                                        id="edit_director_designation_${index}" value="${director.designation || ''}" required>
                                </div>
                                <div class="col-md-4">
                                    <label for="edit_director_din_${index}" class="form-label fw-bold">DIN</label>
                                    <input type="text" name="directors[${index}][din]" class="form-control"
                                        id="edit_director_din_${index}" value="${director.din || ''}">
                                </div>
                                <div class="col-md-4">
                                    <label for="edit_director_pan_${index}" class="form-label fw-bold">PAN</label>
                                    <input type="text" name="directors[${index}][pan]" class="form-control"
                                        id="edit_director_pan_${index}" value="${director.pan || ''}">
                                </div>
                                <div class="col-md-4">
                                    <label for="edit_director_aadhaar_${index}" class="form-label fw-bold">Aadhaar (if applicable)</label>
                                    <input type="text" name="directors[${index}][aadhaar]" class="form-control"
                                        id="edit_director_aadhaar_${index}" value="${director.aadhaar || ''}">
                                </div>
                                <div class="col-md-4">
                                    <label for="edit_director_contact_${index}" class="form-label fw-bold">Contact Number</label>
                                    <input type="text" name="directors[${index}][contact_number]" class="form-control"
                                        id="edit_director_contact_${index}" value="${director.contact_number || ''}">
                                </div>
                                <div class="col-md-4">
                                    <label for="edit_director_email_${index}" class="form-label fw-bold">Email ID</label>
                                    <input type="email" name="directors[${index}][email]" class="form-control"
                                        id="edit_director_email_${index}" value="${director.email || ''}">
                                </div>
                                <div class="col-md-4">
                                    <label for="edit_director_appointment_date_${index}" class="form-label fw-bold">Date of Appointment</label>
                                    <input type="date" name="directors[${index}][appointment_date]" class="form-control"
                                        id="edit_director_appointment_date_${index}" value="${director.appointment_date || ''}">
                                </div>
                                <div class="col-md-4">
                                    <label for="edit_director_resignation_date_${index}" class="form-label fw-bold">Resignation Date</label>
                                    <input type="date" name="directors[${index}][resignation_date]" class="form-control"
                                        id="edit_director_resignation_date_${index}" value="${director.resignation_date || ''}">
                                </div>
                                <div class="col-12 mt-3">
                                    <h6 class="fw-bold">Director Documents</h6>
                                    <hr class="mt-1 mb-4">
                                </div>
                                <div class="col-md-4">
                                    <label for="edit_director_aadhar_doc_${index}" class="form-label">Aadhar Document</label>
                                    <input type="file" class="form-control" id="edit_director_aadhar_doc_${index}"
                                        name="directors[${index}][documents][aadhar_doc]" accept=".pdf,.jpg,.png">
                                    <div id="existing_director_aadhar_doc_${index}" class="existing-document mt-2">
                                        ${director.documents.aadhar_doc ? `
                                            <div class="d-flex align-items-center">
                                                <a href="/storage/${director.documents.aadhar_doc}" target="_blank" class="text-primary me-2">View Aadhar Document</a>
                                                <button type="button" class="btn btn-sm btn-danger delete-director-document"
                                                        data-id="${director.id}"
                                                        data-type="aadhar_doc"
                                                        data-index="${index}"
                                                        title="Delete Document">
                                                    <i class="ri-delete-bin-line"></i>
                                                </button>
                                            </div>
                                        ` : ''}
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label for="edit_director_pan_doc_${index}" class="form-label">PAN Document</label>
                                    <input type="file" class="form-control" id="edit_director_pan_doc_${index}"
                                        name="directors[${index}][documents][pan_doc]" accept=".pdf,.jpg,.png">
                                    <div id="existing_director_pan_doc_${index}" class="existing-document mt-2">
                                        ${director.documents.pan_doc ? `
                                            <div class="d-flex align-items-center">
                                                <a href="/storage/${director.documents.pan_doc}" target="_blank" class="text-primary me-2">View PAN Document</a>
                                                <button type="button" class="btn btn-sm btn-danger delete-director-document"
                                                        data-id="${director.id}"
                                                        data-type="pan_doc"
                                                        data-index="${index}"
                                                        title="Delete Document">
                                                    <i class="ri-delete-bin-line"></i>
                                                </button>
                                            </div>
                                        ` : ''}
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label for="edit_director_passport_doc_${index}" class="form-label">Passport</label>
                                    <input type="file" class="form-control" id="edit_director_passport_doc_${index}"
                                        name="directors[${index}][documents][passport_doc]" accept=".pdf,.jpg,.png">
                                    <div id="existing_director_passport_doc_${index}" class="existing-document mt-2">
                                        ${director.documents.passport_doc ? `
                                            <div class="d-flex align-items-center">
                                                <a href="/storage/${director.documents.passport_doc}" target="_blank" class="text-primary me-2">View Passport</a>
                                                <button type="button" class="btn btn-sm btn-danger delete-director-document"
                                                        data-id="${director.id}"
                                                        data-type="passport_doc"
                                                        data-index="${index}"
                                                        title="Delete Document">
                                                    <i class="ri-delete-bin-line"></i>
                                                </button>
                                            </div>
                                        ` : ''}
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label for="edit_director_driving_license_doc_${index}" class="form-label">Driving License</label>
                                    <input type="file" class="form-control" id="edit_director_driving_license_doc_${index}"
                                        name="directors[${index}][documents][driving_license_doc]" accept=".pdf,.jpg,.png">
                                    <div id="existing_director_driving_license_doc_${index}" class="existing-document mt-2">
                                        ${director.documents.driving_license_doc ? `
                                            <div class="d-flex align-items-center">
                                                <a href="/storage/${director.documents.driving_license_doc}" target="_blank" class="text-primary me-2">View Driving License</a>
                                                <button type="button" class="btn btn-sm btn-danger delete-director-document"
                                                        data-id="${director.id}"
                                                        data-type="driving_license_doc"
                                                        data-index="${index}"
                                                        title="Delete Document">
                                                    <i class="ri-delete-bin-line"></i>
                                                </button>
                                            </div>
                                        ` : ''}
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label for="edit_director_bank_passbook_doc_${index}" class="form-label">Bank Passbook</label>
                                    <input type="file" class="form-control" id="edit_director_bank_passbook_doc_${index}"
                                        name="directors[${index}][documents][bank_passbook_doc]" accept=".pdf,.jpg,.png">
                                    <div id="existing_director_bank_passbook_doc_${index}" class="existing-document mt-2">
                                        ${director.documents.bank_passbook_doc ? `
                                            <div class="d-flex align-items-center">
                                                <a href="/storage/${director.documents.bank_passbook_doc}" target="_blank" class="text-primary me-2">View Bank Passbook</a>
                                                <button type="button" class="btn btn-sm btn-danger delete-director-document"
                                                        data-id="${director.id}"
                                                        data-type="bank_passbook_doc"
                                                        data-index="${index}"
                                                        title="Delete Document">
                                                    <i class="ri-delete-bin-line"></i>
                                                </button>
                                            </div>
                                        ` : ''}
                                    </div>
                                </div>
                                <div class="col-md-4 d-flex align-items-end">
                                    <a href="javascript:void(0)" class="remove-director-row w-100 text-danger" title="Remove Director">
                                        <i class="ri-delete-bin-line me-1"></i> Remove Director
                                    </a>
                                </div>
                            </div>
                        `);
                        editDirectorIndex = index + 1;
                    });
                }).fail(function(xhr) {
                    alert('Failed to load company details: ' + xhr.responseJSON.error);
                });
            });

            // Add director row in Edit Company Modal
            $('#editAddDirectorRow').click(function(e) {
                e.preventDefault();
                $('#editCompanyForm .director-details').append(`
                    <div class="row g-3 director-row border-bottom pb-3 mb-3" data-index="${editDirectorIndex}">
                        <div class="col-12">
                            <h6 class="fw-bold">Director ${editDirectorIndex + 1}</h6>
                        </div>
                        <div class="col-md-4">
                            <label for="edit_director_name_${editDirectorIndex}" class="form-label fw-bold">Name of Director <span class="text-danger">*</span></label>
                            <input type="text" name="directors[${editDirectorIndex}][name]" class="form-control"
                                id="edit_director_name_${editDirectorIndex}" required>
                        </div>
                        <div class="col-md-4">
                            <label for="edit_director_designation_${editDirectorIndex}" class="form-label fw-bold">Designation / Role <span class="text-danger">*</span></label>
                            <input type="text" name="directors[${editDirectorIndex}][designation]" class="form-control"
                                id="edit_director_designation_${editDirectorIndex}" required>
                        </div>
                        <div class="col-md-4">
                            <label for="edit_director_din_${editDirectorIndex}" class="form-label fw-bold">DIN</label>
                            <input type="text" name="directors[${editDirectorIndex}][din]" class="form-control"
                                id="edit_director_din_${editDirectorIndex}">
                        </div>
                        <div class="col-md-4">
                            <label for="edit_director_pan_${editDirectorIndex}" class="form-label fw-bold">PAN</label>
                            <input type="text" name="directors[${editDirectorIndex}][pan]" class="form-control"
                                id="edit_director_pan_${editDirectorIndex}">
                        </div>
                        <div class="col-md-4">
                            <label for="edit_director_aadhaar_${editDirectorIndex}" class="form-label fw-bold">Aadhaar (if applicable)</label>
                            <input type="text" name="directors[${editDirectorIndex}][aadhaar]" class="form-control"
                                id="edit_director_aadhaar_${editDirectorIndex}">
                        </div>
                        <div class="col-md-4">
                            <label for="edit_director_contact_${editDirectorIndex}" class="form-label fw-bold">Contact Number</label>
                            <input type="text" name="directors[${editDirectorIndex}][contact_number]" class="form-control"
                                id="edit_director_contact_${editDirectorIndex}">
                        </div>
                        <div class="col-md-4">
                            <label for="edit_director_email_${editDirectorIndex}" class="form-label fw-bold">Email ID</label>
                            <input type="email" name="directors[${editDirectorIndex}][email]" class="form-control"
                                id="edit_director_email_${editDirectorIndex}">
                        </div>
                        <div class="col-md-4">
                            <label for="edit_director_appointment_date_${editDirectorIndex}" class="form-label fw-bold">Date of Appointment</label>
                            <input type="date" name="directors[${editDirectorIndex}][appointment_date]" class="form-control"
                                id="edit_director_appointment_date_${editDirectorIndex}">
                        </div>
                        <div class="col-md-4">
                            <label for="edit_director_resignation_date_${editDirectorIndex}" class="form-label fw-bold">Resignation Date</label>
                            <input type="date" name="directors[${editDirectorIndex}][resignation_date]" class="form-control"
                                id="edit_director_resignation_date_${editDirectorIndex}">
                        </div>
                        <div class="col-12 mt-3">
                            <h6 class="fw-bold">Director Documents</h6>
                            <hr class="mt-1 mb-4">
                        </div>
                        <div class="col-md-4">
                            <label for="edit_director_aadhar_doc_${editDirectorIndex}" class="form-label">Aadhar Document</label>
                            <input type="file" class="form-control" id="edit_director_aadhar_doc_${editDirectorIndex}"
                                name="directors[${editDirectorIndex}][documents][aadhar_doc]" accept=".pdf,.jpg,.png">
                            <div id="existing_director_aadhar_doc_${editDirectorIndex}" class="existing-document mt-2"></div>
                        </div>
                        <div class="col-md-4">
                            <label for="edit_director_pan_doc_${editDirectorIndex}" class="form-label">PAN Document</label>
                            <input type="file" class="form-control" id="edit_director_pan_doc_${editDirectorIndex}"
                                name="directors[${editDirectorIndex}][documents][pan_doc]" accept=".pdf,.jpg,.png">
                            <div id="existing_director_pan_doc_${editDirectorIndex}" class="existing-document mt-2"></div>
                        </div>
                        <div class="col-md-4">
                            <label for="edit_director_passport_doc_${editDirectorIndex}" class="form-label">Passport</label>
                            <input type="file" class="form-control" id="edit_director_passport_doc_${editDirectorIndex}"
                                name="directors[${editDirectorIndex}][documents][passport_doc]" accept=".pdf,.jpg,.png">
                            <div id="existing_director_passport_doc_${editDirectorIndex}" class="existing-document mt-2"></div>
                        </div>
                        <div class="col-md-4">
                            <label for="edit_director_driving_license_doc_${editDirectorIndex}" class="form-label">Driving License</label>
                            <input type="file" class="form-control" id="edit_director_driving_license_doc_${editDirectorIndex}"
                                name="directors[${editDirectorIndex}][documents][driving_license_doc]" accept=".pdf,.jpg,.png">
                            <div id="existing_director_driving_license_doc_${editDirectorIndex}" class="existing-document mt-2"></div>
                        </div>
                        <div class="col-md-4">
                            <label for="edit_director_bank_passbook_doc_${editDirectorIndex}" class="form-label">Bank Passbook</label>
                            <input type="file" class="form-control" id="edit_director_bank_passbook_doc_${editDirectorIndex}"
                                name="directors[${editDirectorIndex}][documents][bank_passbook_doc]" accept=".pdf,.jpg,.png">
                            <div id="existing_director_bank_passbook_doc_${editDirectorIndex}" class="existing-document mt-2"></div>
                        </div>
                        <div class="col-md-4 d-flex align-items-end">
                            <a href="javascript:void(0)" class="remove-director-row w-100 text-danger" title="Remove Director">
                                <i class="ri-delete-bin-line me-1"></i> Remove Director
                            </a>
                        </div>
                    </div>
                `);
                editDirectorIndex++;
            });

            // Remove director row in Edit Company Modal
            $(document).on('click', '#editCompanyForm .remove-director-row', function(e) {
                e.preventDefault();
                if ($('#editCompanyForm .director-row').length > 1) {
                    $(this).closest('.director-row').remove();
                    $('#editCompanyForm .director-row').each(function(index) {
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
                        $(this).find('.existing-document').each(function() {
                            const idAttr = $(this).attr('id');
                            if (idAttr) {
                                $(this).attr('id', idAttr.replace(/existing_director_(\w+)_(\d+)/, `existing_director_$1_${index}`));
                            }
                        });
                    });
                } else {
                    alert('At least one director is required.');
                }
            });

            // Delete company document
            $(document).on('click', '.delete-document', function() {
                const documentId = $(this).data('id');
                const documentType = $(this).data('type');
                if (confirm('Are you sure you want to delete this document?')) {
                    $.ajax({
                        url: `/company/document/${documentId}`,
                        method: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            $(`#existing_${documentType}`).empty();
                            $(`#edit_${documentType}`).show();
                            alert('Document deleted successfully.');
                        },
                        error: function(xhr) {
                            alert('Failed to delete document: ' + xhr.responseJSON.error);
                        }
                    });
                }
            });

            // Delete director document
            $(document).on('click', '.delete-director-document', function() {
                const directorId = $(this).data('id');
                const docType = $(this).data('type');
                const index = $(this).data('index');
                if (confirm('Are you sure you want to delete this director document?')) {
                    $.ajax({
                        url: `/company/directors/document/${directorId}/${docType}`,
                        method: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            $(`#existing_director_${docType}_${index}`).empty();
                            $(`#edit_director_${docType}_${index}`).show();
                            alert('Director document deleted successfully.');
                        },
                        error: function(xhr) {
                            alert('Failed to delete director document: ' + xhr.responseJSON.error);
                        }
                    });
                }
            });
        });
    </script>
@endsection