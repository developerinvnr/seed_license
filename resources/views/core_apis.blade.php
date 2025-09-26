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

                <div class="table-responsive mailbox-messages">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="download_label h4 mb-0">Core APIs</div>
                        <div class="btn-group" role="group">
                            <button id="fetchApis" class="btn btn-primary btn-md rounded-pill me-2">
                                <i class="bi bi-cloud-download me-1"></i> Fetch APIs
                            </button>
                            <button id="syncApis" class="btn btn-success btn-md rounded-pill">
                                <i class="bi bi-arrow-repeat me-1"></i> Sync APIs
                            </button>
                        </div>
                    </div>

                    <div id="syncedDataList"
                        style="display:none; margin-bottom: 10px; margin-top: 10px; padding: 10px; border: 1px solid #ccc; background: #f9f9f9;">
                        <strong>Synced Data:</strong>
                        <div id="syncList"></div>
                    </div>

                    <table class="table table-striped table-hover example mt-3">
                        <thead>
                            <tr>
                                <th>S No.</th>
                                <th>API ID</th>
                                <th>API Name</th>
                                <th>Endpoint</th>
                                <th>Description</th>
                                <th>Table Name</th>
                                <th>Parameters</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if (!empty($api_list) && count($api_list))
                                @foreach ($api_list as $i => $api)
                                    <tr>
                                        <td>{{ $i + 1 }}</td>
                                        <td>{{ $api->api_id }}</td>
                                        <td>{{ $api->api_name ?? '' }}</td>
                                        <td>{{ $api->api_end_point }}</td>
                                        <td>{{ $api->description ?? '' }}</td>
                                        <td>{{ $api->table_name }}</td>
                                        <td>{{ $api->parameters }}</td>
                                        <td>
                                            <button class="btn btn-info btn-sm sync-api-btn"
                                                data-parameters="{{ htmlspecialchars($api->parameters ?? '', ENT_QUOTES, 'UTF-8') }}"
                                                data-api="{{ htmlspecialchars($api->api_end_point ?? '', ENT_QUOTES, 'UTF-8') }}"
                                                data-table="{{ htmlspecialchars($api->table_name ?? '', ENT_QUOTES, 'UTF-8') }}"
                                                title="Sync API Data">
                                                Sync
                                            </button>

                                            <button class="btn btn-secondary btn-sm view-data-btn"
                                                data-table="{{ htmlspecialchars($api->table_name) }}"
                                                data-api="{{ htmlspecialchars($api->api_end_point) }}"
                                                title="View API Data" style="background-color: #1873e3;color: white;">
                                                View
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr class="text-center">
                                    <td colspan="8">No API data found.</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>

                    <div id="apiDataModal" class="modal fade" tabindex="-1" role="dialog">
                        <div class="modal-dialog modal-fullscreen" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">API Data (<span id="dataTitle"></span>)</h5>
                                    <button type="button" class="close" data-bs-dismiss="modal">&times;</button>
                                </div>
                                <div class="modal-body">
                                    <div class="table-responsive">
                                        <table class="table table-bordered" id="apiDataTable">
                                            <thead>
                                                <tr id="apiTableHead"></tr>
                                            </thead>
                                            <tbody id="apiTableBody"></tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <script>
                    $(document).ready(function() {
                        function showLoading(button) {
                            button.prop("disabled", true).data("original-text", button.text()).text("Wait...");
                        }

                        function hideLoading(button) {
                            button.prop("disabled", false).text(button.data("original-text"));
                        }


                        $("#fetchApis").click(function() {
                            var button = $(this);
                            showLoading(button);

                            $.ajax({
                                url: "{{ route('core.fetch_apis') }}",
                                headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                },
                                type: "GET",
                                dataType: "json",
                                success: function(response) {
                                    alert(response.message);
                                    location.reload();
                                },
                                error: function() {
                                    alert("Failed to fetch API list.");
                                },
                                complete: function() {
                                    hideLoading(button);
                                },
                            });
                        });

                        $("#syncApis").click(function() {
                            var button = $(this);
                            showLoading(button);

                            $.ajax({
                                url: "{{ route('core.sync_apis') }}",
                                headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                },
                                type: "GET",
                                dataType: "text",
                                success: function(responseText) {
                                    try {

                                        let jsonMatches = responseText.match(/{[^}]+}/g);
                                        let syncedData = [];

                                        if (jsonMatches) {
                                            jsonMatches.forEach(jsonString => {
                                                let response = JSON.parse(jsonString);
                                                if (response.status === "success") {
                                                    syncedData.push(response.message);
                                                }
                                            });
                                        }

                                        if (syncedData.length > 0) {
                                            let syncList = "<ul>";
                                            syncedData.forEach(item => {
                                                syncList += `<li>${item}</li>`;
                                            });
                                            syncList += "</ul>";

                                            $("#syncList").html(syncList);
                                            $("#syncedDataList").fadeIn();

                                            setTimeout(function() {
                                                $("#syncedDataList").fadeOut();
                                            }, 10000);
                                        } else {
                                            alert("No data was synced.");
                                        }
                                    } catch (error) {
                                        console.error("JSON Parsing Error:", error);
                                        alert("Failed to parse API response.");
                                    }
                                },
                                error: function() {
                                    alert("Failed to sync API data.");
                                },
                                complete: function() {
                                    hideLoading(button);
                                },
                            });
                        });

                        $(".sync-api-btn").click(function() {
                            var button = $(this);
                            var apiEndPoint = button.data("api");
                            var tableName = button.data("table");
                            var parameters = button.data("parameters");

                            var requestData = {
                                api_end_point: apiEndPoint,
                                table_name: tableName,
                                params: {}
                            };


                            if (parameters) {
                                var paramArray = parameters.split(",").map(param => param.trim());

                                for (var i = 0; i < paramArray.length; i++) {
                                    var key = paramArray[i];
                                    var userValue = prompt(`Enter value for ${key}:`);

                                    if (userValue !== null && userValue !== "") {
                                        requestData.params[key] = userValue;
                                    } else {
                                        alert("Sync cancelled. Parameter is required.");
                                        return;
                                    }
                                }
                            }

                            showLoading(button);

                            $.ajax({
                                url: "{{ route('core.sync_single_api') }}",
                                headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                },
                                type: "POST",
                                data: JSON.stringify(requestData),
                                contentType: "application/json",
                                dataType: "json",
                                success: function(response) {
                                    alert(response.message);
                                },
                                error: function() {
                                    alert("Failed to sync API.");
                                },
                                complete: function() {
                                    hideLoading(button);
                                },
                            });
                        });

                        $(".view-data-btn").click(function() {
                            var button = $(this);
                            showLoading(button);
                            $("#dataTitle").text(button.data("table"));
                            $.ajax({
                                url: "{{ route('core.get_api_data') }}",
                                headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                },
                                type: "POST",
                                data: {
                                    table_name: button.data("table")
                                },
                                dataType: "json",
                                beforeSend: function() {
                                    $("#apiTableHead, #apiTableBody").html("");
                                },
                                success: function(response) {
                                    if (response.status === "success" && response.data.length > 0) {

                                        var data = response.data;
                                        var headers = Object.keys(data[0]);
                                        var headerRow = headers.map((h) => `<th>${h}</th>`).join("");
                                        $("#apiTableHead").html(headerRow);

                                        var rows = data
                                            .map((row) => {
                                                return `<tr>` + headers.map((h) =>
                                                        `<td>${row[h] !== null ? row[h] : ""}</td>`)
                                                    .join("") + `</tr>`;
                                            })
                                            .join("");
                                        $("#apiTableBody").html(rows);

                                        if ($.fn.DataTable.isDataTable("#apiDataTable")) {
                                            $("#apiDataTable").DataTable().clear().destroy();
                                        }

                                        setTimeout(function() {
                                            $("#apiDataTable").DataTable({
                                                responsive: true,
                                                autoWidth: false,
                                                pageLength: 6,
                                            });
                                        }, 100);

                                        $("#apiDataModal").modal("show");
                                    } else {
                                        alert("No data found for this API.");
                                    }
                                },
                                error: function() {
                                    alert("Failed to fetch API data.");
                                },
                                complete: function() {
                                    hideLoading(button);
                                },
                            });
                        });

                        $(".empty-data-btn").click(function() {
                            var button = $(this);
                            if (!confirm("Are you sure you want to delete all data from this table?")) return;
                            showLoading(button);

                            $.ajax({
                                url: "{{ route('core.empty_table') }}",
                                headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                },
                                type: "POST",
                                data: {
                                    table_name: button.data("table")
                                },
                                dataType: "json",
                                success: function(response) {
                                    alert(response.message);
                                },
                                error: function() {
                                    alert("Failed to empty table.");
                                },
                                complete: function() {
                                    hideLoading(button);
                                },
                            });
                        });

                        $(".drop-api-btn").click(function() {
                            var button = $(this);
                            if (!confirm(
                                    "Are you sure you want to delete this API table? This action cannot be undone."))
                                return;
                            showLoading(button);

                            $.ajax({
                                url: "{{ route('core.drop_table') }}",
                                headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                },
                                type: "POST",
                                data: {
                                    table_name: button.data("table")
                                },
                                dataType: "json",
                                success: function(response) {
                                    alert(response.message);
                                    location.reload();
                                },
                                error: function() {
                                    alert("Failed to drop API table.");
                                },
                                complete: function() {
                                    hideLoading(button);
                                },
                            });
                        });

                        $("#apiDataModal").on("hidden.bs.modal", function() {
                            if ($.fn.DataTable.isDataTable("#apiDataTable")) {
                                $("#apiDataTable").DataTable().clear().destroy();
                            }
                            $("#apiTableHead, #apiTableBody").html("");
                        });
                    });
                </script>

            </div>
        </div>
    </div>
@endsection
