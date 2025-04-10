@extends('layouts/contentNavbarLayout')
@php
  $configData = Helper::appClasses();
  use Illuminate\Support\Str;
@endphp

@section('title', __('Blocked IP Addresses'))

@section('vendor-style')
@vite([
    'resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss',
    'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss',
    'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss',
])
@endsection

@section('page-style')
@vite([
    'resources/assets/css/pages/security.css',
])
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <!-- Stats -->
        <div class="row mb-4">
            <div class="col-lg-3 col-sm-6">
                <div class="card">
                    <div class="card-body d-flex align-items-center">
                        <div class="badge bg-label-danger me-3 rounded p-2">
                            <i class="bx bx-shield-x fs-3"></i>
                        </div>
                        <div>
                            <h5 class="mb-0">{{ $totalBlocked }}</h5>
                            <small>{{ __('Blocked IPs') }}</small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-sm-6">
                <div class="card">
                    <div class="card-body d-flex align-items-center">
                        <div class="badge bg-label-warning me-3 rounded p-2">
                            <i class="bx bx-error-circle fs-3"></i>
                        </div>
                        <div>
                            <h5 class="mb-0">{{ $highRiskCount }}</h5>
                            <small>{{ __('High Risk') }}</small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-sm-6">
                <div class="card">
                    <div class="card-body d-flex align-items-center">
                        <div class="badge bg-label-info me-3 rounded p-2">
                            <i class="bx bx-time fs-3"></i>
                        </div>
                        <div>
                            <h5 class="mb-0">{{ $recentlyBlocked }}</h5>
                            <small>{{ __('Recently Blocked') }}</small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-sm-6">
                <div class="card">
                    <div class="card-body d-flex align-items-center">
                        <div class="badge bg-label-success me-3 rounded p-2">
                            <i class="bx bx-check-shield fs-3"></i>
                        </div>
                        <div>
                            <h5 class="mb-0">{{ $avgRiskScore }}</h5>
                            <small>{{ __('Avg Risk Score') }}</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Blocked IPs List -->
        <div class="card">
            <div class="card-header border-bottom">
                <h5 class="card-title mb-3">{{ __('Blocked IP Addresses') }}</h5>
                <div class="d-flex justify-content-between align-items-center row pb-2 gap-3 gap-md-0">
                    <div class="col-md-4 user_role"></div>
                    <div class="col-md-4 user_plan"></div>
                    <div class="col-md-4 user_status"></div>
                </div>
                <div class="dt-action-buttons text-end pt-3 pt-md-0">
                    <div class="dt-buttons">
                        <button type="button" class="dt-button create-new btn btn-primary" data-bs-toggle="modal" data-bs-target="#blockIpModal">
                            <span>
                                <i class="bx bx-plus me-sm-1"></i>
                                <span class="d-none d-sm-inline-block">{{ __('Block New IP') }}</span>
                            </span>
                        </button>
                    </div>
                </div>
            </div>
            <div class="card-datatable table-responsive">
                <table class="datatables-blocked-ips table border-top" id="blockedIpsTable">
                    <thead>
                        <tr>
                            <th>{{ __('IP Address') }}</th>
                            <th>{{ __('Location') }}</th>
                            <th>{{ __('Last Attempt') }}</th>
                            <th>{{ __('Attempts') }}</th>
                            <th>{{ __('Risk Score') }}</th>
                            <th>{{ __('Attack Type') }}</th>
                            <th>{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                </table>
            </div>

            <!-- Pagination -->
            @if($blockedLogs->hasPages())
            <div class="card-footer">
                <div class="d-flex justify-content-center">
                    {{ $blockedLogs->links() }}
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Block IP Modal -->
<div class="modal fade" id="blockIpModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('Block IP Address') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="blockIpForm" onsubmit="return blockIp(event)">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12 mb-3">
                            <label for="ip_address" class="form-label">{{ __('IP Address') }}</label>
                            <input type="text"
                                   class="form-control"
                                   id="ip_address"
                                   name="ip_address"
                                   required
                                   pattern="^(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$"
                                   placeholder="xxx.xxx.xxx.xxx">
                        </div>
                        <div class="col-12 mb-3">
                            <label for="reason" class="form-label">{{ __('Reason') }}</label>
                            <textarea class="form-control"
                                      id="reason"
                                      name="reason"
                                      required
                                      rows="3"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-primary">{{ __('Block IP') }}</button>
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
    'resources/assets/js/pages/security/blocked-ips.js'
])
<script>
// Translations object for JavaScript
window.translations = {
    unknown: "{{ __('Unknown') }}",
    view_details: "{{ __('View Details') }}",
    unblock_ip: "{{ __('Unblock IP') }}",
    error: "{{ __('Error') }}",
    failed_to_load: "{{ __('Failed to load IP details') }}",
    ok: "{{ __('OK') }}",
    confirm_unblock: "{{ __('Confirm Unblock') }}",
    unblock_confirmation: "{{ __('Are you sure you want to unblock this IP?') }}",
    yes_unblock: "{{ __('Yes, unblock it') }}",
    cancel: "{{ __('Cancel') }}",
    success: "{{ __('Success') }}",
    ip_unblocked: "{{ __('IP has been unblocked') }}",
    failed_to_unblock: "{{ __('Failed to unblock IP') }}"
};
</script>
@endsection
