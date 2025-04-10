@extends('layouts/contentNavbarLayout')

@php
  $configData = Helper::appClasses();
  use Illuminate\Support\Str;
@endphp

@section('title', __('Security Analytics'))

@section('vendor-style')
@vite([
    'resources/assets/vendor/libs/apex-charts/apex-charts.scss',
    'resources/assets/vendor/libs/flatpickr/flatpickr.scss'
])
@endsection

@section('page-style')
@vite(['resources/assets/css/pages/security.css'])
@endsection

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">{{ __('Security') }} /</span> {{ __('Analytics') }}
    </h4>

    <!-- Date Range Filter -->
    <div class="card mb-4">
        <div class="card-body">
            <form id="analytics-filter" class="row g-3">
                <div class="col-12 col-md-4">
                    <label class="form-label">{{ __('Date Range') }}</label>
                    <select class="form-select" name="range" id="dateRange">
                        <option value="today" @selected(request('range') === 'today')>{{ __('Today') }}</option>
                        <option value="week" @selected(request('range') === 'week')>{{ __('Last 7 Days') }}</option>
                        <option value="month" @selected(request('range') === 'month')>{{ __('Last 30 Days') }}</option>
                        <option value="custom" @selected(request('range') === 'custom')>{{ __('Custom Range') }}</option>
                    </select>
                </div>
                
                <div class="col-12 col-md-3 custom-date @if(request('range') !== 'custom') d-none @endif">
                    <label class="form-label">{{ __('Start Date') }}</label>
                    <input type="text" class="form-control flatpickr" name="start_date" value="{{ request('start_date') }}">
                </div>

                <div class="col-12 col-md-3 custom-date @if(request('range') !== 'custom') d-none @endif">
                    <label class="form-label">{{ __('End Date') }}</label>
                    <input type="text" class="form-control flatpickr" name="end_date" value="{{ request('end_date') }}">
                </div>

                <div class="col-12 col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="ti ti-filter me-1"></i>
                        {{ __('Apply') }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="row">
        <!-- Security Score -->
        <div class="col-12 col-xl-4 mb-4">
            <div class="card h-100">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="card-title mb-0">{{ __('Security Score') }}</h5>
                    <button type="button" class="btn btn-icon btn-text-secondary rounded-pill btn-sm" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Based on your security events and response time') }}">
                        <i class="ti ti-info-circle"></i>
                    </button>
                </div>
                <div class="card-body d-flex flex-column align-items-center justify-content-center">
                    <div class="d-flex align-items-center gap-2">
                        <h2 class="mb-0">{{ $securityScore }}%</h2>
                        @if($scoreChange > 0)
                            <small class="text-success">
                                <i class="ti ti-trending-up"></i> +{{ $scoreChange }}%
                            </small>
                        @elseif($scoreChange < 0)
                            <small class="text-danger">
                                <i class="ti ti-trending-down"></i> {{ $scoreChange }}%
                            </small>
                        @endif
                    </div>
                    <div class="progress w-100 mt-3" style="height: 8px;">
                        <div class="progress-bar bg-primary" 
                             role="progressbar" 
                             style="width: {{ $securityScore }}%" 
                             aria-valuenow="{{ $securityScore }}" 
                             aria-valuemin="0" 
                             aria-valuemax="100"></div>
                    </div>
                    <small class="text-muted mt-2">{{ __('Last updated: :time', ['time' => now()->diffForHumans()]) }}</small>
                </div>
            </div>
        </div>

        <!-- Event Distribution -->
        <div class="col-12 col-xl-8 mb-4">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="card-title mb-0">{{ __('Event Distribution') }}</h5>
                </div>
                <div class="card-body">
                    <div id="eventDistributionChart" 
                         data-events="{{ json_encode($eventDistribution->pluck('count')) }}"
                         data-labels="{{ json_encode($eventDistribution->pluck('event_type')->map(fn($type) => __(Str::title(str_replace('_', ' ', $type))))) }}">
                    </div>
                </div>
            </div>
        </div>

        <!-- Security Events Timeline -->
        <div class="col-12 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">{{ __('Security Events Timeline') }}</h5>
                </div>
                <div class="card-body">
                    <div id="securityEventsChart" 
                         data-events="{{ json_encode($eventsTimeline->pluck('count')) }}"
                         data-dates="{{ json_encode($eventsTimeline->pluck('date')) }}">
                    </div>
                </div>
            </div>
        </div>

        <!-- Top Attacked Routes -->
        <div class="col-12 col-xl-6 mb-4">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="card-title mb-0">{{ __('Most Attacked Routes') }}</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-borderless">
                            <thead>
                                <tr>
                                    <th>{{ __('Route') }}</th>
                                    <th>{{ __('Attacks') }}</th>
                                    <th>{{ __('Last Attack') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($topAttackedRoutes as $route)
                                <tr>
                                    <td>
                                        <span class="text-truncate d-inline-block" style="max-width: 200px;" title="{{ $route->route }}">
                                            {{ $route->route }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="progress flex-grow-1" style="height: 6px;">
                                                <div class="progress-bar bg-danger" 
                                                     role="progressbar" 
                                                     style="width: {{ ($route->count / $topAttackedRoutes->max('count')) * 100 }}%" 
                                                     aria-valuenow="{{ $route->count }}" 
                                                     aria-valuemin="0" 
                                                     aria-valuemax="{{ $topAttackedRoutes->max('count') }}">
                                                </div>
                                            </div>
                                            <span>{{ $route->count }}</span>
                                        </div>
                                    </td>
                                    <td>{{ $route->last_attack->diffForHumans() }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Geographic Distribution -->
        <div class="col-12 col-xl-6 mb-4">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="card-title mb-0">{{ __('Geographic Distribution') }}</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-borderless">
                            <thead>
                                <tr>
                                    <th>{{ __('Country') }}</th>
                                    <th>{{ __('Events') }}</th>
                                    <th>{{ __('Blocked IPs') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($geoDistribution as $country)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="fi fi-{{ strtolower($country->country_code) }}"></span>
                                            {{ $country->country_name }}
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="progress flex-grow-1" style="height: 6px;">
                                                <div class="progress-bar bg-primary" 
                                                     role="progressbar" 
                                                     style="width: {{ ($country->events_count / $geoDistribution->max('events_count')) * 100 }}%" 
                                                     aria-valuenow="{{ $country->events_count }}" 
                                                     aria-valuemin="0" 
                                                     aria-valuemax="{{ $geoDistribution->max('events_count') }}">
                                                </div>
                                            </div>
                                            <span>{{ $country->events_count }}</span>
                                        </div>
                                    </td>
                                    <td>{{ $country->blocked_ips_count }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Response Time Analysis -->
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">{{ __('Response Time Analysis') }}</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-12 col-xl-3 mb-4 mb-xl-0">
                            <div class="d-flex flex-column gap-3">
                                <div class="stat-card">
                                    <div class="stat-title">{{ __('Average Response Time') }}</div>
                                    <div class="stat-value">{{ $avgResponseTime }}</div>
                                    <div class="stat-description text-muted">
                                        {{ __('Time to resolve security issues') }}
                                    </div>
                                </div>

                                <div class="stat-card">
                                    <div class="stat-title">{{ __('Resolution Rate') }}</div>
                                    <div class="stat-value">{{ $resolutionRate }}%</div>
                                    <div class="stat-description text-muted">
                                        {{ __('Issues resolved within SLA') }}
                                    </div>
                                </div>

                                <div class="stat-card">
                                    <div class="stat-title">{{ __('Pending Issues') }}</div>
                                    <div class="stat-value">{{ $pendingIssues }}</div>
                                    <div class="stat-description text-muted">
                                        {{ __('Requiring attention') }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 col-xl-9">
                            <div id="responseTimeChart" 
                                 data-times="{{ json_encode($responseTimeTrend->pluck('avg_time')) }}"
                                 data-dates="{{ json_encode($responseTimeTrend->pluck('date')) }}">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('vendor-script')
@vite([
    'resources/assets/vendor/libs/apex-charts/apexcharts.js',
    'resources/assets/vendor/libs/flatpickr/flatpickr.js'
])
@endsection

@section('page-script')
@vite(['resources/assets/js/pages/security.js'])

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle date range selection
    const dateRange = document.getElementById('dateRange');
    const customDateFields = document.querySelectorAll('.custom-date');
    
    dateRange.addEventListener('change', function() {
        if (this.value === 'custom') {
            customDateFields.forEach(field => field.classList.remove('d-none'));
        } else {
            customDateFields.forEach(field => field.classList.add('d-none'));
        }
    });
});
</script>
@endsection
