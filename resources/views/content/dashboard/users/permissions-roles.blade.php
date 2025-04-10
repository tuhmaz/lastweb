@extends('layouts/contentNavbarLayout')

@section('title', __('User Roles & Permissions'))

@section('content')
<h4 class="fw-bold py-3 mb-4">
    <span class="text-muted fw-light">{{ __('Users') }} / {{ $user->name }} /</span> {{ __('Roles & Permissions') }}
</h4>

<div class="row">
    <div class="col-md-12">
        <div class="card mb-4">
            <h5 class="card-header">{{ __('Manage Roles & Permissions') }}</h5>
            <div class="card-body">
                <form method="POST" action="{{ route('dashboard.users.update-permissions-roles', $user) }}">
                    @csrf
                    @method('PUT')
                    
                    <!-- Roles Section -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6 class="fw-semibold">{{ __('Roles') }}</h6>
                            <div class="row g-3">
                                @foreach($roles as $role)
                                <div class="col-md-3">
                                    <div class="form-check">
                                        <input class="form-check-input" 
                                               type="checkbox" 
                                               name="roles[]" 
                                               value="{{ $role->name }}" 
                                               id="role_{{ $role->id }}"
                                               {{ $user->hasRole($role) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="role_{{ $role->id }}">
                                            {{ $role->name }}
                                            <small class="text-muted">({{ $role->guard_name }})</small>
                                        </label>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            @error('roles')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Direct Permissions Section -->
                    <div class="row">
                        <div class="col-12">
                            <h6 class="fw-semibold">{{ __('Direct Permissions') }}</h6>
                            <div class="row g-3">
                                @foreach($permissions as $permission)
                                <div class="col-md-3">
                                    <div class="form-check">
                                        <input class="form-check-input" 
                                               type="checkbox" 
                                               name="permissions[]" 
                                               value="{{ $permission->name }}" 
                                               id="permission_{{ $permission->id }}"
                                               {{ $user->hasDirectPermission($permission) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="permission_{{ $permission->id }}">
                                            {{ $permission->name }}
                                            <small class="text-muted">({{ $permission->guard_name }})</small>
                                        </label>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            @error('permissions')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Inherited Permissions Section -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6 class="fw-semibold">{{ __('Inherited Permissions from Roles') }}</h6>
                                    <div class="row g-3">
                                        @foreach($user->getPermissionsViaRoles() as $permission)
                                        <div class="col-md-3">
                                            <span class="badge bg-label-info">{{ $permission->name }}</span>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary me-2">{{ __('Save Changes') }}</button>
                        <a href="{{ route('dashboard.users.show', $user) }}" class="btn btn-outline-secondary">{{ __('Cancel') }}</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
