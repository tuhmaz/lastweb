@extends('layouts/layoutFront')

@section('title', __('من نحن'))

@section('content')
<!-- Hero Section -->
<section class="section-py first-section-pt help-center-header position-relative overflow-hidden" style="background: linear-gradient(226deg, #202c45 0%, #286aad 100%);">
    <!-- Background Pattern -->
    <div class="position-absolute w-100 h-100" style="background: linear-gradient(45deg, rgba(40, 106, 173, 0.1), transparent); top: 0; left: 0;"></div>

    <!-- Animated Shapes -->
    <div class="position-absolute" style="width: 300px; height: 300px; background: radial-gradient(circle, rgba(40, 106, 173, 0.1) 0%, transparent 70%); top: -150px; right: -150px; border-radius: 50%;"></div>
    <div class="position-absolute" style="width: 200px; height: 200px; background: radial-gradient(circle, rgba(40, 106, 173, 0.1) 0%, transparent 70%); bottom: -100px; left: -100px; border-radius: 50%;"></div>

    <div class="container position-relative">
        <div class="row justify-content-center">
            <div class="col-12 col-lg-8 text-center">
                <h1 class="text-white display-6 fw-bold mb-4">{{ __('من نحن') }}</h1>
                <p class="text-white mb-0 fw-medium">{{ __('مرحبًا بكم في موقع {{ config('settings.site_name') }}، المنصة التعليمية المميزة المصممة لدعم الطلاب والمعلمين في رحلتهم التعليمية.') }}</p>
            </div>
        </div>
    </div>
</section>

<!-- Main Content -->
<div class="container section-py">
    <!-- رؤيتنا ورسالتنا -->
    <div class="row mb-5">
        <div class="col-md-6 mb-4 mb-md-0">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-3">
                        <i class="ti ti-eye fs-3 text-primary me-2"></i>
                        <h2 class="h4 mb-0">{{ __('رؤيتنا') }}</h2>
                    </div>
                    <p class="mb-0">{{ __('نسعى إلى أن نكون المصدر الأول للمحتوى التعليمي الموثوق والشامل، متماشين مع المنهاج الأردني، مع تسهيل الوصول إلى المواد التعليمية والاختبارات والمقالات الإرشادية للطلاب والمعلمين على حد سواء.') }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-3">
                        <i class="ti ti-target fs-3 text-primary me-2"></i>
                        <h2 class="h4 mb-0">{{ __('رسالتنا') }}</h2>
                    </div>
                    <p class="mb-0">{{ __('تقديم تجربة تعليمية متكاملة تعتمد على توفير موارد تعليمية عالية الجودة تساهم في تحسين أداء الطلاب والمعلمين وتطوير البيئة التعليمية بشكل عام.') }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- ماذا نقدم -->
    <div class="card shadow-sm border-0 mb-5">
        <div class="card-body p-4">
            <div class="d-flex align-items-center mb-4">
                <i class="ti ti-gift fs-3 text-primary me-2"></i>
                <h2 class="h4 mb-0">{{ __('ماذا نقدم؟') }}</h2>
            </div>
            <p>{{ __('يقدم موقع {{ config('settings.site_name') }} مجموعة واسعة من الخدمات التعليمية المصممة بعناية، بما في ذلك:') }}</p>
            <div class="row g-4">
                <div class="col-md-6">
                    <div class="d-flex">
                        <i class="ti ti-school fs-4 text-primary me-2"></i>
                        <div>
                            <h3 class="h6 fw-bold">{{ __('صفوف دراسية') }}</h3>
                            <p class="mb-0">{{ __('تغطي جميع الصفوف من التمهيدي حتى الصف الثاني عشر.') }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="d-flex">
                        <i class="ti ti-books fs-4 text-primary me-2"></i>
                        <div>
                            <h3 class="h6 fw-bold">{{ __('مواد دراسية') }}</h3>
                            <ul class="mb-0 ps-3">
                                <li>{{ __('الخطة الدراسية') }}</li>
                                <li>{{ __('أوراق العمل والكورسات') }}</li>
                                <li>{{ __('الاختبارات الشهرية والنهائية') }}</li>
                                <li>{{ __('الكتب الرسمية ودليل المعلم') }}</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="d-flex">
                        <i class="ti ti-news fs-4 text-primary me-2"></i>
                        <div>
                            <h3 class="h6 fw-bold">{{ __('أخبار تربوية') }}</h3>
                            <p class="mb-0">{{ __('تشمل آخر أخبار وزارة التربية والتعليم، وأخبار المعلمين، والمقالات الإرشادية.') }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="d-flex">
                        <i class="ti ti-filter fs-4 text-primary me-2"></i>
                        <div>
                            <h3 class="h6 fw-bold">{{ __('تصفية المحتوى') }}</h3>
                            <p class="mb-0">{{ __('أدوات بحث وتصنيف متقدمة تتيح للمستخدمين الوصول إلى المحتوى المناسب بسهولة.') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- قيمنا -->
    <div class="row mb-5">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-4">
                        <i class="ti ti-heart fs-3 text-primary me-2"></i>
                        <h2 class="h4 mb-0">{{ __('قيمنا') }}</h2>
                    </div>
                    <div class="row g-4">
                        <div class="col-md-4">
                            <div class="d-flex">
                                <i class="ti ti-award fs-4 text-primary me-2"></i>
                                <div>
                                    <h3 class="h6 fw-bold">{{ __('الجودة') }}</h3>
                                    <p class="mb-0">{{ __('تقديم محتوى تعليمي متميز ودقيق.') }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="d-flex">
                                <i class="ti ti-users fs-4 text-primary me-2"></i>
                                <div>
                                    <h3 class="h6 fw-bold">{{ __('التعاون') }}</h3>
                                    <p class="mb-0">{{ __('تعزيز بيئة تعليمية تدعم الشراكة بين الطلاب والمعلمين.') }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="d-flex">
                                <i class="ti ti-bulb fs-4 text-primary me-2"></i>
                                <div>
                                    <h3 class="h6 fw-bold">{{ __('الإبداع') }}</h3>
                                    <p class="mb-0">{{ __('استخدام أدوات وتقنيات حديثة لتحسين تجربة المستخدم.') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- التواصل معنا -->
    <div class="card shadow-sm border-0">
        <div class="card-body p-4">
            <div class="d-flex align-items-center mb-4">
                <i class="ti ti-mail fs-3 text-primary me-2"></i>
                <h2 class="h4 mb-0">{{ __('التواصل معنا') }}</h2>
            </div>
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
