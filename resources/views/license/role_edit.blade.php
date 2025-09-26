{{-- @extends('layouts.app')
@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between bg-galaxy-transparent">
                        <h4 class="mb-sm-0">Edit Role</h4>
                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="javascript: void(0);">Masters</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('users.role') }}">Roles</a></li>
                                <li class="breadcrumb-item active">Edit Role</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Edit Role: {{ $role->name }}</h4>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('roles.update', $role->id) }}" method="POST" id="editRoleForm">
                                @csrf
                                @method('PUT')
                                <div class="row mb-3">
                                    <div class="col-md-4">
                                        <label for="role_name">Role Name:</label> <span class="text-danger">*</span>
                                        <input type="text" name="role_name" id="role_name"
                                            class="form-control @error('role_name') is-invalid @enderror"
                                            value="{{ old('role_name', $role->name) }}">
                                        @error('role_name')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                @error('permission')
                                    <div class="row mb-3">
                                        <div class="col-md-12">
                                            <span class="text-danger">{{ $message }}</span>
                                        </div>
                                    </div>
                                @enderror
                                <div class="accordion" id="permissionsAccordion">
                                    @foreach ($permissions as $module => $groups)
                                        <div class="accordion-item">
                                            <h2 class="accordion-header" id="heading-{{ Str::slug($module) }}">
                                                <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                                    data-bs-target="#collapse-{{ Str::slug($module) }}" aria-expanded="true"
                                                    aria-controls="collapse-{{ Str::slug($module) }}">
                                                    {{ strtoupper($module) }}
                                                </button>
                                            </h2>
                                            <div id="collapse-{{ Str::slug($module) }}"
                                                class="accordion-collapse collapse show"
                                                aria-labelledby="heading-{{ Str::slug($module) }}"
                                                data-bs-parent="#permissionsAccordion">
                                                <div class="accordion-body">
                                                    @foreach ($groups as $group => $groupPermissions)
                                                        <div class="mb-3">
                                                            <div class="d-flex align-items-center">
                                                                <input type="checkbox"
                                                                    class="form-check-input me-2 group-checkbox"
                                                                    data-group="{{ Str::slug($group, '-') }}">
                                                                <h6 class="mb-0 text-primary">{{ strtoupper($group) }}</h6>
                                                            </div>
                                                            <div class="row mt-2">
                                                                @foreach ($groupPermissions as $permission)
                                                                    <div class="col-6 col-md-4">
                                                                        <div class="form-check">
                                                                            <input type="checkbox" name="permission[]"
                                                                                class="form-check-input permission-checkbox {{ Str::slug($group, '-') }}"
                                                                                id="permission-{{ $permission['id'] }}"
                                                                                value="{{ $permission['name'] }}"
                                                                                {{ in_array($permission['name'], $rolePermissions ?? []) ? 'checked' : '' }}>
                                                                            <label class="form-check-label"
                                                                                for="permission-{{ $permission['id'] }}">{{ $permission['name'] }}</label>
                                                                        </div>
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                        <hr>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                <div class="mt-3">
                                    <button type="submit" class="btn btn-primary">Update Role</button>
                                    <a href="{{ route('users.role') }}" class="btn btn-secondary">Cancel</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script_section')
    <script>
        document.querySelectorAll('.group-checkbox').forEach(checkbox => {
            const group = checkbox.getAttribute('data-group');
            const permissions = document.querySelectorAll(`.${group}`);
            const allChecked = Array.from(permissions).every(p => p.checked);
            checkbox.checked = allChecked;
            checkbox.addEventListener('change', function() {
                permissions.forEach(cb => cb.checked = this.checked);
            });
        });

        $('#editRoleForm').on('submit', function(e) {
            const checked = document.querySelectorAll('input[name="permission[]"]:checked');
            if (checked.length === 0) {
                e.preventDefault();
                alert('Please select at least one permission.');
            }
        });
    </script>
@endsection --}}


@extends('layouts.app')
@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between bg-galaxy-transparent">
                        <h4 class="mb-sm-0">Edit Role</h4>
                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="javascript: void(0);">Masters</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('users.role') }}">Roles</a></li>
                                <li class="breadcrumb-item active">Edit Role</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Edit Role: {{ $role->name }}</h4>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('roles.update', $role->id) }}" method="POST" id="editRoleForm">
                                @csrf
                                @method('PUT')
                                <div class="row mb-3">
                                    <div class="col-md-4">
                                        <label for="role_name">Role Name:</label> <span class="text-danger">*</span>
                                        <input type="text" name="role_name" id="role_name"
                                            class="form-control @error('role_name') is-invalid @enderror"
                                            value="{{ old('role_name', $role->name) }}"
                                            {{ $isSuperAdmin ? 'readonly' : '' }}>
                                        @error('role_name')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                @error('permission')
                                    <div class="row mb-3">
                                        <div class="col-md-12">
                                            <span class="text-danger">{{ $message }}</span>
                                        </div>
                                    </div>
                                @enderror
                                <div class="accordion" id="permissionsAccordion">
                                    @foreach ($permissions as $module => $groups)
                                        <div class="accordion-item">
                                            <h2 class="accordion-header" id="heading-{{ Str::slug($module) }}">
                                                <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                                    data-bs-target="#collapse-{{ Str::slug($module) }}" aria-expanded="true"
                                                    aria-controls="collapse-{{ Str::slug($module) }}">
                                                    {{ strtoupper($module) }}
                                                </button>
                                            </h2>
                                            <div id="collapse-{{ Str::slug($module) }}"
                                                class="accordion-collapse collapse show"
                                                aria-labelledby="heading-{{ Str::slug($module) }}"
                                                data-bs-parent="#permissionsAccordion">
                                                <div class="accordion-body">
                                                    @foreach ($groups as $group => $groupPermissions)
                                                        <div class="mb-3">
                                                            <div class="d-flex align-items-center">
                                                                <input type="checkbox"
                                                                    class="form-check-input me-2 group-checkbox"
                                                                    data-group="{{ Str::slug($group, '-') }}"
                                                                    {{ $isSuperAdmin ? 'checked disabled' : '' }}>
                                                                <h6 class="mb-0 text-primary">{{ strtoupper($group) }}</h6>
                                                            </div>
                                                            <div class="row mt-2">
                                                                @foreach ($groupPermissions as $permission)
                                                                    <div class="col-6 col-md-4">
                                                                        <div class="form-check">
                                                                            <input type="checkbox" name="permission[]"
                                                                                class="form-check-input permission-checkbox {{ Str::slug($group, '-') }}"
                                                                                id="permission-{{ $permission['id'] }}"
                                                                                value="{{ $permission['name'] }}"
                                                                                {{ $isSuperAdmin || in_array($permission['name'], $rolePermissions ?? []) ? 'checked' : '' }}
                                                                                {{ $isSuperAdmin ? 'disabled' : '' }}>
                                                                            <label class="form-check-label"
                                                                                for="permission-{{ $permission['id'] }}">{{ $permission['name'] }}</label>
                                                                        </div>
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                        <hr>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                <div class="mt-3">
                                    <button type="submit" class="btn btn-primary">Update Role</button>
                                    <a href="{{ route('users.role') }}" class="btn btn-secondary">Cancel</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script_section')
    <script>
        document.querySelectorAll('.group-checkbox').forEach(checkbox => {
            const group = checkbox.getAttribute('data-group');
            const permissions = document.querySelectorAll(`.${group}`);
            const allChecked = Array.from(permissions).every(p => p.checked);
            checkbox.checked = allChecked;

            if (!checkbox.disabled) {
                checkbox.addEventListener('change', function() {
                    permissions.forEach(cb => {
                        if (!cb.disabled) {
                            cb.checked = this.checked;
                        }
                    });
                });
            }
        });

        $('#editRoleForm').on('submit', function(e) {
            @if (!$isSuperAdmin)
                const checked = document.querySelectorAll('input[name="permission[]"]:checked');
                if (checked.length === 0) {
                    e.preventDefault();
                    alert('Please select at least one permission.');
                }
            @endif
        });
    </script>
@endsection