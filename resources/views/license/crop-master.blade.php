@extends('layouts.app')
@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header align-items-center d-flex">
                            <h4 class="card-title mb-0 flex-grow-1"><i class="ri-list-unordered"></i> Crop Master List</h4>
                        </div>
                        <div class="card-body p-4">
                            @if (session('success'))
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    {{ session('success') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            @endif
                            @if ($errors->any())
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    @foreach ($errors->all() as $error)
                                        {{ $error }}<br>
                                    @endforeach
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            @endif
                            <div class="table-responsive">
                                <table id="cropTable" class="table nowrap align-middle table-hover table-bordered" style="width:100%;">
                                    <thead>
                                        <tr>
                                            <th>Sno</th>
                                            <th>Crop Name</th>
                                            <th>Crop Vertical</th>
                                            <th>Crop Variety Name</th>
                                            <th>Crop Category</th>
                                            <th>Notified / Non-notified</th>
                                            <th>Notification Document Upload</th>
                                            <th>PPVFRA Status</th>
                                            <th>PPVFRA Certificate</th>
                                            <th>PPVFRA Certificate</th>
                                            <th>PPVFRA Certificate</th>
                                            <th>Date of Release</th>
                                            <th>Date of Crop Listing</th>
                                            <th>Date of Crop Deletion</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($crops as $key => $crop)
                                            <tr>
                                                <td>{{ $key + 1 }}</td>
                                                <td>{{ $crop->crop_name }}</td>
                                                <td>{{ $crop->vertical->vertical_name ?? 'Unknown Vertical' }}</td>
                                                <td>
                                                    @if ($crop->varieties->isNotEmpty())
                                                        {{ $crop->varieties->first()->variety_name ?? 'N/A' }}
                                                    @else
                                                        N/A
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($crop->varieties->isNotEmpty() && $crop->varieties->first()->category)
                                                        {{ $crop->varieties->first()->category->category_name ?? 'N/A' }}
                                                    @else
                                                        N/A
                                                    @endif
                                                </td>
                                                <td>N/A</td>
                                                <td>N/A</td>
                                                <td>N/A</td>
                                                <td>N/A</td>
                                                <td>N/A</td>
                                                <td>N/A</td>
                                                <td>N/A</td>
                                                <td>N/A</td>
                                                <td>N/A</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('custom-js')
    <script>
        $(document).ready(function() {
            $('#cropTable').DataTable({
                // Removed responsive: true to avoid + icon
                pageLength: 10,
                order: [[0, 'asc']],
                language: {
                    search: "Filter records:",
                    searchPlaceholder: "Search..."
                }
            }).on('error', function(e, settings, techNote, message) {
                console.log('DataTables Error: ', message);
            });
        });
    </script>
@endpush