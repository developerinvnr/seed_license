{{-- @extends('layouts.app')
@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between bg-galaxy-transparent">
                        <h4 class="mb-sm-0">Auth Draft Master</h4>
                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="javascript: void(0);">Masters</a></li>
                                <li class="breadcrumb-item active">Auth Draft Master</li>
                            </ol>
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
                            <h4 class="card-title mb-0 flex-grow-1"><i class="ri-list-unordered"></i> Auth Draft Master</h4>
                        </div>
                        <div class="card-body">
                            <div class="container-fluid">
                                <div class="row">
                                    <!-- Sidebar for Dynamic Fields -->
                                    <div class="col-lg-3">
                                        <div class="card border">
                                            <div class="card-header bg-light">
                                                <h5 class="card-title mb-0"><b>Dynamic Fields</b></h5>
                                            </div>
                                            <div class="card-body">
                                                <input type="text" id="searchField" class="form-control mb-3"
                                                    placeholder="Search Field...">
                                                <div id="buttonContainer">
                                                    @foreach ($fields as $key => $label)
                                                        <button class="btn btn-custom btn-primary btn-sm w-100 mb-2"
                                                            data-label="{{ $key }}">{{ $label }}</button>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Main Editor Section -->
                                    <div class="col-lg-9">
                                        <!-- Draft Form -->
                                        <div class="card border">
                                            <div class="card-header bg-light">
                                                <h5 class="card-title mb-0">Create/Edit Draft</h5>
                                            </div>
                                            <div class="card-body">
                                                <form id="draftForm" action="{{ route('auth-draft-master.store') }}"
                                                    method="POST">
                                                    @csrf
                                                    <input type="hidden" name="_method" id="formMethod" value="POST">
                                                    <input type="hidden" name="draft_id" id="draftId">
                                                    <div class="mb-3">
                                                        <label for="title" class="form-label fw-medium">Draft Title <span
                                                                class="text-danger">*</span></label>
                                                        <input type="text" name="title" id="title"
                                                            class="form-control" required>
                                                        @error('title')
                                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="editor" class="form-label fw-medium">Draft
                                                            Content</label>
                                                           <textarea id="editor" name="content" class="form-control"></textarea>

                                                    </div>
                                                    <input type="hidden" name="input_fields[]" id="input_fields">
                                                    <button type="submit" class="btn btn-primary" id="submitButton">
                                                        <i class="ri-save-line align-middle me-1"></i> Save Draft
                                                    </button>
                                                    <button type="button" class="btn btn-secondary" id="cancelEdit"
                                                        style="display: none;">
                                                        <i class="ri-close-line align-middle me-1"></i> Cancel Edit
                                                    </button>
                                                </form>
                                            </div>
                                        </div>

                                        <!-- Drafts Table -->
                                        <div class="card border mt-4">
                                            <div class="card-header bg-light">
                                                <h5 class="card-title mb-0">Existing Drafts</h5>
                                            </div>
                                            <div class="card-body">
                                                <table class="table table-nowrap table-hover table-bordered"
                                                    style="width: 100%;">
                                                    <thead>
                                                        <tr>
                                                            <th>Sr. No.</th>
                                                            <th>Title</th>
                                                            <th>Actions</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @forelse ($drafts as $index => $draft)
                                                            <tr>
                                                                <td>{{ $index + 1 }}</td>
                                                                <td>{{ $draft->title }}</td>
                                                                <td>
                                                                    <button
                                                                        class="btn btn-sm btn-outline-warning edit-draft"
                                                                        data-id="{{ $draft->id }}"
                                                                        data-title="{{ $draft->title }}"
                                                                        data-content="{{ ($draft->content) }}"
                                                                        data-fields="{{ json_encode($draft->input_fields) }}">
                                                                        <i class="ri-edit-line"></i> Edit
                                                                    </button>
                                                                </td>
                                                            </tr>
                                                        @empty
                                                            <tr>
                                                                <td colspan="3" class="text-center text-muted">No
                                                                    drafts found.</td>
                                                            </tr>
                                                        @endforelse
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('components.theme.footer')

    <!-- Include jQuery and TinyMCE -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/5.10.2/tinymce.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Check if TinyMCE is loaded
            if (typeof tinymce === 'undefined') {
                console.error('TinyMCE is not loaded. Check CDN or network issues.');
                return;
            }

            tinymce.init({
                selector: '#editor',
                height: 450,
                plugins: 'lists link image table preview paste',
                toolbar: 'bold italic underline | alignleft aligncenter alignright | bullist numlist | link image | table | preview',
                menubar: false,
                statusbar: true,
                setup: function(editor) {
                    editor.on('init', function() {
                        console.log('TinyMCE initialized successfully');
                        resetForm();
                    });
                }
            });

            // Auto-close success alert
            setTimeout(function() {
                let alert = document.querySelector('.alert-success');
                if (alert) {
                    let bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                }
            }, 2000);

            // Sidebar button click to add placeholder
            const buttons = document.querySelectorAll('.btn-custom');
            buttons.forEach(button => {
                button.addEventListener('click', () => {
                    const label = button.dataset.label;
                    addPlaceholder(label);
                });
            });

            // Search field filtering
            const searchField = document.getElementById('searchField');
            searchField.addEventListener('keyup', () => {
                const filter = searchField.value.toLowerCase();
                const buttons = document.querySelectorAll('#buttonContainer button');
                buttons.forEach(button => {
                    const label = button.getAttribute('data-label').toLowerCase();
                    button.style.display = label.includes(filter) ? 'block' : 'none';
                });
            });

            // Extract placeholders before form submission
            document.getElementById('draftForm').addEventListener('submit', function(e) {
                const editor = tinymce.get('editor');
                if (editor) {
                    const content = editor.getContent();
                    const placeholders = extractPlaceholders(content);
                    document.getElementById('input_fields').value = JSON.stringify(placeholders);
                } else {
                    console.error('TinyMCE editor not found during form submission.');
                    e.preventDefault(); // Prevent submission if editor is missing
                }
            });

            // Edit button click to populate form
            document.querySelectorAll('.edit-draft').forEach(button => {
                button.addEventListener('click', function() {
                    const id = this.dataset.id;
                    const title = this.dataset.title;
                    const content = this.dataset.content;
                    const fields = this.dataset.fields;

                    document.getElementById('draftForm').action =
                        '{{ url('/auth-draft-master') }}/' + id;
                    document.getElementById('formMethod').value = 'PUT';
                    document.getElementById('draftId').value = id;
                    document.getElementById('title').value = title;
                    const editor = tinymce.get('editor');
                    if (editor) {
                        editor.setContent(content);
                    } else {
                        console.error('TinyMCE editor not found during edit.');
                    }
                    document.getElementById('input_fields').value = fields;
                    document.getElementById('submitButton').innerHTML =
                        '<i class="ri-save-line align-middle me-1"></i> Update Draft';
                    document.getElementById('cancelEdit').style.display = 'inline-block';
                    document.getElementById('draftForm').scrollIntoView({
                        behavior: 'smooth'
                    });
                });
            });

            // Cancel edit button
            document.getElementById('cancelEdit').addEventListener('click', resetForm);
        });

        function addPlaceholder(label) {
            const editor = tinymce.get('editor');
            if (!editor) {
                console.error('TinyMCE editor is not initialized yet.');
                return;
            }
            const uniqueKey = generateUniqueKey(label);
            const placeholderText = `[${label}]_${uniqueKey}`;
            editor.execCommand('mceToggleFormat', false, 'bold');
            editor.execCommand('mceInsertContent', false, placeholderText);
            editor.execCommand('mceToggleFormat', false, 'bold');
        }

        let uniqueCounterMap = {};

        function generateUniqueKey(label) {
            if (!uniqueCounterMap[label]) uniqueCounterMap[label] = 1;
            else uniqueCounterMap[label]++;
            if (uniqueCounterMap[label] > 999) uniqueCounterMap[label] = 1;
            return `${uniqueCounterMap[label]}`;
        }

        function extractPlaceholders(content) {
            const regex = /\[[a-zA-Z0-9_]+\]_\d+/g;
            return content.match(regex) || [];
        }

        function resetForm() {
            const editor = tinymce.get('editor');
            document.getElementById('draftForm').action = '{{ route('auth-draft-master.store') }}';
            document.getElementById('formMethod').value = 'POST';
            document.getElementById('draftId').value = '';
            document.getElementById('title').value = '';
            if (editor) editor.setContent('');
            else console.error('TinyMCE editor not found during form reset.');
            document.getElementById('input_fields').value = '';
            document.getElementById('submitButton').innerHTML = '<i class="ri-save-line align-middle me-1"></i> Save Draft';
            document.getElementById('cancelEdit').style.display = 'none';
        }
    </script>

    <style>
        /* Ensure editor is visible and properly styled */
        .editor-container {
            min-height: 450px !important;
            visibility: visible !important;
            display: block !important;
            width: 100% !important;
        }

        .tox-tinymce {
            z-index: 1000 !important;
            /* Higher z-index to avoid overlap */
            border: 1px solid #ced4da !important;
            border-radius: 0.25rem !important;
            min-height: 450px !important;
            width: 100% !important;
            max-width: 100% !important;
        }

        .card-body .tox-tinymce {
            margin: 0 !important;
            padding: 0 !important;
        }
    </style>
@endsection --}}


@extends('layouts.app')

@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between bg-galaxy-transparent">
                        <h4 class="mb-sm-0">Auth Draft Master</h4>
                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="javascript: void(0);">Masters</a></li>
                                <li class="breadcrumb-item active">Auth Draft Master</li>
                            </ol>
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
                            <h4 class="card-title mb-0 flex-grow-1"><i class="ri-list-unordered"></i> Auth Draft Master</h4>
                        </div>
                        <div class="card-body">
                            <div class="container-fluid">
                                <div class="row">
                                    <!-- Sidebar for Dynamic Fields -->
                                    <div class="col-lg-3">
                                        <div class="card border h-100">
                                            <div class="card-header bg-light">
                                                <h5 class="card-title mb-0"><b>Dynamic Fields</b></h5>
                                            </div>
                                            <div class="card-body p-0">
                                                <div class="p-3">
                                                    <input type="text" id="searchField" class="form-control mb-3" placeholder="Search Field...">
                                                </div>
                                                <div class="dynamic-fields-list" style="max-height: 70vh; overflow-y: auto;">
                                                    <div class="accordion" id="fieldsAccordion">
                                                        <div class="accordion-item">
                                                            <h2 class="accordion-header" id="fieldsHeading">
                                                                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFields" aria-expanded="true" aria-controls="collapseFields">
                                                                    Available Fields
                                                                </button>
                                                            </h2>
                                                            <div id="collapseFields" class="accordion-collapse collapse show" aria-labelledby="fieldsHeading" data-bs-parent="#fieldsAccordion">
                                                                <div class="accordion-body p-0">
                                                                    <div id="buttonContainer" class="list-group list-group-flush">
                                                                        @foreach ($fields as $key => $label)
                                                                            <button class="list-group-item list-group-item-action btn-custom" data-label="{{ $key }}">
                                                                                <i class="ri-checkbox-blank-circle-line me-2"></i> {{ $label }}
                                                                            </button>
                                                                        @endforeach
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Main Editor Section -->
                                    <div class="col-lg-9">
                                        <!-- Draft Form -->
                                        <div class="card border">
                                            <div class="card-header bg-light">
                                                <h5 class="card-title mb-0">Create/Edit Draft</h5>
                                            </div>
                                            <div class="card-body">
                                                <form id="draftForm" action="{{ route('auth-draft-master.store') }}" method="POST">
                                                    @csrf
                                                    <input type="hidden" name="_method" id="formMethod" value="POST">
                                                    <input type="hidden" name="draft_id" id="draftId">
                                                    <div class="mb-3">
                                                        <label for="title" class="form-label fw-medium">Draft Title <span class="text-danger">*</span></label>
                                                        <input type="text" name="title" id="title" class="form-control" required>
                                                        @error('title')
                                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="editor" class="form-label fw-medium">Draft Content</label>
                                                        <textarea id="editor" name="content" class="form-control"></textarea>
                                                        @error('content')
                                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                    <input type="hidden" name="input_fields[]" id="input_fields">
                                                    <button type="submit" class="btn btn-primary" id="submitButton">
                                                        <i class="ri-save-line align-middle me-1"></i> Save Draft
                                                    </button>
                                                    <button type="button" class="btn btn-secondary" id="cancelEdit" style="display: none;">
                                                        <i class="ri-close-line align-middle me-1"></i> Cancel Edit
                                                    </button>
                                                </form>
                                            </div>
                                        </div>

                                        <!-- Drafts Table -->
                                        <div class="card border mt-4">
                                            <div class="card-header bg-light">
                                                <h5 class="card-title mb-0">Existing Drafts</h5>
                                            </div>
                                            <div class="card-body">
                                                <table class="table table-nowrap table-hover table-bordered" style="width: 100%;">
                                                    <thead>
                                                        <tr>
                                                            <th>Sr. No.</th>
                                                            <th>Title</th>
                                                            <th>Actions</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @forelse ($drafts as $index => $draft)
                                                            <tr>
                                                                <td>{{ $index + 1 }}</td>
                                                                <td>{{ $draft->title }}</td>
                                                                <td>
                                                                    <button class="btn btn-sm btn-outline-warning edit-draft" data-id="{{ $draft->id }}"
                                                                        data-title="{{ $draft->title }}" data-content="{{ ($draft->content) }}"
                                                                        data-fields="{{ json_encode($draft->input_fields) }}">
                                                                        <i class="ri-edit-line"></i> Edit
                                                                    </button>
                                                                </td>
                                                            </tr>
                                                        @empty
                                                            <tr>
                                                                <td colspan="3" class="text-center text-muted">No drafts found.</td>
                                                            </tr>
                                                        @endforelse
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('components.theme.footer')

    <!-- Include jQuery and TinyMCE -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/5.10.2/tinymce.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Check if TinyMCE is loaded
            if (typeof tinymce === 'undefined') {
                console.error('TinyMCE is not loaded. Check CDN or network issues.');
                return;
            }

            tinymce.init({
                selector: '#editor',
                height: 450,
                plugins: 'lists link image table preview paste',
                toolbar: 'bold italic underline | alignleft aligncenter alignright | bullist numlist | link image | table | preview',
                menubar: false,
                statusbar: true,
                setup: function(editor) {
                    editor.on('init', function() {
                        console.log('TinyMCE initialized successfully');
                        resetForm();
                    });
                }
            });

            // Auto-close success alert
            setTimeout(function() {
                let alert = document.querySelector('.alert-success');
                if (alert) {
                    let bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                }
            }, 2000);

            // Sidebar button click to add placeholder
            const buttons = document.querySelectorAll('.btn-custom');
            buttons.forEach(button => {
                button.addEventListener('click', () => {
                    const label = button.dataset.label;
                    addPlaceholder(label);
                });
            });

            // Search field filtering
            const searchField = document.getElementById('searchField');
            searchField.addEventListener('keyup', () => {
                const filter = searchField.value.toLowerCase();
                const buttons = document.querySelectorAll('#buttonContainer .btn-custom');
                buttons.forEach(button => {
                    const label = button.getAttribute('data-label').toLowerCase();
                    button.style.display = label.includes(filter) ? 'flex' : 'none';
                });
            });

            // Extract placeholders before form submission
            document.getElementById('draftForm').addEventListener('submit', function(e) {
                const editor = tinymce.get('editor');
                if (editor) {
                    const content = editor.getContent();
                    const placeholders = extractPlaceholders(content);
                    document.getElementById('input_fields').value = JSON.stringify(placeholders);
                } else {
                    console.error('TinyMCE editor not found during form submission.');
                    e.preventDefault(); // Prevent submission if editor is missing
                }
            });

            // Edit button click to populate form
            document.querySelectorAll('.edit-draft').forEach(button => {
                button.addEventListener('click', function() {
                    const id = this.dataset.id;
                    const title = this.dataset.title;
                    const content = this.dataset.content;
                    const fields = this.dataset.fields;

                    document.getElementById('draftForm').action = '{{ url('/auth-draft-master') }}/' + id;
                    document.getElementById('formMethod').value = 'PUT';
                    document.getElementById('draftId').value = id;
                    document.getElementById('title').value = title;
                    const editor = tinymce.get('editor');
                    if (editor) {
                        editor.setContent(content);
                    } else {
                        console.error('TinyMCE editor not found during edit.');
                    }
                    document.getElementById('input_fields').value = fields;
                    document.getElementById('submitButton').innerHTML = '<i class="ri-save-line align-middle me-1"></i> Update Draft';
                    document.getElementById('cancelEdit').style.display = 'inline-block';
                    document.getElementById('draftForm').scrollIntoView({ behavior: 'smooth' });
                });
            });

            // Cancel edit button
            document.getElementById('cancelEdit').addEventListener('click', resetForm);
        });

        function addPlaceholder(label) {
            const editor = tinymce.get('editor');
            if (!editor) {
                console.error('TinyMCE editor is not initialized yet.');
                return;
            }
            const uniqueKey = generateUniqueKey(label);
            const placeholderText = `[${label}]_${uniqueKey}`;
            editor.execCommand('mceToggleFormat', false, 'bold');
            editor.execCommand('mceInsertContent', false, placeholderText);
            editor.execCommand('mceToggleFormat', false, 'bold');
        }

        let uniqueCounterMap = {};

        function generateUniqueKey(label) {
            if (!uniqueCounterMap[label]) uniqueCounterMap[label] = 1;
            else uniqueCounterMap[label]++;
            if (uniqueCounterMap[label] > 999) uniqueCounterMap[label] = 1;
            return `${uniqueCounterMap[label]}`;
        }

        function extractPlaceholders(content) {
            const regex = /\[[a-zA-Z0-9_]+\]_\d+/g;
            return content.match(regex) || [];
        }

        function resetForm() {
            const editor = tinymce.get('editor');
            document.getElementById('draftForm').action = '{{ route('auth-draft-master.store') }}';
            document.getElementById('formMethod').value = 'POST';
            document.getElementById('draftId').value = '';
            document.getElementById('title').value = '';
            if (editor) editor.setContent('');
            else console.error('TinyMCE editor not found during form reset.');
            document.getElementById('input_fields').value = '';
            document.getElementById('submitButton').innerHTML = '<i class="ri-save-line align-middle me-1"></i> Save Draft';
            document.getElementById('cancelEdit').style.display = 'none';
        }
    </script>

    <style>
        /* Ensure editor is visible and properly styled */
        .editor-container {
            min-height: 450px !important;
            visibility: visible !important;
            display: block !important;
            width: 100% !important;
        }

        .tox-tinymce {
            z-index: 1000 !important;
            /* Higher z-index to avoid overlap */
            border: 1px solid #ced4da !important;
            border-radius: 0.25rem !important;
            min-height: 450px !important;
            width: 100% !important;
            max-width: 100% !important;
        }

        .card-body .tox-tinymce {
            margin: 0 !important;
            padding: 0 !important;
        }

        /* Dynamic Fields Sidebar Styling */
        .dynamic-fields-list {
            padding: 0 0.5rem;
        }

        .list-group-item {
            display: flex;
            align-items: center;
            padding: 0.5rem 1rem;
            border: none;
            background: none;
            transition: background-color 0.2s;
        }

        .list-group-item:hover {
            background-color: #f8f9fa;
            cursor: pointer;
        }

        .list-group-item i {
            color: #0d6efd;
            margin-right: 0.5rem;
        }

        .accordion-button {
            font-weight: 500;
            background-color: #f8f9fa;
        }

        .accordion-button:not(.collapsed) {
            color: #0d6efd;
            background-color: #e9ecef;
        }

        @media (max-width: 992px) {
            .col-lg-3 {
                flex: 0 0 100%;
                max-width: 100%;
            }
            .col-lg-9 {
                flex: 0 0 100%;
                max-width: 100%;
            }
            .dynamic-fields-list {
                max-height: 50vh;
            }
        }
    </style>
@endsection 