@php
$customizerHidden = 'customizer-hide';
$configData = Helper::appClasses();
@endphp

@extends('layouts/blankLayout')

@section('title', __('تأكيد البريد الإلكتروني'))

@section('page-style')
<!-- Page -->
@vite('resources/assets/vendor/scss/pages/page-auth.scss')
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
    <span class="app-brand-text" style="color: white;">{{ config('settings.site_name') }}</span>
  </a>
  <!-- /Logo -->
  <div class="authentication-inner row m-0">

    <!-- /Left Text -->
    <div class="d-none d-lg-flex col-lg-8 p-0">
      <div class="auth-cover-bg auth-cover-bg-color d-flex justify-content-center align-items-center">
        <img src="{{ asset('assets/img/illustrations/auth-verify-email-illustration-'.$configData['style'].'.svg') }}" 
             alt="auth-verify-email-cover" 
             class="my-5 auth-illustration" 
             data-app-light-img="illustrations/auth-verify-email-illustration-light.svg" 
             data-app-dark-img="illustrations/auth-verify-email-illustration-dark.svg">

        <img src="{{ asset('assets/img/illustrations/bg-shape-image-'.$configData['style'].'.png') }}" 
             alt="auth-verify-email-cover" 
             class="platform-bg" 
             data-app-light-img="illustrations/bg-shape-image-light.png" 
             data-app-dark-img="illustrations/bg-shape-image-dark.png">
      </div>
    </div>
    <!-- /Left Text -->

    <!-- Verify Email -->
    <div class="d-flex col-12 col-lg-4 align-items-center authentication-bg p-sm-5 p-4">
      <div class="w-px-400 mx-auto">
        
        <div class="text-center mb-4">
          <h3 class="mb-1">{{ __('تأكيد البريد الإلكتروني') }} 🔒</h3>
          <p>{{ __('تم إرسال رابط التفعيل إلى بريدك الإلكتروني') }}</p>
        </div>
        
        <!-- Success Message -->
        @if (session('status') == 'verification-link-sent')
        <div class="alert alert-success alert-dismissible mb-3" role="alert">
          <div class="alert-body">
            {{ __('تم إرسال رابط تحقق جديد إلى عنوان بريدك الإلكتروني.') }}
          </div>
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif
        
        <!-- Error Messages -->
        @if ($errors->any())
        <div class="alert alert-danger alert-dismissible mb-3">
          <ul class="mb-0">
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
          </ul>
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

        <div class="mb-4">
          <span class="fw-semibold">{{ __('البريد الإلكتروني') }}:</span>
          <span class="fst-italic">{{ Auth::user()->email }}</span>
        </div>

        <div class="d-flex flex-column gap-3">
          <!-- Alert Messages -->
          <div id="alert-container"></div>

          <!-- Resend Verification Link -->
          <form method="POST" action="{{ route('verification.send') }}" id="resend-form" class="mb-3">
            @csrf
            <button type="submit" class="btn btn-primary w-100" id="resend-button">
              <i class="ti ti-mail me-2"></i>
              {{ __('إعادة إرسال رابط التفعيل') }}
            </button>
          </form>

          <!-- Logout -->
          <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn btn-label-secondary w-100">
              <i class="ti ti-logout me-2"></i>
              {{ __('تسجيل الخروج') }}
            </button>
          </form>
        </div>
      </div>
    </div>
    <!-- / Verify Email -->
  </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
  const resendButton = document.getElementById('resend-button');
  const resendForm = document.getElementById('resend-form');
  const alertContainer = document.getElementById('alert-container');
  let cooldown = false;
  let cooldownTime = 60; // 60 seconds cooldown

  function showAlert(message, type = 'success') {
    const alertHtml = `
      <div class="alert alert-${type} alert-dismissible fade show" role="alert">
        <div class="alert-body">
          ${message}
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
    `;
    alertContainer.innerHTML = alertHtml;

    // Auto hide after 5 seconds
    setTimeout(() => {
      const alert = alertContainer.querySelector('.alert');
      if (alert) {
        alert.classList.remove('show');
        setTimeout(() => alertContainer.innerHTML = '', 150);
      }
    }, 5000);
  }

  resendForm.addEventListener('submit', function (e) {
    e.preventDefault();

    if (cooldown) {
      return;
    }

    resendButton.disabled = true;
    const originalText = resendButton.innerHTML;
    resendButton.innerHTML = '<i class="ti ti-loader ti-spin me-2"></i>{{ __('جاري الإرسال...') }}';

    // Send AJAX request
    fetch(resendForm.action, {
      method: 'POST',
      headers: {
        'Accept': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
      },
      body: new FormData(resendForm)
    })
    .then(response => response.json())
    .then(data => {
      if (data.status) {
        showAlert(data.message, 'success');
        
        // Start cooldown
        cooldown = true;
        let timeLeft = cooldownTime;

        const interval = setInterval(() => {
          timeLeft--;
          resendButton.innerHTML = `<i class="ti ti-clock me-2"></i>{{ __('انتظر') }} ${timeLeft} {{ __('ثانية') }}`;

          if (timeLeft <= 0) {
            clearInterval(interval);
            cooldown = false;
            resendButton.disabled = false;
            resendButton.innerHTML = originalText;
          }
        }, 1000);
      } else {
        throw new Error(data.message || 'حدث خطأ أثناء إرسال رابط التحقق');
      }
    })
    .catch(error => {
      showAlert(error.message, 'danger');
      resendButton.disabled = false;
      resendButton.innerHTML = originalText;
    });
  });
});
</script>
@endpush
@endsection