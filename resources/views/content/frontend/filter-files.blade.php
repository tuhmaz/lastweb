@php
$configData = Helper::appClasses();
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Detection\MobileDetect;

$detect = new MobileDetect;
@endphp

@extends('layouts/layoutFront')

@section('title', __('Filter Results'))

@section('content')
<!-- Hero Section -->

<section class="section-py first-section-pt help-center-header position-relative overflow-hidden" style="background: linear-gradient(226deg, #202c45 0%, #286aad 100%);">
    <!-- Background Pattern -->
    <div class="position-absolute w-100 h-100" style="background: linear-gradient(45deg, rgba(40, 106, 173, 0.1), transparent); top: 0; left: 0;"></div>

    <!-- Animated Shapes -->
    <div class="position-absolute" style="width: 300px; height: 300px; background: radial-gradient(circle, rgba(40, 106, 173, 0.1) 0%, transparent 70%); top: -150px; right: -150px; border-radius: 50%;"></div>
    <div class="position-absolute" style="width: 200px; height: 200px; background: radial-gradient(circle, rgba(40, 106, 173, 0.1) 0%, transparent 70%); bottom: -100px; left: -100px; border-radius: 50%;"></div>

    <div class="container position-relative">
        <div class="row justify-content-center">
            <div class="col-12 col-lg-8 text-center">

                <!-- Main Title with Animation -->
                <h2 class="display-6 text-white mb-4 animate__animated animate__fadeInDown" style="text-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                {{ __('Search Results') }}
                <p class="text-white-50 mt-2">{{ __('Discover relevant articles and resources') }}</p>
                </h2>
                @guest
                <!-- Call to Action Buttons -->
                <div class="d-flex justify-content-center gap-3 animate__animated animate__fadeInUp animate__delay-1s">
                    <a href="{{ route('login') }}" class="btn btn-primary btn-lg" style="background: linear-gradient(45deg, #3498db, #2980b9); border: none; box-shadow: 0 4px 15px rgba(52, 152, 219, 0.3);">
                        <i class="ti ti-user-plus me-2"></i>{{ __('Get Started') }}
                    </a>
                    <a href="#features" class="btn btn-outline-light btn-lg">
                        <i class="ti ti-info-circle me-2"></i>{{ __('Learn More') }}
                    </a>
                </div>
                @endguest
            </div>
        </div>
    </div>

    <!-- Wave Shape Divider -->
    <div class="position-absolute bottom-0 start-0 w-100 overflow-hidden" style="height: 60px;">
        <svg viewBox="0 0 1200 120" preserveAspectRatio="none" style="width: 100%; height: 60px; transform: rotate(180deg);">
            <path d="M321.39,56.44c58-10.79,114.16-30.13,172-41.86,82.39-16.72,168.19-17.73,250.45-.39C823.78,31,906.67,72,985.66,92.83c70.05,18.48,146.53,26.09,214.34,3V0H0V27.35A600.21,600.21,0,0,0,321.39,56.44Z" style="fill: #ffffff;"></path>
        </svg>
    </div>
</section>
<!-- Breadcrumb -->
<div class="container px-4 mt-4">
    <ol class="breadcrumb breadcrumb-style2 mb-4">
        <li class="breadcrumb-item">
            <a href="{{ route('home') }}" class="text-decoration-none">
                <i class="ti ti-home-check"></i> {{ __('Home') }}
            </a>
        </li>
        <li class="breadcrumb-item active">{{ __('Search Results') }}</li>
    </ol>
</div>

<!-- Main Content -->
<section class="section-py bg-body">
    <div class="container">
        <!-- Articles Section -->
        @if($articles && $articles->count() > 0)
        <div class="row mb-5">
            <div class="col-12">
                <div class="d-flex align-items-center mb-4">
                    <i class="ti ti-article text-primary me-2" style="font-size: 2rem;"></i>
                    <h3 class="mb-0">{{ __('Articles') }}</h3>
                </div>
                <div class="container mt-4 mb-4">
  @if(config('settings.google_ads_desktop_classes') || config('settings.google_ads_mobile_classes'))
  <div class="ads-container text-center">
    @if($detect->isMobile())
    {!! config('settings.google_ads_mobile_classes') !!}
    @else
    {!! config('settings.google_ads_desktop_classes') !!}
    @endif
  </div>
  @endif
</div>
                <div class="row g-4">
                    @foreach($articles as $article)
                    <div class="col-lg-4 col-md-6">
                        <div class="card h-100 shadow-sm hover-elevate-up">
                            <div class="position-relative">
                                @if($article->image_path)
                                <img src="{{ asset('storage/' . $article->image_path) }}" class="card-img-top" alt="{{ $article->title }}" style="height: 200px; object-fit: cover;">
                                @else
                                <div class="bg-light text-center py-5">
                                    <i class="ti ti-article text-primary" style="font-size: 3rem;"></i>
                                </div>
                                @endif
                                <div class="position-absolute top-0 end-0 p-3">
                                    <span class="badge bg-primary">{{ $article->category }}</span>
                                </div>
                            </div>
                            <div class="card-body">
                                <h5 class="card-title text-truncate">{{ $article->title }}</h5>
                                <p class="card-text text-muted">{{ Str::limit($article->description, 100) }}</p>
                            </div>
                            <div class="card-footer bg-transparent border-top-0">
                                <div class="d-flex justify-content-between align-items-center">
                                    <a href="{{ route('frontend.articles.show', ['database' => $database, 'article' => $article->id]) }}"
                                       class="btn btn-outline-primary btn-sm">
                                       <i class="ti ti-eye me-1"></i>{{ __('Read More') }}
                                    </a>
                                    <small class="text-muted">
                                        <i class="ti ti-calendar me-1"></i>{{ $article->created_at->format('Y-m-d') }}
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        <!-- Files Section -->
        @if($files && $files->count() > 0)
        <div class="row">
            <div class="col-12">
                <div class="d-flex align-items-center mb-4">
                    <i class="ti ti-files text-primary me-2" style="font-size: 2rem;"></i>
                    <h3 class="mb-0">{{ __('Files') }}</h3>
                </div>
                <div class="container mt-4 mb-4">
      @if(config('settings.google_ads_desktop_classes_2') || config('settings.google_ads_mobile_classes_2'))
      <div class="ads-container text-center">
        @if($detect->isMobile())
        {!! config('settings.google_ads_mobile_classes_2') !!}
        @else
        {!! config('settings.google_ads_desktop_classes_2') !!}
        @endif
      </div>
      @endif
    </div>
                <div class="row g-4">
                    @foreach($files as $file)
                    <div class="col-lg-4 col-md-6">
                        <div class="card h-100 shadow-sm hover-elevate-up">
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-3">
                                    @php
                                        $fileIcon = 'file-text';
                                        if (in_array($file->file_type, ['pdf'])) {
                                            $fileIcon = 'file-type-pdf';
                                        } elseif (in_array($file->file_type, ['doc', 'docx'])) {
                                            $fileIcon = 'file-type-doc';
                                        } elseif (in_array($file->file_type, ['xls', 'xlsx'])) {
                                            $fileIcon = 'file-type-xls';
                                        }
                                    @endphp
                                    <i class="ti ti-{{ $fileIcon }} text-primary me-2" style="font-size: 2rem;"></i>
                                    <div>
                                        <h5 class="card-title mb-0 text-truncate">{{ $file->file_Name }}</h5>
                                        <small class="text-muted">{{ strtoupper($file->file_type) }}</small>
                                    </div>
                                </div>
                                @if($file->description)
                                <p class="card-text text-muted">{{ Str::limit($file->description, 100) }}</p>
                                @endif
                                <div class="mt-3">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <span class="badge bg-light text-dark">
                                            <i class="ti ti-ruler me-1"></i>
                                            {{ number_format($file->file_size / 1024 / 1024, 2) }} MB
                                        </span>
                                        <span class="badge bg-light text-dark">
                                            <i class="ti ti-calendar me-1"></i>
                                            {{ $file->created_at->format('Y-m-d') }}
                                        </span>
                                    </div>
                                    <a href="{{ route('download.page', ['file' => $file->id, 'database' => $database]) }}"
                                       class="btn btn-outline-success w-100 hover-elevate-up">
                                        <i class="ti ti-download me-2"></i>{{ __('Download') }}
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        @if(!$articles->count() && !$files->count())
        <div class="text-center py-5">
            <div class="mb-4">
                <i class="ti ti-search-off" style="font-size: 4rem; color: #6c757d;"></i>
            </div>
            <h3>{{ __('No Results Found') }}</h3>
            <p class="text-muted">{{ __('Try different search terms or browse our categories') }}</p>
            <a href="{{ route('home') }}" class="btn btn-primary mt-3">
                <i class="ti ti-home me-2"></i>{{ __('Back to Home') }}
            </a>
        </div>
        @endif
    </div>
</section>

@push('styles')
<style>
.hover-elevate-up {
    transition: transform 0.3s ease;
}
.hover-elevate-up:hover {
    transform: translateY(-5px);
}
.card {
    border: none;
    border-radius: 10px;
}
.badge {
    padding: 0.5em 1em;
}
</style>
@endpush
@endsection
