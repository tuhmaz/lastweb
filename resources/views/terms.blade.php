@php
$configData = Helper::appClasses();
$customizerHidden = 'customizer-hide';
@endphp

@extends('layouts/blankLayout')

@section('title', 'Terms Of Service')

@section('page-style')
{{-- Page Css files --}}
@vite('resources/assets/vendor/scss/pages/page-auth.scss')
@endsection

@section('content')
<div class="position-relative">
  <div class="authentication-wrapper authentication-basic container-p-y p-4 p-sm-0">
    <div class="authentication-inner py-6">
      <!-- Logo -->
      <div class="card p-md-7 p-1">
        <div class="app-brand justify-content-center mt-5">
          <a href="{{url('/')}}" class="app-brand-link gap-2">
            <span class="app-brand-logo demo">@include('_partials.macros',["width"=>25,"withbg"=>'var(--bs-primary)'])</span>
            <span class="app-brand-text demo text-heading fw-semibold">{{config('variables.templateName')}}</span>
          </a>
        </div>
        <!-- /Logo -->
        <div class="card-body mt-1">{!! $terms !!}</div>
      </div>
      <img alt="mask" src="{{asset('assets/img/illustrations/auth-basic-forgot-password-mask-'.$configData['style'].'.png') }}" class="authentication-image d-none d-lg-block" data-app-light-img="illustrations/auth-basic-forgot-password-mask-light.png" data-app-dark-img="illustrations/auth-basic-forgot-password-mask-dark.png" />
    </div>
  </div>
</div>
@endsection
