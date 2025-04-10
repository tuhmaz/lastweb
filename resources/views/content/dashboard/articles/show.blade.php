@extends('layouts.contentNavbarLayout')

@section('title', $article->title)

@section('vendor-style')
@vite([
  'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss',
  'resources/assets/vendor/libs/quill/typography.scss',
  'resources/assets/vendor/libs/quill/katex.scss',
  'resources/assets/vendor/libs/quill/editor.scss'
])
@endsection

@section('page-style')
@vite(['resources/assets/css/articles/article-details.css'])
@endsection

@section('content')
<div class="article-container">
  <!-- Article Header -->
  <div class="article-header">
    <div class="d-flex justify-content-between align-items-center flex-wrap">
      <div>
        <h1 class="article-title">{{ $article->title }}</h1>
        <div class="article-meta">
          <span><i class="ti ti-calendar me-1"></i>{{ $article->created_at->format('Y-m-d H:i') }}</span>
          <span><i class="ti ti-bookmark me-1"></i>{{ $article->subject->subject_name ?? __('Not Set') }}</span>
          <span><i class="ti ti-school me-1"></i>{{ $article->schoolClass->grade_name ?? __('Not Set') }}</span>
        </div>
      </div>
      <div class="action-buttons">
        @if(!$article->status)
        <button type="button" class="action-btn btn btn-success publish-btn" data-id="{{ $article->id }}" data-action="publish">
          <i class="ti ti-check"></i>{{ __('Publish') }}
        </button>
        @else
        <button type="button" class="action-btn btn btn-warning unpublish-btn" data-id="{{ $article->id }}" data-action="unpublish">
          <i class="ti ti-x"></i>{{ __('Unpublish') }}
        </button>
        @endif

        <a href="{{ route('dashboard.articles.edit', ['article' => $article->id, 'country' => $country]) }}"
           class="action-btn btn btn-primary">
          <i class="ti ti-edit"></i>{{ __('Edit') }}
        </a>

        <button type="button" class="action-btn btn btn-label-secondary print-article">
          <i class="ti ti-printer"></i>{{ __('Print') }}
        </button>

        <button type="button" class="action-btn btn btn-label-primary share-article">
          <i class="ti ti-share"></i>{{ __('Share') }}
        </button>

        <form action="{{ route('dashboard.articles.destroy', ['article' => $article->id, 'country' => $country]) }}"
              method="POST"
              class="delete-form d-inline"
              data-confirm="{{ __('Are you sure you want to delete this article?') }}">
          @csrf
          @method('DELETE')
          <button type="submit" class="action-btn btn btn-danger">
            <i class="ti ti-trash"></i>{{ __('Delete') }}
          </button>
        </form>
      </div>
    </div>
  </div>

  <div class="row g-4">
    <!-- Main Content -->
    <div class="col-lg-8">
      <div class="article-content">
        @if($article->image)
        <div class="text-center mb-4">
          <img src="{{ Storage::url($article->image) }}"
               alt="{{ $article->title }}"
               class="article-main-image">
        </div>
        @endif

        <div class="ql-content">
          {!! $contentWithKeywords !!}
        </div>

        <!-- Attachments -->
        @if($article->files->count() > 0)
        <div class="attachments-section">
          <h5 class="mb-3">{{ __('Attachments') }}</h5>
          @foreach($article->files as $file)
          <div class="attachment-card">
            <div class="attachment-icon">
              <i class="ti ti-file"></i>
            </div>
            <div class="attachment-info">
              <div class="attachment-name">{{ $file->file_name }}</div>
              <div class="attachment-meta">
                <span class="badge bg-label-{{ $file->file_category == 'plans' ? 'primary' : ($file->file_category == 'papers' ? 'info' : ($file->file_category == 'tests' ? 'warning' : 'success')) }}">
                  {{ __(ucfirst($file->file_category)) }}
                </span>
                <span class="ms-2">{{ number_format($file->file_size / 1024, 2) }} KB</span>
              </div>
            </div>
            <div class="attachment-actions">
              <a href="{{ asset('storage/' . $file->file_path) }}"
                 class="btn btn-sm btn-label-primary attachment-download"
                 data-file-id="{{ $file->id }}"
                 target="_blank"
                 title="{{ __('Download') }}">
                <i class="ti ti-download"></i>
              </a>
              <button type="button"
                      class="btn btn-sm btn-label-secondary"
                      onclick="navigator.clipboard.writeText('{{ asset('storage/' . $file->file_path) }}')"
                      title="{{ __('Copy Link') }}">
                <i class="ti ti-link"></i>
              </button>
            </div>
          </div>
          @endforeach
        </div>
        @endif
      </div>
    </div>

    <!-- Sidebar -->
    <div class="col-lg-4">
      <div class="article-sidebar">
        <!-- Article Details -->
        <div class="card article-details mb-4">
          <div class="card-body">
            <h5 class="card-title mb-3">{{ __('Article Details') }}</h5>

            <dl class="row mb-0">
              <dt class="col-sm-4">{{ __('Status') }}</dt>
              <dd class="col-sm-8">
                <span class="badge bg-label-{{ $article->status ? 'success' : 'warning' }}">
                  {{ $article->status ? __('Published') : __('Draft') }}
                </span>
              </dd>

              <dt class="col-sm-4">{{ __('Subject') }}</dt>
              <dd class="col-sm-8">{{ $article->subject->subject_name ?? __('Not Set') }}</dd>

              <dt class="col-sm-4">{{ __('Class') }}</dt>
              <dd class="col-sm-8">{{ $article->schoolClass->grade_name ?? __('Not Set') }}</dd>

              <dt class="col-sm-4">{{ __('Semester') }}</dt>
              <dd class="col-sm-8">{{ $article->semester->semester_name ?? __('Not Set') }}</dd>

              <dt class="col-sm-4">{{ __('Created') }}</dt>
              <dd class="col-sm-8">{{ $article->created_at->format('Y-m-d H:i') }}</dd>

              <dt class="col-sm-4">{{ __('Updated') }}</dt>
              <dd class="col-sm-8">{{ $article->updated_at->format('Y-m-d H:i') }}</dd>
            </dl>
          </div>
        </div>

        <!-- Keywords -->
        @if($article->keywords->count() > 0)
        <div class="card mb-4">
          <div class="card-body">
            <h5 class="card-title mb-3">{{ __('Keywords') }}</h5>
            <div class="keywords-container">
              @foreach($article->keywords as $keyword)
              <span class="badge bg-label-primary rounded-pill mb-1">
                <i class="ti ti-tag me-1"></i>
                {{ $keyword->keyword }}
              </span>
              @endforeach
            </div>
          </div>
        </div>
        @endif

        <!-- Statistics -->
        <div class="card">
          <div class="card-body">
            <h5 class="card-title mb-3">{{ __('Statistics') }}</h5>
            <div class="d-flex justify-content-between mb-2">
              <div class="stat-label">{{ __('Views') }}</div>
              <div class="stat-value">{{ $article->views_count ?? 0 }}</div>
            </div>
            <div class="d-flex justify-content-between mb-2">
              <div class="stat-label">{{ __('Downloads') }}</div>
              <div class="stat-value">{{ $article->files->sum('download_count') ?? 0 }}</div>
            </div>
            <div class="d-flex justify-content-between">
              <div class="stat-label">{{ __('Attachments') }}</div>
              <div class="stat-value">{{ $article->files->count() }}</div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@section('vendor-script')
@vite([
  'resources/assets/vendor/libs/sweetalert2/sweetalert2.js',
  'resources/assets/vendor/libs/quill/katex.js',
  'resources/assets/vendor/libs/quill/quill.js'
])
@endsection

@section('page-script')
<script>
  window.translations = {
    deleteConfirmation: "{{ __('Delete Confirmation') }}",
    publishConfirmation: "{{ __('Publish Confirmation') }}",
    unpublishConfirmation: "{{ __('Unpublish Confirmation') }}",
    publishMessage: "{{ __('Are you sure you want to publish this article?') }}",
    unpublishMessage: "{{ __('Are you sure you want to unpublish this article?') }}",
    yes: "{{ __('Yes') }}",
    no: "{{ __('No') }}",
    success: "{{ __('Success') }}",
    error: "{{ __('Error') }}",
    generalError: "{{ __('Something went wrong. Please try again.') }}",
    linkCopied: "{{ __('Link copied to clipboard') }}"
  };

  window.routes = {
    articles: {
      publish: "{{ route('dashboard.articles.publish', ['article' => ':id', 'country' => $country]) }}",
      unpublish: "{{ route('dashboard.articles.unpublish', ['article' => ':id', 'country' => $country]) }}",
      search: "{{ route('dashboard.articles.index', ['country' => $country]) }}"
    },
    files: {
      download: "{{ route('dashboard.files.download', ['file' => ':id', 'country' => $country]) }}"
    }
  };
</script>
@vite(['resources/assets/js/articles/article-details.js'])
@endsection
