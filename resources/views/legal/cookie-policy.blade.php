    @php
    $configData = Helper::appClasses();
    use Illuminate\Support\Facades\Storage;
    use Illuminate\Support\Str;
    use Detection\MobileDetect;

    $detect = new MobileDetect;
    @endphp

    @extends('layouts/layoutFront')

    @section('title', __('Cookie Policy'))

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
                    {{ __('Cookie Policy') }}
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
                <li class="breadcrumb-item active" aria-current="page">{{ __('Cookie Policy') }}</li>
            </ol>
        </nav>
        <div class="progress mt-2" style="height: 6px;">
            <div class="progress-bar bg-primary" role="progressbar" style="width: 50%;" aria-valuenow="50"
                 aria-valuemin="0" aria-valuemax="100"></div>
        </div>
    </div>

    <div class="container my-5"> <h1 class="mb-4">{{ __('Cookie Policy') }}</h1> <div class="card"> <div class="card-body"> 
    
        <p>آخر تحديث: 18 يناير 2025</p>
        <p>توضح سياسة ملفات تعريف الارتباط هذه كيفية استخدام موقع <strong>{{ config('settings.site_name') }}</strong> (<a href="https://alemedu.com" target="_blank">https://alemedu.com</a>) ملفات تعريف الارتباط (Cookies) لتحسين تجربة المستخدم وتقديم خدمات مخصصة.</p>

        <h2>1. ما هي ملفات تعريف الارتباط؟</h2>
        <p>ملفات تعريف الارتباط هي ملفات نصية صغيرة يتم تخزينها على جهازك عند زيارة موقعنا. تُستخدم هذه الملفات لتذكر تفضيلاتك وتحسين تجربتك على الموقع.</p>

        <h2>2. كيفية استخدامنا لملفات تعريف الارتباط</h2>
        <p>نستخدم ملفات تعريف الارتباط للأغراض التالية:</p>
        <ul>
            <li><strong>ملفات تعريف الارتباط الضرورية:</strong> تُستخدم لتمكين الميزات الأساسية مثل التنقل في الموقع والوصول الآمن.</li>
            <li><strong>ملفات تعريف الارتباط التحليلية:</strong> تساعدنا في فهم كيفية استخدام الزوار للموقع لتحسين أدائه.</li>
            <li><strong>ملفات تعريف الارتباط الوظيفية:</strong> تُستخدم لتذكر تفضيلاتك مثل اللغة أو الإعدادات المخصصة.</li>
            <li><strong>ملفات تعريف الارتباط الإعلانية:</strong> تُستخدم لعرض الإعلانات ذات الصلة بناءً على اهتماماتك.</li>
        </ul>

        <h2>3. أنواع ملفات تعريف الارتباط التي نستخدمها</h2>
        <p>يمكن تصنيف ملفات تعريف الارتباط التي نستخدمها إلى نوعين:</p>
        <ul>
            <li><strong>ملفات تعريف الارتباط الدائمة:</strong> تظل هذه الملفات مخزنة على جهازك حتى تنتهي صلاحيتها أو تقوم بحذفها يدويًا.</li>
            <li><strong>ملفات تعريف الارتباط المؤقتة (الجلسات):</strong> تُحذف هذه الملفات تلقائيًا عند إغلاق متصفحك.</li>
        </ul>

        <h2>4. التحكم في ملفات تعريف الارتباط</h2>
        <p>يمكنك التحكم في استخدام ملفات تعريف الارتباط أو إيقافها بالكامل من خلال إعدادات متصفحك. ومع ذلك، قد يؤدي ذلك إلى عدم تمكنك من استخدام بعض ميزات الموقع.</p>
        <p>للتحكم في ملفات تعريف الارتباط، اتبع الإرشادات الموجودة في إعدادات متصفحك:</p>
        <ul>
            <li>لـ Google Chrome: <a href="https://support.google.com/chrome/answer/95647?hl=ar" target="_blank">إرشادات كروم</a></li>
            <li>لـ Mozilla Firefox: <a href="https://support.mozilla.org/ar/kb/حظر-ملفات-تعريف-الارتباط" target="_blank">إرشادات فايرفوكس</a></li>
            <li>لـ Safari: <a href="https://support.apple.com/ar-sa/guide/safari/sfri11471/mac" target="_blank">إرشادات سفاري</a></li>
        </ul>

        <h2>5. ملفات تعريف الارتباط الخارجية</h2>
        <p>قد نستخدم خدمات خارجية مثل Google Analytics لتحليل أداء الموقع. هذه الخدمات قد تضع ملفات تعريف ارتباط خاصة بها لجمع البيانات حول استخدامك للموقع. نحن لا نتحكم في ملفات تعريف الارتباط التي يتم وضعها بواسطة الجهات الخارجية.</p>

        <h2>6. تحديث سياسة ملفات تعريف الارتباط</h2>
        <p>قد يتم تحديث سياسة ملفات تعريف الارتباط من وقت لآخر لتلبية المتطلبات القانونية أو التكنولوجية. يُنصح بمراجعة هذه الصفحة بانتظام للحصول على أحدث المعلومات.</p>

    
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
