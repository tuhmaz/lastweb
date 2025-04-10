@php
$configData = Helper::appClasses();
@endphp

@extends('layouts/layoutMaster')

@section('title', __('Edit Subject'))

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <!-- رأس الصفحة -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold py-3 mb-0">{{ __('Edit Subject') }}</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{ route('dashboard.subjects.index') }}">{{ __('Subjects') }}</a>
                    </li>
                    <li class="breadcrumb-item active">{{ __('Edit Subject') }}</li>
                </ol>
            </nav>
        </div>
    </div>

    <!-- نموذج التعديل -->
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header d-flex align-items-center">
                    <div class="card-title mb-0">
                        <i class="ti ti-edit me-2"></i>
                        {{ __('Subject Information') }}
                    </div>
                </div>
                
                <div class="card-body">
                    <form action="{{ route('dashboard.subjects.update', $subject->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row g-4">
                            <div class="col-md-6">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" 
                                           class="form-control @error('subject_name') is-invalid @enderror" 
                                           id="subject_name" 
                                           name="subject_name" 
                                           value="{{ old('subject_name', $subject->subject_name) }}"
                                           placeholder="{{ __('Enter subject name') }}"
                                           required>
                                    <label for="subject_name">{{ __('Subject Name') }}</label>
                                    @error('subject_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-floating form-floating-outline">
                                    <select class="form-select @error('grade_level') is-invalid @enderror" 
                                            id="grade_level" 
                                            name="grade_level" 
                                            required>
                                        <option value="">{{ __('Select Grade Level') }}</option>
                                        @foreach($classes as $class)
                                            <option value="{{ $class->grade_level }}" 
                                                {{ old('grade_level', $subject->grade_level) == $class->grade_level ? 'selected' : '' }}>
                                                {{ $class->grade_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <label for="grade_level">{{ __('Grade Level') }}</label>
                                    @error('grade_level')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mt-4 text-end">
                            <a href="{{ route('dashboard.subjects.index') }}" class="btn btn-label-secondary me-1">
                                <i class="ti ti-arrow-left me-1"></i>
                                <span>{{ __('Back') }}</span>
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="ti ti-device-floppy me-1"></i>
                                <span>{{ __('Save Changes') }}</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
