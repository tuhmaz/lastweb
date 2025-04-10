@php
$database = session('database', 'jo');
$filterUrl = route('content.frontend.news.filter', ['database' => $database]);
use Illuminate\Support\Str;
@endphp

@extends('layouts/layoutFront')

@section('title', __('news_title'))

@section('vendor-style')
@vite([
'resources/assets/vendor/libs/animate-css/animate.scss',
])
@endsection

@section('content')
<!-- Section 1: ترويسة الصفحة -->
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
          {{ __('welcome_school_classes') }}
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


<!-- Breadcrumb + Progress -->
<div class="container px-4 mt-4">
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb breadcrumb-style2">
      <li class="breadcrumb-item">
        <a href="{{ route('home') }}" class="text-decoration-none">
          <i class="ti ti-home-check"></i> {{ __('Home') }}
        </a>
      </li>
      <li class="breadcrumb-item active" aria-current="page">{{ __('News') }}</li>
    </ol>
  </nav>
  <div class="progress mt-2" style="height: 6px;">
    <div class="progress-bar bg-primary" role="progressbar" style="width: 50%;" aria-valuenow="50"
      aria-valuemin="0" aria-valuemax="100"></div>
  </div>
</div>
<!-- News Content Section -->
<section id="news-section" class="section-py bg-body">
  <div class="container">
    <div class="row g-4">
      <!-- Categories Sidebar -->
      <div class="col-lg-3 col-md-4 mb-4">
        <div class="card shadow-sm border-0 sticky-lg-top" style="top: 2rem; z-index: 1020;">
          <div class="card-header bg-primary bg-opacity-10 border-bottom-0">
            <h5 class="mb-0 text-white">{{ __('Categories') }}</h5>
          </div>
          <div class="card-body p-0">
            <div class="list-group list-group-flush rounded-bottom">
              <a href="{{ route('content.frontend.news.index', ['database' => $database]) }}"
                class="list-group-item list-group-item-action d-flex justify-content-between align-items-center {{ !request()->has('category') ? 'active' : '' }}">
                {{ __('All Categories') }}
                <span class="badge bg-primary rounded-pill">{{ number_format($news->total()) }}</span>
              </a>
              @foreach($categories as $cat)
              <a href="{{ route('content.frontend.news.index', ['database' => $database, 'category' => $cat->slug]) }}"
                class="list-group-item list-group-item-action d-flex justify-content-between align-items-center {{ request()->input('category') == $cat->slug ? 'active' : '' }}">
                {{ $cat->name }}
                <span class="badge bg-primary rounded-pill">{{ number_format($cat->news_count ?? 0) }}</span>
              </a>
              @endforeach
            </div>
          </div>
        </div>
      </div>

      <!-- News List -->
      <div class="col-lg-9 col-md-8">
        <div class="row g-4" id="news-container">
          @foreach($news as $item)
          <div class="col-xl-6 col-lg-6 col-md-12 animate__animated animate__fadeInUp" style="animation-delay: {{ $loop->iteration * 0.1 }}s">
            <div class="card h-100 shadow-sm hover-shadow-lg transition-all border-0">
              @if($item->image)
              <div class="position-relative">
                <img src="{{ asset('storage/' . $item->image) }}"
                  class="card-img-top"
                  alt="{{ $item->alt }}"
                  loading="lazy"
                  style="height: 220px; object-fit: cover;">
                @if($item->category)
                <span class="position-absolute top-0 end-0 m-3 badge bg-primary">
                  {{ $item->category->name }}
                </span>
                @endif
              </div>
              @endif
              <div class="card-body">
                <h5 class="card-title mb-3">
                  <a href="{{ route('content.frontend.news.show', ['database' => $database, 'id' => $item->id]) }}"
                    class="text-body text-decoration-none hover-primary stretched-link">
                    {{ $item->title }}
                  </a>
                </h5>
                <p class="card-text text-muted">{{ Str::limit(strip_tags($item->content), 150) }}</p>
              </div>
              <div class="card-footer bg-transparent border-top-0 pt-0">
                <div class="d-flex align-items-center text-muted">
                  <i class="ti ti-calendar me-2"></i>
                  <small>{{ $item->created_at->diffForHumans() }}</small>
                </div>
              </div>
            </div>
          </div>
          @endforeach
        </div>

        <!-- Pagination -->
        <div class="d-flex justify-content-center mt-5">
          {{ $news->links() }}
        </div>
      </div>
    </div>
  </div>
</section>
@endsection

@push('page-style')
@vite(['resources/scss/base/pages/news.scss'])
@endpush

@push('page-script')
@vite(['resources/js/pages/news.js'])
<script>
  document.addEventListener('DOMContentLoaded', function() {
    const filterUrl = '{{ $filterUrl }}';

    // Category filter functionality with loading state
    document.querySelectorAll('.category-filter').forEach(link => {
      link.addEventListener('click', async (e) => {
        e.preventDefault();
        const categoryId = e.target.dataset.categoryId;
        const container = document.getElementById('news-container');

        try {
          container.style.opacity = '0.5';
          const response = await fetch(`${filterUrl}?category_id=${categoryId}`);
          const data = await response.json();

          if (data.success) {
            container.innerHTML = data.html;
            // Re-initialize animations for new content
            container.querySelectorAll('.animate__animated').forEach(el => {
              el.classList.add('animate__fadeInUp');
            });
          }
        } catch (error) {
          console.error('Error filtering news:', error);
        } finally {
          container.style.opacity = '1';
        }
      });
    });

    // Smooth scroll with offset for fixed header
    document.querySelectorAll('.scroll-btn').forEach(anchor => {
      anchor.addEventListener('click', function(e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        const headerOffset = 80;
        if (target) {
          const elementPosition = target.getBoundingClientRect().top;
          const offsetPosition = elementPosition - headerOffset;

          window.scrollBy({
            top: offsetPosition,
            behavior: 'smooth'
          });
        }
      });
    });

    // Lazy loading for images
    if ('loading' in HTMLImageElement.prototype) {
      const images = document.querySelectorAll('img[loading="lazy"]');
      images.forEach(img => {
        img.src = img.src;
      });
    }
  });
</script>
@endpush
