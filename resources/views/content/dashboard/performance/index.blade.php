@extends('layouts/contentNavbarLayout')

@section('title', __('System Performance'))

@section('vendor-style')
@vite([
  'resources/assets/vendor/libs/apex-charts/apex-charts.scss'
])
@endsection

@section('page-style')
@vite(['resources/assets/css/pages/performance.css'])
@endsection

@push('head')
<meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <!-- System Info -->
        <div class="col-12 mb-4">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <h5 class="card-title mb-0">{{ __('System Information') }}</h5>
                    <small id="last-updated">{{ __('Last Updated') }}: <span></span></small>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <p><strong>{{ __('OS') }}:</strong> <span id="system-info"></span></p>
                            <p><strong>{{ __('PHP') }}:</strong> <span id="php-version"></span></p>
                        </div>
                        <div class="col-md-3">
                            <p><strong>{{ __('Laravel') }}:</strong> <span id="laravel-version"></span></p>
                            <p><strong>{{ __('Server') }}:</strong> <span id="server-software"></span></p>
                        </div>
                        <div class="col-md-3">
                            <p><strong>{{ __('Database') }}:</strong> <span id="db-type"></span></p>
                            <p><strong>{{ __('DB Name') }}:</strong> <span id="db-name"></span></p>
                        </div>
                        <div class="col-md-3">
                            <p><strong>{{ __('DB Size') }}:</strong> <span id="db-size"></span></p>
                            <p><strong>{{ __('Uptime') }}:</strong> <span id="uptime"></span></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- CPU & Memory -->
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">{{ __('CPU Usage') }}</h5>
                </div>
                <div class="card-body">
                    <div id="cpuChart"></div>
                    <div class="mt-3">
                        <p><strong>{{ __('Cores') }}:</strong> <span id="cpu-cores"></span></p>
                        <p><strong>{{ __('Usage') }}:</strong> <span id="cpu-usage"></span></p>
                        <p><strong>{{ __('Load Average') }}:</strong> <span id="cpu-load"></span></p>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">{{ __('Memory Usage') }}</h5>
                </div>
                <div class="card-body">
                    <div id="memoryChart"></div>
                    <div class="mt-3">
                        <p><strong>{{ __('Total') }}:</strong> <span id="memory-total"></span></p>
                        <p><strong>{{ __('Used') }}:</strong> <span id="memory-used"></span></p>
                        <p><strong>{{ __('Free') }}:</strong> <span id="memory-free"></span></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Disk & Load -->
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">{{ __('Disk Usage') }}</h5>
                </div>
                <div class="card-body">
                    <div id="diskChart"></div>
                    <div class="mt-3">
                        <p><strong>{{ __('Total') }}:</strong> <span id="disk-total"></span></p>
                        <p><strong>{{ __('Used') }}:</strong> <span id="disk-used"></span></p>
                        <p><strong>{{ __('Free') }}:</strong> <span id="disk-free"></span></p>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">{{ __('Load Average') }}</h5>
                </div>
                <div class="card-body">
                    <div id="loadChart"></div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('vendor-script')
@vite([
  'resources/assets/vendor/libs/apex-charts/apexcharts.js',
  'resources/assets/vendor/libs/sweetalert2/sweetalert2.js'
])
@endsection

@section('page-script')
@vite(['resources/assets/js/pages/performance.js' ])
@endsection
