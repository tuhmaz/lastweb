@php
use Illuminate\Support\Str;
@endphp

@extends('layouts.contentNavbarLayout')

@section('title', __('Articles Management'))

@section('vendor-style')
@vite([
  'resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss',
  'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss',
  'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss',
  'resources/assets/vendor/libs/animate-css/animate.scss'
])
@endsection

@section('page-style')
@vite(['resources/assets/css/articles/articles-list-style.css'])
<style>
.article-card {
  transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
}
.article-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}
.country-selector {
  min-width: 200px;
}
.keyword-badge {
  transition: all 0.2s ease;
}
.keyword-badge:hover {
  transform: scale(1.1);
}
.stats-card {
  border-radius: 10px;
  border-left: 4px solid;
}
.stats-icon {
  width: 45px;
  height: 45px;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 50%;
}

/* Responsive styles */
@media (max-width: 768px) {
  .header-actions {
    flex-direction: column;
    gap: 1rem !important;
    width: 100%;
  }
  .country-selector {
    width: 100%;
    min-width: auto;
  }
  .add-article-btn {
    width: 100%;
  }
  .stats-card {
    margin-bottom: 1rem;
  }
  .table-responsive {
    margin: 0 -1.5rem;
  }
  .dropdown-menu {
    position: fixed !important;
    top: auto !important;
    right: 1rem !important;
    bottom: 1rem !important;
    left: 1rem !important;
    margin: 0;
    border-radius: 0.5rem;
    box-shadow: 0 -2px 10px rgba(0,0,0,0.1);
  }
  .dropdown-item {
    padding: 0.75rem 1rem;
  }
}

/* Floating action button for mobile */
.floating-action-button {
  display: none;
}

@media (max-width: 768px) {
  .floating-action-button {
    display: block;
    position: fixed;
    right: 1rem;
    bottom: 1rem;
    z-index: 1000;
  }
  .floating-action-button .btn {
    width: 56px;
    height: 56px;
    border-radius: 50%;
    padding: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 2px 10px rgba(0,0,0,0.2);
  }
  .floating-action-button .btn i {
    font-size: 1.5rem;
  }
  .desktop-add-button {
    display: none;
  }
}
</style>
@endsection

@section('vendor-script')
@vite([
  'resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js',
  'resources/assets/vendor/libs/sweetalert2/sweetalert2.js',

])
@endsection

@section('page-script')
<script>
  window.translations = {
    deleteConfirmation: "{{ __('Delete Confirmation') }}",
    deleteArticleConfirmation: "{{ __('Are you sure you want to delete article: :title?') }}",
    yes: "{{ __('Yes') }}",
    no: "{{ __('No') }}",
    search: "{{ __('Search...') }}",
    show: "{{ __('Show') }}",
    showing: "{{ __('Showing') }}",
    to: "{{ __('to') }}",
    of: "{{ __('of') }}",
    entries: "{{ __('entries') }}",
    first: "{{ __('First') }}",
    last: "{{ __('Last') }}",
    next: "{{ __('Next') }}",
    previous: "{{ __('Previous') }}"
  };

  window.routes = {
    articles: {
      index: "{{ route('dashboard.articles.index', ['country' => ':country']) }}"
    }
  };
</script>
@vite(['resources/assets/js/articles/articles-management.js'])
@endsection

@section('content')
<div class="row mb-4">
  <div class="col-12">
    <div class="card bg-primary text-white">
      <div class="card-body">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
          <div>
            <h4 class="text-white mb-0">{{ __('Articles Management') }}</h4>
            <p class="mb-0">{{ __('Manage and organize your educational content') }}</p>
          </div>
          <div class="d-flex header-actions gap-3 align-items-center">
            <select id="country-selector" name="country" class="form-select country-selector bg-white text-dark"
                    onchange="window.location.href='{{ route('dashboard.articles.index') }}?country=' + this.value">
              <option value="1" {{ $country == '1' ? 'selected' : '' }}>🇯🇴 {{ __('Jordan') }}</option>
              <option value="2" {{ $country == '2' ? 'selected' : '' }}>🇸🇦 {{ __('Saudi Arabia') }}</option>
              <option value="3" {{ $country == '3' ? 'selected' : '' }}>🇪🇬 {{ __('Egypt') }}</option>
              <option value="4" {{ $country == '4' ? 'selected' : '' }}>🇵🇸 {{ __('Palestine') }}</option>
            </select>
            <a href="{{ route('dashboard.articles.create', ['country' => $country]) }}"
               class="btn btn-white desktop-add-button">
              <i class="ti ti-plus me-1"></i>{{ __('Add New Article') }}
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Statistics Cards -->
<div class="row mb-4">
  <div class="col-6 col-md-3">
    <div class="card stats-card" style="border-left-color: #696cff;">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-center">
          <div>
            <h5 class="mb-0">{{ $articles->total() }}</h5>
            <small class="text-muted">{{ __('Total Articles') }}</small>
          </div>
          <div class="stats-icon bg-label-primary">
            <i class="ti ti-article text-primary"></i>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-6 col-md-3">
    <div class="card stats-card" style="border-left-color: #03c3ec;">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-center">
          <div>
            <h5 class="mb-0">{{ $articles->where('status', true)->count() }}</h5>
            <small class="text-muted">{{ __('Published') }}</small>
          </div>
          <div class="stats-icon bg-label-info">
            <i class="ti ti-check text-info"></i>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-6 col-md-3">
    <div class="card stats-card" style="border-left-color: #ffab00;">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-center">
          <div>
            <h5 class="mb-0">{{ $articles->where('status', false)->count() }}</h5>
            <small class="text-muted">{{ __('Drafts') }}</small>
          </div>
          <div class="stats-icon bg-label-warning">
            <i class="ti ti-pencil text-warning"></i>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-6 col-md-3">
    <div class="card stats-card" style="border-left-color: #71dd37;">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-center">
          <div>
            <h5 class="mb-0">{{ $articles->sum(function($article) { return $article->files->count(); }) }}</h5>
            <small class="text-muted">{{ __('Files') }}</small>
          </div>
          <div class="stats-icon bg-label-success">
            <i class="ti ti-files text-success"></i>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Floating Action Button for Mobile -->
<div class="floating-action-button">
  <a href="{{ route('dashboard.articles.create', ['country' => $country]) }}"
     class="btn btn-primary">
    <i class="ti ti-plus"></i>
  </a>
</div>

<!-- Alerts -->
@if(session('success'))
<div class="alert alert-success alert-dismissible animate__animated animate__fadeIn" role="alert">
  <div class="d-flex">
    <i class="ti ti-check-circle me-2"></i>
    <div>{{ session('success') }}</div>
  </div>
  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

@if(session('error'))
<div class="alert alert-danger alert-dismissible animate__animated animate__fadeIn" role="alert">
  <div class="d-flex">
    <i class="ti ti-alert-circle me-2"></i>
    <div>{{ session('error') }}</div>
  </div>
  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

<!-- Articles Table Card -->
<div class="card article-card">
  <div class="card-header">
    <h5 class="card-title mb-0">{{ __('Articles List') }}</h5>
  </div>
  <div class="card-body">
    <div class="table-responsive">
      <table class="table table-hover">
        <thead>
          <tr>
            <th>{{ __('Title') }}</th>
            <th>{{ __('Class') }}</th>
            <th>{{ __('Subject') }}</th>
            <th>{{ __('Semester') }}</th>
            <th>{{ __('Status') }}</th>
            <th>{{ __('Keywords') }}</th>
            <th>{{ __('Files') }}</th>
            <th>{{ __('Actions') }}</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($articles as $article)
          <tr>
            <td>
              <div class="d-flex align-items-center">
                <i class="ti ti-article text-primary me-2"></i>
                <div>
                  <div class="fw-bold">{{ Str::limit($article->title, 30) }}</div>
                  <small class="text-muted">{{ __('Created') }}: {{ $article->created_at->format('M d, Y') }}</small>
                </div>
              </div>
            </td>
            <td>
              <span class="badge bg-label-primary">{{ $article->subject->schoolClass->grade_name }}</span>
            </td>
            <td>
              <span class="badge bg-label-info">{{ $article->subject->subject_name }}</span>
            </td>
            <td>
              <span class="badge bg-label-warning">{{ $article->semester->semester_name }}</span>
            </td>
            <td>
              @if($article->status)
                <span class="badge bg-success">{{ __('Published') }}</span>
              @else
                <span class="badge bg-warning">{{ __('Draft') }}</span>
              @endif
            </td>
            <td>
              <div class="d-flex flex-wrap gap-1">
                @if($article->keywords && $article->keywords->isNotEmpty())
                  @foreach($article->keywords as $keyword)
                    <span class="badge bg-label-secondary keyword-badge">{{ $keyword->keyword }}</span>
                  @endforeach
                @else
                  <span class="text-muted fst-italic">{{ __('No keywords') }}</span>
                @endif
              </div>
            </td>
            <td>
              <div class="d-flex flex-wrap gap-1">
                @forelse ($article->files as $file)
                  <span class="badge bg-label-info keyword-badge" title="{{ $file->file_name }}">
                    <i class="ti ti-file me-1"></i>{{ $file->file_category }}
                  </span>
                @empty
                  <span class="text-muted fst-italic">{{ __('No files') }}</span>
                @endforelse
              </div>
            </td>
            <td>
              <div class="dropdown">
                <button type="button" class="btn btn-text-secondary rounded-pill btn-icon dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                  <i class="ti ti-dots-vertical"></i>
                </button>
                <div class="dropdown-menu">
                  <a class="dropdown-item" href="{{ route('dashboard.articles.show', ['article' => $article->id, 'country' => $country]) }}">
                    <i class="ti ti-eye me-1"></i>{{ __('View') }}
                  </a>
                  <a class="dropdown-item" href="{{ route('dashboard.articles.edit', ['article' => $article->id, 'country' => $country]) }}">
                    <i class="ti ti-pencil me-1"></i>{{ __('Edit') }}
                  </a>
                  <form id="delete-form-{{ $article->id }}"
                        action="{{ route('dashboard.articles.destroy', ['article' => $article->id, 'country' => $country]) }}"
                        method="POST"
                        style="display: none;">
                    @csrf
                    @method('DELETE')
                  </form>
                  <a class="dropdown-item text-danger delete-article" href="#"
                     data-id="{{ $article->id }}"
                     data-title="{{ $article->title }}">
                    <i class="ti ti-trash me-1"></i>{{ __('Delete') }}
                  </a>
                </div>
              </div>
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="8" class="text-center py-5">
              <div class="text-center">
                <i class="ti ti-article-off mb-2" style="font-size: 3rem;"></i>
                <h6 class="mt-2">{{ __('No Articles Found') }}</h6>
                <p class="text-muted mb-3">{{ __('Start by adding your first article') }}</p>
                <a href="{{ route('dashboard.articles.create', ['country' => $country]) }}"
                   class="btn btn-primary">
                  <i class="ti ti-plus me-1"></i>{{ __('Add New Article') }}
                </a>
              </div>
            </td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>

  
    <div class="pagination pagination-outline-secondary">
          {{ $articles->links('components.pagination.custom') }}
        </div>
  </div>
</div>
@endsection
