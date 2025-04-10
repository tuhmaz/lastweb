@extends('layouts/contentNavbarLayout')
@php
  $configData = Helper::appClasses();
  use Illuminate\Support\Str;

@endphp

@section('title', __('Security Logs'))

@section('vendor-style')
@vite([
    'resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss',
    'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss',
    'resources/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.scss',
    'resources/assets/vendor/libs/flatpickr/flatpickr.scss'
])
@endsection

@section('page-style')
@vite(['resources/assets/css/pages/security.css'])
@endsection

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">{{ __('Security') }} /</span> {{ __('Logs') }}
    </h4>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="card-title mb-0">{{ __('Filters') }}</h5>
        </div>
        <div class="card-body">
            <form id="security-filters" class="row g-3">
                <div class="col-12 col-md-3">
                    <label class="form-label">{{ __('Event Type') }}</label>
                    <select class="form-select" name="event_type">
                        <option value="">{{ __('All Types') }}</option>
                        @foreach($eventTypes as $key => $type)
                            <option value="{{ $type }}" @selected(request('event_type') === $type)>
                                {{ __(Str::title(str_replace('_', ' ', $type))) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-12 col-md-3">
                    <label class="form-label">{{ __('Severity') }}</label>
                    <select class="form-select" name="severity">
                        <option value="">{{ __('All Severities') }}</option>
                        @foreach($severityLevels as $key => $level)
                            <option value="{{ $level }}" @selected(request('severity') === $level)>
                                {{ __(Str::upper($level)) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-12 col-md-3">
                    <label class="form-label">{{ __('IP Address') }}</label>
                    <input type="text" class="form-control" name="ip" value="{{ request('ip') }}" placeholder="{{ __('Search IP...') }}">
                </div>

                <div class="col-12 col-md-3">
                    <label class="form-label">{{ __('Status') }}</label>
                    <select class="form-select" name="is_resolved">
                        <option value="">{{ __('All Status') }}</option>
                        <option value="true" @selected(request('is_resolved') === 'true')>{{ __('Resolved') }}</option>
                        <option value="false" @selected(request('is_resolved') === 'false')>{{ __('Pending') }}</option>
                    </select>
                </div>

                <div class="col-12 col-md-3">
                    <label class="form-label">{{ __('Date From') }}</label>
                    <input type="text" class="form-control flatpickr" name="date_from" value="{{ request('date_from') }}" placeholder="YYYY-MM-DD">
                </div>

                <div class="col-12 col-md-3">
                    <label class="form-label">{{ __('Date To') }}</label>
                    <input type="text" class="form-control flatpickr" name="date_to" value="{{ request('date_to') }}" placeholder="YYYY-MM-DD">
                </div>

                <div class="col-12 col-md-6 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="ti ti-search me-1"></i>
                        {{ __('Filter') }}
                    </button>
                    <a href="{{ route('dashboard.security.logs') }}" class="btn btn-secondary">
                        <i class="ti ti-refresh me-1"></i>
                        {{ __('Reset') }}
                    </a>
                    <a href="{{ route('dashboard.security.export') }}" class="btn btn-outline-primary ms-auto">
                        <i class="ti ti-file-export me-1"></i>
                        {{ __('Export') }}
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Logs Table -->
    <div class="card">
        <div class="card-datatable table-responsive">
            <table class="table security-table border-top" id="security-logs-table">
                <thead>
                    <tr>
                        <th>{{ __('Time') }}</th>
                        <th>{{ __('Event') }}</th>
                        <th>{{ __('IP Address') }}</th>
                        <th>{{ __('Description') }}</th>
                        <th>{{ __('User') }}</th>
                        <th>{{ __('Severity') }}</th>
                        <th>{{ __('Status') }}</th>
                        <th>{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($logs as $log)
                    <tr>
                        <td>
                            <div class="text-body">{{ $log->created_at->format('Y-m-d H:i:s') }}</div>
                            <small class="text-muted">{{ $log->created_at->diffForHumans() }}</small>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <span class="badge rounded-pill me-2" style="background-color: var(--bs-{{ $log->event_type_color }})">
                                    <i class="ti ti-{{ $log->event_type === 'login_failed' ? 'x' : 'check' }}"></i>
                                </span>
                                {{ __(Str::title(str_replace('_', ' ', $log->event_type))) }}
                            </div>
                        </td>
                        <td>
                            <div class="d-flex flex-column">
                                <span class="fw-semibold">{{ $log->ip_address }}</span>
                                @if($log->country_code)
                                <small class="text-muted">
                                    {{ $log->country_code }} - {{ $log->city }}
                                </small>
                                @endif
                            </div>
                        </td>
                        <td>
                            <span class="text-truncate d-inline-block" style="max-width: 200px;" title="{{ $log->description }}">
                                {{ $log->description }}
                            </span>
                        </td>
                        <td>
                            @if($log->user)
                                <div class="d-flex justify-content-start align-items-center user-name">
                                    <div class="avatar-wrapper">
                                        <div class="avatar avatar-sm me-2">
                                            @if($log->user->profile_photo_path)
                                                <img src="{{ $log->user->profile_photo_url }}" alt="Avatar" class="rounded-circle">
                                            @else
                                                <span class="avatar-initial rounded-circle bg-label-{{ ['primary', 'success', 'danger', 'warning', 'info'][array_rand(['primary', 'success', 'danger', 'warning', 'info'])] }}">
                                                    {{ strtoupper(substr($log->user->name, 0, 2)) }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="d-flex flex-column">
                                        <span class="fw-semibold">{{ $log->user->name }}</span>
                                        <small class="text-muted">{{ $log->user->email }}</small>
                                    </div>
                                </div>
                            @else
                                <span class="text-muted">{{ __('System') }}</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge badge-{{ $log->severity }}">
                                {{ __(Str::upper($log->severity)) }}
                            </span>
                        </td>
                        <td>
                            @if($log->is_resolved)
                                <span class="badge bg-success bg-opacity-10 text-success">
                                    {{ __('Resolved') }}
                                </span>
                            @else
                                <span class="badge bg-warning bg-opacity-10 text-warning">
                                    {{ __('Pending') }}
                                </span>
                            @endif
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-1">
                                @if(!$log->is_resolved)
                                    <button type="button"
                                            class="btn btn-sm btn-icon btn-text-secondary rounded-pill btn-icon dropdown-toggle hide-arrow"
                                            data-bs-toggle="dropdown">
                                        <i class="ti ti-dots-vertical"></i>
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-end">
                                        <button class="dropdown-item"
                                                data-action="resolve-log"
                                                data-log-id="{{ $log->id }}">
                                            <i class="ti ti-check me-2"></i>
                                            {{ __('Mark as Resolved') }}
                                        </button>
                                        <button class="dropdown-item"
                                                data-action="block-ip"
                                                data-ip="{{ $log->ip_address }}">
                                            <i class="ti ti-ban me-2"></i>
                                            {{ __('Block IP') }}
                                        </button>
                                        <div class="dropdown-divider"></div>
                                        <form action="{{ route('dashboard.security.logs.destroy', $log) }}"
                                              method="POST"
                                              class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="dropdown-item text-danger"
                                                    onclick="return confirm('{{ __('Are you sure you want to delete this log?') }}')">
                                                <i class="ti ti-trash me-2"></i>
                                                {{ __('Delete Log') }}
                                            </button>
                                        </form>
                                    </div>
                                @else
                                    <button type="button"
                                            class="btn btn-sm btn-icon btn-text-secondary rounded-pill"
                                            data-bs-toggle="tooltip"
                                            title="{{ __('Resolved by :user at :time', ['user' => $log->resolvedByUser?->name ?? 'System', 'time' => $log->resolved_at?->format('Y-m-d H:i:s')]) }}">
                                        <i class="ti ti-info-circle"></i>
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($logs->hasPages())
        <div class="card-footer">
            <div class="d-flex justify-content-center">
                {{ $logs->links() }}
            </div>
        </div>
        @endif
    </div>
</div>
@endsection

@section('vendor-script')
@vite([
    'resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js',
    'resources/assets/vendor/libs/flatpickr/flatpickr.js'
])
@endsection

@section('page-script')
@vite(['resources/assets/js/pages/security.js'])
@endsection
