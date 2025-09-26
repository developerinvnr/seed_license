@extends('layouts.app')

@section('content')
<div class="page-content">
    <div class="container-fluid">
        <!-- Page Title -->
        <div class="row mb-3">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between bg-galaxy-transparent">
                    <h4 class="mb-sm-0">{{ ucwords(str_replace('-', ' ', Request::path())) }}</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript:void(0);">Masters</a></li>
                            <li class="breadcrumb-item active">{{ ucwords(str_replace('-', ' ', Request::path())) }}</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <!-- Activity Log Section -->
        <div class="row justify-content-center">
            <div class="col-lg-12">
                <div class="card shadow-sm">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">License Activity Log</h5>
                        <a href="{{ route('license-list') }}" class="btn btn-outline-secondary">
                            <i class="ri-arrow-left-line"></i> Back to License List
                        </a>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered align-middle table-hover">
                                <thead class="table-text-center">
                                    <tr>
                                        <th>#</th>
                                        <th>Event</th>
                                        <th>Description</th>
                                        <th>Performed By</th>
                                        <th>License ID</th>
                                        <th>Properties</th>
                                        <th>Timestamp</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($activities as $index => $activity)
                                        <tr>
                                            <td class="text-center">{{ $activities->firstItem() + $index }}</td>
                                            <td>{{ $activity->event ?? 'N/A' }}</td>
                                            <td>{{ $activity->description }}</td>
                                            <td>
                                                @if ($activity->causer)
                                                    {{ $activity->causer->name ?? 'Unknown User' }}
                                                    <br><small class="text-muted">(ID: {{ $activity->causer_id }})</small>
                                                @else
                                                    <span class="badge bg-warning">System</span>
                                                @endif
                                            </td>
                                            <td class="text-center">{{ $activity->subject_id ?? 'N/A' }}</td>
                                            <td>
                                                @if ($activity->properties->isNotEmpty())
                                                    <div class="accordion" id="propertiesAccordion{{ $index }}">
                                                        <div class="accordion-item">
                                                            <h2 class="accordion-header" id="heading{{ $index }}">
                                                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse{{ $index }}" aria-expanded="false" aria-controls="collapse{{ $index }}">
                                                                    View Properties
                                                                </button>
                                                            </h2>
                                                            <div id="collapse{{ $index }}" class="accordion-collapse collapse" aria-labelledby="heading{{ $index }}" data-bs-parent="#propertiesAccordion{{ $index }}">
                                                                <div class="accordion-body bg-light">
                                                                    <pre class="mb-0">{{ json_encode($activity->properties->toArray(), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @else
                                                    <span class="text-muted">No additional properties</span>
                                                @endif
                                            </td>
                                            <td>{{ $activity->created_at->format('d M Y, h:i A') }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center text-muted">No activities found.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="d-flex justify-content-center mt-4">
                            {{ $activities->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div> <!-- container-fluid -->
</div> <!-- page-content -->
@endsection
