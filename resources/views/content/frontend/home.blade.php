@php
$configData = Helper::appClasses();
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Detection\MobileDetect;
$detect = new MobileDetect;
$colors = ['primary', 'secondary', 'success', 'danger', 'warning', 'info', 'dark'];
$colorCount = count($colors);

$icons = [
'1' => 'ti ti-number-0',
'2' => 'ti ti-number-1',
'3' => 'ti ti-number-2',
'4' => 'ti ti-number-3',
'5' => 'ti ti-number-4',
'6' => 'ti ti-number-5',
'7' => 'ti ti-number-6',
'8' => 'ti ti-number-7',
'9' => 'ti ti-number-8',
'10' => 'ti ti-number-9',
'11' => 'ti ti-number-10-small',
'12' => 'ti ti-number-11-small',
'13' => 'ti ti-number-12-small',
'default' => 'ti ti-book',
];
@endphp

@extends('layouts/layoutFront')

@section('title')

@section('page-style')
@vite([ 'resources/assets/vendor/scss/but.scss','resources/assets/vendor/scss/calendar.scss'
])
@endsection

@section('page-script')
@vite(['resources/assets/vendor/js/filterhome.js','resources/assets/vendor/js/but.js','resources/assets/vendor/js/calendar.js'])
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
        <h1 class="display-4 text-white mb-4 animate__animated animate__fadeInDown" style="font-weight: 600; text-shadow: 0 2px 4px rgba(0,0,0,0.1);">
          {{ __('welcome') }}
          <span class="text-primary" style="color: #3498db !important;">{{ config('settings.site_name') }}</span>
        </h1>
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

<div class="container px-4 mt-6">
  <ol class="breadcrumb breadcrumb-style2" aria-label="breadcrumbs">
    <li class="breadcrumb-item">
      <a href="{{ route('home') }}">
        <i class="ti ti-home-check"></i>
        {{ __('Home') }}
      </a>
    </li>
  </ol>
  <div class="progress mt-2">
    <div class="progress-bar" role="progressbar" style="width: 25%;"></div>
  </div>
</div>

<section class="section-py pt-3 " id="testimonials">

  <div class="school-classes container py-5">
    <div class="card">
      <div class="card-body">
        <div class="container mt-4">
          @if(config('settings.google_ads_desktop_home') || config('settings.google_ads_mobile_home'))
          <div class="ads-container text-center my-4">
            @if($detect->isMobile())
            <div class="mobile-ad">
              {!! config('settings.google_ads_mobile_home') !!}
            </div>
            @else
            <div class="desktop-ad">
              {!! config('settings.google_ads_desktop_home') !!}
            </div>
            @endif
          </div>
          @endif
        </div>
        <div class="row">
          <div class="col-md-7 mb-4">
            <div class="row">
              @forelse($classes as $index => $class)
              @php
              $icon = $icons[$class->grade_level] ?? $icons['default'];
              $routeName = request()->is('dashboard/*') ? 'dashboard.class.show' : 'frontend.lesson.show';
              $color = $colors[$index % $colorCount];
              $database = session('database', 'jo');
              @endphp
              <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12 mb-4">
                <a href="{{ route($routeName,  ['database' => $database, 'id' => $class->id]) }}"
                  class="btn btn-outline-{{ $color }} bubbly-button btn-block d-flex align-items-center justify-content-center"
                  style="padding: 15px;">
                  {{ $class->grade_name }}
                </a>
              </div>
              @empty
              <div class="col-12">
                <p class="text-center">{{ __('No classes available.') }}</p>
              </div>
              @endforelse
            </div>

            @if(config('settings.google_ads_desktop_home_2') || config('settings.google_ads_mobile_home_2'))
            <div class="ads-container text-center">
              @if($detect->isMobile())
              {!! config('settings.google_ads_mobile_home_2') !!}
              @else
              {!! config('settings.google_ads_desktop_home_2') !!}
              @endif
            </div>
            @endif
          </div>
          <div class="col-md-5 mb-4">


            <div class="calendar-wrapper">
              <div class="calendar">
                <div class="month-year">
                  <button class="nav-btn" onclick="prevMonth()">
                    <i class="fas fa-chevron-right"></i>
                  </button>
                  <span id="currentMonthYear"></span>
                  <button class="nav-btn" onclick="nextMonth()">
                    <i class="fas fa-chevron-left"></i>
                  </button>
                </div>
                <div class="days">
                  @foreach(['الأحد', 'الإثنين', 'الثلاثاء', 'الأربعاء', 'الخميس', 'الجمعة', 'السبت'] as $day)
                  <div class="day-label">{{ $day }}</div>
                  @endforeach

                  @foreach($calendar as $date => $events)
                  @php
                  $dateObj = \Carbon\Carbon::parse($date);
                  $isToday = $dateObj->isToday();
                  $hasEvents = count($events) > 0;
                  $isDull = $dateObj->month != $currentMonth;
                  @endphp

                  <div class="day {{ $isToday ? 'today' : '' }}
                                              {{ $hasEvents ? 'event' : '' }}
                                              {{ $isDull ? 'dull' : '' }}"
                    @if($hasEvents)
                    data-bs-toggle="modal"
                    data-bs-target="#eventModal"
                    data-title="{{ $events[0]['title'] }}"
                    data-description="{{ $events[0]['description'] }}"
                    data-date="{{ $date }}"
                    @endif>
                    <div class="content">{{ $dateObj->day }}</div>
                  </div>
                  @endforeach
                </div>
              </div>


            </div>

            <div class="row mb-4 mt-12" style="padding-right: 50px;padding-left: 50px;">
              <h3 class="text-center mb-4">{{ __('Quick search') }}</h3>
              <form id="filter-form" method="GET" action="{{ route('files.filter') }}">
                @csrf
                <div class="row mb-4">
                  <div class="form-group">
                    <label for="class-select">{{ __('Select Class') }}</label>
                    <select id="class-select" name="class_id" class="form-control">
                      <option value="">{{ __('Select Class') }}</option>
                      @foreach($classes as $class)
                      <option value="{{ $class->id }}">{{ $class->grade_name }}</option>
                      @endforeach
                    </select>
                  </div>
                  <div class="form-group">
                    <label for="subject-select">{{ __('Select Subject') }}</label>
                    <select id="subject-select" name="subject_id" class="form-control" disabled>
                      <option value="">{{ __('Select Subject') }}</option>
                    </select>
                  </div>
                  <div class="form-group">
                    <label for="semester-select">{{ __('Select Semester') }}</label>
                    <select id="semester-select" name="semester_id" class="form-control" disabled>
                      <option value="">{{ __('Select Semester') }}</option>
                    </select>
                  </div>
                  <div class="form-group">
                    <label for="file_category">{{ __('File Category') }}</label>
                    <select class="form-control" id="file_category" name="file_category">
                      <option value="">{{ __('Select Category') }}</option>
                      <option value="plans">{{ __('Plans') }}</option>
                      <option value="papers">{{ __('Papers') }}</option>
                      <option value="tests">{{ __('Tests') }}</option>
                      <option value="books">{{ __('Books') }}</option>
                    </select>
                  </div>
                </div>
                <div class="text-center mt-4">
                  <button type="submit" class="btn btn-primary w-100 mb-2" style="max-width: 300px;">{{ __('Filter Files') }}</button>
                  <button type="reset" class="btn btn-secondary w-100" style="max-width: 300px;">{{ __('Reset') }}</button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  </div>
</section>

<!-- Modal -->
<div class="modal fade" id="eventModal" tabindex="-1" aria-labelledby="eventModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="eventModalLabel"></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p id="eventDescription"></p>
        <p id="eventDate" class="text-muted"></p>
      </div>
    </div>
  </div>
</div>

<section class="section-py bg-light" id="testimonials">
  <div class="container">
    <div class="row">
      <div class="col-md-6">
        <div class="category-section">
          @php
          $firstHalfCategories = $categories->slice(0, 1);
          @endphp

          @foreach($firstHalfCategories as $category)
          @php
          $filteredNews = $news->filter(function($newsItem) use ($category) {
          return $newsItem->category_id == $category->id;
          })->sortByDesc('created_at')->take(4);
          @endphp

          @if($filteredNews->isNotEmpty())
          <div class="category-section mb-5">
            <div class="card border-primary shadow-sm">
              <div class="card-body">
                <h5 class="card-title mb-4">{{ $category->name }}</h5>
                <ul class="list-group">
                  @foreach($filteredNews as $newsItem)
                  @php
                  $imagePath = $newsItem->image ? asset('storage/' . $newsItem->image) : asset('assets/img/pages/news-default.jpg');
                  @endphp

                  <li class="list-group-item d-flex align-items-center">
                    <img src="{{ $imagePath }}" class="img-thumbnail me-3" style="width: 80px;height: 80px;" alt="{{ $newsItem->title }}" loading="lazy">
                    <div>
                      <h6 class="mb-0">{{ $newsItem->title }}</h6>
                      <small class="text-muted">{{ Str::limit(strip_tags($newsItem->description), 60) }}</small>
                    </div>
                    <a href="{{ route('content.frontend.news.show', ['database' => $database ?? session('database', 'default_database'),'id' => $newsItem->id]) }}" class="btn btn-sm btn-outline-primary ms-auto">{{ __('Read More') }}</a>
                  </li>
                  @endforeach
                </ul>
              </div>
            </div>
          </div>
          @else
          <p class="text-muted">{{ __('No news available for') }} {{ $category->name }}.</p>
          @endif
          @endforeach
        </div>

      </div>

      <div class="col-md-6">
        <div class="category-section">
          @php
          $secondHalfCategories = $categories->slice(1, 1);
          @endphp

          @foreach($secondHalfCategories as $category)
          @php
          $filteredNews = $news->filter(function($newsItem) use ($category) {
          return $newsItem->category_id == $category->id;
          })->sortByDesc('created_at')->take(4);
          @endphp

          @if($filteredNews->isNotEmpty())
          <div class="category-section mb-5">
            <div class="card border-primary shadow-sm">
              <div class="card-body">
                <h5 class="card-title mb-4">{{ $category->name }}</h5>
                <ul class="list-group">
                  @foreach($filteredNews as $newsItem)
                  @php
                  $imagePath = $newsItem->image ? asset('storage/' . $newsItem->image) : asset('assets/img/pages/news-default.jpg');
                  @endphp

                  <li class="list-group-item d-flex align-items-center">
                    <img src="{{ $imagePath }}" class="img-thumbnail me-3" style="width: 80px;height: 80px;" alt="{{ $newsItem->title }}" loading="lazy">
                    <div>
                      <h6 class="mb-0">{{ $newsItem->title }}</h6>
                      <small class="text-muted">{{ Str::limit($newsItem->description, 50) }}</small>
                    </div>
                    <a href="{{ route('content.frontend.news.show', ['database' => $database ?? session('database', 'default_database'),'id' => $newsItem->id]) }}" class="btn btn-sm btn-outline-primary ms-auto">{{ __('Read More') }}</a>
                  </li>
                  @endforeach
                </ul>
              </div>
            </div>
          </div>
          @else
          <p class="text-muted">{{ __('No news available for') }} {{ $category->name }}.</p>
          @endif
          @endforeach
        </div>
      </div>
    </div>
  </div>

  <div class="container mt-4">
    @if(config('settings.google_ads_desktop_home') || config('settings.google_ads_mobile_home'))
    <div class="ads-container text-center my-4">
      @if($detect->isMobile())
      <div class="mobile-ad">
        {!! config('settings.google_ads_mobile_home') !!}
      </div>
      @else
      <div class="desktop-ad">
        {!! config('settings.google_ads_desktop_home') !!}
      </div>
      @endif
    </div>
    @endif
  </div>
</section>

<div class="topics-container">
  <h2>{{ __('Educational Topics') }}</h2>
  <div class="categories-container">
    @php
    $selectedCategories = $categories->slice(2, 8);
    $firstRowCategories = $selectedCategories->slice(0, 4);
    $secondRowCategories = $selectedCategories->slice(4, 4);
    @endphp

    <div class="categories-row" style="display: flex; gap: 20px; justify-content: center; margin-bottom: 20px;">
      @foreach($firstRowCategories as $category)
      @php
      $filteredNews = $news->filter(function($newsItem) use ($category) {
      return $newsItem->category_id == $category->id;
      })->sortByDesc('created_at')->take(4);
      @endphp

      @if($filteredNews->isNotEmpty())
      <div class="flip-card">
        <div class="flip-card-inner">
          <div class="flip-card-front" style="background-image: url('{{ asset('assets/img/pages/news-jo.png') }}'); background-size: cover; background-position: center; color: white;">
            <h3 style="color: white;background: rgba(0, 0, 0, 0.5); padding: 10px; border-radius: 5px;">{{ $category->name }}</h3>
          </div>
          <div class="flip-card-back">
            @foreach ($filteredNews as $newsItem)
            @php
            $imagePath = $newsItem->image ? asset('storage/' . $newsItem->image) : asset('assets/img/pages/news-default.jpg');
            @endphp

            <div class="card-content">
              <h4 class="card-title">
                <a href="{{ route('content.frontend.news.show', ['database' => $database ?? session('database', 'default_database'), 'id' => $newsItem->id]) }}" class="read-more">
                  {{ $newsItem->title }}
                </a>
              </h4>
            </div>
            <hr>
            @endforeach
          </div>
        </div>
      </div>
      @else
      <p class="text-muted">{{ __('No news available for') }} {{ $category->name }}.</p>
      @endif
      @endforeach
    </div>

    <div class="categories-row" style="display: flex; gap: 20px; justify-content: center; margin-bottom: 20px;">
      @foreach($secondRowCategories as $category)
      @php
      $filteredNews = $news->filter(function($newsItem) use ($category) {
      return $newsItem->category_id == $category->id;
      })->sortByDesc('created_at')->take(4);
      @endphp

      @if($filteredNews->isNotEmpty())
      <div class="flip-card">
        <div class="flip-card-inner">
          <div class="flip-card-front">
            <h3 style="color: white;">{{ $category->name }}</h3>
          </div>
          <div class="flip-card-back">
            @foreach ($filteredNews as $newsItem)
            @php
            $imagePath = $newsItem->image ? asset('storage/' . $newsItem->image) : asset('assets/img/pages/news-default.jpg');
            @endphp

            <div class="card-content">
              <h4 class="card-title">
                <a href="{{ route('content.frontend.news.show', ['database' => $database ?? session('database', 'default_database'), 'id' => $newsItem->id]) }}" class="read-more">
                  {{ $newsItem->title }}
                </a>
              </h4>
            </div>
            <hr>
            @endforeach
          </div>
        </div>
      </div>
      @else
      <p class="text-muted">{{ __('No news available for') }} {{ $category->name }}.</p>
      @endif
      @endforeach
    </div>
  </div>

  @if(config('settings.google_ads_desktop_home_2') || config('settings.google_ads_mobile_home_2'))
  <div class="ads-container text-center">
    @if($detect->isMobile())
    {!! config('settings.google_ads_mobile_home_2') !!}
    @else
    {!! config('settings.google_ads_desktop_home_2') !!}
    @endif
  </div>
  @endif
</div>

@push('scripts')
<script>
  // استمع لتغييرات قاعدة البيانات
  document.addEventListener('DOMContentLoaded', function() {
        const calendarEl = document.getElementById('calendar');
        const calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            locale: 'ar',
            events: {
                url: '/dashboard/calendar-events',
                method: 'GET',
                failure: function(error) {
                    console.error('Failed to fetch events:', error);
                }
            },
            eventDidMount: function(info) {
                console.log('Rendered event:', info.event);
            }
        });
        calendar.render();
    });
</script>
@endpush

@endsection
