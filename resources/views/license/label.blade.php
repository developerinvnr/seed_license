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
                        <h4 class="card-title mb-0 flex-grow-1"><i class="ri-list-unordered"></i> Label List</h4>
                        <div class="flex-shrink-0">
                            <button type="button" class="btn btn-primary btn-label waves-effect waves-light rounded-pill"
                                style="background-color: #132649; color: white;" data-bs-toggle="modal"
                                data-bs-target="#addLicenseModal">
                                <i class="ri-add-circle-fill label-icon align-middle rounded-pill fs-16 me-2"></i> Add New
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
                        <table class="table nowrap dt-responsive align-middle table-hover table-bordered"
                            style="width: 100%;">
                            <thead>
                                <tr>
                                    <th>Sno</th>
                                    <th>Label Name</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($labels as $index => $label)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $label->label_name }}</td>
                                        <td>
                                            <button class="btn btn-sm btn-warning edit-btn" title="Edit License"
                                                data-id="{{ $label->id }}" data-name="{{ $label->label_name }}"
                                                data-bs-toggle="modal" data-bs-target="#editLicenseModal">
                                                <i class="ri-edit-2-line"></i>
                                            </button>

                                            <button class="btn btn-sm btn-info map-btn" title="Map Sub Fields"
                                                data-id="{{ $label->id }}" data-name="{{ $label->label_name }}"
                                                data-bs-toggle="modal" data-bs-target="#mapLabelModal">
                                                <i class="ri-map-pin-line"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Add License Modal -->
        <div class="modal fade" id="addLicenseModal" tabindex="-1" aria-labelledby="addLicenseModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <form method="POST" action="{{ route('license_label.store') }}">
                    @csrf
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="addLicenseModalLabel">Add New Label</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="label_name" class="form-label">Label Name</label>
                                <input type="text" class="form-control" id="label_name" name="label_name" required>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Save Label</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Edit License Modal -->
        <div class="modal fade" id="editLicenseModal" tabindex="-1" aria-labelledby="editLicenseModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <form method="POST" action="" id="editLicenseForm">
                    @csrf
                    @method('PUT')
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="editLicenseModalLabel">Edit Label</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="edit_label_name" class="form-label">Label Name</label>
                                <input type="text" class="form-control" id="edit_label_name" name="label_name"
                                    required>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Update Label</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
       
        <!-- Map Label Modal -->
        <div class="modal fade" id="mapLabelModal" tabindex="-1" aria-labelledby="mapLabelModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <form method="POST" action="{{ route('map.sub.fields') }}">
                        @csrf
                        <input type="hidden" name="label_id" id="modalLabelId">

                        <div class="modal-header">
                            <h5 class="modal-title" id="mapLabelModalLabel">Map Sub Fields to Label: <span
                                    id="modalLabelName" style="color: rgb(213, 156, 30);" class="fw-bold"></span></h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>

                        <div class="modal-body">
                            <div class="row">
                                @foreach ($subFields as $subField)
                                    <div class="col-md-4">
                                        <div class="form-check mb-3">
                                            <input class="form-check-input sub-field-checkbox" type="checkbox"
                                                name="sub_field_ids[]" value="{{ $subField->id }}"
                                                id="subfield{{ $subField->id }}">
                                            <label class="form-check-label" for="subfield{{ $subField->id }}">
                                                {{ $subField->field_name }}
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary">Map Sub Fields</button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
    <script>
        $(document).on('click', '.edit-btn', function() {
            var id = $(this).data('id');
            var name = $(this).data('name');

            $('#edit_label_name').val(name);
            $('#editLicenseForm').attr('action', '/license-label/' + id);

        });

        setTimeout(function() {
            let alert = document.querySelector('.alert');
            if (alert) {
                alert.classList.remove('show');
                alert.classList.add('fade');
                setTimeout(() => alert.remove(), 500);
            }
        }, 2000);
      
        document.addEventListener('DOMContentLoaded', function() {
            const modal = document.getElementById('mapLabelModal');

            document.querySelectorAll('.map-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const labelId = this.dataset.id;
                    const labelName = this.dataset.name;
                    document.getElementById('modalLabelId').value = labelId;
                    document.getElementById('modalLabelName').textContent = labelName;
                    document.querySelectorAll('.sub-field-checkbox').forEach(cb => cb.checked =
                        false);
                    fetch(`/license/get-mapped-sub-fields/${labelId}`)
                        .then(response => response.json())
                        .then(data => {
                            data.forEach(id => {
                                const checkbox = document.getElementById(
                                    `subfield${id}`);
                                if (checkbox) checkbox.checked = true;
                            });
                        });
                });
            });
        });
    </script>
@endsection
