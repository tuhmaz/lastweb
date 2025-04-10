@extends('layouts/contentNavbarLayout')

@section('title', __('System Monitoring'))

@section('vendor-style')
@vite([
  'resources/assets/vendor/libs/apex-charts/apex-charts.scss',
  'resources/assets/vendor/libs/leaflet/leaflet.scss'
])
@endsection

@section('vendor-script')
@vite([
  'resources/assets/vendor/libs/apex-charts/apexcharts.js',
  'resources/assets/vendor/libs/leaflet/leaflet.js',
  'resources/assets/vendor/libs/jquery/jquery.js',
  'resources/assets/js/pages/monitoring.js'
])
@endsection

@section('content')
<div class="container-fluid">
    <div class="row g-4">
        <!-- Visitors Overview -->
        <div class="col-12">
            @include('components.visitors-overview')
        </div>

        <!-- Request Stats -->
        <div class="col-12">
            @include('components.request-stats')
        </div>
        <!-- Active Users -->
<div class="col-12">
    @include('components.active-users')
</div>

<!-- Error Log -->
<div class="col-12">
    @include('components.error-log')
</div>

       
    </div>
</div>
@endsection

 