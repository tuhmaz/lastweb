@extends('layouts/contentNavbarLayout')

@section('title', __('Create User'))

@section('content')
<h4 class="fw-bold py-3 mb-4">
    <span class="text-muted fw-light">{{ __('User Management') }} /</span> {{ __('Create New User') }}
</h4>

<div class="row">
    <div class="col-md-12">
        <div class="card mb-4">
            <h5 class="card-header">{{ __('Create New User') }}</h5>
            <div class="card-body">
                <form method="POST" action="{{ route('dashboard.users.store') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <!-- Name -->
                        <div class="mb-3 col-md-6">
                            <label for="name" class="form-label">{{ __('Full Name') }}</label>
                            <input class="form-control @error('name') is-invalid @enderror" 
                                   type="text" 
                                   id="name" 
                                   name="name" 
                                   value="{{ old('name') }}" 
                                   placeholder="John Doe" />
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Email -->
                        <div class="mb-3 col-md-6">
                            <label for="email" class="form-label">{{ __('Email') }}</label>
                            <input class="form-control @error('email') is-invalid @enderror" 
                                   type="text" 
                                   id="email" 
                                   name="email" 
                                   value="{{ old('email') }}" 
                                   placeholder="john@example.com" />
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Password -->
                        <div class="mb-3 col-md-6">
                            <label for="password" class="form-label">{{ __('Password') }}</label>
                            <input class="form-control @error('password') is-invalid @enderror" 
                                   type="password" 
                                   id="password" 
                                   name="password" />
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Password Confirmation -->
                        <div class="mb-3 col-md-6">
                            <label for="password_confirmation" class="form-label">{{ __('Confirm Password') }}</label>
                            <input class="form-control" 
                                   type="password" 
                                   id="password_confirmation" 
                                   name="password_confirmation" />
                        </div>

                        <!-- Role -->
                        <div class="mb-3 col-md-6">
                            <label for="role" class="form-label">{{ __('Role') }}</label>
                            <select class="form-select @error('role') is-invalid @enderror" 
                                    id="role" 
                                    name="role">
                                <option value="">{{ __('Select Role') }}</option>
                                @foreach($roles as $role)
                                    <option value="{{ $role->name }}" {{ old('role') == $role->name ? 'selected' : '' }}>
                                        {{ ucfirst($role->name) }}
                                    </option>
                                @endforeach
                            </select>
                            @error('role')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Profile Photo -->
                        <div class="mb-3 col-md-6">
                            <label for="profile_photo" class="form-label">{{ __('Profile Photo') }}</label>
                            <input class="form-control @error('profile_photo') is-invalid @enderror" 
                                   type="file" 
                                   id="profile_photo" 
                                   name="profile_photo" 
                                   accept="image/*" />
                            @error('profile_photo')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mt-2">
                        <button type="submit" class="btn btn-primary me-2">{{ __('Create User') }}</button>
                        <a href="{{ route('dashboard.users.index') }}" class="btn btn-outline-secondary">{{ __('Cancel') }}</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
