@extends('layouts/contentNavbarLayout')

@section('title', __('Settings'))

@section('page-script')
@vite(['resources/assets/js/settings.js'])
@endsection

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">{{ __('Settings') }}</h5>
      </div>
      <div class="card-body">
        <div class="nav-align-top">
          <ul class="nav nav-tabs" role="tablist">
            <li class="nav-item">
              <button type="button" class="nav-link active" role="tab" data-bs-toggle="tab" data-bs-target="#general" aria-controls="general" aria-selected="true">
                <i class="tf-icons bx bx-cog me-1"></i> {{ __('General') }}
              </button>
            </li>
            <li class="nav-item">
              <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#appearance" aria-controls="appearance" aria-selected="false">
                <i class="tf-icons bx bx-palette me-1"></i> {{ __('Appearance') }}
              </button>
            </li>
            <li class="nav-item">
              <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#seo" aria-controls="seo" aria-selected="false">
                <i class="tf-icons bx bx-search-alt me-1"></i> {{ __('SEO') }}
              </button>
            </li>
            <li class="nav-item">
              <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#contact" aria-controls="contact" aria-selected="false">
                <i class="tf-icons bx bx-envelope me-1"></i> {{ __('Contact') }}
              </button>
            </li>
            <li class="nav-item">
              <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#mail" aria-controls="mail" aria-selected="false">
                <i class="tf-icons bx bx-mail-send me-1"></i> {{ __('Mail') }}
              </button>
            </li>
            <li class="nav-item">
              <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#notifications" aria-controls="notifications" aria-selected="false">
                <i class="tf-icons bx bx-bell me-1"></i> {{ __('Notifications') }}
              </button>
            </li>
            <li class="nav-item">
              <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#security" aria-controls="security" aria-selected="false">
                <i class="tf-icons bx bx-lock-alt me-1"></i> {{ __('Security') }}
              </button>
            </li>
            <li class="nav-item">
              <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#ads" aria-controls="ads" aria-selected="false">
                <i class="tf-icons bx bx-purchase-tag me-1"></i> {{ __('Ads') }}
              </button>
            </li>
          </ul>
          <div class="tab-content">
            <!-- General Tab -->
            <div class="tab-pane fade show active" id="general" role="tabpanel">
              <form id="generalSettingsForm" class="settings-form" action="{{ route('dashboard.settings.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="row">
                  <div class="col-md-6 mb-3">
                    <label class="form-label">{{ __('Site Name') }}</label>
                    <input type="text" class="form-control" name="site_name" value="{{ $settings['site_name'] }}">
                  </div>
                  <div class="col-md-6 mb-3">
                    <label class="form-label">{{ __('Site Email') }}</label>
                    <input type="email" class="form-control" name="site_email" value="{{ $settings['site_email'] }}">
                  </div>
                  <div class="col-md-12 mb-3">
                    <label class="form-label">{{ __('Site Description') }}</label>
                    <textarea class="form-control" name="site_description" rows="3">{{ $settings['site_description'] }}</textarea>
                  </div>
                  <div class="col-md-6 mb-3">
                    <label class="form-label">{{ __('Site Logo') }}</label>
                    <input type="file" class="form-control" name="site_logo" accept="image/*">
                    @if($settings['site_logo'])
                    <div class="mt-2">
                      <img src="{{ asset('storage/' . $settings['site_logo']) }}" alt="Site Logo" class="img-thumbnail" style="max-height: 100px">
                    </div>
                    @endif
                  </div>
                  <div class="col-md-6 mb-3">
                    <label class="form-label">{{ __('Site Favicon') }}</label>
                    <input type="file" class="form-control" name="site_favicon" accept="image/*">
                    @if($settings['site_favicon'])
                    <div class="mt-2">
                      <img src="{{ asset('storage/' . $settings['site_favicon']) }}" alt="Site Favicon" class="img-thumbnail" style="max-height: 100px">
                    </div>
                    @endif
                  </div>
                  <div class="col-md-6 mb-3">
                    <label class="form-label">{{ __('Site Language') }}</label>
                    <select class="form-select" name="site_language">
                      <option value="ar" {{ $settings['site_language'] == 'ar' ? 'selected' : '' }}>العربية</option>
                      <option value="en" {{ $settings['site_language'] == 'en' ? 'selected' : '' }}>English</option>
                    </select>
                  </div>
                  <div class="col-md-6 mb-3">
                    <label class="form-label">{{ __('Timezone') }}</label>
                    <select class="form-select" name="timezone">
                      @foreach(timezone_identifiers_list() as $timezone)
                      <option value="{{ $timezone }}" {{ $settings['timezone'] == $timezone ? 'selected' : '' }}>
                        {{ $timezone }}
                      </option>
                      @endforeach
                    </select>
                  </div>
                </div>
                <button type="submit" class="btn btn-primary">{{ __('Save Changes') }}</button>
              </form>
            </div>

            <!-- Appearance Tab -->
            <div class="tab-pane fade" id="appearance" role="tabpanel">
              <form id="appearanceSettingsForm" class="settings-form">
                <div class="row">
                  <div class="col-md-6 mb-3">
                    <label class="form-label">{{ __('Primary Color') }}</label>
                    <input type="color" class="form-control form-control-color" name="primary_color" value="{{ $settings['primary_color'] }}">
                  </div>
                  <div class="col-md-6 mb-3">
                    <label class="form-label">{{ __('Secondary Color') }}</label>
                    <input type="color" class="form-control form-control-color" name="secondary_color" value="{{ $settings['secondary_color'] }}">
                  </div>
                </div>
                <button type="submit" class="btn btn-primary">{{ __('Save Changes') }}</button>
              </form>
            </div>

            <!-- SEO Tab -->
            <div class="tab-pane fade" id="seo" role="tabpanel">
              <form id="seoSettingsForm" class="settings-form" action="{{ route('dashboard.settings.update') }}" method="POST">
                @csrf
                <div class="row">
                  <div class="col-md-12 mb-3">
                    <label class="form-label">{{ __('Meta Title') }}</label>
                    <input type="text" class="form-control" name="meta_title" value="{{ $settings['meta_title'] }}">
                  </div>
                  <div class="col-md-12 mb-3">
                    <label class="form-label">{{ __('Meta Description') }}</label>
                    <textarea class="form-control" name="meta_description" rows="3">{{ $settings['meta_description'] }}</textarea>
                  </div>
                  <div class="col-md-12 mb-3">
                    <label class="form-label">{{ __('Meta Keywords') }}</label>
                    <input type="text" class="form-control" name="meta_keywords" value="{{ $settings['meta_keywords'] }}">
                    <small class="text-muted">{{ __('Separate keywords with commas') }}</small>
                  </div>
                  <div class="col-md-12 mb-3">
                    <label class="form-label">{{ __('Robots.txt Content') }}</label>
                    <textarea class="form-control" name="robots_txt" rows="5">{{ $settings['robots_txt'] }}</textarea>
                  </div>
                  <div class="col-md-6 mb-3">
                    <label class="form-label">{{ __('Sitemap URL') }}</label>
                    <input type="url" class="form-control" name="sitemap_url" value="{{ $settings['sitemap_url'] }}">
                  </div>
                  <div class="col-md-6 mb-3">
                    <label class="form-label">{{ __('Canonical URL') }}</label>
                    <input type="url" class="form-control" name="canonical_url" value="{{ $settings['canonical_url'] }}">
                  </div>
                  <div class="col-md-6 mb-3">
                    <label class="form-label">{{ __('Google Analytics ID') }}</label>
                    <input type="text" class="form-control" name="google_analytics_id" value="{{ $settings['google_analytics_id'] }}">
                  </div>
                  <div class="col-md-6 mb-3">
                    <label class="form-label">{{ __('Facebook Pixel ID') }}</label>
                    <input type="text" class="form-control" name="facebook_pixel_id" value="{{ $settings['facebook_pixel_id'] }}">
                  </div>
                </div>
                <button type="submit" class="btn btn-primary">{{ __('Save Changes') }}</button>
              </form>
            </div>

            <!-- Contact Tab -->
            <div class="tab-pane fade" id="contact" role="tabpanel">
              <form id="contactSettingsForm" class="settings-form" action="{{ route('dashboard.settings.update') }}" method="POST">
                @csrf
                <div class="row">
                  <div class="col-md-6 mb-3">
                    <label class="form-label">{{ __('Contact Email') }}</label>
                    <input type="email" class="form-control" name="contact_email" value="{{ $settings['contact_email'] }}">
                  </div>
                  <div class="col-md-6 mb-3">
                    <label class="form-label">{{ __('Contact Phone') }}</label>
                    <input type="text" class="form-control" name="contact_phone" value="{{ $settings['contact_phone'] }}">
                  </div>
                  <div class="col-md-12 mb-3">
                    <label class="form-label">{{ __('Contact Address') }}</label>
                    <textarea class="form-control" name="contact_address" rows="3">{{ $settings['contact_address'] }}</textarea>
                  </div>
                  <div class="col-md-6 mb-3">
                    <label class="form-label">{{ __('Facebook URL') }}</label>
                    <input type="url" class="form-control" name="social_facebook" value="{{ $settings['social_facebook'] }}">
                  </div>
                  <div class="col-md-6 mb-3">
                    <label class="form-label">{{ __('Twitter URL') }}</label>
                    <input type="url" class="form-control" name="social_twitter" value="{{ $settings['social_twitter'] }}">
                  </div>
                  <div class="col-md-6 mb-3">
                    <label class="form-label">{{ __('LinkedIn URL') }}</label>
                    <input type="url" class="form-control" name="social_linkedin" value="{{ $settings['social_linkedin'] }}">
                  </div>
                  <div class="col-md-6 mb-3">
                    <label class="form-label">{{ __('WhatsApp Number') }}</label>
                    <input type="text" class="form-control" name="social_whatsapp" value="{{ $settings['social_whatsapp'] }}">
                  </div>
                  <div class="col-md-6 mb-3">
                    <label class="form-label">{{ __('TikTok URL') }}</label>
                    <input type="url" class="form-control" name="social_tiktok" value="{{ $settings['social_tiktok'] }}">
                  </div>
                </div>
                <button type="submit" class="btn btn-primary">{{ __('Save Changes') }}</button>
              </form>
            </div>

            <!-- Mail Tab -->
            <div class="tab-pane fade" id="mail" role="tabpanel">
              <form id="mailSettingsForm" class="settings-form" action="{{ route('dashboard.settings.update') }}" method="POST">
                @csrf
                <div class="row">
                  <div class="col-md-6 mb-3">
                    <label class="form-label">{{ __('Mail Mailer') }}</label>
                    <select class="form-select" name="mail_mailer">
                      <option value="smtp" {{ $settings['mail_mailer'] == 'smtp' ? 'selected' : '' }}>SMTP</option>
                      <option value="sendmail" {{ $settings['mail_mailer'] == 'sendmail' ? 'selected' : '' }}>Sendmail</option>
                    </select>
                  </div>
                  <div class="col-md-6 mb-3">
                    <label class="form-label">{{ __('Mail Host') }}</label>
                    <input type="text" class="form-control" name="mail_host" value="{{ $settings['mail_host'] }}">
                  </div>
                  <div class="col-md-6 mb-3">
                    <label class="form-label">{{ __('Mail Port') }}</label>
                    <input type="text" class="form-control" name="mail_port" value="{{ $settings['mail_port'] }}">
                  </div>
                  <div class="col-md-6 mb-3">
                    <label class="form-label">{{ __('Mail Username') }}</label>
                    <input type="text" class="form-control" name="mail_username" value="{{ $settings['mail_username'] }}">
                  </div>
                  <div class="col-md-6 mb-3">
                    <label class="form-label">{{ __('Mail Password') }}</label>
                    <input type="password" class="form-control" name="mail_password" value="{{ $settings['mail_password'] }}">
                  </div>
                  <div class="col-md-6 mb-3">
                    <label class="form-label">{{ __('Mail Encryption') }}</label>
                    <select class="form-select" name="mail_encryption">
                      <option value="tls" {{ $settings['mail_encryption'] == 'tls' ? 'selected' : '' }}>TLS</option>
                      <option value="ssl" {{ $settings['mail_encryption'] == 'ssl' ? 'selected' : '' }}>SSL</option>
                    </select>
                  </div>
                  <div class="col-md-6 mb-3">
                    <label class="form-label">{{ __('Mail From Address') }}</label>
                    <input type="email" class="form-control" name="mail_from_address" value="{{ $settings['mail_from_address'] }}">
                  </div>
                  <div class="col-md-6 mb-3">
                    <label class="form-label">{{ __('Mail From Name') }}</label>
                    <input type="text" class="form-control" name="mail_from_name" value="{{ $settings['mail_from_name'] }}">
                  </div>
                </div>
                <div class="row mt-3">
                  <div class="col-12">
                    <button type="submit" class="btn btn-primary me-2">{{ __('Save Changes') }}</button>
                    <button type="button" id="test-smtp-btn" class="btn btn-info me-2" 
                            data-url="{{ route('dashboard.settings.test-smtp') }}">
                      {{ __('Test SMTP Connection') }}
                    </button>
                    <button type="button" class="btn btn-success" data-bs-toggle="modal" 
                            data-bs-target="#testEmailModal">
                      {{ __('Send Test Email') }}
                    </button>
                  </div>
                </div>
              </form>
            </div>

            <!-- Notifications Tab -->
            <div class="tab-pane fade" id="notifications" role="tabpanel">
              <form id="notificationSettingsForm" class="settings-form" action="{{ route('dashboard.settings.update') }}" method="POST">
                @csrf
                <div class="row">
                  <div class="col-md-4 mb-3">
                    <div class="form-check form-switch">
                      <input class="form-check-input" type="checkbox" name="notification_email" {{ $settings['notification_email'] ? 'checked' : '' }}>
                      <label class="form-check-label">{{ __('Email Notifications') }}</label>
                    </div>
                  </div>
                  <div class="col-md-4 mb-3">
                    <div class="form-check form-switch">
                      <input class="form-check-input" type="checkbox" name="notification_sms" {{ $settings['notification_sms'] ? 'checked' : '' }}>
                      <label class="form-check-label">{{ __('SMS Notifications') }}</label>
                    </div>
                  </div>
                  <div class="col-md-4 mb-3">
                    <div class="form-check form-switch">
                      <input class="form-check-input" type="checkbox" name="notification_push" {{ $settings['notification_push'] ? 'checked' : '' }}>
                      <label class="form-check-label">{{ __('Push Notifications') }}</label>
                    </div>
                  </div>
                </div>
                <button type="submit" class="btn btn-primary">{{ __('Save Changes') }}</button>
              </form>
            </div>

            <!-- Security Tab -->
            <div class="tab-pane fade" id="security" role="tabpanel">
              <form id="securitySettingsForm" class="settings-form">
                <div class="row">
                  <div class="col-md-6 mb-3">
                    <div class="form-check form-switch">
                      <input class="form-check-input" type="checkbox" name="two_factor_auth" {{ $settings['two_factor_auth'] ? 'checked' : '' }}>
                      <label class="form-check-label">{{ __('Two Factor Authentication') }}</label>
                    </div>
                  </div>
                  <div class="col-md-6 mb-3">
                    <label class="form-label">{{ __('Auto Lock Time (minutes)') }}</label>
                    <input type="number" class="form-control" name="auto_lock_time" value="{{ $settings['auto_lock_time'] }}" min="1">
                  </div>
                </div>
                <button type="submit" class="btn btn-primary">{{ __('Save Changes') }}</button>
              </form>
            </div>

            <!-- Ads Tab -->
            <div class="tab-pane fade" id="ads" role="tabpanel">
              <form id="adsSettingsForm" class="settings-form" action="{{ route('dashboard.settings.update') }}" method="POST">
                @csrf
                <div class="row">
                  <div class="col-12">
                    <h6 class="mb-3">{{ __('Desktop Ads') }}</h6>
                  </div>
                  <div class="col-md-6 mb-3">
                    <label class="form-label">{{ __('Home Page Ad 1') }}</label>
                    <textarea class="form-control" name="google_ads_desktop_home" rows="3">{{ $settings['google_ads_desktop_home'] }}</textarea>
                  </div>
                  <div class="col-md-6 mb-3">
                    <label class="form-label">{{ __('Home Page Ad 2') }}</label>
                    <textarea class="form-control" name="google_ads_desktop_home_2" rows="3">{{ $settings['google_ads_desktop_home_2'] }}</textarea>
                  </div>
                  <div class="col-md-6 mb-3">
                    <label class="form-label">{{ __('Classes Page Ad 1') }}</label>
                    <textarea class="form-control" name="google_ads_desktop_classes" rows="3">{{ $settings['google_ads_desktop_classes'] }}</textarea>
                  </div>
                  <div class="col-md-6 mb-3">
                    <label class="form-label">{{ __('Classes Page Ad 2') }}</label>
                    <textarea class="form-control" name="google_ads_desktop_classes_2" rows="3">{{ $settings['google_ads_desktop_classes_2'] }}</textarea>
                  </div>
                  <div class="col-md-6 mb-3">
                    <label class="form-label">{{ __('subject Page Ad 1') }}</label>
                    <textarea class="form-control" name="google_ads_desktop_subject" rows="3">{{ $settings['google_ads_desktop_subject'] }}</textarea>
                  </div>
                  <div class="col-md-6 mb-3">
                    <label class="form-label">{{ __('Articles Page Ad 1') }}</label>
                    <textarea class="form-control" name="google_ads_desktop_article" rows="3">{{ $settings['google_ads_desktop_article'] }}</textarea>
                  </div>
                  <div class="col-md-6 mb-3">
                    <label class="form-label">{{ __('Articles Page Ad 2') }}</label>
                    <textarea class="form-control" name="google_ads_desktop_article_2" rows="3">{{ $settings['google_ads_desktop_article_2'] }}</textarea>
                  </div>
                  <div class="col-md-6 mb-3">
                    <label class="form-label">{{ __('Download Page Ad 1') }}</label>
                    <textarea class="form-control" name="google_ads_desktop_download" rows="3">{{ $settings['google_ads_desktop_download'] }}</textarea>
                  </div>
                  <div class="col-md-6 mb-3">
                    <label class="form-label">{{ __('Download Page Ad 2') }}</label>
                    <textarea class="form-control" name="google_ads_desktop_download_2" rows="3">{{ $settings['google_ads_desktop_download_2'] }}</textarea>
                  </div>
                  
                  <!-- Add other desktop ads fields -->

                  <div class="col-12">
                    <h6 class="mb-3 mt-4">{{ __('Mobile Ads') }}</h6>
                  </div>
                  <div class="col-md-6 mb-3">
                    <label class="form-label">{{ __('Home Page Ad 1 (Mobile)') }}</label>
                    <textarea class="form-control" name="google_ads_mobile_home" rows="3">{{ $settings['google_ads_mobile_home'] }}</textarea>
                  </div>
                  <div class="col-md-6 mb-3">
                    <label class="form-label">{{ __('Home Page Ad 2 (Mobile)') }}</label>
                    <textarea class="form-control" name="google_ads_mobile_home_2" rows="3">{{ $settings['google_ads_mobile_home_2'] }}</textarea>
                  </div>
                  <div class="col-md-6 mb-3">
                    <label class="form-label">{{ __('Classes Page Ad 1 (Mobile)') }}</label>
                    <textarea class="form-control" name="google_ads_mobile_classes" rows="3">{{ $settings['google_ads_mobile_classes'] }}</textarea>
                  </div>
                  <div class="col-md-6 mb-3">
                    <label class="form-label">{{ __('Classes Page Ad 2 (Mobile)') }}</label>
                    <textarea class="form-control" name="google_ads_mobile_classes_2" rows="3">{{ $settings['google_ads_mobile_classes_2'] }}</textarea>
                  </div>
                  <div class="col-md-6 mb-3">
                    <label class="form-label">{{ __('Subject Page Ad 1 (Mobile)') }}</label>
                    <textarea class="form-control" name="google_ads_mobile_subject" rows="3">{{ $settings['google_ads_mobile_subject'] }}</textarea>
                  </div>
                  <div class="col-md-6 mb-3">
                    <label class="form-label">{{ __('Articles Page Ad 1 (Mobile)') }}</label>
                    <textarea class="form-control" name="google_ads_mobile_article" rows="3">{{ $settings['google_ads_mobile_article'] }}</textarea>
                  </div>
                  <div class="col-md-6 mb-3">
                    <label class="form-label">{{ __('Articles Page Ad 2 (Mobile)') }}</label>
                    <textarea class="form-control" name="google_ads_mobile_article_2" rows="3">{{ $settings['google_ads_mobile_article_2'] }}</textarea>
                  </div>
                  <div class="col-md-6 mb-3">
                    <label class="form-label">{{ __('Download Page Ad 1 (Mobile)') }}</label>
                    <textarea class="form-control" name="google_ads_mobile_download" rows="3">{{ $settings['google_ads_mobile_download'] }}</textarea>
                  </div>
                  <div class="col-md-6 mb-3">
                    <label class="form-label">{{ __('Download Page Ad 2 (Mobile)') }}</label>
                    <textarea class="form-control" name="google_ads_mobile_download_2" rows="3">{{ $settings['google_ads_mobile_download_2'] }}</textarea>
                  </div>                 
                  <!-- Add other mobile ads fields -->
                </div>
                <button type="submit" class="btn btn-primary">{{ __('Save Changes') }}</button>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Test Email Modal -->
<div class="modal fade" id="testEmailModal" tabindex="-1" aria-hidden="true" 
     data-url="{{ route('dashboard.settings.test-email') }}">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">{{ __('Send Test Email') }}</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <label class="form-label">{{ __('Email Address') }}</label>
          <input type="email" class="form-control" id="test_email" placeholder="{{ __('Enter email address') }}">
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Close') }}</button>
        <button type="button" class="btn btn-primary" id="send-test-email-btn">{{ __('Send Test Email') }}</button>
      </div>
    </div>
  </div>
</div>
@endsection
