@php
    use Illuminate\Support\Facades\Storage;
    use Illuminate\Support\Str;
    use Detection\MobileDetect;
    $detect = new MobileDetect;

    // Array of available colors
    $colors = ['primary', 'secondary', 'success', 'danger', 'warning', 'info', 'dark'];
    $colorCount = count($colors);

    // Icons based on grade_name
    $icons = [
        '1'       => 'ti ti-number-0',
        '2'       => 'ti ti-number-1',
        '3'       => 'ti ti-number-2',
        '4'       => 'ti ti-number-3',
        '5'       => 'ti ti-number-4',
        '6'       => 'ti ti-number-5',
        '7'       => 'ti ti-number-6',
        '8'       => 'ti ti-number-7',
        '9'       => 'ti ti-number-8',
        '10'      => 'ti ti-number-9',
        '11'      => 'ti ti-number-10-small',
        '12'      => 'ti ti-number-11-small',
        '13'      => 'ti ti-number-12-small',
        'default' => 'ti ti-book',
    ];

    // Get the selected database from the session
    $database = session('database', 'jo'); // Default to 'jo' if not set
@endphp

@extends('layouts.layoutFront')

{{-- Title Tag --}}
@section('title', __('Our Classes'))

{{-- Page Styles --}}
@section('page-style')
    {{-- استيراد ملفات CSS إضافية إن لزم --}}
    @vite(['resources/assets/vendor/scss/but.scss'])
    <style>
        .hover-elevate-up {
            transition: transform 0.3s ease;
        }
        .hover-elevate-up:hover {
            transform: translateY(-5px);
        }
        /* يمكنك إضافة تنسيقات إضافية خاصة بهذه الصفحة هنا */
    </style>
@endsection

{{-- Page Scripts --}}
@section('page-script')
    @vite(['resources/assets/vendor/js/but.js'])
@endsection

{{-- Meta Tags --}}
@section('meta')
    <meta name="description" content="{{ __('Explore our comprehensive collection of educational classes') }}">
    <meta name="keywords" content="education, classes, learning, {{ $database }}">
    <link rel="canonical" href="{{ url()->current() }}">
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
                <li class="breadcrumb-item active" aria-current="page">{{ __('Classes') }}</li>
            </ol>
        </nav>
        <div class="progress mt-2" style="height: 6px;">
            <div class="progress-bar bg-primary" role="progressbar" style="width: 50%;" aria-valuenow="50"
                 aria-valuemin="0" aria-valuemax="100"></div>
        </div>
    </div>

    <!-- إعلانات (اختياري) -->
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

    <!-- Section 2: قائمة الصفوف -->
    <section class="section-classes text-center" id="classes-section">
        <div class="school-classes container py-5">
            <h2 class="text-center mb-4">{{ __('Our Classes') }}</h2>
            <div class="row">
                @forelse($lesson as $index => $class)
                    @php
                        // Assign icon based on grade_name or use default
                        $icon = $icons[$class->grade_level] ?? $icons['default'];
                        // Determine the display route
                        $routeName = request()->is('dashboard/*')
                                    ? 'dashboard.class.show'
                                    : 'frontend.lesson.show';
                        // Assign color based on index
                        $color = $colors[$index % $colorCount];
                    @endphp

                    <div class="col-xl-4 col-md-6 col-sm-12 mb-4">
                    <a href="{{ route('frontend.lesson.show', ['database' => session('database', 'jo'),'id' => $class->id]) }}"
   class="btn btn-outline-{{ $color }} bubbly-button btn-block d-flex align-items-center justify-content-center hover-elevate-up"
   style="padding: 15px;">
    <i class="badge bg-cyan text-cyan-fg {{ $icon }} me-2"></i>
    {{ $class->grade_name }}
</a>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="alert alert-info">
                            {{ __('No lessons available') }}
                        </div>
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    <!-- إعلانات ثانية (اختياري) -->
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
@endsection
