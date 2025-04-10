@extends('layouts/contentNavbarLayout')

@section('title', __('Performance Metrics'))

@section('vendor-style')
@vite([
    'resources/assets/vendor/libs/apex-charts/apex-charts.scss',
])
@endsection

@section('vendor-script')
@vite([
    'resources/assets/vendor/libs/apex-charts/apexcharts.js',
])
@endsection

@section('page-script')
@vite(['resources/assets/js/pages/performance-metrics.js'])
@endsection

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

<div class="row">
    <div class="col-12">
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">{{ __('System Performance Metrics') }}</h5>
                <div class="dropdown">
                    <button class="btn btn-secondary dropdown-toggle" type="button" id="timeRangeDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        {{ __('Last 24 Hours') }}
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="timeRangeDropdown">
                        <li><a class="dropdown-item" href="#" data-range="24h">{{ __('Last 24 Hours') }}</a></li>
                        <li><a class="dropdown-item" href="#" data-range="7d">{{ __('Last 7 Days') }}</a></li>
                        <li><a class="dropdown-item" href="#" data-range="30d">{{ __('Last 30 Days') }}</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- CPU Usage -->
    <div class="col-md-6 col-12 mb-4">
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="card-title mb-0">{{ __('CPU Usage') }}</h5>
                <small class="text-muted">{{ __('Real-time') }}</small>
            </div>
            <div class="card-body">
                <div id="cpuUsageChart"></div>
            </div>
        </div>
    </div>

    <!-- Memory Usage -->
    <div class="col-md-6 col-12 mb-4">
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="card-title mb-0">{{ __('Memory Usage') }}</h5>
                <small class="text-muted">{{ __('Real-time') }}</small>
            </div>
            <div class="card-body">
                <div id="memoryUsageChart"></div>
            </div>
        </div>
    </div>

    <!-- Response Time -->
    <div class="col-md-6 col-12 mb-4">
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="card-title mb-0">{{ __('Average Response Time') }}</h5>
                <small class="text-muted">{{ __('milliseconds') }}</small>
            </div>
            <div class="card-body">
                <div id="responseTimeChart"></div>
            </div>
        </div>
    </div>

    <!-- Request Rate -->
    <div class="col-md-6 col-12 mb-4">
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="card-title mb-0">{{ __('Request Rate') }}</h5>
                <small class="text-muted">{{ __('requests/minute') }}</small>
            </div>
            <div class="card-body">
                <div id="requestRateChart"></div>
            </div>
        </div>
    </div>

    <!-- Error Rate -->
    <div class="col-md-12 mb-4">
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="card-title mb-0">{{ __('Error Rate') }}</h5>
                <small class="text-muted">{{ __('percentage') }}</small>
            </div>
            <div class="card-body">
                <div id="errorRateChart"></div>
            </div>
        </div>
    </div>

    <!-- Performance Stats -->
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">{{ __('Performance Statistics') }}</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>{{ __('Metric') }}</th>
                                <th>{{ __('Current') }}</th>
                                <th>{{ __('Average') }}</th>
                                <th>{{ __('Peak') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>{{ __('CPU Usage') }}</td>
                                <td id="currentCpu">-</td>
                                <td id="avgCpu">-</td>
                                <td id="peakCpu">-</td>
                            </tr>
                            <tr>
                                <td>{{ __('Memory Usage') }}</td>
                                <td id="currentMemory">-</td>
                                <td id="avgMemory">-</td>
                                <td id="peakMemory">-</td>
                            </tr>
                            <tr>
                                <td>{{ __('Response Time') }}</td>
                                <td id="currentResponse">-</td>
                                <td id="avgResponse">-</td>
                                <td id="peakResponse">-</td>
                            </tr>
                            <tr>
                                <td>{{ __('Request Rate') }}</td>
                                <td id="currentRequests">-</td>
                                <td id="avgRequests">-</td>
                                <td id="peakRequests">-</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
