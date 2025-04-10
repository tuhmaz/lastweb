@extends('layouts.contentNavbarLayout')

@section('title', __('Edit Category'))

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">{{ __('Edit Category') }}: {{ $category->name }}</h5>
        <a href="{{ route('dashboard.categories.index', ['country' => $country]) }}" class="btn btn-secondary">
          <i class="ti ti-arrow-left me-1"></i>{{ __('Back to Categories') }}
        </a>
      </div>
      <div class="card-body">
        @if(session('error'))
          <div class="alert alert-danger alert-dismissible mb-3" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>
        @endif

        <form action="{{ route('dashboard.categories.update', ['category' => $category->id]) }}" method="POST">
          @csrf
          @method('PUT')
          
          <div class="row g-3">
            <div class="col-12">
              <label class="form-label" for="country">{{ __('Country') }} <span class="text-danger">*</span></label>
              <select class="form-select @error('country') is-invalid @enderror" 
                      id="country" 
                      name="country" 
                      required>
                @foreach($countries as $code => $name)
                  <option value="{{ $code }}" {{ old('country', $category->country) == $code ? 'selected' : '' }}>{{ $name }}</option>
                @endforeach
              </select>
              @error('country')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <div class="col-12">
              <label class="form-label" for="name">{{ __('Category Name') }} <span class="text-danger">*</span></label>
              <input type="text" 
                     class="form-control @error('name') is-invalid @enderror" 
                     id="name" 
                     name="name" 
                     value="{{ old('name', $category->name) }}" 
                     required>
              @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <div class="col-12">
              <div class="form-check form-switch">
                <input class="form-check-input" 
                       type="checkbox" 
                       id="is_active" 
                       name="is_active" 
                       value="1" 
                       {{ old('is_active', $category->is_active) ? 'checked' : '' }}>
                <label class="form-check-label" for="is_active">{{ __('Active') }}</label>
              </div>
            </div>

            <div class="col-12">
              <div class="alert alert-info d-flex align-items-center" role="alert">
                <i class="ti ti-info-circle me-2"></i>
                <div>
                  {{ __('Current Slug') }}: <strong>{{ $category->slug }}</strong>
                  <br>
                  <small>{{ __('The slug will be automatically updated based on the category name') }}</small>
                </div>
              </div>
            </div>

            <div class="col-12 text-end">
              <button type="submit" class="btn btn-primary">
                <i class="ti ti-device-floppy me-1"></i>{{ __('Update Category') }}
              </button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
