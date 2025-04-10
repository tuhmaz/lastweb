@php
$configData = Helper::appClasses();
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\News;
use App\Models\User;
use Detection\MobileDetect;

$detect = new MobileDetect;

// Get the author from the main database (jo)
$author = User::on('jo')->find($news->author_id);
$news->setRelation('author', $author);

// Fetch random news items from the selected database
$randomNews = \App\Models\News::on($database)
->inRandomOrder()
->take(5)
->get();
@endphp



@extends('layouts/layoutFront')

@section('title', $news->title)
@section('meta_title', $news->title . ' - ' . ($news->meta_title ?? config('settings.meta_title')))

@section('meta')
<meta property="og:type" content="article" />
<meta property="og:title" content="{{ $news->title }}" />
<meta property="og:description" content="{{ $news->meta_description }}" />
<meta property="og:image" content="{{ asset('storage/images/' . $news->image) }}" />
<meta property="og:url" content="{{ request()->fullUrl() }}" />
<meta property="og:site_name" content="{{ config('settings.site_name') }}" />
<meta property="article:modified_time" content="{{ $news->updated_at->toIso8601String() }}" />
<meta property="article:published_time" content="{{ $news->created_at->toIso8601String() }}" />

<meta property="article:author" content="{{ $author->name ?? 'Unknown' }}" />

<meta property="article:section" content="{{ $news->category->name }}" />

@php
$keywordsArray = is_string($news->keywords) ? array_map('trim', explode(',', $news->keywords)) : [];
@endphp
<meta property="article:tag" content="{{ implode(',', $keywordsArray) }}" />

<meta name="twitter:card" content="summary_large_image" />
<meta name="twitter:title" content="{{ $news->title }}" />
<meta name="twitter:description" content="{{ $news->meta_description }}" />
<meta name="twitter:image" content="{{ asset('storage/images/' . $news->image) }}" />
<meta name="twitter:site" content="{{ config('settings.twitter') }}" />
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
                {{ $news->title }}
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
<div class="container px-4 mt-4">
  <ol class="breadcrumb breadcrumb-style2" aria-label="breadcrumbs">
    <li class="breadcrumb-item"><a href="{{ route('home') }}"><i class="ti ti-home-check"></i>{{ __('Home') }}</a></li>
    <li class="breadcrumb-item"><a href="{{ route('content.frontend.news.index',['database' => $database ?? session('database', 'default_database')]) }}">{{ __('News') }}</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ $news->title }}</li>
  </ol>
  <div class="progress mt-2">
    <div class="progress-bar" role="progressbar" style="width: 75%;"></div>
  </div>
</div>

<div class="container mt-4 mb-4">
  @if(config('settings.google_ads_desktop_news') || config('settings.google_ads_mobile_news'))
  <div class="ads-container text-center">
    @if($detect->isMobile())
    {!! config('settings.google_ads_mobile_news') !!}
    @else
    {!! config('settings.google_ads_desktop_news') !!}
    @endif
  </div>
  @endif
</div>

<section class="section-py bg-body">
  <div class="container">
    <div class="row g-4">
      <div class="col-lg-8 col-md-12">
        <div class="card">
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-center flex-wrap mb-4 gap-2">
              <div>
                <h2 class="mb-2">{{ $news->title }}</h2>
                <p class="text-muted mb-0">{{ __('Published on') }} <span class="fw-medium text-heading">{{ $news->created_at->format('d M Y') }}</span></p>
              </div>
              <div class="social-share">
                <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(request()->fullUrl()) }}" target="_blank" class="btn btn-icon btn-outline-primary">
                  <i class="ti ti-brand-facebook"></i>
                </a>
                <a href="https://twitter.com/intent/tweet?url={{ urlencode(request()->fullUrl()) }}" target="_blank" class="btn btn-icon btn-outline-info">
                  <i class="ti ti-brand-twitter"></i>
                </a>
                <a href="https://www.linkedin.com/shareArticle?mini=true&url={{ urlencode(request()->fullUrl()) }}" target="_blank" class="btn btn-icon btn-outline-primary">
                  <i class="ti ti-brand-linkedin"></i>
                </a>
              </div>
            </div>

            @php
    // التحقق إذا كانت الصورة موجودة أم لا
    $imagePath = $news->image ? asset('storage/' . $news->image) : asset('path_to_default_image/default.jpg');
    
    // معالجة محتوى الخبر لتحويل الكلمات الدلالية إلى روابط
    $processedContent = $news->content;
    $keywords = is_string($news->keywords) ? array_map('trim', explode(',', $news->keywords)) : [];
    
    // ترتيب الكلمات الدلالية من الأطول إلى الأقصر لتجنب استبدال أجزاء من الكلمات الأطول
    usort($keywords, function($a, $b) {
        return strlen($b) - strlen($a);
    });
    
    foreach ($keywords as $keyword) {
        if (!empty($keyword)) {
            $link = '<a href="' . route('keywords.indexByKeyword', ['database' => $database, 'keywords' => $keyword]) . '" class="keyword-link">' . $keyword . '</a>';
            // استبدال الكلمة الدلالية بالرابط، مع مراعاة أن تكون كلمة كاملة وليست جزءًا من كلمة أخرى
            $processedContent = preg_replace('/\b(' . preg_quote($keyword, '/') . ')\b/ui', $link, $processedContent);
        }
    }
@endphp

<img src="{{ $imagePath }}" class="card-img-top img-fluid mb-4" alt="{{ $news->title }}">
<h4 class="mb-4"> {!! $processedContent !!}</h4>

            <div class="news-details pt-12">
              <div class="row">
                <div class="col-md-6">
                  <h6>{{ __('Tags') }}</h6>
                  @php
                  $colors = ['primary', 'secondary', 'success', 'danger', 'warning', 'info', 'dark'];
                  $keywordIndex = 0;
                  $keywords = is_string($news->keywords) ? array_map('trim', explode(',', $news->keywords)) : [];
                  @endphp

                  <div class="keywords-container">
                    @foreach($keywords as $keyword)
                  @php
                  $color = $colors[$keywordIndex % count($colors)];
                  $keywordIndex++;
                  @endphp
                  <span class="badge bg-{{ $color }} bg-glow me-1">{{ $keyword }}</span>
                    @endforeach
                  </div>
                </div>

                <div class="col-md-6">
                  <h6>{{ __('Meta Description') }}</h6>
                  <span class="badge bg-info bg-glow" style="text-wrap: wrap;">{{ $news->meta_description }}</span>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="container mt-4 mb-4">
          @if(config('settings.google_ads_desktop_news_2') || config('settings.google_ads_mobile_news_2'))
          <div class="ads-container text-center">
            @if($detect->isMobile())
            {!! config('settings.google_ads_mobile_news_2') !!}
            @else
            {!! config('settings.google_ads_desktop_news_2') !!}
            @endif
          </div>
          @endif
        </div>

      </div>

      <div class="col-lg-4 col-md-12 mt-4 mt-lg-0">
        <h5 class="mb-3">{{ __('Related News') }}</h5>
        <div class="accordion stick-top accordion-custom-button" id="relatedNews">
          @foreach($randomNews as $index => $randomItem)
          <div class="accordion-item @if($index === 1) active @endif mb-0">
            <div class="accordion-header" id="heading{{ $index }}">
              <button type="button" class="accordion-button collapsed" data-bs-toggle="collapse" data-bs-target="#collapse{{ $index }}" aria-expanded="false" aria-controls="collapse{{ $index }}">
                <span class="d-flex flex-column">
                  <span class="h6 mb-0">{{ Str::limit($randomItem->title, 50) }}</span>
                </span>
              </button>
            </div>
            <div id="collapse{{ $index }}" class="accordion-collapse collapse @if($index === 2) show @endif" data-bs-parent="#relatedNews">
              <div class="accordion-body py-4">
                <img src="{{ asset('storage/' . $randomItem->image) }}" class="card-img-top img-fluid mb-2" alt="{{ $randomItem->title }}">
                <p class="text-body fw-normal">{{ Str::limit(strip_tags($randomItem->description), 100) }}</p>
                <a href="{{ route('content.frontend.news.show', ['database' => $database ?? session('database', 'default_database'), 'id' => $randomItem->id]) }}" class="btn btn-primary btn-sm">{{ __('Read more') }}</a>
              </div>
            </div>
          </div>
          @endforeach
        </div>

        <div class="container mt-4 mb-4">
          @if(config('settings.google_ads_desktop_news') || config('settings.google_ads_mobile_news_2'))
          <div class="ads-container text-center">
            @if($detect->isMobile())
            {!! config('settings.google_ads_mobile_news_2') !!}
            @else
            {!! config('settings.google_ads_desktop_news') !!}
            @endif
          </div>
          @endif
        </div>
      </div>
    </div>
  </div>
</section>

<div class="card mt-4">
  <div class="card-body">
    <h3 class="mb-4">{{ __('Add a Comment') }}</h3>
    <form action="{{ route('frontend.comments.store', ['database' => $database ?? session('database')]) }}" method="POST">
      @csrf
      <div class="mb-3">
        <label for="comment" class="form-label">{{ __('Your Comment') }}</label>
        <textarea class="form-control" id="comment" name="body" rows="4" required></textarea>
      </div>
      <input type="hidden" name="commentable_id" value="{{ $news->id }}">
      <input type="hidden" name="commentable_type" value="{{ get_class($news) }}">
      <button type="submit" class="btn btn-primary">{{ __('Submit Comment') }}</button>
    </form>

    @foreach($news->comments as $comment)
    <div class="mt-4">
      <div class="divider divider-success">
        <div class="divider-text">{{ $comment->user->name }}</div>
      </div>
      <p>{{ $comment->body }}</p>

      <div class="reactions-inline-spacing d-flex justify-content-center align-items-center">
        <form action="{{ route('frontend.reactions.store', ['database' => $database ?? session('database')]) }}" method="POST" class="d-inline-block">
          @csrf
          <input type="hidden" name="comment_id" value="{{ $comment->id }}">
          <input type="hidden" name="type" value="like">
          <button type="submit" class="btn btn-outline-info btn-sm">
            <i class="ti ti-thumb-up me-1"></i> {{ __('Like') }}
            <span class="badge bg-white text-info ms-1">{{ $comment->reactions->where('type', 'like')->count() }}</span>
          </button>
        </form>

        <form action="{{ route('frontend.reactions.store', ['database' => $database ?? session('database')]) }}" method="POST" class="d-inline-block">
          @csrf
          <input type="hidden" name="comment_id" value="{{ $comment->id }}">
          <input type="hidden" name="type" value="love">
          <button type="submit" class="btn btn-outline-danger btn-sm">
            <i class="ti ti-heart me-1"></i> {{ __('Love') }}
            <span class="badge bg-white text-danger ms-1">{{ $comment->reactions->where('type', 'love')->count() }}</span>
          </button>
        </form>

        <form action="{{ route('frontend.reactions.store', ['database' => $database ?? session('database')]) }}" method="POST" class="d-inline-block">
          @csrf
          <input type="hidden" name="comment_id" value="{{ $comment->id }}">
          <input type="hidden" name="type" value="laugh">
          <button type="submit" class="btn btn-outline-warning btn-sm">
            <i class="ti ti-mood-happy me-1"></i> {{ __('Laugh') }}
            <span class="badge bg-white text-warning ms-1">{{ $comment->reactions->where('type', 'laugh')->count() }}</span>
          </button>
        </form>

        @if(auth()->check() && $comment->user_id === auth()->id())
        <form action="{{ route('frontend.comments.destroy', ['database' => $database ?? session('database'), 'id' => $comment->id]) }}" method="POST" class="d-inline-block ms-2">
          @csrf
          @method('DELETE')
          <button type="submit" class="btn btn-outline-danger btn-sm" onclick="return confirm('{{ __('Are you sure you want to delete this comment?') }}')">
            <i class="ti ti-trash me-1"></i> {{ __('Delete') }}
          </button>
        </form>
        @endif
      </div>
    </div>
    @endforeach
  </div>
</div>

<style>
    .keyword-link {
        color: #3498db;
        text-decoration: none;
        font-weight: 500;
    }
    .keyword-link:hover {
        color: #2980b9;
        text-decoration: none;
    }
</style>

<script type="application/ld+json">
  {
    "@context": "https://schema.org",
    "@type": "NewsArticle",
    "headline": "{{ $news->title }}",
    "image": "{{ asset('storage/images/' . $news->image) }}",
    "datePublished": "{{ $news->created_at->toIso8601String() }}",
    "author": {
      "@type": "Person",
      "name": "{{ $author->name ?? 'Unknown' }}"
    },
    "publisher": {
      "@type": "Organization",
      "name": "{{ config('settings.site_name') }}",
      "logo": {
        "@type": "ImageObject",
        "url": "{{ asset('storage/' . config('settings.site_logo')) }}"
      }
    },
    "description": "{{ $news->meta_description }}"
  }
</script>

<script type="application/ld+json">
  {
    "@context": "https://schema.org",
    "@type": "BreadcrumbList",
    "itemListElement": [{
        "@type": "ListItem",
        "position": 1,
        "name": "Home",
        "item": "{{ url('/') }}"
      },
      {
        "@type": "ListItem",
        "position": 2,
        "name": "News",
        "item": "{{ route('content.frontend.news.index',['database' => $database ?? session('database', 'default_database')]) }}"
      },
      {
        "@type": "ListItem",
        "position": 3,
        "name": "{{ $news->title }}",
        "item": "{{ request()->url() }}"
      }
    ]
  }
</script>

@endsection
