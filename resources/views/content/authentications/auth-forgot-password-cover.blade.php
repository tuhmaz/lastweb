@php
$customizerHidden = 'customizer-hide';
$configData = Helper::appClasses();
@endphp

@extends('layouts/blankLayout')

@section('title', 'Forgot Password')

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
<span class="app-brand-text"style="color: white;">{{ config('settings.site_name') }}</span>
  </a>
  <div class="authentication-inner row m-0">
    <div class="d-none d-lg-flex col-lg-8 p-0">
      <div class="auth-cover-bg auth-cover-bg-color d-flex justify-content-center align-items-center">
        <img src="{{ asset('assets/img/illustrations/auth-forgot-password-illustration-'.$configData['style'].'.webp') }}" alt="auth-forgot-password-cover" class="my-5 auth-illustration d-lg-block d-none" data-app-light-img="illustrations/auth-forgot-password-illustration-light.webp" data-app-dark-img="illustrations/auth-forgot-password-illustration-dark.webp">

        <img src="{{ asset('assets/img/illustrations/bg-shape-image-'.$configData['style'].'.png') }}" alt="auth-forgot-password-cover" class="platform-bg" data-app-light-img="illustrations/bg-shape-image-light.png" data-app-dark-img="illustrations/bg-shape-image-dark.png">
      </div>
    </div>

    <div class="d-flex col-12 col-lg-4 align-items-center authentication-bg p-sm-12 p-6">
      <div class="w-px-400 mx-auto mt-12 pt-5">
        <h4 class="mb-1">{{ __('Forgot Password?') }} 🔒 </h4>
        <p class="mb-6">{{ __('Enter your email and we\'ll send you instructions to reset your password') }}</p>

        @if(session('status'))
        <div class="alert alert-success" role="alert">
            {{ session('status') }}
        </div>
        @endif

        <form id="formAuthentication" class="mb-6" action="{{ route('password.email') }}" method="POST">
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
          <button type="submit" class="btn btn-primary d-grid w-100">{{ __('Send Reset Link') }}</button>
        </form>

        <div class="text-center">
          <a href="{{ route('login') }}" class="d-flex align-items-center justify-content-center">
            <i class="ti ti-chevron-left scaleX-n1-rtl"></i>
            {{ __('Back to login') }}
          </a>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
