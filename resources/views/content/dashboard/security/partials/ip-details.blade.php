<div class="ip-details">
    <!-- IP Header -->
    <div class="d-flex align-items-center mb-3">
        <i class="fas fa-network-wired fa-2x me-2"></i>
        <div>
            <h4 class="mb-0">{{ $ip }}</h4>
            <p class="text-muted mb-0">
                {{ __('First seen') }}: {{ $stats['first_seen'] ? $stats['first_seen']->diffForHumans() : __('N/A') }} |
                {{ __('Last seen') }}: {{ $stats['last_seen'] ? $stats['last_seen']->diffForHumans() : __('N/A') }}
            </p>
        </div>
    </div>

    <div class="row">
        <!-- Risk Assessment -->
        <div class="col-md-6 mb-3">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="card-title mb-0">{{ __('Risk Assessment') }}</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">{{ __('Current Risk Score') }}</label>
                        <div class="progress" style="height: 10px;">
                            <div class="progress-bar bg-{{ $stats['risk_scores']['current'] >= 75 ? 'danger' : ($stats['risk_scores']['current'] >= 50 ? 'warning' : 'info') }}"
                                 role="progressbar"
                                 style="width: {{ $stats['risk_scores']['current'] }}%"
                                 aria-valuenow="{{ $stats['risk_scores']['current'] }}"
                                 aria-valuemin="0"
                                 aria-valuemax="100">
                            </div>
                        </div>
                        <small class="text-muted">{{ $stats['risk_scores']['current'] }}/100</small>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>{{ __('Average Score') }}:</span>
                        <span class="fw-bold">{{ round($stats['risk_scores']['average'], 2) }}</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span>{{ __('Highest Score') }}:</span>
                        <span class="fw-bold">{{ $stats['risk_scores']['max'] }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Event Statistics -->
        <div class="col-md-6 mb-3">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="card-title mb-0">{{ __('Event Statistics') }}</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-2">
                            <span>{{ __('Total Events') }}:</span>
                            <span class="badge bg-primary">{{ $stats['total_events'] }}</span>
                        </div>
                    </div>
                    <div class="event-types">
                        <label class="form-label">{{ __('Event Types') }}</label>
                        @foreach($stats['event_types'] as $type => $count)
                            <div class="d-flex justify-content-between mb-2">
                                <span>{{ __(Str::title(str_replace('_', ' ', $type))) }}</span>
                                <span class="badge bg-{{ $type === 'blocked_access' ? 'danger' : ($type === 'suspicious_activity' ? 'warning' : 'info') }}">
                                    {{ $count }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Geographic Information -->
        <div class="col-md-6 mb-3">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="card-title mb-0">{{ __('Geographic Information') }}</h5>
                </div>
                <div class="card-body">
                    @forelse($stats['locations'] as $country => $data)
                        <div class="mb-3">
                            <div class="d-flex align-items-center mb-2">
                                <img src="{{ asset('images/flags/' . strtolower($country) . '.svg') }}"
                                     alt="{{ $country }}"
                                     class="me-2"
                                     style="width: 24px;">
                                <span class="fw-bold">{{ $country }}</span>
                                <span class="badge bg-secondary ms-auto">{{ $data['count'] }} {{ __('events') }}</span>
                            </div>
                            @if(count($data['cities']) > 0)
                                <div class="ms-4">
                                    <small class="text-muted">{{ __('Cities') }}: {{ implode(', ', $data['cities']->toArray()) }}</small>
                                </div>
                            @endif
                        </div>
                    @empty
                        <p class="text-muted mb-0">{{ __('No location data available') }}</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Technical Details -->
        <div class="col-md-6 mb-3">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="card-title mb-0">{{ __('Technical Details') }}</h5>
                </div>
                <div class="card-body">
                    <!-- User Agents -->
                    <div class="mb-3">
                        <label class="form-label">{{ __('User Agents') }}</label>
                        <div class="user-agents">
                            @forelse($stats['user_agents'] as $agent)
                                <div class="mb-2">
                                    <i class="fas fa-browser me-2"></i>
                                    <small>{{ $agent }}</small>
                                </div>
                            @empty
                                <p class="text-muted mb-0">{{ __('No user agent data available') }}</p>
                            @endforelse
                        </div>
                    </div>

                    <!-- Accessed Routes -->
                    <div class="mb-3">
                        <label class="form-label">{{ __('Accessed Routes') }}</label>
                        <div class="routes">
                            @forelse($stats['routes'] as $route)
                                <div class="mb-2">
                                    <i class="fas fa-link me-2"></i>
                                    <small>{{ $route }}</small>
                                </div>
                            @empty
                                <p class="text-muted mb-0">{{ __('No route data available') }}</p>
                            @endforelse
                        </div>
                    </div>

                    <!-- Associated Users -->
                    @if(count($stats['users']) > 0)
                        <div>
                            <label class="form-label">{{ __('Associated Users') }}</label>
                            <div class="users">
                                @foreach($stats['users'] as $user)
                                    <div class="mb-2">
                                        <i class="fas fa-user me-2"></i>
                                        <small>{{ $user }}</small>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Events -->
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">{{ __('Recent Events') }}</h5>
        </div>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>{{ __('Time') }}</th>
                        <th>{{ __('Event') }}</th>
                        <th>{{ __('Description') }}</th>
                        <th>{{ __('Risk Score') }}</th>
                        <th>{{ __('Status') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs->take(10) as $log)
                        <tr>
                            <td>
                                <div data-bs-toggle="tooltip" title="{{ $log->created_at }}">
                                    {{ $log->created_at->diffForHumans() }}
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-{{ $log->event_type === 'blocked_access' ? 'danger' : ($log->event_type === 'suspicious_activity' ? 'warning' : 'info') }}">
                                    {{ __(Str::title(str_replace('_', ' ', $log->event_type))) }}
                                </span>
                            </td>
                            <td>{{ $log->description }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="progress" style="width: 50px; height: 4px;">
                                        <div class="progress-bar bg-{{ $log->risk_score >= 75 ? 'danger' : ($log->risk_score >= 50 ? 'warning' : 'info') }}"
                                             role="progressbar"
                                             style="width: {{ $log->risk_score }}%">
                                        </div>
                                    </div>
                                    <span class="ms-2">{{ $log->risk_score }}</span>
                                </div>
                            </td>
                            <td>
                                @if($log->is_resolved)
                                    <span class="badge bg-success">{{ __('Resolved') }}</span>
                                @else
                                    <span class="badge bg-warning">{{ __('Pending') }}</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center">{{ __('No events found') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
