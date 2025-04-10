@extends('layouts.layoutFront')

@section('title', __('all_keywords'))



@section('content')
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
                {{ __('all_keywords') }}
                <p class="text-center text-white px-4 mb-0" style="font-size: medium;">{{ __('explore_keywords') }}</p>

                </h2>


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

<div class="container px-4 mt-4">
  <ol class="breadcrumb breadcrumb-style2" aria-label="breadcrumbs">
    <li class="breadcrumb-item">
      <a href="{{ route('home') }}">
        <i class="ti ti-home-check"></i>{{ __('home') }}
      </a>
    </li>
    <li class="breadcrumb-item active" aria-current="page">{{ __('keywords') }}</li>
  </ol>
  <div class="progress mt-2">
    <div class="progress-bar" role="progressbar" style="width: 100%;"></div>
  </div>
</div>

<section class="section-py bg-body first-section-pt" style="padding-top: 10px;">
  <div class="container">
    <div class="card px-3 mt-6 shadow-sm">
      <div class="row">
        <div class="content-header text-center bg-primary py-3">
          <h2 class="text-white">{{ __('article_keywords') }}</h2>
        </div>
        <div class="content-body text-center mt-3">
          @if($articleKeywords->count())
          <div class="keywords-cloud">
            @foreach($articleKeywords as $keyword)
            <a href="{{ route('keywords.indexByKeyword', ['database' => $database ?? session('database', 'jo'), 'keywords' => $keyword->keyword]) }}" class="keyword-item btn btn-outline-secondary m-1">
              {{ $keyword->keyword }}
            </a>
            @endforeach
          </div>
          @else
          <p>{{ __('no_article_keywords') }}</p>
          @endif
        </div>
      </div>
    </div>
  </div>
</section>

<section class="section-py bg-body first-section-pt" style="padding-top: 10px;">
  <div class="container">
    <div class="card px-3 mt-6 shadow-sm">
      <div class="row">
        <div class="content-header text-center bg-primary py-3">
          <h2 class="text-white">{{ __('news_keywords') }}</h2>
        </div>
        <div class="content-body text-center mt-3">
          @if($newsKeywords->count())
          <div class="keywords-cloud">
            @foreach($newsKeywords as $keyword)
            <a href="{{ route('keywords.indexByKeyword', ['database' => $database ?? session('database', 'jo'), 'keywords' => $keyword->keyword]) }}" class="keyword-item btn btn-outline-secondary m-1">
              {{ $keyword->keyword }}
            </a>
            @endforeach
          </div>
          @else
          <p>{{ __('no_news_keywords') }}</p>
          @endif
        </div>
      </div>
    </div>
  </div>
</section>

@endsection
