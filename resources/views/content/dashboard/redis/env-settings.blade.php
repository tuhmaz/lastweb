@extends('layouts/contentNavbarLayout')

@section('title', __('Edit .env Settings'))

@section('content')
<div class="container mt-4">
    <h4 class="fw-bold py-3 mb-4">{{ __('Edit Redis .env Settings') }}</h4>

    <!-- تنبيه النجاح -->
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <!-- نموذج تعديل .env -->
    <div class="card">
        <div class="card-header">
            <h5>{{ __('Update Redis Settings') }}</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('dashboard.redis.updateEnv') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="REDIS_HOST" class="form-label">{{ __('Redis Host') }}</label>
                    <input type="text" class="form-control" id="REDIS_HOST" name="REDIS_HOST" value="{{ $envData['REDIS_HOST'] ?? '' }}" required>
                </div>
                <div class="mb-3">
                    <label for="REDIS_PORT" class="form-label">{{ __('Redis Port') }}</label>
                    <input type="text" class="form-control" id="REDIS_PORT" name="REDIS_PORT" value="{{ $envData['REDIS_PORT'] ?? '' }}" required>
                </div>
                <div class="mb-3">
                    <label for="REDIS_PASSWORD" class="form-label">{{ __('Redis Password') }}</label>
                    <input type="password" class="form-control" id="REDIS_PASSWORD" name="REDIS_PASSWORD" value="{{ $envData['REDIS_PASSWORD'] ?? '' }}">
                </div>
                <div class="mb-3">
                    <label for="REDIS_DB" class="form-label">{{ __('Redis Database') }}</label>
                    <input type="text" class="form-control" id="REDIS_DB" name="REDIS_DB" value="{{ $envData['REDIS_DB'] ?? '' }}">
                </div>
                <button type="submit" class="btn btn-primary">{{ __('Update Settings') }}</button>
            </form>
        </div>
    </div>
</div>
@endsection
