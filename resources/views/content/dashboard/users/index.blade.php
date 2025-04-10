@extends('layouts/contentNavbarLayout')

@section('title', __('Users List'))

@section('vendor-style')
@vite([
    'resources/assets/vendor/libs/select2/select2.scss',
    'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss',
    
])
@endsection

@section('vendor-script')
@vite([
    'resources/assets/vendor/libs/select2/select2.js',
    'resources/assets/vendor/libs/sweetalert2/sweetalert2.js',
    'resources/assets/vendor/libs/jquery/jquery.js',
    'resources/assets/js/pages/userlist.js'
])
@endsection

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold m-0">
        <span class="text-muted fw-light">{{ __('User Management') }} /</span> {{ __('Users List') }}
    </h4>
    @can('admin users')
    <button type="button" class="btn btn-primary" onclick="window.location.href='{{ route('dashboard.users.create') }}'">
        <i class="ti ti-plus me-1"></i>{{ __('Add User') }}
    </button>
    @endcan
</div>

<div class="card">
    <div class="card-header border-bottom">
        <form id="filterForm" class="row g-3" 
            data-users-url="{{ route('dashboard.users.index') }}"
            data-network-error="{{ __('Network error. Please check your connection.') }}"
            data-loading-error="{{ __('Error loading users. Please try again.') }}"
            data-delete-confirm="{{ __('Are you sure you want to delete this user?') }}">
            <div class="col-md-5">
                <label class="form-label" for="UserRole">{{ __('Role') }}</label>
                <select id="UserRole" name="role" class="form-select select2">
                    <option value="">{{ __('All Roles') }}</option>
                    @foreach($roles as $role)
                        <option value="{{ $role->name }}" {{ request('role') == $role->name ? 'selected' : '' }}>
                            {{ ucfirst($role->name) }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-5">
                <label class="form-label" for="UserSearch">{{ __('Search') }}</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="ti ti-search"></i></span>
                    <input type="text" id="UserSearch" name="search" class="form-control" 
                           placeholder="{{ __('Search by name or email') }}" value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="button" id="resetFiltersBtn" class="btn btn-secondary w-100">
                    <i class="ti ti-refresh me-1"></i>{{ __('Reset') }}
                </button>
            </div>
        </form>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-light">
                    <tr>
                        <th>{{ __('User') }}</th>
                        <th>{{ __('Role') }}</th>
                        <th>{{ __('Status') }}</th>
                        <th>{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody id="users-table-body">
                    @include('content.dashboard.users.partials.users-table', ['users' => $users])
                </tbody>
            </table>
        </div>
    </div>
    
    <div class="card-footer">
        <div class="pagination pagination-outline-secondary">
          {{ $users->links('components.pagination.custom') }}
        </div>
    </div>
</div>
@endsection

@section('page-script')
<script>

</script>
@endsection