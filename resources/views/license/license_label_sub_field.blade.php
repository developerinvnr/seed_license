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

                    <div class="card-header d-flex align-items-center justify-content-between">
                        <h4 class="card-title mb-0"><i class="ri-list-unordered"></i> Label Sub Field List</h4>
                        <button type="button" class="btn btn-primary rounded-pill" data-bs-toggle="modal"
                            data-bs-target="#addSubFieldModal">
                            <i class="ri-add-circle-fill me-2"></i> Add New
                        </button>
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
                                {{ $errors->first('field_name') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"
                                    aria-label="Close"></button>
                            </div>
                        @endif

                        <script>
                            setTimeout(() => {
                                document.querySelectorAll('.alert').forEach(alert => {
                                    alert.classList.remove('show');
                                    alert.classList.add('fade');
                                    setTimeout(() => alert.remove(), 500);
                                });
                            }, 2000);
                        </script>

                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Field Name</th>
                                    <th>Input Type</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($subFields as $index => $field)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $field->field_name }}</td>
                                        <td>
                                            @if ($field->input_type === 'text')
                                                Text
                                            @elseif($field->input_type === 'select')
                                                Select
                                            @elseif($field->input_type === 'date')
                                                Date
                                            @elseif($field->input_type === 'upload')
                                                File Upload
                                            @endif
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-warning btn-sm" data-bs-toggle="modal"
                                                data-bs-target="#editSubFieldModal" data-id="{{ $field->id }}"
                                                data-name="{{ $field->field_name }}"
                                                data-input_type="{{ $field->input_type }}"
                                                data-table_name="{{ $field->table_name }}"
                                                data-column_name="{{ $field->column_name }}">
                                                <i class="ri-edit-2-line"></i>
                                            </button>

                                            <button type="button" class="btn btn-info btn-sm view-mapped-labels-btn"
                                                data-id="{{ $field->id }}">
                                                <i class="ri-eye-line"></i>
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

        <!-- Add Modal -->
        <div class="modal fade" id="addSubFieldModal" tabindex="-1" aria-labelledby="addSubFieldLabel" aria-hidden="true">
            <div class="modal-dialog">
                <form action="{{ route('store_label_sub_field') }}" method="POST">
                    @csrf
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Add Sub Field</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="field_name" class="form-label">Field Name</label>
                                <input type="text" class="form-control" id="field_name" name="field_name" required>
                            </div>

                            <div class="mb-3">
                                <label for="input_type" class="form-label">Input Type</label>
                                <select class="form-control" id="input_type" name="input_type" required>
                                    <option value="text">Input Text</option>
                                    <option value="select">Select Type</option>
                                    <option value="date">Date</option>
                                    <option value="upload">Upload</option>
                                </select>
                            </div>

                            <div class="mb-3 d-none" id="table-section">
                                <label for="table_name" class="form-label">Select Table</label>
                                <select class="form-control" id="table_name" name="table_name">
                                    @foreach (DB::select('SHOW TABLES') as $table)
                                        @php $tableName = array_values((array) $table)[0]; @endphp
                                        <option value="{{ $tableName }}">{{ $tableName }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-3 d-none" id="column-section">
                                <label for="column_name" class="form-label">Select Column</label>
                                <select class="form-control" id="column_name" name="column_name">
                                    <!-- Filled dynamically with JS -->
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary">Save</button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Edit Modal -->
        <div class="modal fade" id="editSubFieldModal" tabindex="-1" aria-labelledby="editSubFieldLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <form action="{{ route('update_label_sub_field') }}" method="POST">
                    @csrf
                    <input type="hidden" name="edit_id" id="edit_id">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Edit Sub Field</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="edit_field_name" class="form-label">Field Name</label>
                                <input type="text" class="form-control" id="edit_field_name" name="edit_field_name"
                                    required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Input Type</label>
                                <select class="form-select" name="edit_input_type" id="edit_input_type" required>
                                    <option value="text">Input Text</option>
                                    <option value="select">Select Type</option>
                                    <option value="date">Date</option>
                                    <option value="upload">File Upload</option>
                                </select>
                            </div>

                            <div class="mb-3 table-fields d-none">
                                <label class="form-label">Table Name</label>
                                <select class="form-select" name="edit_table_name" id="edit_table_name">
                                    <option value=""> Select Table </option>
                                    @foreach (DB::select('SHOW TABLES') as $table)
                                        @php $tableName = array_values((array) $table)[0]; @endphp
                                        <option value="{{ $tableName }}">{{ $tableName }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-3 column-fields d-none">
                                <label class="form-label">Column Name</label>
                                <select class="form-select" name="edit_column_name" id="edit_column_name">
                                    <option value=""> Select Column </option>
                                </select>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary">Update</button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        </div>
                    </div>
            </div>
            </form>
        </div>

        <!-- View Mapped Labels Modal -->
        <div class="modal fade" id="viewMappedLabelsModal" tabindex="-1" aria-labelledby="viewMappedLabelsModalLabel"
            aria-hidden="true" data-bs-backdrop="static">
            <div class="modal-dialog modal-sm d-flex justify-content-center align-items-center">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="viewMappedLabelsModalLabel">Mapped Labels Name</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <ul id="mappedLabelsList" class="list-group"></ul>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <script>
        const editModal = document.getElementById('editSubFieldModal');
        editModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const id = button.getAttribute('data-id');
            const name = button.getAttribute('data-name');
            const inputType = button.getAttribute('data-input_type');
            const tableName = button.getAttribute('data-table_name');
            const columnName = button.getAttribute('data-column_name');
            document.getElementById('edit_id').value = id;
            document.getElementById('edit_field_name').value = name;
            document.getElementById('edit_input_type').value = inputType;
            toggleTableFields(inputType);
            if (inputType === 'select') {
                document.getElementById('edit_table_name').value = tableName;
                if (columnName) {
                    const columnSelect = document.getElementById('edit_column_name');
                    columnSelect.innerHTML = '<option value=""> Select Column </option>';
                    fetch(`/get-columns/${tableName}`)
                        .then(response => response.json())
                        .then(columns => {
                            columns.forEach(col => {
                                const option = document.createElement('option');
                                option.value = col;
                                option.text = col;
                                columnSelect.appendChild(option);
                            });
                            columnSelect.value = columnName;
                        });
                }
            }
        });

        document.getElementById('input_type').addEventListener('change', function() {
            const isSelect = this.value === 'select';
            document.getElementById('table-section').classList.toggle('d-none', !isSelect);
            document.getElementById('column-section').classList.toggle('d-none', !isSelect);
        });

        document.getElementById('table_name').addEventListener('change', function() {
            const tableName = this.value;

            fetch(`/get-table-columns/${tableName}`)
                .then(response => response.json())
                .then(columns => {
                    const columnSelect = document.getElementById('column_name');
                    columnSelect.innerHTML = '<option value="">Select Column</option>';
                    columns.forEach(col => {
                        columnSelect.innerHTML += `<option value="${col}">${col}</option>`;
                    });
                });
        });

        document.getElementById('edit_input_type').addEventListener('change', function() {
            toggleTableFields(this.value);
        });

        function toggleTableFields(type) {
            const tableFields = document.querySelector('.table-fields');
            const columnFields = document.querySelector('.column-fields');
            if (type === 'select') {
                tableFields.classList.remove('d-none');
                columnFields.classList.remove('d-none');
            } else {
                tableFields.classList.add('d-none');
                columnFields.classList.add('d-none');
            }
        }

        document.getElementById('edit_table_name').addEventListener('change', function() {
            const table = this.value;

            if (table) {
                fetch(`/get-columns/${table}`)
                    .then(response => response.json())
                    .then(columns => {
                        const columnSelect = document.getElementById('edit_column_name');
                        columnSelect.innerHTML = '<option value="">-- Select Column --</option>';
                        columns.forEach(col => {
                            const option = document.createElement('option');
                            option.value = col;
                            option.text = col;
                            columnSelect.appendChild(option);
                        });
                        const selectedColumn = document.getElementById('edit_column_name').value;
                        if (selectedColumn) {
                            columnSelect.value = selectedColumn;
                        }
                    });
            }
        });

        document.querySelectorAll('.view-mapped-labels-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const subFieldId = this.dataset.id;

                fetch(`/license/get-labels-by-sub-field/${subFieldId}`)
                    .then(response => response.json())
                    .then(data => {
                        const list = document.getElementById('mappedLabelsList');
                        list.innerHTML = '';

                        if (data.length > 0) {
                            data.forEach((label, index) => {
                                const li = document.createElement('li');
                                li.className = 'list-group-item';
                                li.textContent = `${index + 1}. ${label.label_name}`;
                                list.appendChild(li);
                            });
                        } else {
                            const li = document.createElement('li');
                            li.className = 'list-group-item text-muted';
                            li.textContent = 'No labels mapped';
                            list.appendChild(li);
                        }

                        new bootstrap.Modal(document.getElementById('viewMappedLabelsModal')).show();
                    });
            });
        });
    </script>
@endsection
