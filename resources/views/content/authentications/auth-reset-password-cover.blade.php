@php
$customizerHidden = 'customizer-hide';
$configData = Helper::appClasses();
@endphp

@extends('layouts/blankLayout')

@section('title', 'Reset Password Cover - Pages')

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
  <!-- Logo -->
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
  <!-- /Logo -->
  <div class="authentication-inner row m-0">

    <!-- /Left Text -->
    <div class="d-none d-lg-flex col-lg-8 p-0">
      <div class="auth-cover-bg auth-cover-bg-color d-flex justify-content-center align-items-center">
        <img src="{{ asset('assets/img/illustrations/auth-reset-password-illustration-'.$configData['style'].'.svg') }}" alt="auth-reset-password-cover" class="my-5 auth-illustration" data-app-light-img="illustrations/auth-reset-password-illustration-light.svg" data-app-dark-img="illustrations/auth-reset-password-illustration-dark.svg">

        <img src="{{ asset('assets/img/illustrations/bg-shape-image-'.$configData['style'].'.png') }}" alt="auth-reset-password-cover" class="platform-bg" data-app-light-img="illustrations/bg-shape-image-light.png" data-app-dark-img="illustrations/bg-shape-image-dark.png">
      </div>
    </div>
    <!-- /Left Text -->

    <!-- Reset Password -->
    <div class="d-flex col-12 col-lg-4 align-items-center authentication-bg p-6 p-sm-12">
      <div class="w-px-400 mx-auto mt-12 pt-5">
        <h4 class="mb-1">{{ __('Reset Password') }} 🔒</h4>
        <p class="mb-6"><span class="fw-medium">{{ __('Your new password must be different from previously used passwords') }}</span></p>
        <form id="formAuthentication" class="mb-6" action="{{url('login')}}" method="GET">
          <div class="mb-6 form-password-toggle">
            <label class="form-label" for="password">{{ __('New Password') }}</label>
            <div class="input-group input-group-merge">
              <input type="password" id="password" class="form-control" name="password" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" aria-describedby="password" />
              <span class="input-group-text cursor-pointer"><i class="ti ti-eye-off"></i></span>
            </div>
          </div>
          <div class="mb-6 form-password-toggle">
            <label class="form-label" for="confirm-password">{{ __('Confirm Password') }}</label>
            <div class="input-group input-group-merge">
              <input type="password" id="confirm-password" class="form-control" name="confirm-password" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" aria-describedby="password" />
              <span class="input-group-text cursor-pointer"><i class="ti ti-eye-off"></i></span>
            </div>
          </div>
          <button class="btn btn-primary d-grid w-100 mb-6">
             {{ __('Set new password') }}
          </button>
          <div class="text-center">
            <a href="{{url('login')}}">
              <i class="ti ti-chevron-left scaleX-n1-rtl me-1_5"></i>
               {{ __('Back to login') }}
            </a>
          </div>
        </form>
      </div>
    </div>
    <!-- /Reset Password -->
  </div>
</div>
@endsection
