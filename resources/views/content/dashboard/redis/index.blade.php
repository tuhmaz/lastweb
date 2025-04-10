@extends('layouts/contentNavbarLayout')

@section('title', __('Redis Management'))

@section('content')
<div class="container-fluid mt-4">
    <h4 class="fw-bold py-3 mb-4">{{ __('Redis Management') }}</h4>

    <!-- رسالة النجاح -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- رسالة الخطأ -->
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- نموذج البحث -->
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">{{ __('Search Redis Keys') }}</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('dashboard.redis.index') }}" method="GET">
                <div class="input-group">
                    <input type="text" name="search" class="form-control" placeholder="{{ __('Enter key to search') }}" value="{{ request('search') }}">
                    <button class="btn btn-outline-secondary" type="submit">{{ __('Search') }}</button>
                </div>
            </form>
        </div>
    </div>

    <!-- معلومات Redis -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">{{ __('Redis Information') }}</h5>
        </div>
        <div class="card-body">
            <ul class="list-unstyled">
                <li><strong>{{ __('Total Keys:') }}</strong> {{ count($logs) }}</li>
                <li><strong>{{ __('Used Memory:') }}</strong> {{ $redisInfo['used_memory_human'] ?? 'N/A' }}</li>
                <li><strong>{{ __('Redis Version:') }}</strong> {{ $redisInfo['redis_version'] ?? 'N/A' }}</li>
            </ul>
        </div>
    </div>

    <!-- تنظيف المفاتيح -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">{{ __('Clean Redis Keys') }}</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('dashboard.redis.cleanKeys') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-danger">{{ __('Clean Expired Keys') }}</button>
            </form>
        </div>
    </div>

    <!-- نموذج إضافة مفتاح جديد -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">{{ __('Add New Key') }}</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('dashboard.redis.addKey') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="key" class="form-label">{{ __('Key') }}</label>
                    <input type="text" class="form-control" id="key" name="key" placeholder="{{ __('Enter Key') }}" required>
                </div>
                <div class="mb-3">
                    <label for="value" class="form-label">{{ __('Value') }}</label>
                    <input type="text" class="form-control" id="value" name="value" placeholder="{{ __('Enter Value') }}" required>
                </div>
                <div class="mb-3">
                    <label for="ttl" class="form-label">{{ __('TTL (Time to Live)') }}</label>
                    <input type="number" class="form-control" id="ttl" name="ttl" placeholder="{{ __('Enter TTL (optional)') }}">
                </div>
                <button type="submit" class="btn btn-primary">{{ __('Add Key') }}</button>
            </form>
        </div>
    </div>

    <!-- جدول عرض السجلات -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">{{ __('Redis Logs') }}</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>{{ __('Key') }}</th>
                            <th>{{ __('Value') }}</th>
                            <th>{{ __('TTL') }}</th>
                            <th>{{ __('Action') }}</th>
                            <th>{{ __('User') }}</th>
                            <th>{{ __('Timestamp') }}</th>
                            <th>{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($logs as $log)
                            <tr>
                                <td>{{ $log['key'] ?? __('N/A') }}</td>
                                <td>{{ $log['value'] }}</td>
                                <td>{{ $log['ttl'] }}</td>
                                <td>{{ $log['action'] ?? __('N/A') }}</td>
                                <td>{{ $log['user'] ?? __('N/A') }}</td>
                                <td>{{ $log['created_at'] }}</td>
                                <td>
                                    <form action="{{ route('dashboard.redis.deleteKey', ['key' => $log['key']]) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm">{{ __('Delete') }}</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- تقسيم الصفحات -->
            <div class="mt-3 d-flex justify-content-center">
                {{ $logs->links() }}
            </div>
        </div>
    </div>
</div>
@endsection