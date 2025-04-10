@extends('layouts/layoutFront')

@section('title', __('اتصل بنا'))

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
                <h1 class="text-white display-6 fw-bold mb-4">{{ __('اتصل بنا') }}</h1>
                <p class="text-white mb-0 fw-medium">{{ __('نحن هنا للإجابة على استفساراتك ومساعدتك في كل ما تحتاج') }}</p>
            </div>
        </div>
    </div>
</section>

<!-- Main Content -->
<div class="container section-py">
    <div class="row g-4">
        <!-- Contact Information -->
        <div class="col-lg-4">
            <div class="row g-4">
                <!-- Email -->
                <div class="col-12">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center mb-3">
                                <div class="avatar avatar-lg">
                                    <span class="avatar-initial rounded bg-label-primary">
                                        <i class="ti ti-mail fs-4"></i>
                                    </span>
                                </div>
                                <div class="ms-3">
                                    <h5 class="mb-0">{{ __('البريد الإلكتروني') }}</h5>
                                    <p class="mb-0">{{ __('تواصل معنا عبر البريد') }}</p>
                                </div>
                            </div>
                            <a href="mailto:{{ $settings['contact_email'] ?? 'info@alemedu.com' }}" class="d-flex align-items-center">
                                <i class="ti ti-mail me-2"></i>
                                {{ $settings['contact_email'] ?? 'info@alemedu.com' }}
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Phone -->
                @if(isset($settings['contact_phone']) && !empty($settings['contact_phone']))
                <div class="col-12">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center mb-3">
                                <div class="avatar avatar-lg">
                                    <span class="avatar-initial rounded bg-label-success">
                                        <i class="ti ti-phone fs-4"></i>
                                    </span>
                                </div>
                                <div class="ms-3">
                                    <h5 class="mb-0">{{ __('الهاتف') }}</h5>
                                    <p class="mb-0">{{ __('اتصل بنا مباشرة') }}</p>
                                </div>
                            </div>
                            <a href="tel:{{ $settings['contact_phone'] }}" class="d-flex align-items-center">
                                <i class="ti ti-phone me-2"></i>
                                {{ $settings['contact_phone'] }}
                            </a>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Social Media -->
                <div class="col-12">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center mb-3">
                                <div class="avatar avatar-lg">
                                    <span class="avatar-initial rounded bg-label-warning">
                                        <i class="ti ti-brand-facebook fs-4"></i>
                                    </span>
                                </div>
                                <div class="ms-3">
                                    <h5 class="mb-0">{{ __('وسائل التواصل الاجتماعي') }}</h5>
                                    <p class="mb-0">{{ __('تابعنا على منصاتنا') }}</p>
                                </div>
                            </div>
                            <div class="d-flex gap-3">
                                @if(isset($settings['social_facebook']) && !empty($settings['social_facebook']))
                                <a href="{{ $settings['social_facebook'] }}" class="btn btn-icon btn-label-primary" target="_blank">
                                    <i class="ti ti-brand-facebook"></i>
                                </a>
                                @endif
                                @if(isset($settings['social_twitter']) && !empty($settings['social_twitter']))
                                <a href="{{ $settings['social_twitter'] }}" class="btn btn-icon btn-label-info" target="_blank">
                                    <i class="ti ti-brand-twitter"></i>
                                </a>
                                @endif
                                @if(isset($settings['social_linkedin']) && !empty($settings['social_linkedin']))
                                <a href="{{ $settings['social_linkedin'] }}" class="btn btn-icon btn-label-primary" target="_blank">
                                    <i class="ti ti-brand-linkedin"></i>
                                </a>
                                @endif
                                @if(isset($settings['social_whatsapp']) && !empty($settings['social_whatsapp']))
                                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $settings['social_whatsapp']) }}" class="btn btn-icon btn-label-success" target="_blank">
                                    <i class="ti ti-brand-whatsapp"></i>
                                </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                @if(isset($settings['contact_address']) && !empty($settings['contact_address']))
                <!-- Address -->
                <div class="col-12">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center mb-3">
                                <div class="avatar avatar-lg">
                                    <span class="avatar-initial rounded bg-label-info">
                                        <i class="ti ti-map-pin fs-4"></i>
                                    </span>
                                </div>
                                <div class="ms-3">
                                    <h5 class="mb-0">{{ __('العنوان') }}</h5>
                                    <p class="mb-0">{{ __('موقعنا') }}</p>
                                </div>
                            </div>
                            <p class="mb-0">{{ $settings['contact_address'] }}</p>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- Contact Form -->
        <div class="col-lg-8">
            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    <h4 class="mb-4">{{ __('أرسل لنا رسالة') }}</h4>
                    @if(session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                    @endif
                    <form id="contactForm" method="POST" action="{{ route('contact.submit') }}">
                        @csrf
                        <div class="row g-4">
                            <!-- Name -->
                            <div class="col-md-6">
                                <label class="form-label" for="name">{{ __('الاسم الكامل') }}</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="ti ti-user"></i></span>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                                    @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Email -->
                            <div class="col-md-6">
                                <label class="form-label" for="email">{{ __('البريد الإلكتروني') }}</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="ti ti-mail"></i></span>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required>
                                    @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Phone -->
                            <div class="col-md-6">
                                <label class="form-label" for="phone">{{ __('رقم الهاتف') }}</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="ti ti-phone"></i></span>
                                    <input type="tel" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone') }}">
                                    @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Subject -->
                            <div class="col-md-6">
                                <label class="form-label" for="subject">{{ __('الموضوع') }}</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="ti ti-article"></i></span>
                                    <input type="text" class="form-control @error('subject') is-invalid @enderror" id="subject" name="subject" value="{{ old('subject') }}" required>
                                    @error('subject')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Message -->
                            <div class="col-12">
                                <label class="form-label" for="message">{{ __('الرسالة') }}</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="ti ti-message"></i></span>
                                    <textarea class="form-control @error('message') is-invalid @enderror" id="message" name="message" rows="4" required>{{ old('message') }}</textarea>
                                    @error('message')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <div class="col-12 text-end">
                                <button type="submit" class="btn btn-primary">
                                    <i class="ti ti-send me-2"></i>
                                    {{ __('إرسال الرسالة') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @if(isset($settings['contact_address']) && !empty($settings['contact_address']))
    <!-- Map Section -->
    <div class="row mt-5">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-body p-0">
                    <div class="ratio ratio-21x9">
                        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d108513.06859756269!2d35.8747931!3d31.9515694!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x151ca7e4aee722d5%3A0x8693a9183825010b!2z2LnZhdmR2KfZhtiMINin2YTYo9ix2K_Zhg!5e0!3m2!1sar!2s!4v1660000000000!5m2!1sar!2s" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection
