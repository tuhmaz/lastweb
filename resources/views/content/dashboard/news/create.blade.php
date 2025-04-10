@extends('layouts.contentNavbarLayout')

@section('title', __('Create News'))

@section('vendor-style')
@vite([
  'resources/assets/vendor/libs/summernote/summernote.scss'
])
@endsection

@section('vendor-script')
@vite([
  'resources/assets/vendor/libs/summernote/summernote.js',
  'resources/assets/js/forms-editors.js'
])
@endsection

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">{{ __('Create New News') }}</h5>
        <a href="{{ route('dashboard.news.index', ['country' => $country]) }}" class="btn btn-secondary">
          <i class="ti ti-arrow-left me-1"></i>{{ __('Back to News') }}
        </a>
      </div>

      <div class="card-body">
        @if(session('success'))
          <div class="alert alert-success alert-dismissible mb-3" role="alert">
            <div class="d-flex gap-2 align-items-center">
              <i class="ti ti-check"></i>
              {{ session('success') }}
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>
        @endif

        @if(session('error'))
          <div class="alert alert-danger alert-dismissible mb-3" role="alert">
            <div class="d-flex gap-2 align-items-center">
              <i class="ti ti-alert-triangle"></i>
              {{ session('error') }}
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>
        @endif

        <form action="{{ route('dashboard.news.store', ['country' => $country]) }}" method="POST" enctype="multipart/form-data">
          @csrf

          <div class="row">
            <!-- البلد -->
            <div class="col-md-6 mb-3">
              <label class="form-label">{{ __('Country') }}</label>
              <select class="form-select" disabled>
                <option>{{ $countries[$country] }}</option>
              </select>
              <input type="hidden" name="country" value="{{ $country }}">
            </div>

            <!-- الفئة -->
            <div class="col-md-6 mb-3">
              <label class="form-label" for="category_id">{{ __('Category') }} <span class="text-danger">*</span></label>
              <select class="form-select @error('category_id') is-invalid @enderror" id="category_id" name="category_id" required>
                <option value="">{{ __('Select Category') }}</option>
                @foreach($categories as $category)
                  <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                    {{ $category->name }}
                  </option>
                @endforeach
              </select>
              @error('category_id')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <!-- العنوان -->
            <div class="col-12 mb-3">
              <label class="form-label" for="title">{{ __('Title') }} <span class="text-danger">*</span></label>
              <input type="text"
                     class="form-control @error('title') is-invalid @enderror"
                     id="title"
                     name="title"
                     value="{{ old('title') }}"
                     required
                     maxlength="255"
                     placeholder="{{ __('Enter news title') }}">
              @error('title')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <!-- المحتوى -->
            <div class="col-12 mb-3">
              <label class="form-label" for="content">{{ __('Content') }} <span class="text-danger">*</span></label>
              <textarea id="summernote"
                        name="content"
                        class="form-control @error('content') is-invalid @enderror"
                        required>{{ old('content') }}</textarea>
              @error('content')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <!-- الصورة -->
            <div class="col-md-12 mb-3">
              <label class="form-label" for="image">{{ __('Image') }} <span class="text-danger">*</span></label>
              <div class="input-group">
                <input type="file"
                       class="form-control @error('image') is-invalid @enderror"
                       id="image"
                       name="image"
                       accept="image/*"
                       required>
                <label class="input-group-text" for="image">
                  <i class="ti ti-photo-up"></i>
                </label>
              </div>
              <small class="text-muted">{{ __('Allowed formats: jpeg, png, jpg, gif. Max size: 2MB') }}</small>
              @error('image')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <!-- النص البديل للصورة -->
            <div class="col-md-12 mb-3">
              <label class="form-label" for="alt">{{ __('Image Alt Text') }}</label>
              <input type="text"
                     class="form-control @error('alt') is-invalid @enderror"
                     id="alt"
                     name="alt"
                     value="{{ old('alt') }}"
                     placeholder="{{ __('Enter alternative text for the image') }}">
              <small class="text-muted">{{ __('Important for SEO and accessibility') }}</small>
              @error('alt')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <!-- الكلمات المفتاحية -->
            <div class="col-md-6 mb-3">
              <label class="form-label" for="keywords">{{ __('Keywords') }}</label>
              <input type="text"
                     class="form-control @error('keywords') is-invalid @enderror"
                     id="keywords"
                     name="keywords"
                     value="{{ old('keywords') }}"
                     placeholder="{{ __('Enter keywords separated by commas') }}">
              <small class="text-muted">{{ __('Used for SEO purposes') }}</small>
              @error('keywords')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <!-- الوصف -->
            <div class="col-md-6 mb-3">
              <label class="form-label" for="meta_description">{{ __('Meta Description') }}</label>
              <textarea class="form-control @error('meta_description') is-invalid @enderror"
                        id="meta_description"
                        name="meta_description"
                        rows="1"
                        maxlength="255"
                        placeholder="{{ __('Enter meta description for SEO') }}">{{ old('meta_description') }}</textarea>
              @error('meta_description')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <!-- الحالة والتمييز -->
            <div class="col-md-6 mb-3">
              <div class="form-check form-switch mb-2">
                <input type="checkbox"
                       class="form-check-input"
                       id="is_active"
                       name="is_active"
                       value="1"
                       {{ old('is_active', true) ? 'checked' : '' }}>
                <label class="form-check-label" for="is_active">{{ __('Active') }}</label>
              </div>

              <div class="form-check form-switch">
                <input type="checkbox"
                       class="form-check-input"
                       id="is_featured"
                       name="is_featured"
                       value="1"
                       {{ old('is_featured') ? 'checked' : '' }}>
                <label class="form-check-label" for="is_featured">{{ __('Featured') }}</label>
              </div>
            </div>
          </div>

          <div class="row mt-3">
            <div class="col-12">
              <button type="submit" class="btn btn-primary me-sm-3 me-1">
                <i class="ti ti-device-floppy me-1"></i>{{ __('Save News') }}
              </button>
              <a href="{{ route('dashboard.news.index', ['country' => $country]) }}" class="btn btn-label-secondary">
                {{ __('Cancel') }}
              </a>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

@push('page-scripts')
<script>
$(document).ready(function() {
  // تهيئة محرر النصوص
  $('#summernote').summernote({
    height: 300,
    toolbar: [
      ['style', ['style']],
      ['font', ['bold', 'underline', 'clear']],
      ['color', ['color']],
      ['para', ['ul', 'ol', 'paragraph']],
      ['table', ['table']],
      ['insert', ['link', 'picture']],
      ['view', ['fullscreen', 'codeview', 'help']]
    ],
    callbacks: {
      onImageUpload: function(files) {
        // يمكنك إضافة كود لرفع الصور هنا
      }
    }
  });

  // معاينة الصورة قبل الرفع
  $('#image').change(function() {
    const file = this.files[0];
    if (file) {
      const reader = new FileReader();
      reader.onload = function(e) {
        // يمكنك إضافة كود لعرض معاينة الصورة هنا
      }
      reader.readAsDataURL(file);
    }
  });
});
</script>
@endpush

@endsection
