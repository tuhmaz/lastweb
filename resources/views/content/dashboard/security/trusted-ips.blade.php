@extends('layouts/contentNavbarLayout')
@php
  $configData = Helper::appClasses();
  use Illuminate\Support\Str;

@endphp

@section('title', __('Trusted IP Addresses'))

@section('vendor-style')
@vite([
    'resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss',
    'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss',
    'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss'
])
@endsection

@section('page-style')
@vite([
    'resources/assets/css/pages/security.css'
])
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <!-- Stats -->
        <div class="row security-stats mb-3">
            <div class="col-md-6">
                <div class="stat-card">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon bg-success bg-opacity-10 text-success">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                        <div>
                            <h3 class="stat-value">{{ $totalTrusted }}</h3>
                            <p class="stat-label mb-0">{{ __('Total Trusted IPs') }}</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="stat-card">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon bg-info bg-opacity-10 text-info">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div>
                            <h3 class="stat-value">{{ $trustedLogs->lastItem() ?? 0 }}</h3>
                            <p class="stat-label mb-0">{{ __('Recent Additions') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Trusted IPs List -->
        <div class="card">
            <div class="card-header border-bottom">
                <h4 class="card-title">{{ __('Trusted IP Addresses') }}</h4>
                <div class="card-header-actions">
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#trustIpModal">
                        <i class="fas fa-plus me-1"></i>{{ __('Add Trusted IP') }}
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="card-datatable table-responsive">
                    <table class="datatables-trusted-ips table border-top" id="trustedIpsTable">
                        <thead>
                            <tr>
                                <th>{{ __('IP Address') }}</th>
                                <th>{{ __('Location') }}</th>
                                <th>{{ __('Added Date') }}</th>
                                <th>{{ __('Added By') }}</th>
                                <th>{{ __('Notes') }}</th>
                                <th>{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($trustedLogs as $log)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <i class="bx bx-shield-check text-success me-2"></i>
                                        <span>{{ $log->ip_address }}</span>
                                    </div>
                                </td>
                                <td>
                                    @if($log->country_code)
                                        <img src="{{ asset('assets/img/flags/' . strtolower($log->country_code) . '.png') }}"
                                             alt="{{ $log->country_code }}"
                                             class="me-1"
                                             style="width: 20px;">
                                        {{ $log->city ? $log->city . ', ' : '' }}{{ $log->country_code }}
                                    @else
                                        {{ __('Unknown') }}
                                    @endif
                                </td>
                                <td>
                                    <div data-bs-toggle="tooltip" title="{{ $log->created_at }}">
                                        {{ $log->created_at->diffForHumans() }}
                                    </div>
                                </td>
                                <td>{{ $log->user ? $log->user->name : '-' }}</td>
                                <td>{{ $log->notes ?: '-' }}</td>
                                <td>
                                    <div class="d-inline-block">
                                        <button type="button"
                                                class="btn btn-sm btn-icon"
                                                data-bs-toggle="tooltip"
                                                title="{{ __('View Details') }}"
                                                onclick="viewIpDetails('{{ $log->ip_address }}')">
                                            <i class="bx bx-show text-primary"></i>
                                        </button>
                                        <button type="button"
                                                class="btn btn-sm btn-icon delete-record"
                                                data-bs-toggle="tooltip"
                                                title="{{ __('Untrust IP') }}"
                                                onclick="untrustIp('{{ $log->ip_address }}')">
                                            <i class="bx bx-shield-x text-danger"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-center mt-3">
                    {{ $trustedLogs->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Trust IP Modal -->
<div class="modal fade" id="trustIpModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('Add Trusted IP Address') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="trustIpForm" onsubmit="return trustIp(event)">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="ip_address" class="form-label">{{ __('IP Address') }}</label>
                        <input type="text"
                               class="form-control"
                               id="ip_address"
                               name="ip_address"
                               required
                               pattern="^(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$"
                               placeholder="xxx.xxx.xxx.xxx">
                    </div>
                    <div class="mb-3">
                        <label for="reason" class="form-label">{{ __('Reason') }}</label>
                        <textarea class="form-control"
                                  id="reason"
                                  name="reason"
                                  required
                                  rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-primary">{{ __('Add to Trusted') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- IP Details Modal -->
<div class="modal fade" id="ipDetailsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('IP Details') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="ipDetailsContent">
                <!-- Content will be loaded dynamically -->
            </div>
        </div>
    </div>
</div>
@endsection

@section('vendor-script')
@vite([
    'resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js',
    'resources/assets/vendor/libs/sweetalert2/sweetalert2.js'
])
@endsection

@section('page-script')
@vite([
    'resources/assets/js/pages/security/trusted-ips.js'
])
<script>
// Translations object for JavaScript
window.translations = {
    unknown: "{{ __('Unknown') }}",
    view_details: "{{ __('View Details') }}",
    untrust_ip: "{{ __('Untrust IP') }}",
    error: "{{ __('Error') }}",
    failed_to_load: "{{ __('Failed to load IP details') }}",
    ok: "{{ __('OK') }}",
    confirm_untrust: "{{ __('Confirm Untrust') }}",
    untrust_confirmation: "{{ __('Are you sure you want to remove this IP from trusted list?') }}",
    yes_untrust: "{{ __('Yes, untrust it') }}",
    cancel: "{{ __('Cancel') }}",
    success: "{{ __('Success') }}",
    ip_untrusted: "{{ __('IP has been removed from trusted list') }}",
    failed_to_untrust: "{{ __('Failed to untrust IP') }}",
    search: "{{ __('Search') }}",
    show_entries: "{{ __('Show _MENU_ entries') }}",
    showing_entries: "{{ __('Showing _START_ to _END_ of _TOTAL_ entries') }}",
    showing_zero: "{{ __('Showing 0 to 0 of 0 entries') }}",
    filtered: "{{ __('(filtered from _MAX_ total entries)') }}",
    no_trusted_ips: "{{ __('No trusted IPs found') }}",
    first: "{{ __('First') }}",
    last: "{{ __('Last') }}",
    next: "{{ __('Next') }}",
    previous: "{{ __('Previous') }}"
};

// Pass data to DataTables
window.trustedLogs = @json($trustedLogs->items());
</script>
@endsection
