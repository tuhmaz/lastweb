@extends('layouts.contentNavbarLayout')

@section('title', __('File Details'))

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">{{ __('File Details') }}</h5>
        <div>
          <a href="{{ route('dashboard.files.download', ['file' => $file->id, 'country' => $country]) }}" 
             class="btn btn-primary">
            <i class="ti ti-download me-1"></i>{{ __('Download File') }}
          </a>
          <a href="{{ route('dashboard.files.index', ['country' => $country]) }}" 
             class="btn btn-secondary">
            <i class="ti ti-arrow-left me-1"></i>{{ __('Back to List') }}
          </a>
        </div>
      </div>
      
      <div class="card-body">
        <div class="row">
          <!-- معلومات الملف -->
          <div class="col-md-6">
            <div class="card bg-light border-0 h-100">
              <div class="card-body">
                <h6 class="card-subtitle mb-3 text-muted">{{ __('File Information') }}</h6>
                <div class="mb-3">
                  <div class="d-flex align-items-center mb-2">
                    @php
                      $iconClass = match($file->file_type) {
                        'pdf' => 'ti-file-text text-danger',
                        'doc', 'docx' => 'ti-file-description text-primary',
                        'xls', 'xlsx' => 'ti-file-spreadsheet text-success',
                        'jpg', 'jpeg', 'png', 'gif' => 'ti-photo text-info',
                        default => 'ti-file text-secondary'
                      };
                    @endphp
                    <i class="ti {{ $iconClass }} me-2" style="font-size: 2rem;"></i>
                    <h5 class="mb-0">{{ $file->file_Name }}</h5>
                  </div>
                  <span class="badge bg-label-{{ match($file->file_category) {
                    'plans' => 'success',
                    'papers' => 'info',
                    'tests' => 'warning',
                    'books' => 'primary',
                    default => 'secondary'
                  } }}">
                    {{ match($file->file_category) {
                      'plans' => __('Plans'),
                      'papers' => __('Papers'),
                      'tests' => __('Tests'),
                      'books' => __('Books'),
                      default => __('Other')
                    } }}
                  </span>
                </div>
                
                <dl class="row mb-0">
                  <dt class="col-sm-4">{{ __('File Type') }}</dt>
                  <dd class="col-sm-8">{{ strtoupper($file->file_type) }}</dd>
                  
                  <dt class="col-sm-4">{{ __('Upload Date') }}</dt>
                  <dd class="col-sm-8">{{ $file->created_at->format('Y-m-d H:i') }}</dd>
                  
                  <dt class="col-sm-4">{{ __('Last Update') }}</dt>
                  <dd class="col-sm-8">{{ $file->updated_at->format('Y-m-d H:i') }}</dd>
                </dl>
              </div>
            </div>
          </div>

          <!-- معلومات المقالة المرتبطة -->
          <div class="col-md-6">
            <div class="card bg-light border-0 h-100">
              <div class="card-body">
                <h6 class="card-subtitle mb-3 text-muted">{{ __('Related Article') }}</h6>
                @if($file->article)
                  <h5 class="mb-3">{{ $file->article->title }}</h5>
                  <dl class="row mb-0">
                    <dt class="col-sm-4">{{ __('Class') }}</dt>
                    <dd class="col-sm-8">{{ $file->article->schoolClass->grade_name ?? __('N/A') }}</dd>
                    
                    <dt class="col-sm-4">{{ __('Subject') }}</dt>
                    <dd class="col-sm-8">{{ $file->article->subject->subject_name ?? __('N/A') }}</dd>
                    
                    <dt class="col-sm-4">{{ __('Semester') }}</dt>
                    <dd class="col-sm-8">{{ $file->article->semester->semester_name ?? __('N/A') }}</dd>
                  </dl>
                  
                  <div class="mt-3">
                    <a href="{{ route('dashboard.articles.show', ['article' => $file->article->id, 'country' => $country]) }}" 
                       class="btn btn-outline-primary btn-sm">
                      <i class="ti ti-article me-1"></i>{{ __('View Article') }}
                    </a>
                  </div>
                @else
                  <div class="text-center py-4">
                    <i class="ti ti-article-off mb-2" style="font-size: 3rem;"></i>
                    <p class="mb-0">{{ __('No article associated with this file') }}</p>
                  </div>
                @endif
              </div>
            </div>
          </div>
        </div>

        <!-- معاينة الملف -->
        @if(in_array($file->file_type, ['jpg', 'jpeg', 'png', 'gif', 'pdf']))
        <div class="row mt-4">
          <div class="col-12">
            <div class="card">
              <div class="card-header">
                <h6 class="mb-0">{{ __('File Preview') }}</h6>
              </div>
              <div class="card-body text-center">
                @php
                  $fileUrl = Storage::url($file->file_path);
                @endphp
                @if(in_array($file->file_type, ['jpg', 'jpeg', 'png', 'gif']))
                  <img src="{{ $fileUrl }}" 
                       class="img-fluid" 
                       alt="{{ $file->file_Name }}"
                       style="max-height: 600px; width: auto;">
                @elseif($file->file_type === 'pdf')
                  <div class="ratio ratio-16x9" style="height: 600px;">
                    <iframe src="{{ $fileUrl }}" 
                            type="application/pdf" 
                            width="100%" 
                            height="100%"
                            class="rounded">
                      <p>{{ __('Your browser does not support PDF preview.') }} 
                         <a href="{{ route('dashboard.files.download', ['file' => $file->id, 'country' => $country]) }}">
                           {{ __('Click here to download') }}
                         </a>
                      </p>
                    </iframe>
                  </div>
                @endif
              </div>
            </div>
          </div>
        </div>
        @endif
      </div>
    </div>
  </div>
</div>
@endsection
