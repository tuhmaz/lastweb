<div class="card h-100">
    <div class="card-header border-bottom">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">
                <span class="text-primary">
                    <i class="ti ti-users me-2"></i>
                </span>
                {{ __('Active Users') }}
            </h5>
            <div class="dropdown">
                <button class="btn p-0" type="button" id="activeUsersMenu" data-bs-toggle="dropdown"
                    aria-haspopup="true" aria-expanded="false">
                    <i class="ti ti-dots-vertical"></i>
                </button>
                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="activeUsersMenu">
                    <a class="dropdown-item" href="javascript:void(0);" onclick="refreshActiveUsers()">
                        <i class="ti ti-refresh me-1"></i>
                        تحديث
                    </a>
                    <a class="dropdown-item" href="javascript:void(0);" onclick="exportActiveUsers()">
                        <i class="ti ti-download me-1"></i>
                        تصدير
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="card-datatable table-responsive">
        <div class="loading-spinner text-center d-none">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">جاري التحميل...</span>
            </div>
        </div>
        <table class="table table-striped table-hover border-top" id="active-users-table">
            <thead>
                <tr class="text-nowrap">
                    <th>{{ __('IP Address') }}</th>
                    <th>{{ __('User ID') }}</th>
                    <th>{{ __('URL') }}</th>
                    <th>{{ __('Location') }}</th>
                    <th>{{ __('Device Info') }}</th>
                    <th>{{ __('Last Activity') }}</th>
                    <th>{{ __('Status') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($activeUsers as $user)
                <tr>
                    <td>
                        <span class="badge bg-label-primary">{{ $user['ip_address'] ?? 'Unknown' }}</span>
                    </td>
                    <td>
                        <div class="d-flex justify-content-start align-items-center">
                            <div class="avatar avatar-sm me-3">
                                <span
                                    class="avatar-initial rounded-circle bg-label-{{ $user['user_id'] ? 'success' : 'warning' }}">
                                    {{ $user['user_id'] ? 'U' : 'G' }}
                                </span>
                            </div>
                            <span class="fw-semibold">{{ $user['user_id'] ?? 'Guest' }}</span>
                        </div>
                    </td>
                    <td>
                        <span class="text-truncate d-inline-block mw-200" data-bs-toggle="tooltip"
                            title="{{ $user['url'] ?? '-' }}">
                            {{ $user['url'] ?? '-' }}
                        </span>
                    </td>
                    <td>
                        <div class="d-flex flex-column">
                            <span class="fw-semibold">{{ $user['country'] ?? 'Unknown' }}</span>
                            <small class="text-muted">{{ $user['city'] ?? 'Unknown' }}</small>
                        </div>
                    </td>
                    <td>
                        <div class="d-flex flex-column">
                            <span class="fw-semibold">{{ $user['browser'] ?? 'Unknown' }}</span>
                            <small class="text-muted">{{ $user['os'] ?? 'Unknown' }}</small>
                        </div>
                    </td>
                    <td>
                        <span class="text-muted" data-bs-toggle="tooltip" title="{{ $user['last_activity'] }}">
                            {{ \Carbon\Carbon::parse($user['last_activity'] ?? now())->diffForHumans() }}
                        </span>
                    </td>
                    <td>
                        <span class="badge bg-label-success">Active</span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center">{{ __('No active users found') }}</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<style>
    .card {
        transition: all 0.3s ease-in-out;
    }

    .mw-200 {
        max-width: 200px;
    }

    .loading-spinner {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        z-index: 1000;
        background: rgba(255, 255, 255, 0.8);
        padding: 1rem;
        border-radius: 0.5rem;
    }

    .table-responsive {
        position: relative;
        min-height: 400px;
    }

    .avatar-initial {
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .badge {
        transition: all 0.2s ease-in-out;
    }

    .badge:hover {
        transform: scale(1.1);
    }

    @media (max-width: 768px) {
        .mw-200 {
            max-width: 150px;
        }
    }
</style>
 