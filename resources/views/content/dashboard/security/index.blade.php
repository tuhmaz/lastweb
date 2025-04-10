@extends('layouts/contentNavbarLayout')

@section('title', __('Security Dashboard'))

@section('vendor-style')
@vite([
    'resources/assets/vendor/libs/apex-charts/apex-charts.scss'
])
@endsection

@section('page-style')
@vite(['resources/assets/css/pages/security.css'])
@endsection

@section('vendor-script')
@vite([
    'resources/assets/vendor/libs/apex-charts/apexcharts.js'
])
@endsection

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">{{ __('Security') }} /</span> {{ __('Dashboard') }}
    </h4>

    <!-- Stats Cards -->
    <div class="security-stats">
        <div class="stat-card">
            <div class="d-flex align-items-center">
                <div class="stat-icon bg-primary bg-opacity-10 text-primary">
                    <i class="ti ti-shield"></i>
                </div>
                <div>
                    <div class="stat-value">{{ $stats['total_events'] }}</div>
                    <div class="stat-label">{{ __('Total Events') }}</div>
                </div>
            </div>
        </div>

        <div class="stat-card">
            <div class="d-flex align-items-center">
                <div class="stat-icon bg-danger bg-opacity-10 text-danger">
                    <i class="ti ti-alert-triangle"></i>
                </div>
                <div>
                    <div class="stat-value">{{ $stats['critical_events'] }}</div>
                    <div class="stat-label">{{ __('Critical Events') }}</div>
                </div>
            </div>
        </div>

        <div class="stat-card">
            <div class="d-flex align-items-center">
                <div class="stat-icon bg-warning bg-opacity-10 text-warning">
                    <i class="ti ti-clock"></i>
                </div>
                <div>
                    <div class="stat-value">{{ $stats['unresolved_issues'] }}</div>
                    <div class="stat-label">{{ __('Unresolved Issues') }}</div>
                </div>
            </div>
        </div>

        <div class="stat-card">
            <div class="d-flex align-items-center">
                <div class="stat-icon bg-info bg-opacity-10 text-info">
                    <i class="ti ti-ban"></i>
                </div>
                <div>
                    <div class="stat-value">{{ $stats['blocked_ips'] }}</div>
                    <div class="stat-label">{{ __('Blocked IPs') }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Recent Events -->
        <div class="col-12 col-lg-8 order-2 order-lg-1">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">{{ __('Recent Security Events') }}</h5>
                    <a href="{{ route('dashboard.security.logs') }}" class="btn btn-primary btn-sm">
                        {{ __('View All') }}
                    </a>
                </div>
                <div class="table-responsive">
                    <table class="table security-table">
                        <thead>
                            <tr>
                                <th>{{ __('Time') }}</th>
                                <th>{{ __('Event') }}</th>
                                <th>{{ __('IP') }}</th>
                                <th>{{ __('Severity') }}</th>
                                <th>{{ __('Status') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentLogs as $log)
                            <tr>
                                <td>{{ $log->created_at->diffForHumans() }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <span class="badge rounded-pill me-2" style="background-color: var(--bs-{{ $log->event_type_color }})">
                                            <i class="ti ti-{{ $log->event_type === 'login_failed' ? 'x' : 'check' }}"></i>
                                        </span>
                                        {{ __(Str::title(str_replace('_', ' ', $log->event_type))) }}
                                    </div>
                                </td>
                                <td>
                                    <span class="ip-address">{{ $log->ip_address }}</span>
                                    @if($log->country_code)
                                    <small class="text-muted d-block">{{ $log->country_code }} - {{ $log->city }}</small>
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
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="col-12 col-lg-4 order-1 order-lg-2">
            <div class="row">
                <div class="col-12">
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0">{{ __('Quick Actions') }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="list-group">
                                <a href="{{ route('dashboard.security.logs') }}" class="list-group-item list-group-item-action">
                                    <div class="d-flex align-items-center">
                                        <i class="ti ti-list me-2"></i>
                                        {{ __('View All Logs') }}
                                    </div>
                                </a>
                                <a href="{{ route('dashboard.security.analytics') }}" class="list-group-item list-group-item-action">
                                    <div class="d-flex align-items-center">
                                        <i class="ti ti-chart-bar me-2"></i>
                                        {{ __('Security Analytics') }}
                                    </div>
                                </a>
                                <a href="{{ route('dashboard.security.blocked-ips') }}" class="list-group-item list-group-item-action">
                                    <div class="d-flex align-items-center">
                                        <i class="ti ti-ban me-2"></i>
                                        {{ __('Manage Blocked IPs') }}
                                    </div>
                                </a>
                                <a href="{{ route('dashboard.security.trusted-ips') }}" class="list-group-item list-group-item-action">
                                    <div class="d-flex align-items-center">
                                        <i class="ti ti-shield-check me-2"></i>
                                        {{ __('Manage Trusted IPs') }}
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Most Attacked Routes -->
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">{{ __('Most Attacked Routes') }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="list-group list-group-flush">
                                @foreach($stats['top_attacked_routes'] as $route)
                                <div class="list-group-item px-0">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="text-truncate">
                                            <small class="d-block text-muted">{{ $route->route }}</small>
                                        </div>
                                        <span class="badge bg-danger rounded-pill">{{ $route->count }}</span>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('page-script')
@vite(['resources/assets/js/pages/security.js'])
@endsection
