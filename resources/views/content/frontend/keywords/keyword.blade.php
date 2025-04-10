{{-- resources/views/frontend/keyword/keyword.blade.php --}}
@extends('layouts/layoutFront')

@php
$configData = Helper::appClasses();
$pageTitle = __('content_related_to', ['keyword' => $keyword->keyword]);
use Illuminate\Support\Str;

@endphp

@section('title', $pageTitle)

@section('page-style')
@vite('resources/assets/vendor/scss/pages/front-page-help-center.scss')
@endsection

@section('meta')
<meta name="keywords" content="{{ $keyword->keyword }}">

<meta name="description" content="{{ __('find_articles_news_related_to', ['keyword' => $keyword->keyword]) }}">

<link rel="canonical" href="{{ url()->current() }}">

<meta property="og:title" content="{{ __('content_related_to', ['keyword' => $keyword->keyword]) }}" />
<meta property="og:description" content="{{ __('find_articles_news_related_to', ['keyword' => $keyword->keyword]) }}" />
<meta property="og:url" content="{{ url()->current() }}" />
<meta property="og:image" content="{{ $articles->first()->image_url ?? asset('assets/img/front-pages/icons/articles_default_image.jpg') }}" />

<meta name="twitter:card" content="summary_large_image" />
<meta name="twitter:title" content="{{ __('content_related_to', ['keyword' => $keyword->keyword]) }}" />
<meta name="twitter:description" content="{{ __('find_articles_news_related_to', ['keyword' => $keyword->keyword]) }}" />
<meta name="twitter:image" content="{{ $articles->first()->image_url ?? asset('assets/img/front-pages/icons/articles_default_image.jpg') }}" />
@endsection

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
                {{ __('content_related_to') }} {{ $keyword->keyword }}

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
    <li class="breadcrumb-item">
      <a href="{{ route('frontend.keywords.index', ['database' => $database ?? session('database', 'jo')]) }}">{{ __('keywords') }}</a>
    </li>
    <li class="breadcrumb-item active" aria-current="page">{{ $keyword->keyword }}</li>
  </ol>
</div>

<div class="container mt-4">
  @if($articles->isEmpty() && $news->isEmpty())
    <p class="text-center">{{ __('no_content_for_keyword') }}</p>
  @else
  <h3>{{ __('articles') }}</h3>
  <div class="row">
    @foreach($articles as $article)
    <div class="col-md-4 mb-4">
      <div class="card h-100 d-flex flex-column">
        <img src="{{ $article->image_url ?? asset('assets/img/front-pages/icons/articles_default_image.jpg') }}" class="card-img-top" alt="{{ $article->title }}" style="height: 200px; object-fit: cover;">
        <div class="card-body d-flex flex-column">
          <h5 class="card-title">{{ $article->title }}</h5>
          <p class="card-text">{{ Str::limit(strip_tags($article->content), 100) }}</p>
          <div class="mt-auto">
            <a href="{{ route('frontend.articles.show', ['database' => $database ?? session('database', 'jo'), 'article' => $article->id]) }}" class="btn btn-primary">{{ __('read_more') }}</a>
          </div>
        </div>
      </div>
    </div>
    @endforeach
  </div>

  <h3>{{ __('news') }}</h3>
  <div class="row">
    @foreach($news as $newsItem)
    <div class="col-md-4 mb-4">
      <div class="card h-100 d-flex flex-column">
        @php
          $imagePath = $newsItem->image ? asset('storage/' . $newsItem->image) : asset('assets/img/pages/news-default.jpg');
        @endphp

        <img src="{{ $imagePath }}" class="card-img-top" alt="{{ $newsItem->title }}" style="height: 200px; object-fit: cover;">
        <div class="card-body d-flex flex-column">
          <h5 class="card-title">{{ $newsItem->title }}</h5>
          <p class="card-text">{{ Str::limit(strip_tags($newsItem->description), 100) }}</p>
          <div class="mt-auto">
            <a href="{{ route('content.frontend.news.show', ['database' => $database ?? session('database', 'jo'), 'id' => $newsItem->id]) }}" class="btn btn-primary">{{ __('read_more') }}</a>
          </div>
        </div>
      </div>
    </div>
    @endforeach
  </div>
  @endif
</div>
@endsection
