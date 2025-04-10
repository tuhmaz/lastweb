@extends('layouts/contentNavbarLayout')

@section('title', __('Edit Role'))

@section('content')
<h4 class="fw-bold py-3 mb-4">
    <span class="text-muted fw-light">{{ __('Roles') }} /</span> {{ __('Edit Role') }}
</h4>

<div class="row">
    <div class="col-md-12">
        <div class="card mb-4">
            <h5 class="card-header">{{ __('Edit Role') }}</h5>
            <div class="card-body">
                <form method="POST" action="{{ route('dashboard.roles.update', $role) }}">
                    @csrf
                    @method('PUT')
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="name" class="form-label">{{ __('Role Name') }}</label>
                            <input type="text" 
                                   class="form-control @error('name') is-invalid @enderror" 
                                   id="name" 
                                   name="name" 
                                   value="{{ old('name', $role->name) }}" 
                                   required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <h6>{{ __('Permissions') }}</h6>
                            <div class="row g-3">
                                @foreach($permissions as $permission)
                                <div class="col-md-3">
                                    <div class="form-check">
                                        <input class="form-check-input" 
                                               type="checkbox" 
                                               name="permissions[]" 
                                               value="{{ $permission->name }}" 
                                               id="permission_{{ $permission->id }}"
                                               {{ in_array($permission->name, old('permissions', $role->permissions->pluck('name')->toArray())) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="permission_{{ $permission->id }}">
                                            {{ $permission->name }}
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

                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary me-2">{{ __('Update Role') }}</button>
                        <a href="{{ route('dashboard.roles.index') }}" class="btn btn-outline-secondary">{{ __('Cancel') }}</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
