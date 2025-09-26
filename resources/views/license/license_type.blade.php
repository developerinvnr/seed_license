@extends('layouts.app')
@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between bg-galaxy-transparent">
                        <div class="page-title-right ms-auto">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="javascript:void(0);">Masters</a></li>
                                <li class="breadcrumb-item active">
                                    {{ ucwords(str_replace('-', ' ', Request::path())) }}
                                </li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header align-items-center d-flex">
                            <h4 class="card-title mb-0 flex-grow-1"><i class="ri-list-unordered"></i> License Category
                                List</h4>
                            <div class="flex-shrink-0">
                                @can('add-License Type')
                                    <button type="button"
                                        class="btn btn-primary btn-label waves-effect waves-light rounded-pill"
                                        data-bs-toggle="modal" data-bs-target="#addLicenseModal"> <i
                                            class="ri-add-circle-fill label-icon align-middle rounded-pill fs-16 me-2"></i>Add
                                        New</button>
                                @endcan
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
                            <table class="table nowrap dt-responsive align-middle table-hover table-bordered"
                                style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th>Sno</th>
                                        <th>License Category</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($licenseTypes as $index => $license)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $license->license_type }}</td>
                                            <td>
                                                @can('edit-License Type')
                                                    <a href="javascript:void(0);" class="text-dark  edit-btn"
                                                        data-id="{{ $license->id }}" data-name="{{ $license->license_type }}"
                                                        data-bs-toggle="modal" data-bs-target="#editLicenseModal">
                                                        <i class="ri-edit-2-line fs-5">edit</i>
                                                    </a>
                                                @endcan
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

            <!-- Add License Modal -->
            <div class="modal fade" id="addLicenseModal" tabindex="-1" aria-labelledby="addLicenseModalLabel"
                aria-hidden="true"  data-bs-backdrop="static" data-bs-keyboard="false">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="addLicenseModalLabel">Add License Category</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form action="{{ route('license_type.store') }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label for="licenseName" class="form-label">License Category</label>
                                    <input type="text" name="license_type" id="licenseName" class="form-control"
                                        required>
                                </div>
                                <div class="d-flex justify-content-end">
                                    <button type="submit" class="btn btn-primary"> <i
                                            class="ri-check-double-line label-icon align-middle rounded-pill fs-16 me-2">
                                            <span class="loader" style="display: none;"></span>
                                        </i>Submit
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Edit License Modal -->
            <div class="modal fade" id="editLicenseModal" tabindex="-1" aria-labelledby="editLicenseModalLabel"
                aria-hidden="true"  data-bs-backdrop="static" data-bs-keyboard="false">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="editLicenseModalLabel">Edit License Category</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form action="{{ route('license_type.update') }}" method="POST">
                            @csrf
                            <input type="hidden" name="id" id="edit_license_id">
                            <div class="modal-body">
                                <label for="edit_license_type" class="form-label">License Category</label>
                                <input type="text" name="license_type" id="edit_license_type" class="form-control"
                                    required>
                            </div>
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-primary"> <i
                                        class="ri-check-double-line label-icon align-middle rounded-pill fs-16 me-2">
                                        <span class="loader" style="display: none;"></span>
                                    </i>Update</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.edit-btn').forEach(button => {
                button.addEventListener('click', function() {
                    var licenseId = this.getAttribute('data-id');
                    $.ajax({
                        url: '/license-type/edit/' + licenseId,
                        type: 'GET',
                        success: function(response) {
                            if (response.license) {
                                document.getElementById('edit_license_id').value =
                                    response.license.id;
                                document.getElementById('edit_license_type').value =
                                    response.license.license_type;
                                var selectedFieldsArray = response.license.fields.split(
                                    ',').map(item => item.trim());
                                document.querySelectorAll('input[name="fields[]"]')
                                    .forEach(function(checkbox) {
                                        if (selectedFieldsArray.includes(checkbox
                                                .value.trim())) {
                                            checkbox.checked = true;
                                        } else {
                                            checkbox.checked = false;
                                        }
                                    });

                                var editModal = new bootstrap.Modal(document
                                    .getElementById('editLicenseModal'));
                                editModal.show();
                            } else {
                                console.error("Error: License data not found.");
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error("Error fetching data:", error);
                        }
                    });
                });
            });

            setTimeout(() => {
                let alert = document.querySelector('.alert');
                if (alert) {
                    alert.remove();
                }
            }, 3000);
        });

        $(document).ready(function() {
            $(document).on('click', '.btn-close', function(e) {
                e.preventDefault();
                $(".modal-backdrop").removeClass("show");
                $(".modal-backdrop").removeClass("fade");
                $(".modal-backdrop").remove();
            });
        });
    </script>
@endsection
