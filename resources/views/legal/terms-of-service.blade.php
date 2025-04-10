@php
$configData = Helper::appClasses();
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Detection\MobileDetect;

$detect = new MobileDetect;
@endphp

@extends('layouts/layoutFront')

@section('title', __('Terms of Service'))

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
                {{ __('Terms of Service') }}
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
                <li class="breadcrumb-item active" aria-current="page">{{ __('Terms of Service') }}</li>
            </ol>
        </nav>
        <div class="progress mt-2" style="height: 6px;">
            <div class="progress-bar bg-primary" role="progressbar" style="width: 50%;" aria-valuenow="50"
                 aria-valuemin="0" aria-valuemax="100"></div>
        </div>
    </div>

<div class="container my-5"> <h1 class="mb-4">{{ __('Terms of Service') }}</h1> <div class="card"> <div class="card-body">  
 
    <p>آخر تحديث: 18 يناير 2025</p>
    <p>يرجى قراءة شروط وأحكام الاستخدام بعناية قبل استخدام موقع <strong>موقع {{ config('settings.site_name') }}</strong> (<a href="https://alemedu.com" target="_blank">https://alemedu.com</a>). باستخدامك لهذا الموقع، فإنك توافق على الالتزام بالشروط والأحكام التالية.</p>

    <h2>1. مقدمة</h2>
    <p>يهدف موقع {{ config('settings.site_name') }} إلى توفير محتوى تعليمي متكامل ومحدث يتماشى مع المنهاج الأردني. يتم تقسيم المحتوى إلى صفوف دراسية، مواد تعليمية، وأقسام مرفقات تهدف لدعم العملية التعليمية. يوفر الموقع أيضًا مقالات تعليمية وأخبار مخصصة للمعلمين والإدارة المدرسية.</p>

    <h2>2. التعريفات</h2>
    <ul>
        <li><strong>"الموقع":</strong> يشير إلى موقع {{ config('settings.site_name') }} (<a href="https://alemedu.com" target="_blank">https://alemedu.com</a>).</li>
        <li><strong>"الخدمة":</strong> تعني جميع المحتويات، المواد التعليمية، والمرفقات التي يوفرها الموقع.</li>
        <li><strong>"المستخدم":</strong> أي شخص يصل إلى الموقع أو يستخدمه، سواء كان مديرًا، مشرفًا، أو عضوًا.</li>
        <li><strong>"العضوية":</strong> تعني الحساب المسجل الذي يمكن للمستخدم الوصول من خلاله إلى ميزات محددة.</li>
    </ul>

    <h2>3. الأدوار والصلاحيات</h2>
    <p>يقسم الموقع صلاحيات المستخدمين إلى ثلاث فئات:</p>
    <ul>
        <li><strong>المدير:</strong> يتمتع بكامل الصلاحيات لإدارة المحتوى، المستخدمين، والإعدادات.</li>
        <li><strong>المشرف:</strong> يقتصر دوره على إدارة المقالات (إضافة، تعديل، حذف).</li>
        <li><strong>العضو:</strong> يمكنه التعليق على المقالات وتحميل المرفقات فقط.</li>
    </ul>

    <h2>4. استخدام الخدمة</h2>
    <p>باستخدامك للموقع، فإنك توافق على:</p>
    <ul>
        <li>عدم استخدام الموقع لأي غرض غير قانوني أو ينتهك القوانين الأردنية.</li>
        <li>عدم محاولة اختراق أو تعطيل عمل الموقع.</li>
        <li>استخدام المحتوى المتاح فقط للأغراض التعليمية الشخصية وعدم إعادة توزيعه دون إذن مسبق.</li>
    </ul>

    <h2>5. الملكية الفكرية</h2>
    <p>جميع المحتويات المنشورة على الموقع، بما في ذلك النصوص، الصور، والشعارات، هي ملك لموقع {{ config('settings.site_name') }} وتحميها قوانين حقوق الملكية الفكرية. يُحظر نسخ أو استخدام أي جزء من الموقع دون إذن كتابي مسبق.</p>

    <h2>6. سياسة المرفقات</h2>
    <p>يحتوي الموقع على مرفقات تعليمية تشمل:</p>
    <ul>
        <li>الخطة الدراسية.</li>
        <li>أوراق العمل وكورسات المواد.</li>
        <li>الاختبارات الشهرية والنهائية.</li>
        <li>الكتب الرسمية ودليل المعلم.</li>
    </ul>
    <p>يُسمح بتنزيل المرفقات للاستخدام الشخصي فقط، ويحظر توزيعها أو استخدامها لأغراض تجارية.</p>

    <h2>7. حدود المسؤولية</h2>
    <p>لا يتحمل الموقع أي مسؤولية عن:</p>
    <ul>
        <li>أي أضرار مباشرة أو غير مباشرة ناتجة عن استخدامك للمحتوى أو المرفقات.</li>
        <li>أي معلومات غير دقيقة أو غير محدثة على الموقع.</li>
        <li>أي انقطاع في الخدمة بسبب عوامل خارجة عن إرادتنا.</li>
    </ul>

    <h2>8. التعديلات</h2>
    <p>يحتفظ موقع {{ config('settings.site_name') }} بالحق في تعديل هذه الشروط والأحكام في أي وقت. سيتم إشعار المستخدمين بأي تغييرات من خلال تحديث هذه الصفحة. استمرارك في استخدام الموقع يعني قبولك للشروط المعدلة.</p>

    <h2>9. القانون الحاكم</h2>
    <p>تُفسر هذه الشروط والأحكام وفقًا لقوانين المملكة الأردنية الهاشمية.</p>

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
</div>




@endsection

 
