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
                        <h4 class="card-title mb-0 flex-grow-1"><i class="ri-list-unordered"></i>API Data
                        </h4>
                        <div class="flex-shrink-0">
                            <form id="syncForm" action="{{ route('api_data.sync') }}" method="POST">
                                @csrf
                                <button type="submit" id="syncButton"
                                    class="btn btn-primary btn-label waves-effect waves-light rounded-pill"
                                    style="background-color: #132649; color: white;">
                                    <i class="label-icon align-middle rounded-pill fs-16 me-2">
                                        <span id="loader" class="spinner-border spinner-border-sm d-none" role="status"
                                            aria-hidden="true"></span>
                                        <i id="successIcon" class="ri-check-line d-none ms-2"
                                            style="color: #28a745;"></i></i>
                                    <span id="buttonText">Sync Data</span>
                                </button>
                            </form>
                        </div>

                    </div>
                    <div class="card-body">
                        @if (session('success'))
                            <div class="alert alert-success" id="successAlert">{{ session('success') }}</div>
                        @elseif(session('error'))
                            <div class="alert alert-danger" id="errorAlert">{{ session('error') }}</div>
                        @endif

                        <form id="importForm" action="{{ route('api_data.import') }}" method="POST">
                            @csrf
                            <table class="table nowrap dt-responsive align-middle table-hover table-bordered"
                                style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th><input type="checkbox" id="selectAll"></th>
                                        <th>S.No.</th>
                                        <th>API Name</th>
                                        <th>Endpoint</th>
                                        <th>Description</th>
                                        <th>Parameters</th>
                                        <th>Table Name</th>
                                    </tr>
                                </thead>
                                <tbody id="apiDataTable">
                                    @forelse($apiData as $api)
                                        <tr>
                                            <td>
                                                <input type="checkbox" name="selected_ids[]" value="{{ $api->id }}"
                                                    class="selectBox">
                                            </td>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $api->api_name }}</td>
                                            <td>{{ $api->api_end_point }}</td>
                                            <td>{{ $api->description }}</td>
                                            <td>{{ $api->parameters }}</td>
                                            <td>{{ $api->table_name }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center">No API data available.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                            <button type="submit" class="btn btn-success mt-3">
                                <i class="ri-upload-line me-1"></i> Import Selected
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('syncForm').addEventListener('submit', function(e) {
            e.preventDefault();

            let syncButton = document.getElementById('syncButton');
            let buttonText = document.getElementById('buttonText');
            let loader = document.getElementById('loader');
            let successIcon = document.getElementById('successIcon');

            syncButton.disabled = true;
            buttonText.innerText = 'Syncing...';
            loader.classList.remove('d-none');

            fetch(this.action, {
                    method: 'POST',
                    body: new FormData(this),
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                })
                .then(res => res.json())
                .then(data => {
                    loader.classList.add('d-none');

                    if (data.success) {
                        buttonText.innerText = 'Data Synced';
                        successIcon.classList.remove('d-none');
                        updateTable(data.apiData);
                    } else {
                        buttonText.innerText = 'Sync Failed';
                        syncButton.classList.remove('btn-primary');
                        syncButton.classList.add('btn-danger');
                        alert(data.message); // show error message
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    buttonText.innerText = 'Sync Failed';
                    loader.classList.add('d-none');
                    syncButton.classList.remove('btn-primary');
                    syncButton.classList.add('btn-danger');
                })
                .finally(() => {
                    setTimeout(() => {
                        buttonText.innerText = 'Sync Data';
                        syncButton.disabled = false;
                        successIcon.classList.add('d-none');
                        syncButton.classList.remove('btn-danger');
                        syncButton.classList.add('btn-primary');
                    }, 2000);
                });
        });

        function updateTable(apiData) {
            let apiDataTable = document.getElementById('apiDataTable');
            apiDataTable.innerHTML = '';

            if (apiData.length === 0) {
                apiDataTable.innerHTML = `
                <tr>
                    <td colspan="7" class="text-center">No API data available.</td>
                </tr>`;
            } else {
                apiData.forEach((api, index) => {
                    apiDataTable.innerHTML += `
                    <tr>
                        <td><input type="checkbox" name="selected_ids[]" value="${api.id}" class="selectBox"></td>
                        <td>${index + 1}</td>
                        <td>${api.api_name}</td>
                        <td>${api.api_end_point}</td>
                        <td>${api.description}</td>
                        <td>${api.parameters}</td>
                        <td>${api.table_name}</td>
                    </tr>`;
                });
            }
        }

        setTimeout(function() {
            let successAlert = document.getElementById('successAlert');
            let errorAlert = document.getElementById('errorAlert');

            if (successAlert) {
                successAlert.style.display = 'none';
            }
            if (errorAlert) {
                errorAlert.style.display = 'none';
            }
        }, 2000);
    </script>
@endsection
