@php
$configData = Helper::appClasses();
use Detection\MobileDetect;
$detect = new MobileDetect;

$database = session('database', 'jo');

$subjectData = \App\Models\Subject::on($database)->find($subject->id);
@endphp

@extends('layouts/layoutFront')

@section('title', ($subject->schoolClass->grade_name?? 'Grade Name') . ' - ' . ($subjectData->subject_name ?? 'Subject Name')  . ' - ' . __($category) . ' - ' . $semester->semester_name)

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
                {{ $subjectData->subject_name }}
                <p class="text-center text-white px-4 mb-0" style="font-size: medium;">{{ $subjectData->subject_name }} - {{ $semester->semester_name }}</p>
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
     <li class="breadcrumb-item">
      <a href="{{ route('home') }}">
        <i class="ti ti-home-check"></i>{{ __('Home') }}
      </a>
    </li>

     <li class="breadcrumb-item">
      <a href="{{ route('frontend.lesson.index', ['database' => $database]) }}">
        {{ __('Classes') }}
      </a>
    </li>

     <li class="breadcrumb-item">
    <a href="{{ route('frontend.lesson.show', [
    'database' => $database ?? session('database', 'jo'),
    'id' => $subject->schoolClass->id]) }}">
    {{ $subject->schoolClass->grade_name }}
</a>

    </li>

     <li class="breadcrumb-item">
    <a href="{{ route('frontend.subjects.show', [
        'id' => $subjectData->schoolClass->id,
        'subject' => $subjectData->id,
        'database' => $database
    ]) }}">
        {{ $subjectData->subject_name }}
    </a>
</li>

     <li class="breadcrumb-item active" aria-current="page">
      {{ __($category) }} - {{ $semester->semester_name }}
    </li>
  </ol>
  <div class="progress mt-2">
    <div class="progress-bar" role="progressbar" style="width: 75%;"></div>
  </div>
</div>



<section class="section-py bg-body first-section-pt " style="padding-top: 10px;">
 <div class="container mt-4 mb-4">
  @if(config('settings.google_ads_desktop_article') || config('settings.google_ads_mobile_article'))
  <div class="ads-container text-center">
    @if($detect->isMobile())
    {!! config('settings.google_ads_mobile_article') !!}
    @else
    {!! config('settings.google_ads_desktop_article') !!}
    @endif
  </div>
  @endif
</div>
  <div class="container">
    <div class="card px-3 mt-6">
      <div class="row">
        <div class="content-header text-white text-center bg-primary py-4">
          <h3 class="text-white">
            @switch($category)
            @case('plans')
            {{ __('study_plans') }}
            @break
            @case('papers')
            {{ __('worksheets') }}
            @break
            @case('tests')
            {{ __('tests') }}
            @break
            @case('books')
            {{ __('school_books') }}
            @break
            @default
            {{ __('articles') }}
            @endswitch - {{ $subjectData->subject_name }}
          </h3>
        </div>

        <div class="content-body text-center">
    @forelse ($articles as $article)
        @php
            $file = $article->files->first();
            $fileType = $file ? $file->file_type : 'default';

            $fileTypeText = match($fileType) {
                'pdf' => 'PDF',
                'doc', 'docx' => 'Word Document',
                'xls', 'xlsx' => 'Excel Spreadsheet',
                default => 'Unknown',
            };

            $imagePath = match($fileType) {
                'pdf' => asset('assets/img/icon/pdf-icon.png'),
                'doc', 'docx' => asset('assets/img/icon/word-icon.png'),
                'xls', 'xlsx' => asset('assets/img/icon/excel-icon.png'),
                default => asset('assets/img/icon/default-icon.png'),
            };
        @endphp

        <div class="list-group mb-3">
            <a href="{{ route('frontend.articles.show', ['article' => $article->id, 'database' => $database]) }}" class="list-group-item list-group-item-action d-flex align-items-center">
                <img src="{{ $imagePath }}" class="rounded-circle me-4 w-px-50 img-fluid" alt="File Icon">
                <div class="w-100 d-flex justify-content-between">
                    <div class="file-info">
                        <h6 class="mb-1">{{ $article->title }}</h6>
                    </div>
                </div>
            </a>
        </div>
    @empty
        <p>{{ __('no_articles_available') }}</p>
    @endforelse
 <!-- روابط التنقل بين الصفحات -->
 <div class="pagination pagination-outline-secondary">
        {{ $articles->links('components.pagination.custom') }}
    </div>
</div>

     <div class="container mt-4 mb-4">
      @if(config('settings.google_ads_desktop_article_2') || config('settings.google_ads_mobile_article_2'))
      <div class="ads-container text-center">
        @if($detect->isMobile())
        {!! config('settings.google_ads_mobile_article_2') !!}
        @else
        {!! config('settings.google_ads_desktop_article_2') !!}
        @endif
      </div>
      @endif
    </div>


  </div>
</section>

@endsection
