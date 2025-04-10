@php
$configData = Helper::appClasses();
$class = App\Models\SchoolClass::where('grade_level', $subject->grade_level)->first();
@endphp

@extends('layouts/layoutMaster')

@section('title', __('Subject Details'))

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <!-- رأس الصفحة -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold py-3 mb-0">{{ __('Subject Details') }}</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{ route('dashboard.subjects.index') }}">{{ __('Subjects') }}</a>
                    </li>
                    <li class="breadcrumb-item active">{{ __('Subject Details') }}</li>
                </ol>
            </nav>
        </div>
    </div>

    <!-- تفاصيل المادة -->
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header d-flex align-items-center">
                    <div class="card-title mb-0">
                        <i class="ti ti-book me-2"></i>
                        {{ __('Subject Information') }}
                    </div>
                </div>
                
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="form-floating form-floating-outline">
                                <input type="text" 
                                       class="form-control" 
                                       id="subject_name" 
                                       value="{{ $subject->subject_name }}"
                                       readonly>
                                <label for="subject_name">{{ __('Subject Name') }}</label>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-floating form-floating-outline">
                                <input type="text" 
                                       class="form-control" 
                                       id="grade_level" 
                                       value="{{ $class ? $class->grade_name : '-' }}"
                                       readonly>
                                <label for="grade_level">{{ __('Grade Level') }}</label>
                            </div>
                        </div>

                        <!-- معلومات إضافية -->
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body">
                                    <h6 class="mb-3">{{ __('Additional Information') }}</h6>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <p class="mb-2">
                                                <strong>{{ __('Created At') }}:</strong>
                                                <span>{{ $subject->created_at ? $subject->created_at->format('Y-m-d H:i') : '-' }}</span>
                                            </p>
                                            <p class="mb-2">
                                                <strong>{{ __('Created By') }}:</strong>
                                                <span>{{ $subject->created_by && $subject->creator ? $subject->creator->name : '-' }}</span>
                                            </p>
                                        </div>
                                        <div class="col-md-6">
                                            <p class="mb-2">
                                                <strong>{{ __('Last Updated') }}:</strong>
                                                <span>{{ $subject->updated_at ? $subject->updated_at->format('Y-m-d H:i') : '-' }}</span>
                                            </p>
                                            <p class="mb-2">
                                                <strong>{{ __('Updated By') }}:</strong>
                                                <span>{{ $subject->updated_by && $subject->updater ? $subject->updater->name : '-' }}</span>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 text-end">
                        <a href="{{ route('dashboard.subjects.index') }}" class="btn btn-label-secondary me-1">
                            <i class="ti ti-arrow-left me-1"></i>
                            <span>{{ __('Back') }}</span>
                        </a>
                        <a href="{{ route('dashboard.subjects.edit', $subject->id) }}" class="btn btn-primary">
                            <i class="ti ti-edit me-1"></i>
                            <span>{{ __('Edit Subject') }}</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
