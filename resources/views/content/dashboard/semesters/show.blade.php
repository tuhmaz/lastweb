@extends('layouts.contentNavbarLayout')

@section('title', __('Semester Details'))

@section('content')
<h4 class="py-3 mb-4">
  <span class="text-muted fw-light">{{ __('Semesters') }} /</span> {{ __('Details') }}
</h4>

<div class="row">
  <div class="col-md-12">
    <div class="card mb-4">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">{{ __('Semester Information') }}</h5>
        <div class="d-flex gap-2">
          <a href="{{ route('dashboard.semesters.edit', ['semester' => $semester->id, 'country' => $country]) }}" 
             class="btn btn-warning">
            <i class="ti ti-pencil me-1"></i>
            {{ __('Edit') }}
          </a>
          <a href="{{ route('dashboard.semesters.index', ['country' => $country]) }}" class="btn btn-secondary">
            <i class="ti ti-arrow-left me-1"></i>
            {{ __('Back to List') }}
          </a>
        </div>
      </div>
      
      <div class="card-body">
        <div class="row mb-3">
          <div class="col-md-3">
            <strong>{{ __('Semester Name') }}:</strong>
          </div>
          <div class="col-md-9">
            {{ $semester->semester_name }}
          </div>
        </div>

        <div class="row mb-3">
          <div class="col-md-3">
            <strong>{{ __('Grade Level') }}:</strong>
          </div>
          <div class="col-md-9">
            {{ $semester->schoolClass->grade_name }}
          </div>
        </div>

        <div class="row mb-3">
          <div class="col-md-3">
            <strong>{{ __('Created At') }}:</strong>
          </div>
          <div class="col-md-9">
            {{ $semester->created_at->format('F j, Y H:i:s') }}
          </div>
        </div>

        <div class="row">
          <div class="col-md-3">
            <strong>{{ __('Last Updated') }}:</strong>
          </div>
          <div class="col-md-9">
            {{ $semester->updated_at->format('F j, Y H:i:s') }}
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
