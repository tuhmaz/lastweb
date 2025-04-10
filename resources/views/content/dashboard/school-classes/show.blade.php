@php
$configData = Helper::appClasses();
@endphp

@extends('layouts/layoutMaster')

@section('title', __('Class Details'))

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="py-3 mb-4">
        <span class="text-muted fw-light">{{ __('School Classes') }} /</span> {{ __('Class Details') }}
    </h4>

    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-0">{{ $class->grade_name }}</h5>
                        <p class="mb-0 text-muted">{{ __('Grade Level') }}: {{ $class->grade_level }}</p>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('dashboard.school-classes.edit', $class) }}" class="btn btn-primary">
                            <i class="ti ti-edit me-1"></i>{{ __('Edit Class') }}
                        </a>
                        <form action="{{ route('dashboard.school-classes.destroy', $class) }}" 
                              method="POST" 
                              class="d-inline-block"
                              onsubmit="return confirm('{{ __('Are you sure you want to delete this class?') }}');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">
                                <i class="ti ti-trash me-1"></i>{{ __('Delete Class') }}
                            </button>
                        </form>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card shadow-none border mb-4">
                                <div class="card-body">
                                    <h6 class="card-title mb-3">{{ __('Class Information') }}</h6>
                                    <div class="row g-3">
                                        <div class="col-12 col-md-6">
                                            <div class="d-flex flex-column">
                                                <span class="text-muted mb-1">{{ __('Grade Name') }}</span>
                                                <span class="fw-medium">{{ $class->grade_name }}</span>
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-6">
                                            <div class="d-flex flex-column">
                                                <span class="text-muted mb-1">{{ __('Grade Level') }}</span>
                                                <span class="fw-medium">
                                                    <span class="badge bg-label-primary">{{ $class->grade_level }}</span>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-6">
                                            <div class="d-flex flex-column">
                                                <span class="text-muted mb-1">{{ __('Country') }}</span>
                                                <span class="fw-medium">{{ $class->country->name }}</span>
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-6">
                                            <div class="d-flex flex-column">
                                                <span class="text-muted mb-1">{{ __('Created Date') }}</span>
                                                <span class="fw-medium">{{ $class->created_at->format('Y-m-d') }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="card shadow-none bg-transparent border">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <h6 class="card-title mb-0">{{ __('Quick Actions') }}</h6>
                                    </div>
                                    <p class="mb-3 text-muted">{{ __('Manage class-related actions') }}</p>
                                    <div class="d-flex flex-wrap gap-2">
                                        <a href="{{ route('dashboard.subjects.create', ['class_id' => $class->id]) }}" 
                                           class="btn btn-outline-primary">
                                            <i class="ti ti-plus me-1"></i>
                                            {{ __('Add New Subject') }}
                                        </a>
                                        <a href="{{ route('dashboard.school-classes.index') }}" 
                                           class="btn btn-outline-secondary">
                                            <i class="ti ti-arrow-left me-1"></i>
                                            {{ __('Back to Classes') }}
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
