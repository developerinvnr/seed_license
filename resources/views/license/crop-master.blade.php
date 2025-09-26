@extends('layouts.app')
@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div
                        class="page-title-box d-sm-flex align-items-center justify-content-between bg-galaxy-transparent p-3 rounded">
                        <h4 class="mb-sm-0 text-white">{{ ucwords(str_replace('-', ' ', Request::path())) }}</h4>
                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="javascript:void(0);" class="text-white">Masters</a></li>
                                <li class="breadcrumb-item active text-white">
                                    {{ ucwords(str_replace('-', ' ', Request::path())) }}</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header align-items-center d-flex">
                            <h4 class="card-title mb-0 flex-grow-1"><i class="ri-list-unordered"></i> Crop Master List</h4>
                            @can('add-Crop Master')
                                <div class="flex-shrink-0">
                                    <button type="button"
                                        class="btn btn-primary btn-label waves-effect waves-light rounded-pill"
                                        data-bs-toggle="modal" data-bs-target="#addCropMasterModal">
                                        <i class="ri-add-circle-fill label-icon align-middle rounded-pill fs-16 me-2"></i> Add
                                        New
                                    </button>
                                </div>
                            @endcan
                        </div>
                        <div class="card-body p-4">
                            @if (session('success'))
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    {{ session('success') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"
                                        aria-label="Close"></button>
                                </div>
                            @endif
                            @if ($errors->any())
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    @foreach ($errors->all() as $error)
                                        {{ $error }}<br>
                                    @endforeach
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"
                                        aria-label="Close"></button>
                                </div>
                            @endif
                            <table class="table nowrap dt-responsive align-middle table-hover table-bordered"
                                style="width:100%;">
                                <thead>
                                    <tr>
                                        <th>Sno</th>
                                        <th>Crop Vertical</th>
                                        <th>Crop Name</th>
                                        <th>Crop Variety Name</th>
                                        @canany(['view-Crop Master', 'edit-Crop Master', 'delete-Crop Master'])
                                            <th>Actions</th>
                                        @endcanany
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($cropMasters as $index => $cropMaster)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $cropMaster->vertical->vertical_name ?? 'N/A' }}</td>
                                            <td>{{ $cropMaster->crop->crop_name ?? 'N/A' }}</td>
                                            <td>{{ $cropMaster->variety->variety_name ?? 'N/A' }}</td>
                                            @canany(['view-Crop Master', 'edit-Crop Master', 'delete-Crop Master'])
                                                <td>
                                                    <button class="btn btn-sm btn-info view-btn" title="View Crop Master"
                                                        data-id="{{ $cropMaster->id }}" data-bs-toggle="modal"
                                                        data-bs-target="#viewCropMasterModal">
                                                        <i class="ri-eye-line"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-warning edit-btn" title="Edit Crop Master"
                                                        data-id="{{ $cropMaster->id }}" data-bs-toggle="modal"
                                                        data-bs-target="#editCropMasterModal">
                                                        <i class="ri-edit-2-line"></i>
                                                    </button>
                                                    <form action="{{ route('crop-master.destroy', $cropMaster->id) }}"
                                                        method="POST" style="display:inline;">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger"
                                                            title="Delete Crop Master"
                                                            onclick="return confirm('Are you sure you want to delete this crop master?')">
                                                            <i class="ri-delete-bin-line"></i>
                                                        </button>
                                                    </form>
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
            <!-- Add Crop Master Modal -->
            @can('add-Crop Master')
                <div class="modal fade" id="addCropMasterModal" tabindex="-1" aria-labelledby="addCropMasterModalLabel"
                    aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="addCropMasterModalLabel">Add New Crop Master</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <form action="{{ route('crop-master.store') }}" method="POST">
                                @csrf
                                <div class="modal-body">
                                    <!-- Crop Vertical -->
                                    <div class="mb-3">
                                        <label for="crop_vertical_id" class="form-label">Crop Vertical</label>
                                        <select class="form-select" id="crop_vertical_id" name="crop_vertical_id" required>
                                            <option value="">Select Crop Vertical</option>
                                            @foreach ($verticals as $vertical)
                                                <option value="{{ $vertical->id }}">{{ $vertical->vertical_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <!-- Crop Name -->
                                    <div class="mb-3">
                                        <label for="crop_id" class="form-label">Crop Name</label>
                                        <select class="form-select" id="crop_id" name="crop_id" required>
                                            <option value="">Select Crop Name</option>
                                        </select>
                                    </div>

                                    <!-- Crop Variety -->
                                    <div class="mb-3">
                                        <label for="crop_variety_id" class="form-label">Crop Variety Name</label>
                                        <select class="form-select" id="crop_variety_id" name="crop_variety_id" required>
                                            <option value="">Select Crop Variety Name</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                    <button type="submit" class="btn btn-primary">Save</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            @endcan
        </div>
    </div>

    <script>
        $(document).ready(function() {
          $(document).on("change", "#crop_vertical_id", function(e) {
             
                $("#crop_id").html('<option value="">Select Crop Name</option>');
                $("#crop_variety_id").html('<option value="">Select Crop Variety Name</option>');

                if ($(this).val()) {
                    $.ajax({
                        url: "/get-crops/" + $(this).val(),
                        type: "GET",
                        dataType: "json",
                        success: function(data) {
                            $.each(data, function(index, crop) {
                                $("#crop_id").append(
                                    `<option value="${crop.id}">${crop.crop_name}</option>`
                                );
                            });
                        }
                    });
                }
            });

            // When Crop changes -> load varieties
            $("#crop_id").on("change", function() {
                $("#crop_variety_id").html('<option value="">Select Crop Variety Name</option>');

                if ($(this).val()) {
                    $.ajax({
                        url: "/get-varieties/" + $(this).val(),
                        type: "GET",
                        dataType: "json",
                        success: function(data) {
                            $.each(data, function(index, variety) {
                                $("#crop_variety_id").append(
                                    `<option value="${variety.id}">${variety.variety_name}</option>`
                                );
                            });
                        }
                    });
                }
            });
        });
    </script>
@endsection
