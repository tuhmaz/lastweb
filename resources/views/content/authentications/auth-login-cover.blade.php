@php
$customizerHidden = 'customizer-hide';
$configData = Helper::appClasses();
@endphp

@extends('layouts/blankLayout')

@section('title', 'Login')

@section('vendor-style')
@vite([
  'resources/assets/vendor/libs/@form-validation/form-validation.scss'
])
@endsection

@section('page-style')
@vite([
  'resources/assets/vendor/scss/pages/page-auth.scss'
])
@endsection

@section('vendor-script')
@vite([
  'resources/assets/vendor/libs/@form-validation/popular.js',
  'resources/assets/vendor/libs/@form-validation/bootstrap5.js',
  'resources/assets/vendor/libs/@form-validation/auto-focus.js'
])
@endsection

@section('page-script')
@vite([
  'resources/assets/js/pages-auth.js'
])
@endsection

@section('content')
<div class="authentication-wrapper authentication-cover">
  <a href="{{url('/')}}" class="app-brand auth-cover-brand">
  <span class="app-brand-logo">

<img src="{{ asset('storage/' . config('settings.site_logo')) }}"
     alt="{{ config('settings.site_name') }} Logo"
     width="45"
     height="45"
     loading="lazy" />
</span>
<span class="app-brand-text"style="color: #223553;">{{ config('settings.site_name') }}</span>
  </a>
  <div class="authentication-inner row m-0">
    <div class="d-none d-lg-flex col-lg-8 p-0">
      <div class="auth-cover-bg auth-cover-bg-color d-flex justify-content-center align-items-center">
        <img src="{{ asset('assets/img/illustrations/auth-login-illustration-'.$configData['style'].'.svg') }}" alt="auth-login-cover" class="my-5 auth-illustration" data-app-light-img="illustrations/auth-login-illustration-light.svg" data-app-dark-img="illustrations/auth-login-illustration-dark.svg">
        <img src="{{ asset('assets/img/illustrations/bg-shape-image-'.$configData['style'].'.png') }}" alt="auth-login-cover" class="platform-bg" data-app-light-img="illustrations/bg-shape-image-light.png" data-app-dark-img="illustrations/bg-shape-image-dark.png">
      </div>
    </div>

    <div class="d-flex col-12 col-lg-4 align-items-center authentication-bg p-sm-12 p-6">
      <div class="w-px-400 mx-auto mt-12 pt-5">
        <h4 class="mb-1">{{ __('Welcome to') }} {{ config('settings.site_name') }}! </h4>
        <p class="mb-6">{{ __('Please sign-in to your account and start') }}</p>

        @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
        @endif

        <form id="formAuthentication" class="mb-6" action="{{ route('login') }}" method="POST">
          @csrf
          <div class="mb-6">
            <label for="email" class="form-label">{{ __('Email') }}</label>
            <input type="text" class="form-control @error('email') is-invalid @enderror"
                   id="email"
                   name="email"
                   placeholder="{{ __('Enter your email') }}"
                   value="{{ old('email') }}"
                   autofocus>
            @error('email')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
            @enderror
          </div>
          <div class="mb-6 form-password-toggle">
            <div class="d-flex justify-content-between">
              <label class="form-label" for="password">{{ __('Password') }}</label>
              <a href="{{ route('password.request') }}">
                <small>{{ __('Forgot Password?') }}</small>
              </a>
            </div>
            <div class="input-group input-group-merge">
              <input type="password"
                     id="password"
                     class="form-control @error('password') is-invalid @enderror"
                     name="password"
                     placeholder="{{ __('Enter your password') }}"
                     aria-describedby="password" />
              <span class="input-group-text cursor-pointer"><i class="ti ti-eye-off"></i></span>
            </div>
            @error('password')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
            @enderror
          </div>
          <div class="mb-6">
            <div class="form-check mb-0 ms-2">
              <input class="form-check-input" type="checkbox" id="remember" name="remember" {{ old('remember') ? 'checked' : '' }}>
              <label class="form-check-label" for="remember">
                {{ __('Remember Me') }}
              </label>
            </div>
          </div>
          <div class="mb-3">
            <button class="btn btn-primary d-grid w-100" type="submit">{{ __('Sign in') }}</button>
          </div>
        </form>

        <div class="divider my-4">
          <div class="divider-text">{{ __('or') }}</div>
        </div>

        <div class="d-grid mb-3">
          <a href="{{ route('login.google') }}" class="btn btn-outline-danger">
            <i class="tf-icons fa-brands fa-google me-2"></i>
            {{ __('Sign in with Google') }}
          </a>
        </div>

        <p class="text-center">
          <span>{{ __('New on our platform?') }}</span>
          <a href="{{ route('register') }}">
            <span>{{ __('Create an account') }}</span>
          </a>
        </p>

        

        
      </div>
    </div>
  </div>
</div>
@endsection
