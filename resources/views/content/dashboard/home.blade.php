@php
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
$stats = [
    'articles' => [
        'count' => $totalArticles,
        'trend' => $articlesTrend,
        'icon' => 'bx bx-news',
        'color' => 'primary'
    ],
    'news' => [
        'count' => $totalNews,
        'trend' => $newsTrend,
        'icon' => 'bx bx-broadcast',
        'color' => 'success'
    ],
    'users' => [
        'count' => $totalUsers,
        'trend' => $usersTrend,
        'icon' => 'bx bx-user',
        'color' => 'warning'
    ],
    'online' => [
        'count' => $onlineUsersCount,
        'trend' => ['percentage' => $onlineUsersCount, 'trend' => 'up'],
        'icon' => 'bx bx-group',
        'color' => 'info'
    ]
];


$noindex = true;
@endphp



@extends('layouts/contentNavbarLayout')

@section('title', __('Dashboard'))

@section('vendor-style')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/boxicons@2.1.4/css/boxicons.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/animate.css@4.1.1/animate.min.css">
@endsection

@section('content')
<div class="container-fluid p-0">
    <!-- Welcome Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card bg-primary text-white">
                <div class="card-body py-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="welcome-text">
                            <h3 class="text-white mb-2">{{ __('Welcome back') }}, {{ Auth::user()->name }}! 👋</h3>
                            <p class="mb-0">{{ __('Here\'s what\'s happening with your content today.') }}</p>
                        </div>
                        <div class="welcome-image d-none d-md-block">
                            <img src="{{ Auth::user()->profile_photo_path ? asset('storage/' . Auth::user()->profile_photo_path) : asset($defaultAvatar) }}"
                                 class="rounded-circle shadow-lg"
                                 width="100"
                                 height="100"
                                 style="object-fit: cover; border: 4px solid rgba(255,255,255,0.2);">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
        @foreach($stats as $key => $stat)
        <div class="col-sm-6 col-xl-3 mb-4 mb-xl-0">
            <div class="card h-100 animate__animated animate__fadeIn">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div class="content-left">
                            <span class="fw-semibold d-block mb-1">{{ __(Str::title($key)) }}</span>
                            <div class="d-flex align-items-baseline mt-2">
                                <h4 class="card-title mb-0 me-2">{{ number_format($stat['count']) }}</h4>
                                <small class="text-{{ $stat['trend']['trend'] === 'up' ? 'success' : 'danger' }}">
                                    <i class="bx {{ $stat['trend']['trend'] === 'up' ? 'bx-up-arrow-alt' : 'bx-down-arrow-alt' }}"></i>
                                    {{ $stat['trend']['percentage'] }}%
                                </small>
                            </div>
                        </div>
                        <div class="avatar">
                            <span class="avatar-initial rounded bg-label-{{ $stat['color'] }}">
                                <i class="{{ $stat['icon'] }}"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Charts Row -->
    <div class="row mb-4">
        <div class="col-12 col-xl-8 mb-4 mb-xl-0">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">{{ __('Content Analytics') }}</h5>
                    <div class="dropdown">
                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" id="timeRangeButton">
                            {{ __('Last 7 Days') }}
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item active" href="#" data-days="7">{{ __('Last 7 Days') }}</a></li>
                            <li><a class="dropdown-item" href="#" data-days="30">{{ __('Last 30 Days') }}</a></li>
                            <li><a class="dropdown-item" href="#" data-days="90">{{ __('Last 90 Days') }}</a></li>
                        </ul>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="small-stat">
                                <h6 class="text-muted mb-1">{{ __('Total Content') }}</h6>
                                <h3 class="mb-0">{{ number_format($totalArticles + $totalNews) }}</h3>
                                <small class="text-{{ $articlesTrend['trend'] === 'up' ? 'success' : 'danger' }}">
                                    <i class="bx {{ $articlesTrend['trend'] === 'up' ? 'bx-up-arrow-alt' : 'bx-down-arrow-alt' }}"></i>
                                    {{ $articlesTrend['percentage'] }}%
                                </small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="small-stat">
                                <h6 class="text-muted mb-1">{{ __('Total Views') }}</h6>
                                <h3 class="mb-0">{{ number_format($analyticsData['views']->sum()) }}</h3>
                                <small class="text-success">
                                    <i class="bx bx-trending-up"></i>
                                    {{ __('This Week') }}
                                </small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="small-stat">
                                <h6 class="text-muted mb-1">{{ __('Active Authors') }}</h6>
                                <h3 class="mb-0">{{ number_format($analyticsData['authors']->max()) }}</h3>
                                <small class="text-muted">
                                    {{ __('Contributing') }}
                                </small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="small-stat">
                                <h6 class="text-muted mb-1">{{ __('Comments') }}</h6>
                                <h3 class="mb-0">{{ number_format($analyticsData['comments']->sum()) }}</h3>
                                <small class="text-muted">
                                    {{ __('This Week') }}
                                </small>
                            </div>
                        </div>
                    </div>
                    <div id="contentChart" style="height: 350px;"></div>
                </div>
            </div>
        </div>
        <div class="col-12 col-xl-4">
        <div class="card h-100">
        <div class="card-header d-flex align-items-center justify-content-between">
            <h5 class="card-title m-0">المستخدمون المتصلون</h5>
            <div class="dropdown">
                <button class="btn p-0" type="button" data-bs-toggle="dropdown">
                    <i class="ti ti-dots-vertical"></i>
                </button>
                <div class="dropdown-menu dropdown-menu-end">
                    <a class="dropdown-item" href="javascript:void(0);">تحديث</a>
                    <a class="dropdown-item" href="javascript:void(0);">مشاركة</a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <ul class="p-0 m-0">
                @forelse($onlineUsers as $user)
                <li class="d-flex mb-4 pb-1">
                    <div class="avatar flex-shrink-0 me-3">
                        @if($user->profile_photo_path)
                            <img src="{{ asset('storage/'.$user->profile_photo_path) }}" alt="User Avatar" class="rounded">
                        @else
                            <span class="avatar-initial rounded bg-label-primary">
                                {{ substr($user->name, 0, 2) }}
                            </span>
                        @endif
                        <span class="status-indicator position-absolute bottom-0 end-0 rounded-circle
                            @if($user->status === 'online') bg-success
                            @elseif($user->status === 'away') bg-warning
                            @else bg-secondary @endif"
                            style="width: 10px; height: 10px;">
                        </span>
                    </div>
                    <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                        <div class="me-2">
                            <h6 class="mb-0">{{ $user->name }}</h6>
                            <small class="text-muted">
                                @if($user->status === 'online')
                                    متصل الآن
                                @else
                                    {{ $user->last_activity ? 'آخر ظهور ' . $user->last_activity->diffForHumans() : 'غير متصل' }}
                                @endif
                            </small>
                        </div>
                        <div class="user-status">
                            <small class="badge bg-label-{{ $user->status === 'online' ? 'success' : 'secondary' }}">
                                {{ $user->status === 'online' ? 'متصل' : 'غير متصل' }}
                            </small>
                        </div>
                    </div>
                </li>
                @empty
                <li class="text-center py-4">
                    <p class="mb-0">لا يوجد مستخدمون متصلون حالياً</p>
                </li>
                @endforelse
            </ul>
        </div>
    </div>
        </div>
    </div>

    <!-- Recent Activity & Quick Actions -->
    <div class="row">
        <div class="col-md-6 col-lg-4">
            <div class="card h-100">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="card-title m-0">النشاطات الأخيرة</h5>
                    <a href="{{ route('dashboard.activities.index') }}" class="btn btn-sm btn-primary">
                        {{ __('View All') }}
                        <i class="bx bx-right-arrow-alt ms-1"></i>
                    </a>
                </div>
                <div class="card-body" style="height: 600px; overflow-y: auto;" id="activities-container">
                    <ul class="timeline timeline-center mt-3" id="activities-list">
                        @forelse($recentActivities as $activity)
                            <li class="timeline-item">
                                <span class="timeline-indicator
                                    @if($activity['type'] === 'article')
                                        bg-primary
                                    @elseif($activity['type'] === 'news')
                                        bg-info
                                    @elseif($activity['type'] === 'comment')
                                        bg-warning
                                    @else
                                        bg-secondary
                                    @endif
                                ">
                                    <i class="bx {{ $activity['icon'] }}"></i>
                                </span>
                                <div class="timeline-event">
                                    <div class="timeline-header">
                                        <h6 class="mb-0">{{ $activity['action'] }}</h6>
                                        <small class="text-muted">{{ $activity['time']->diffForHumans() }}</small>
                                    </div>
                                    <div class="timeline-body">
                                        <p class="mb-2">{{ Str::limit($activity['description'] ?? '', 100) }}</p>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <img src="{{ $activity['user_avatar'] ?? asset($defaultAvatar) }}" 
                                             class="rounded-circle me-2" 
                                             width="24" 
                                             height="24" 
                                             alt="{{ $activity['user'] }}">
                                        <small>{{ $activity['user'] }}</small>
                                    </div>
                                </div>
                            </li>
                        @empty
                            <li class="text-center py-4">
                                <p class="mb-0">لا توجد نشاطات حديثة</p>
                            </li>
                        @endforelse
                    </ul>
                    
                    <div id="activities-loader" class="text-center d-none">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-xl-8">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="card-title mb-0">{{ __('Quick Actions') }}</h5>
                </div>
                <div class="card-body">
                    <div class="quick-actions-grid">
                        <a href="{{ route('dashboard.news.create') }}" class="quick-action-item animate__animated animate__fadeIn">
                            <i class="bx bx-news text-primary mb-2"></i>
                            <span>{{ __('Add News') }}</span>
                        </a>
                        <a href="{{ route('dashboard.articles.create') }}" class="quick-action-item animate__animated animate__fadeIn">
                            <i class="bx bx-edit text-success mb-2"></i>
                            <span>{{ __('Write Article') }}</span>
                        </a>
                        <a href="{{ route('dashboard.users.index') }}" class="quick-action-item animate__animated animate__fadeIn">
                            <i class="bx bx-user-plus text-warning mb-2"></i>
                            <span>{{ __('Manage Users') }}</span>
                        </a>
                        <a href="{{ route('dashboard.settings.index') }}" class="quick-action-item animate__animated animate__fadeIn">
                            <i class="bx bx-cog text-info mb-2"></i>
                            <span>{{ __('Settings') }}</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('page-script')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize chart with analytics data
    const analyticsData = @json($analyticsData);
    
    // Chart options
    const options = {
        series: [
            {
                name: '{{ __("Articles") }}',
                data: analyticsData.articles,
                type: 'line'
            },
            {
                name: '{{ __("News") }}',
                data: analyticsData.news,
                type: 'line'
            },
            {
                name: '{{ __("Comments") }}',
                data: analyticsData.comments,
                type: 'area'
            },
            {
                name: '{{ __("Views") }}',
                data: analyticsData.views,
                type: 'area'
            }
        ],
        chart: {
            height: 350,
            type: 'line',
            toolbar: {
                show: false
            },
            zoom: {
                enabled: false
            }
        },
        dataLabels: {
            enabled: false
        },
        stroke: {
            width: [2, 2, 1, 1],
            curve: 'smooth'
        },
        colors: ['#696cff', '#03c3ec', '#ffab00', '#71dd37'],
        fill: {
            opacity: [1, 1, 0.3, 0.3],
            type: ['solid', 'solid', 'gradient', 'gradient'],
            gradient: {
                shade: 'light',
                type: 'vertical',
                opacityFrom: 0.4,
                opacityTo: 0.1,
            }
        },
        legend: {
            position: 'top',
            horizontalAlign: 'right',
            offsetY: -20,
            labels: {
                colors: document.documentElement.classList.contains('dark-style') ? '#fff' : undefined
            }
        },
        markers: {
            size: 4,
            hover: {
                size: 6
            }
        },
        xaxis: {
            categories: analyticsData.dates,
            labels: {
                style: {
                    colors: document.documentElement.classList.contains('dark-style') ? '#fff' : undefined
                }
            },
            axisBorder: {
                show: false
            }
        },
        yaxis: {
            labels: {
                style: {
                    colors: document.documentElement.classList.contains('dark-style') ? '#fff' : undefined
                }
            }
        },
        grid: {
            borderColor: document.documentElement.classList.contains('dark-style') ? '#3b3b3b' : '#e7e7e7'
        },
        tooltip: {
            shared: true,
            intersect: false,
            y: {
                formatter: function (y) {
                    if (typeof y !== "undefined") {
                        return y.toFixed(0);
                    }
                    return y;
                }
            }
        }
    };

    const chart = new ApexCharts(document.querySelector("#contentChart"), options);
    chart.render();

    // Handle time range changes
    const timeRangeDropdown = document.querySelector('.dropdown-menu');
    const timeRangeButton = document.getElementById('timeRangeButton');

    timeRangeDropdown.addEventListener('click', function(e) {
        if (e.target.classList.contains('dropdown-item')) {
            e.preventDefault();
            
            // Update button text
            const days = e.target.dataset.days;
            timeRangeButton.textContent = e.target.textContent;
            
            // Update active state
            timeRangeDropdown.querySelectorAll('.dropdown-item').forEach(item => {
                item.classList.remove('active');
            });
            e.target.classList.add('active');
            
            // Fetch new data
            fetch(`/dashboard/analytics?days=${days}`)
                .then(response => response.json())
                .then(data => {
                    // Update chart data
                    chart.updateOptions({
                        xaxis: {
                            categories: data.dates
                        }
                    });
                    
                    chart.updateSeries([
                        {
                            name: '{{ __("Articles") }}',
                            data: data.articles
                        },
                        {
                            name: '{{ __("News") }}',
                            data: data.news
                        },
                        {
                            name: '{{ __("Comments") }}',
                            data: data.comments
                        },
                        {
                            name: '{{ __("Views") }}',
                            data: data.views
                        }
                    ]);

                    // Update statistics
                    document.querySelector('[data-stat="total-views"]').textContent = data.totalViews;
                    document.querySelector('[data-stat="active-authors"]').textContent = data.activeAuthors;
                    document.querySelector('[data-stat="total-comments"]').textContent = data.totalComments;
                });
        }
    });
});
</script>
@endsection
