@php
$configData = Helper::appClasses();
use Detection\MobileDetect;

$detect = new MobileDetect;
@endphp

@extends('layouts.layoutFront')

@section('title', ($subject->schoolClass->grade_name ?? 'Grade Name') . ' - ' . ($subject->subject_name ?? 'Subject Name'))

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
                    {{ $subject->subject_name }}
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
    <ol class="breadcrumb breadcrumb-style2" aria-label="breadcrumbs">
        <li class="breadcrumb-item"><a href="{{ route('home') }}"><i class="ti ti-home-check"></i>{{ __('Home') }}</a></li>
        <li class="breadcrumb-item"><a href="{{ route('frontend.lesson.index', ['database' => $database ?? session('database', 'default_database')]) }}">{{ __('Classes') }}</a></li>
        <li class="breadcrumb-item">
            <a href="{{ route('frontend.lesson.show', ['database' => $database ?? session('database'),'id' => $subject->schoolClass->id]) }}">
                {{ $subject->schoolClass->grade_name }}
            </a>
        </li>
        <li class="breadcrumb-item active" aria-current="page">{{ $subject->subject_name }}</li>
    </ol>
    <div class="progress mt-2">
        <div class="progress-bar bg-primary" role="progressbar" style="width: 50%;"></div>
    </div>
</div>

<!-- Content Section -->
<div class="container mt-4 mb-4">
    @if(config('settings.google_ads_desktop_subject') || config('settings.google_ads_mobile_subject'))
    <div class="ads-container text-center">
        @if($detect->isMobile())
        {!! config('settings.google_ads_mobile_subject') !!}
        @else
        {!! config('settings.google_ads_desktop_subject') !!}
        @endif
    </div>
    @endif

    <!-- Semesters Section -->
    <section class="section-py bg-body first-section-pt" style="padding-top: 10px;">
        <div class="container">
            @foreach(['الفصل الدراسي الأول', 'الفصل الدراسي الثاني'] as $semester_name)
            <div class="my-4">
                <div class="row justify-content-center">
                    <div class="col-lg-10">
                        <div class="p-4 pb-0 pe-lg-0 pt-lg-4 align-items-center rounded-3 border shadow-lg">
                            <div class="text-center p-3 p-lg-4 pt-lg-3">
                                <h5 class="fw-bold lh-3 text-body-emphasis">{{ $semester_name }} - {{ $subject->subject_name }}</h5>
                                @foreach($semesters->where('semester_name', $semester_name)->where('grade_level', $subject->grade_level) as $semester)
                                <div class="d-flex flex-wrap justify-content-center gap-3 mt-4">
                                    <a href="{{ route('frontend.subject.articles', ['database' => $database ?? session('database'),'subject' => $subject->id, 'semester' => $semester->id, 'category' => 'plans']) }}" class="btn btn-outline-secondary">{{ __('Study Plans') }}</a>
                                    <a href="{{ route('frontend.subject.articles', ['database' => $database ?? session('database'),'subject' => $subject->id, 'semester' => $semester->id, 'category' => 'papers']) }}" class="btn btn-outline-success">{{ __('Worksheets') }}</a>
                                    <a href="{{ route('frontend.subject.articles', ['database' => $database ?? session('database'),'subject' => $subject->id, 'semester' => $semester->id, 'category' => 'tests']) }}" class="btn btn-outline-danger">{{ __('Tests') }}</a>
                                    <a href="{{ route('frontend.subject.articles', ['database' => $database ?? session('database'),'subject' => $subject->id, 'semester' => $semester->id, 'category' => 'books']) }}" class="btn btn-outline-warning">{{ __('School Books') }}</a>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </section>
</div>
@endsection
