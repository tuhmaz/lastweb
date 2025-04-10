@php
$configData = Helper::appClasses();
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Detection\MobileDetect;

$detect = new MobileDetect;
@endphp

@extends('layouts/layoutFront')

@section('title', __('Disclaimer'))

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
                {{ __('Disclaimer') }}
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
                <li class="breadcrumb-item active" aria-current="page">{{ __('Disclaimer') }}</li>
            </ol>
        </nav>
        <div class="progress mt-2" style="height: 6px;">
            <div class="progress-bar bg-primary" role="progressbar" style="width: 50%;" aria-valuenow="50"
                 aria-valuemin="0" aria-valuemax="100"></div>
        </div>
    </div>

<div class="container my-5"> <h1 class="mb-4">{{ __('Disclaimer') }}</h1> <div class="card"> <div class="card-body"> 
 
    <p>آخر تحديث: 18 يناير 2025</p>

    <h2>1. الغرض من الموقع</h2>
    <p>موقع <strong>{{ config('settings.site_name') }}</strong> (<a href="{{ $settings['canonical_url'] ?? 'https://alemedu.com' }}" target="_blank" class="text-decoration-none">{{ $settings['canonical_url'] ?? 'https://alemedu.com' }}</a>) هو منصة تعليمية تهدف إلى تقديم محتوى تعليمي محدث ومصمم لدعم العملية التعليمية وفقًا للمنهاج الأردني. جميع المعلومات والمحتويات المقدمة على هذا الموقع هي لأغراض تعليمية وإرشادية فقط.</p>

    <h2>2. دقة المعلومات</h2>
    <p>نحن نسعى لضمان دقة وصحة جميع المعلومات المقدمة على الموقع. ومع ذلك، لا نضمن أن تكون جميع المواد والمحتويات خالية تمامًا من الأخطاء أو محدثة بشكل كامل. يتحمل المستخدم مسؤولية التحقق من المعلومات قبل الاعتماد عليها.</p>

    <h2>3. حدود المسؤولية</h2>
    <p>موقع {{ config('settings.site_name') }} غير مسؤول عن:</p>
    <ul>
        <li>أي أضرار مباشرة أو غير مباشرة قد تنجم عن استخدامك للموقع أو الاعتماد على محتوياته.</li>
        <li>أي خسائر أو أضرار تتعلق بتنزيل المرفقات أو المستندات التعليمية من الموقع.</li>
        <li>أي انقطاع في الخدمة بسبب مشكلات تقنية أو خارجية.</li>
    </ul>

    <h2>4. الروابط الخارجية</h2>
    <p>قد يحتوي الموقع على روابط لمواقع إلكترونية خارجية. هذه الروابط توفرها موقع {{ config('settings.site_name') }} لتسهيل الوصول إلى مصادر إضافية. نحن غير مسؤولين عن محتوى أو سياسات الخصوصية الخاصة بهذه المواقع الخارجية.</p>

    <h2>5. الاستخدام الشخصي وغير التجاري</h2>
    <p>جميع المحتويات والمواد التعليمية المقدمة على الموقع مصممة للاستخدام الشخصي وغير التجاري. يُحظر نسخ أو إعادة توزيع أي محتوى دون إذن كتابي مسبق.</p>

    <h2>6. تحديث إخلاء المسؤولية</h2>
    <p>قد يتم تحديث هذه الصفحة من وقت لآخر لتعكس تغييرات في السياسات أو اللوائح. يُنصح المستخدمون بمراجعة هذه الصفحة بانتظام للتأكد من فهمهم لأحدث النسخ.</p>

    <h2 class="h4 mb-0">{{ __('التواصل معنا') }}</h2>
                
                <p>{{ __('إذا كانت لديك أي أسئلة أو اقتراحات، يسعدنا أن نتواصل معك عبر:') }}</p>
                <div class="d-flex flex-column gap-2">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-mail fs-4 text-primary me-2"></i>
                        <a href="mailto:{{ $settings['contact_email'] ?? 'info@alemedu.com' }}" class="d-flex align-items-center">
                                    
                                    {{ $settings['contact_email'] ?? 'info@alemedu.com' }}
                                </a>
                    </div>
                    <div class="d-flex align-items-center">
                        <i class="ti ti-world fs-4 text-primary me-2"></i>
                        <a href="{{ $settings['canonical_url'] ?? 'https://alemedu.com' }}" target="_blank" class="text-decoration-none">{{ $settings['canonical_url'] ?? 'https://alemedu.com' }}</a>
                    </div>
                </div>
                </div>
</div>
</div>




@endsection
