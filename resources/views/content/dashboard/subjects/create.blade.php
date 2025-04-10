@php
$configData = Helper::appClasses();
@endphp

@extends('layouts/layoutMaster')

@section('title', __('Create New Subject'))

@section('vendor-style')
@vite([
    'resources/assets/vendor/libs/select2/select2.scss'
])
@endsection

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="py-3 mb-4">
        <span class="text-muted fw-light">{{ __('Subjects') }} /</span> {{ __('Create Subject') }}
    </h4>

    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">{{ __('Add New Subject') }}</h5>
                    <a href="{{ route('dashboard.subjects.index') }}" class="btn btn-secondary">
                        <i class="bx bx-arrow-back me-1"></i>{{ __('Back to Subjects') }}
                    </a>
                </div>

                <div class="card-body">
                    <form method="POST" action="{{ route('dashboard.subjects.store') }}" class="row g-3">
                        @csrf

                        <!-- الصف الدراسي -->
                        <div class="col-md-6">
                            <label class="form-label" for="grade_level">{{ __('Grade Level') }} <span class="text-danger">*</span></label>
                            <select name="grade_level" id="grade_level" class="select2 form-select @error('grade_level') is-invalid @enderror" required>
                                <option value="">{{ __('Select Grade Level') }}</option>
                                @foreach($classes as $class)
                                    <option value="{{ $class->grade_level }}" 
                                            {{ old('grade_level') == $class->grade_level ? 'selected' : '' }}
                                            data-country="{{ $class->country->name }}">
                                        {{ $class->grade_name }} - {{ $class->country->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('grade_level')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">{{ __('Select the grade level for this subject') }}</small>
                        </div>

                        <!-- اسم المادة -->
                        <div class="col-md-6">
                            <label class="form-label" for="subject_name">{{ __('Subject Name') }} <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bx bx-book"></i></span>
                                <input type="text" 
                                       id="subject_name" 
                                       name="subject_name" 
                                       class="form-control @error('subject_name') is-invalid @enderror" 
                                       value="{{ old('subject_name') }}"
                                       placeholder="{{ __('Enter subject name') }}"
                                       required>
                            </div>
                            @error('subject_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">{{ __('Enter a unique name for the subject') }}</small>
                        </div>

                        <!-- أزرار التحكم -->
                        <div class="col-12 mt-4">
                            <div class="demo-inline-spacing">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bx bx-save me-1"></i>{{ __('Create Subject') }}
                                </button>
                                <a href="{{ route('dashboard.subjects.index') }}" class="btn btn-label-secondary">
                                    <i class="bx bx-x me-1"></i>{{ __('Cancel') }}
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('vendor-script')
@vite([
    'resources/assets/vendor/libs/select2/select2.js'
])
@endsection

@section('page-script')
<script>
$(function() {
    // تهيئة Select2
    $('.select2').select2({
        theme: 'bootstrap-5',
        width: '100%',
        placeholder: "{{ __('Select an option') }}"
    });

    // تحسين تجربة المستخدم
    $('#subject_name').focus();
});</script>
@endsection
