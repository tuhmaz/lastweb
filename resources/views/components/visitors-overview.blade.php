<div class="card shadow-sm h-100 overflow-hidden">
    <div class="card-header bg-light border-bottom">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0 text-primary fw-semibold">
                <i class="ti ti-chart-bar me-2"></i>{{ __('Visitors Overview') }}
            </h5>
            <div class="dropdown">
                <button class="btn btn-light btn-sm" type="button" id="visitors-overview-dropdown" data-bs-toggle="dropdown">
                    <i class="ti ti-calendar me-1"></i>{{ __('Filter') }}
                </button>
                <div class="dropdown-menu dropdown-menu-end shadow-sm">
                    <a class="dropdown-item" href="javascript:void(0);">
                        <i class="ti ti-clock me-2"></i>{{ __('Last 24 Hours') }}
                    </a>
                    <a class="dropdown-item" href="javascript:void(0);">
                        <i class="ti ti-calendar-week me-2"></i>{{ __('Last 7 Days') }}
                    </a>
                    <a class="dropdown-item" href="javascript:void(0);">
                        <i class="ti ti-calendar-month me-2"></i>{{ __('Last 30 Days') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
    <div class="card-body p-4">
        <div class="row g-4">
            <div class="col-lg-6 col-12">
                <div id="visitor-map" class="rounded border" style="height: 320px;"></div>
            </div>
            <div class="col-lg-6 col-12">
                <div id="visitor-chart" class="h-100"></div>
            </div>
        </div>
    </div>
</div>