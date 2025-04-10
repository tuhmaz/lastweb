@php
$configData = Helper::appClasses();
@endphp

@extends('layouts/layoutMaster')

@section('title', __('Edit School Class'))

@section('vendor-style')
@vite([
    'resources/assets/vendor/libs/select2/select2.scss'
])
@endsection

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="py-3 mb-4">
        <span class="text-muted fw-light">{{ __('School Classes') }} /</span> {{ __('Edit Class') }}
    </h4>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">{{ __('Edit Class Details') }}</h5>
        </div>

        <div class="card-body">
            <form method="POST" action="{{ route('dashboard.school-classes.update', $class) }}">
                @csrf
                @method('PUT')

                <div class="row g-3">
                    <!-- اختيار الدولة -->
                    <div class="col-12">
                        <label class="form-label" for="country_id">{{ __('Country') }} <span class="text-danger">*</span></label>
                        <select name="country_id" id="country_id" class="select2 form-select @error('country_id') is-invalid @enderror" required>
                            <option value="">{{ __('Select Country') }}</option>
                            @foreach($countries as $country)
                                <option value="{{ $country->id }}" {{ old('country_id', $class->country_id) == $country->id ? 'selected' : '' }}>
                                    {{ $country->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('country_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- اسم الصف -->
                    <div class="col-12">
                        <label class="form-label" for="grade_name">{{ __('Grade Name') }} <span class="text-danger">*</span></label>
                        <input type="text"
                               id="grade_name"
                               name="grade_name"
                               class="form-control @error('grade_name') is-invalid @enderror"
                               value="{{ old('grade_name', $class->grade_name) }}"
                               placeholder="{{ __('Enter grade name') }}"
                               required>
                        @error('grade_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- مستوى الصف -->
                    <div class="col-12">
                        <label class="form-label" for="grade_level">{{ __('Grade Level') }} <span class="text-danger">*</span></label>
                        <input type="number"
                               id="grade_level"
                               name="grade_level"
                               class="form-control @error('grade_level') is-invalid @enderror"
                               value="{{ old('grade_level', $class->grade_level) }}"
                               placeholder="{{ __('Enter grade level') }}"
                               min="1"
                               required>
                        @error('grade_level')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- أزرار التحكم -->
                    <div class="col-12 mt-4">
                        <button type="submit" class="btn btn-primary me-sm-3 me-1">{{ __('Update Class') }}</button>
                        <a href="{{ route('dashboard.school-classes.show', $class) }}" class="btn btn-label-secondary">{{ __('Cancel') }}</a>
                    </div>
                </div>
            </form>
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
 
@endsection
