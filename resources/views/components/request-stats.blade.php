<div class="row g-4">
    <!-- العمود الأول -->
    <div class="col-12 col-md-6">
        <div class="card h-100">
            <div class="card-header border-bottom">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <span class="text-primary">
                            <i class="ti ti-chart-bar me-2"></i>
                        </span>
                        إحصائيات الطلبات
                    </h5>
                    <div class="dropdown">
                        <button class="btn p-0" type="button" id="statsMenu" data-bs-toggle="dropdown"
                                aria-haspopup="true" aria-expanded="false">
                            <i class="ti ti-dots-vertical"></i>
                        </button>
                        <div class="dropdown-menu dropdown-menu-end" aria-labelledby="statsMenu">
                            <a class="dropdown-item" href="javascript:void(0);" onclick="refreshStats()">
                                <i class="ti ti-refresh me-1"></i>
                                تحديث
                            </a>
                            <a class="dropdown-item" href="javascript:void(0);" onclick="exportStats()">
                                <i class="ti ti-download me-1"></i>
                                تصدير
                            </a>
                        </div>
                    </div>
                </div>
                <small class="text-muted" id="last-update">
                    آخر تحديث: {{ now()->format('H:i:s') }}
                </small>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <!-- بدلاً من col-4 نستخدم col-12 col-md-4 -->
                    <div class="col-12 col-md-4">
                        <div class="d-flex align-items-center position-relative pb-3">
                            <div class="badge rounded-pill bg-label-primary p-2 me-3 animation-pulse">
                                <i class="ti ti-chart-pie-2 ti-sm"></i>
                            </div>
                            <div class="card-info">
                                <h5 class="mb-0 display-6 fw-bold" id="total-requests" data-target="0">0</h5>
                                <small class="text-muted">الإجمالي</small>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-md-4">
                        <div class="d-flex align-items-center position-relative pb-3">
                            <div class="badge rounded-pill bg-label-success p-2 me-3 animation-bounce">
                                <i class="ti ti-user-check ti-sm"></i>
                            </div>
                            <div class="card-info">
                                <h5 class="mb-0 display-6 fw-bold" id="online-requests" data-target="0">0</h5>
                                <small class="text-muted">متصل</small>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-md-4">
                        <div class="d-flex align-items-center position-relative pb-3">
                            <div class="badge rounded-pill bg-label-danger p-2 me-3">
                                <i class="ti ti-user-off ti-sm"></i>
                            </div>
                            <div class="card-info">
                                <h5 class="mb-0 display-6 fw-bold" id="offline-requests" data-target="0">0</h5>
                                <small class="text-muted">غير متصل</small>
                            </div>
                        </div>
                    </div>
                </div> <!-- .row -->
            </div>
        </div>
    </div>

    <!-- العمود الثاني -->
    <div class="col-12 col-md-6">
        <div class="card h-100">
            <div class="card-header border-bottom">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <span class="text-primary">
                            <i class="ti ti-timer me-2"></i>
                        </span>
                        {{ __('Response Times') }}
                    </h5>
                </div>
            </div>
            <div class="card-body">
                <div id="response-times-chart"></div>
                <div class="mt-3">
                    <div class="row">
                        <!-- نفس الفكرة، لضمان الترتيب الجيد على الشاشات الصغيرة -->
                        <div class="col-12 col-md-4">
                            <div class="d-flex align-items-center">
                                <div class="badge rounded-pill bg-label-info me-2">
                                    <i class="ti ti-clock"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0">{{ __('Average') }}</h6>
                                    <small id="avg-response-time">0ms</small>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 col-md-4">
                            <div class="d-flex align-items-center">
                                <div class="badge rounded-pill bg-label-success me-2">
                                    <i class="ti ti-arrow-down"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0">{{ __('Min') }}</h6>
                                    <small id="min-response-time">0ms</small>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 col-md-4">
                            <div class="d-flex align-items-center">
                                <div class="badge rounded-pill bg-label-warning me-2">
                                    <i class="ti ti-arrow-up"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0">{{ __('Max') }}</h6>
                                    <small id="max-response-time">0ms</small>
                                </div>
                            </div>
                        </div>
                    </div> <!-- .row -->
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .animation-pulse {
        animation: pulse 2s infinite;
    }
    .animation-bounce {
        animation: bounce 2s infinite;
    }
    @keyframes pulse {
        0% { transform: scale(1); }
        50% { transform: scale(1.1); }
        100% { transform: scale(1); }
    }
    @keyframes bounce {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-5px); }
    }
    .display-6 {
        font-size: 1.5rem;
        font-weight: 600;
        line-height: 1.2;
    }
    .card {
        transition: transform 0.3s ease-in-out, box-shadow 0.3s ease-in-out;
    }
    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }
    .badge {
        transition: all 0.3s ease;
    }
    .badge:hover {
        transform: scale(1.1);
    }
    #last-update {
        font-size: 0.75rem;
        color: #6c757d;
    }
</style>
