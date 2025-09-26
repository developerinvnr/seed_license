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
                        <h4 class="card-title mb-0 flex-grow-1"><i class="ri-list-unordered"></i> License Name List</h4>
                        <div class="flex-shrink-0">
                            <button type="button" class="btn btn-primary btn-label waves-effect waves-light rounded-pill"
                                data-bs-toggle="modal" data-bs-target="#addLicensenameModal">
                                <i class="ri-add-circle-fill label-icon align-middle rounded-pill fs-16 me-2"></i> License
                                Name
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        @if (session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"
                                    aria-label="Close"></button>
                            </div>
                        @endif
                        @if ($errors->any())
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"
                                    aria-label="Close"></button>
                            </div>
                        @endif

                        <div class="table-responsive">
                            <table class="table nowrap dt-responsive align-middle table-hover table-bordered"
                                style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th>Sno</th>
                                        <th>License Category</th>
                                        <th>License Name <small class="text-muted">(Title)</small></th>
                                        <th>Issuing Authority Name</th>
                                        <th>Issuing Authority Address</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody id="licenseTableBody">
                                    @forelse($licenseNames as $index => $license)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $license->licenseType->license_type ?? 'N/A' }}</td>
                                            <td>{{ $license->license_name }}</td>
                                            <td>{{ $license->department_name }}</td>
                                            <td>
                                                {{ $license->state->state_name ?? '' }},
                                                {{ $license->district->district_name ?? '' }},
                                                {{ $license->cityVillage->city_village_name ?? '' }},
                                                {{ $license->pincode ?? '' }}
                                            </td>
                                            <td>
                                                <a href="javascript:void(0);" class="text-primary edit-btn"
                                                    data-license_type_id="{{ $license->license_type_id }}"
                                                    data-id="{{ $license->id }}" data-name="{{ $license->license_name }}"
                                                    data-department_name="{{ $license->department_name }}"
                                                    data-state_id="{{ $license->state_id }}"
                                                    data-district_id="{{ $license->district_id }}"
                                                    data-city_village_id="{{ $license->city_village_id }}"
                                                    data-pincode="{{ $license->pincode }}" data-bs-toggle="modal"
                                                    data-bs-target="#editLicensenameModal">
                                                    <i class="ri-edit-2-line fs-5">Edit</i>
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Add License Modal -->
        <div class="modal fade" id="addLicensenameModal" tabindex="-1" aria-labelledby="addLicensenameModalLabel"
            aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addLicensenameModalLabel">Add License Name</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="addLicenseForm" action="{{ route('license_name.store') }}" method="POST">
                            @csrf
                            @if ($errors->any())
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <ul class="mb-0">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"
                                        aria-label="Close"></button>
                                </div>
                            @endif
                            <div>
                                <h5 class="fw-bold border-bottom pb-2">License Details</h5>
                            </div>
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <label for="license_type" class="form-label">License Category</label>
                                    <select class="form-control form-control-sm select2" id="license_type"
                                        name="license_type_id" required>
                                        <option value="">Select License Category</option>
                                        @foreach ($licenseTypes as $type)
                                            <option value="{{ $type->id }}"
                                                {{ old('license_type_id') == $type->id ? 'selected' : '' }}>
                                                {{ $type->license_type }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="license_name" class="form-label">License Name <small
                                            class="text-muted">(Title)</small></label>
                                    <input type="text"
                                        class="form-control form-control-sm @error('license_name') is-invalid @enderror"
                                        id="license_name" name="license_name" value="{{ old('license_name') }}" required>
                                    @error('license_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div>
                                <h5 class="fw-bold border-bottom pb-2 mt-4">Issuing Authority Details</h5>
                            </div>
                            <div class="row g-4 mt-2">
                                <div class="col-md-6">
                                    <label for="department_name" class="form-label">Issuing Authority Name</label>
                                    <input type="text" name="department_name" id="department_name"
                                        class="form-control form-control-sm @error('department_name') is-invalid @enderror"
                                        value="{{ old('department_name') }}" required>
                                    @error('department_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="state_id" class="form-label">State</label>
                                    <select
                                        class="form-control form-control-sm select2 @error('state_id') is-invalid @enderror"
                                        id="state_id" name="state_id" required>
                                        <option value="">Select State</option>
                                        @foreach ($states as $state)
                                            <option value="{{ $state->id }}"
                                                {{ old('state_id') == $state->id ? 'selected' : '' }}>
                                                {{ $state->state_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('state_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="district_id" class="form-label">District</label>
                                    <select
                                        class="form-control form-control-sm select2 @error('district_id') is-invalid @enderror"
                                        id="district_id" name="district_id" required>
                                        <option value="">Select District</option>
                                    </select>
                                    @error('district_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="city_village_id" class="form-label">City/Village</label>
                                    <select
                                        class="form-control form-control-sm select2 @error('city_village_id') is-invalid @enderror"
                                        id="city_village_id" name="city_village_id" required>
                                        <option value="">Select City/Village</option>
                                    </select>
                                    @error('city_village_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="pincode" class="form-label">Pincode</label>
                                    <input type="text"
                                        class="form-control form-control-sm @error('pincode') is-invalid @enderror"
                                        id="pincode" name="pincode" value="{{ old('pincode') }}" required readonly>
                                    @error('pincode')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div>
                                <h5 class="fw-bold border-bottom pb-2 mt-4">Add License Additional Details</h5>
                            </div>
                            <div class="mb-3">
                                <div class="row">
                                    @foreach ($labels as $label)
                                        <div class="col-md-4">
                                            <div class="form-check">
                                                <input type="checkbox" name="fields[]" value="{{ $label->id }}"
                                                    class="form-check-input" id="label_{{ $label->id }}"
                                                    {{ in_array($label->id, old('fields', [])) ? 'checked' : '' }}>
                                                <label class="form-check-label sub-field-label"
                                                    for="label_{{ $label->id }}" data-label-id="{{ $label->id }}"
                                                    data-label-name="{{ $label->label_name }}">{{ $label->label_name }}</label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary">
                                <i class="ri-check-double-line label-icon align-middle rounded-pill fs-16 me-2">
                                    <span class="loader" style="display: none;"></span>
                                </i>Submit
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Edit License Modal -->
        <div class="modal fade" id="editLicensenameModal" tabindex="-1" aria-labelledby="editLicensenameModalLabel"
            aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editLicensenameModalLabel">Edit License Name</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="editLicenseForm" action="{{ route('license_name.update') }}" method="POST">
                            @csrf
                            <input type="hidden" name="id" id="edit_license_name_id">

                            @if ($errors->any())
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <ul class="mb-0">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"
                                        aria-label="Close"></button>
                                </div>
                            @endif

                            <!-- License Details -->
                            <div>
                                <h5 class="fw-bold border-bottom pb-2">License Details</h5>
                            </div>
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <label for="edit_license_type" class="form-label">License Category</label>
                                    <select class="form-control form-control-sm select2" id="edit_license_type"
                                        name="license_type_id" required>
                                        <option value="">Select License Category</option>
                                        @foreach ($licenseTypes as $type)
                                            <option value="{{ $type->id }}">{{ $type->license_type }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="edit_license_name" class="form-label">License Name <small
                                            class="text-muted">(Title)</small></label>
                                    <input type="text" class="form-control form-control-sm" id="edit_license_name"
                                        name="license_name" required>
                                </div>
                            </div>

                            <!-- Issuing Authority Details -->
                            <div>
                                <h5 class="fw-bold border-bottom pb-2 mt-4">Issuing Authority Details</h5>
                            </div>
                            <div class="row g-4 mt-2">
                                <div class="col-md-6">
                                    <label for="edit_department_name" class="form-label">Issuing Authority Name</label>
                                    <input type="text" name="department_name" id="edit_department_name"
                                        class="form-control form-control-sm" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="edit_state_id" class="form-label">State</label>
                                    <select class="form-control form-control-sm select2" id="edit_state_id"
                                        name="state_id" required>
                                        <option value="">Select State</option>
                                        @foreach ($states as $state)
                                            <option value="{{ $state->id }}">{{ $state->state_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="edit_district_id" class="form-label">District</label>
                                    <select class="form-control form-control-sm select2" id="edit_district_id"
                                        name="district_id" required>
                                        <option value="">Select District</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="edit_city_village_id" class="form-label">City/Village</label>
                                    <select class="form-control form-control-sm select2" id="edit_city_village_id"
                                        name="city_village_id" required>
                                        <option value="">Select City/Village</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="edit_pincode" class="form-label">Pincode</label>
                                    <input type="text" name="pincode" id="edit_pincode"
                                        class="form-control form-control-sm" required readonly>
                                </div>
                            </div>

                            <!-- License Additional Details -->
                            <div>
                                <h5 class="fw-bold border-bottom pb-2 mt-4">Edit License Additional Details</h5>
                            </div>
                            <div class="mb-3">
                                <div class="row">
                                    @foreach ($labels as $label)
                                        <div class="col-md-4">
                                            <div class="form-check">
                                                <input type="checkbox" name="fields[]" value="{{ $label->id }}"
                                                    class="form-check-input" id="edit_label_{{ $label->id }}">
                                                <label class="form-check-label sub-field-label"
                                                    for="edit_label_{{ $label->id }}"
                                                    data-label-id="{{ $label->id }}"
                                                    data-label-name="{{ $label->label_name }}">
                                                    {{ $label->label_name }}
                                                </label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary">
                                <i class="ri-check-double-line label-icon align-middle rounded-pill fs-16 me-2">
                                    <span class="loader" style="display: none;"></span>
                                </i>Update
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sub-Fields Modal -->
        <div class="modal fade" id="showSubFieldsModal" tabindex="-1" aria-labelledby="showSubFieldsModalLabel"
            aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog modal-sm">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="showSubFieldsModalLabel">Sub-Fields</h5>
                    </div>
                    <div class="modal-body">
                        <h6 id="subFieldsLabelName"></h6>
                        <ul id="subFieldsList" class="list-group"></ul>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('components.theme.footer')
    <script>
        $(document).ready(function() {
            // Function to populate districts based on state
            function populateDistricts(stateId, districtSelectId, cityVillageSelectId, pincodeInputId,
                selectedDistrictId = null, callback = null) {
                if (!stateId) {
                    $('#' + districtSelectId).html('<option value="">Select District</option>');
                    $('#' + cityVillageSelectId).html('<option value="">Select City/Village</option>');
                    $('#' + pincodeInputId).val('');
                    if (callback) callback();
                    return;
                }

                $.ajax({
                    url: '/get-districts/' + stateId,
                    type: 'GET',
                    dataType: 'json',
                    beforeSend: function() {
                        $('#' + districtSelectId).prop('disabled', true).html(
                            '<option value="">Loading...</option>');
                        $('#' + cityVillageSelectId).prop('disabled', true).html(
                            '<option value="">Select City/Village</option>');
                        $('#' + pincodeInputId).val('');
                    },
                    success: function(response) {
                        let districtSelect = $('#' + districtSelectId);
                        districtSelect.html('<option value="">Select District</option>');
                        if (response.districts && response.districts.length > 0) {
                            $.each(response.districts, function(index, district) {
                                districtSelect.append('<option value="' + district.id + '">' +
                                    district.district_name + '</option>');
                            });
                            if (selectedDistrictId) {
                                districtSelect.val(selectedDistrictId);
                                populateCityVillages(selectedDistrictId, cityVillageSelectId,
                                    pincodeInputId, null, callback);
                            } else if (callback) {
                                callback();
                            }
                        } else {
                            console.warn('No districts found for state ID: ' + stateId);
                            alert('No districts available for this state.');
                            if (callback) callback();
                        }
                        districtSelect.prop('disabled', false);
                    },
                    error: function(xhr, status, error) {
                        console.error('Error fetching districts:', xhr.responseText);
                        alert('Failed to load districts. Please try again.');
                        $('#' + districtSelectId).prop('disabled', false).html(
                            '<option value="">Select District</option>');
                        if (callback) callback();
                    }
                });
            }

            // Function to populate city/villages based on district
            function populateCityVillages(districtId, cityVillageSelectId, pincodeInputId, selectedCityVillageId =
                null, callback = null) {
                if (!districtId) {
                    $('#' + cityVillageSelectId).html('<option value="">Select City/Village</option>');
                    $('#' + pincodeInputId).val('');
                    if (callback) callback();
                    return;
                }

                $.ajax({
                    url: '/get-city-villages/' + districtId,
                    type: 'GET',
                    dataType: 'json',
                    beforeSend: function() {
                        $('#' + cityVillageSelectId).prop('disabled', true).html(
                            '<option value="">Loading...</option>');
                        $('#' + pincodeInputId).val('');
                    },
                    success: function(response) {
                        let cityVillageSelect = $('#' + cityVillageSelectId);
                        cityVillageSelect.html('<option value="">Select City/Village</option>');
                        if (response.cityVillages && response.cityVillages.length > 0) {
                            $.each(response.cityVillages, function(index, cityVillage) {
                                cityVillageSelect.append('<option value="' + cityVillage.id +
                                    '" data-pincode="' + cityVillage.pincode + '">' +
                                    cityVillage.city_village_name + '</option>');
                            });
                            if (selectedCityVillageId) {
                                cityVillageSelect.val(selectedCityVillageId);
                                let selectedOption = cityVillageSelect.find('option:selected');
                                $('#' + pincodeInputId).val(selectedOption.data('pincode') || '');
                            }
                        } else {
                            console.warn('No city/villages found for district ID: ' + districtId);
                            alert('No city/villages available for this district.');
                        }
                        cityVillageSelect.prop('disabled', false);
                        if (callback) callback();
                    },
                    error: function(xhr, status, error) {
                        console.error('Error fetching city/villages:', xhr.responseText);
                        alert('Failed to load city/villages. Please try again.');
                        $('#' + cityVillageSelectId).prop('disabled', false).html(
                            '<option value="">Select City/Village</option>');
                        if (callback) callback();
                    }
                });
            }

            // Function to fetch and show sub-fields in modal
            function showSubFields(labelId, labelName) {
                $.ajax({
                    url: '/get-sub-fields/' + labelId,
                    type: 'GET',
                    dataType: 'json',
                    beforeSend: function() {
                        $('#subFieldsList').html('<li class="list-group-item">Loading...</li>');
                        $('#subFieldsLabelName').html('Sub-Fields for <strong>' + labelName +
                            '</strong>');
                        var subFieldsModal = new bootstrap.Modal(document.getElementById(
                            'showSubFieldsModal'));
                        subFieldsModal.show();
                    },
                    success: function(response) {
                        let subFieldsList = $('#subFieldsList');
                        subFieldsList.empty();
                        if (response.subFields && response.subFields.length > 0) {
                            $.each(response.subFields, function(index, subField) {
                                subFieldsList.append('<li class="list-group-item">' + subField
                                    .field_name + '</li>');
                            });
                        } else {
                            subFieldsList.append(
                                '<li class="list-group-item">No sub-fields available.</li>');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error fetching sub-fields:', xhr.responseText);
                        $('#subFieldsList').html(
                            '<li class="list-group-item text-danger">Failed to load sub-fields.</li>'
                        );
                    }
                });
            }

            // Reset Add modal checkboxes when shown
            $('#addLicensenameModal').on('show.bs.modal', function() {
                $('#addLicenseForm input[name="fields[]"]').prop('checked', false);
                $('#addLicenseForm').find('.is-invalid').removeClass('is-invalid');
                $('#addLicenseForm').find('.invalid-feedback').remove();
                $('#license_type, #license_name, #department_name, #state_id, #district_id, #city_village_id, #pincode')
                    .val('');
                $('#district_id').html('<option value="">Select District</option>');
                $('#city_village_id').html('<option value="">Select City/Village</option>');
            });

            // Add modal state change
            $('#state_id').on('change', function() {
                let stateId = $(this).val();
                populateDistricts(stateId, 'district_id', 'city_village_id', 'pincode');
            });

            // Add modal district change
            $('#district_id').on('change', function() {
                let districtId = $(this).val();
                populateCityVillages(districtId, 'city_village_id', 'pincode');
            });

            // Add modal city/village change
            $('#city_village_id').on('change', function() {
                let selectedOption = $(this).find('option:selected');
                $('#pincode').val(selectedOption.data('pincode') || '');
            });

            // Edit modal state change
            $('#edit_state_id').on('change', function() {
                let stateId = $(this).val();
                populateDistricts(stateId, 'edit_district_id', 'edit_city_village_id', 'edit_pincode');
            });

            // Edit modal district change
            $('#edit_district_id').on('change', function() {
                let districtId = $(this).val();
                populateCityVillages(districtId, 'edit_city_village_id', 'edit_pincode');
            });

            // Edit modal city/village change
            $('#edit_city_village_id').on('change', function() {
                let selectedOption = $(this).find('option:selected');
                $('#edit_pincode').val(selectedOption.data('pincode') || '');
            });

            // Edit button click
            // $('.edit-btn').on('click', function() {
            //     let licenseId = $(this).data('id');
            //     $.ajax({
            //         url: '/license-name/edit/' + licenseId,
            //         type: 'GET',
            //         dataType: 'json',
            //         beforeSend: function() {
            //             $('#edit_license_type, #edit_license_name, #edit_department_name, #edit_state_id, #edit_district_id, #edit_city_village_id, #edit_pincode')
            //                 .prop('disabled', true);
            //         },
            //         success: function(response) {
            //             if (response.license) {
            //                 console.log('License Data:', response.license);
            //                 $('#edit_license_name_id').val(response.license.id);
            //                 $('#edit_license_name').val(response.license.license_name);
            //                 $('#edit_department_name').val(response.license.department_name);
            //                 $('#edit_license_type').val(response.license.license_type_id);
            //                 $('#edit_state_id').val(response.license.state_id);
            //                 $('#edit_pincode').val(response.license.pincode);

            //                 // Reset Edit modal checkboxes before setting new values
            //                 $('#editLicensenameModal input[name="fields[]"]').prop('checked',
            //                     false);

            //                 // Populate checkboxes only in Edit modal
            //                 let selectedFieldsArray = response.license.fields ? response.license
            //                     .fields.split(',').map(item => item.trim()) : [];
            //                 $('#editLicensenameModal input[name="fields[]"]').each(function() {
            //                     $(this).prop('checked', selectedFieldsArray.includes($(
            //                         this).val()));
            //                 });

            //                 if (response.license.state_id) {
            //                     populateDistricts(response.license.state_id, 'edit_district_id',
            //                         'edit_city_village_id', 'edit_pincode', response.license
            //                         .district_id,
            //                         function() {
            //                             if (response.license.district_id) {
            //                                 populateCityVillages(response.license
            //                                     .district_id, 'edit_city_village_id',
            //                                     'edit_pincode', response.license
            //                                     .city_village_id,
            //                                     function() {
            //                                         $('#edit_license_type, #edit_license_name, #edit_department_name, #edit_state_id, #edit_district_id, #edit_city_village_id, #edit_pincode')
            //                                             .prop('disabled', false);
            //                                         console.log('City/Village set to:',
            //                                             $('#edit_city_village_id')
            //                                             .val());
            //                                     });
            //                             } else {
            //                                 $('#edit_license_type, #edit_license_name, #edit_department_name, #edit_state_id, #edit_district_id, #edit_city_village_id, #edit_pincode')
            //                                     .prop('disabled', false);
            //                             }
            //                         });
            //                 } else {
            //                     $('#edit_license_type, #edit_license_name, #edit_department_name, #edit_state_id, #edit_district_id, #edit_city_village_id, #edit_pincode')
            //                         .prop('disabled', false);
            //                 }

            //                 var editModal = new bootstrap.Modal(document.getElementById(
            //                     'editLicensenameModal'));
            //                 editModal.show();
            //             } else {
            //                 console.error('License data not found.');
            //                 alert('License data not found.');
            //                 $('#edit_license_type, #edit_license_name, #edit_department_name, #edit_state_id, #edit_district_id, #edit_city_village_id, #edit_pincode')
            //                     .prop('disabled', false);
            //             }
            //         },
            //         error: function(xhr, status, error) {
            //             console.error('Error fetching license data:', xhr.responseText);
            //             alert('Failed to load license data. Please try again.');
            //             $('#edit_license_type, #edit_license_name, #edit_department_name, #edit_state_id, #edit_district_id, #edit_city_village_id, #edit_pincode')
            //                 .prop('disabled', false);
            //         }
            //     });
            // });

            // Edit button click
            $('.edit-btn').on('click', function() {
                let licenseId = $(this).data('id');

                $.ajax({
                    url: '/license-name/edit/' + licenseId,
                    type: 'GET',
                    dataType: 'json',
                    beforeSend: function() {
                        $('#edit_license_type, #edit_license_name, #edit_department_name, #edit_state_id, #edit_district_id, #edit_city_village_id, #edit_pincode')
                            .prop('disabled', true);
                    },
                    success: function(response) {
                        if (response.license) {
                            console.log('License Data:', response.license);

                            // Basic fields
                            $('#edit_license_name_id').val(response.license.id);
                            $('#edit_license_name').val(response.license.license_name);
                            $('#edit_department_name').val(response.license.department_name);
                            $('#edit_pincode').val(response.license.pincode);

                            // Select2 fields (License Type & State)
                            $('#edit_license_type').val(response.license.license_type_id)
                                .trigger('change');
                            $('#edit_state_id').val(response.license.state_id).trigger(
                            'change');

                            // Reset Edit modal checkboxes
                            $('#editLicensenameModal input[name="fields[]"]').prop('checked',
                                false);
                            let selectedFieldsArray = response.license.fields ? response.license
                                .fields.split(',').map(item => item.trim()) : [];
                            $('#editLicensenameModal input[name="fields[]"]').each(function() {
                                $(this).prop('checked', selectedFieldsArray.includes($(
                                    this).val()));
                            });

                            // Handle cascading dropdowns (district + city/village)
                            if (response.license.state_id) {
                                populateDistricts(
                                    response.license.state_id,
                                    'edit_district_id',
                                    'edit_city_village_id',
                                    'edit_pincode',
                                    response.license.district_id,
                                    function() {
                                        if (response.license.district_id) {
                                            $('#edit_district_id').val(response.license
                                                .district_id).trigger('change');

                                            populateCityVillages(
                                                response.license.district_id,
                                                'edit_city_village_id',
                                                'edit_pincode',
                                                response.license.city_village_id,
                                                function() {
                                                    $('#edit_city_village_id').val(
                                                        response.license
                                                        .city_village_id).trigger(
                                                        'change');
                                                    $('#edit_license_type, #edit_license_name, #edit_department_name, #edit_state_id, #edit_district_id, #edit_city_village_id, #edit_pincode')
                                                        .prop('disabled', false);
                                                }
                                            );
                                        } else {
                                            $('#edit_license_type, #edit_license_name, #edit_department_name, #edit_state_id, #edit_district_id, #edit_city_village_id, #edit_pincode')
                                                .prop('disabled', false);
                                        }
                                    }
                                );
                            } else {
                                $('#edit_license_type, #edit_license_name, #edit_department_name, #edit_state_id, #edit_district_id, #edit_city_village_id, #edit_pincode')
                                    .prop('disabled', false);
                            }

                            // Show modal
                            var editModal = new bootstrap.Modal(document.getElementById(
                                'editLicensenameModal'));
                            editModal.show();
                        } else {
                            console.error('License data not found.');
                            alert('License data not found.');
                            $('#edit_license_type, #edit_license_name, #edit_department_name, #edit_state_id, #edit_district_id, #edit_city_village_id, #edit_pincode')
                                .prop('disabled', false);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error fetching license data:', xhr.responseText);
                        alert('Failed to load license data. Please try again.');
                        $('#edit_license_type, #edit_license_name, #edit_department_name, #edit_state_id, #edit_district_id, #edit_city_village_id, #edit_pincode')
                            .prop('disabled', false);
                    }
                });
            });


            // Handle label click to show sub-fields
            $(document).on('click', '.sub-field-label', function(e) {
                e.preventDefault();
                let labelId = $(this).data('label-id');
                let labelName = $(this).data('label-name');
                showSubFields(labelId, labelName);
            });

            // Prevent checkbox click from triggering sub-field modal
            $(document).on('click', '.form-check-input', function(e) {
                e.stopPropagation();
            });

            // Handle modal close
            $('.btn-close').on('click', function(e) {
                e.preventDefault();
                $('.modal-backdrop').remove();
                $('#addLicensenameModal, #editLicensenameModal, #showSubFieldsModal').modal('hide');
            });

            // Auto-remove alert after 3 seconds
            setTimeout(() => {
                $('.alert').fadeOut('slow', function() {
                    $(this).remove();
                });
            }, 3000);

            $('#license_type, #state_id, #district_id, #city_village_id').select2({
                dropdownParent: $('#addLicensenameModal'),
                width: '100%'
            });

            $('#edit_license_type, #edit_state_id, #edit_district_id, #edit_city_village_id').select2({
                dropdownParent: $('#editLicensenameModal'),
                width: '100%'
            });


        });
    </script>
@endsection
