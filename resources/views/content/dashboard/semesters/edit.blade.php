@extends('layouts.contentNavbarLayout')

@section('title', __('Edit Semester'))

@section('content')
<h4 class="py-3 mb-4">
  <span class="text-muted fw-light">{{ __('Semesters') }} /</span> {{ __('Edit') }}
</h4>

<div class="row">
  <div class="col-md-12">
    <div class="card mb-4">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">{{ __('Edit Semester') }}</h5>
        <a href="{{ route('dashboard.semesters.index', ['country' => $country]) }}" class="btn btn-secondary">
          <i class="ti ti-arrow-left me-1"></i>
          {{ __('Back to List') }}
        </a>
      </div>
      <div class="card-body">
        <form action="{{ route('dashboard.semesters.update', ['semester' => $semester->id, 'country' => $country]) }}" method="POST">
          @csrf
          @method('PUT')
          
          <div class="mb-3">
            <label class="form-label" for="semester_name">{{ __('Semester Name') }}</label>
            <input type="text" 
                   class="form-control @error('semester_name') is-invalid @enderror" 
                   id="semester_name" 
                   name="semester_name" 
                   value="{{ old('semester_name', $semester->semester_name) }}"
                   placeholder="{{ __('Enter semester name') }}" />
            @error('semester_name')
              <div class="invalid-feedback">
                {{ $message }}
              </div>
            @enderror
          </div>

          <div class="mb-3">
            <label class="form-label" for="grade_level">{{ __('Grade Level') }}</label>
            <select class="form-select @error('grade_level') is-invalid @enderror" 
                    id="grade_level" 
                    name="grade_level">
              <option value="">{{ __('Select Grade Level') }}</option>
              @foreach($classes as $class)
                <option value="{{ $class->id }}" 
                        {{ old('grade_level', $semester->grade_level) == $class->id ? 'selected' : '' }}>
                  {{ $class->grade_name }}
                </option>
              @endforeach
            </select>
            @error('grade_level')
              <div class="invalid-feedback">
                {{ $message }}
              </div>
            @enderror
          </div>

          <button type="submit" class="btn btn-primary">
            <i class="ti ti-device-floppy me-1"></i>
            {{ __('Update Semester') }}
          </button>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
