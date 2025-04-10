@php
$customizerHidden = 'customizer-hide';
$configData = Helper::appClasses();
@endphp

@extends('layouts/layoutFront')

@section('title',  __('Unauthorized'))

@section('page-style')
<!-- Page -->
@vite(['resources/assets/vendor/scss/pages/page-misc.scss'])
@endsection


@section('content')
<!-- Error -->
<div class="container-xxl container-p-y">
  <div class="misc-wrapper">
    <h1 class="mb-2 mx-2 fs-xxlarge" style="line-height: 6rem;font-size: 6rem;">401</h1>
    <h4 class="mb-2 mx-2">{{ __('Unauthorized') }} ⚠️</h4>
    <p class="mb-6 mx-2">{{ __('You are not authorized to access this page.') }}</p>
    <a href="{{url('/')}}" class="btn btn-primary mb-10">{{ __('Back to home') }}</a>
    <div class="mt-4">
      <img src="{{ asset('assets/img/illustrations/unauthorized.svg') }}" alt="unauthorized" width="225" class="img-fluid">
    </div>
  </div>
</div>
<div class="container-fluid misc-bg-wrapper">
  <img src="{{ asset('assets/img/illustrations/bg-shape-image-'.$configData['style'].'.png') }}" height="355" alt="page-misc-error" data-app-light-img="illustrations/bg-shape-image-light.png" data-app-dark-img="illustrations/bg-shape-image-dark.png">
</div>
<!-- /Error -->
@endsection
