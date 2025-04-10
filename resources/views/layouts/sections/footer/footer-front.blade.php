<footer class="landing-footer">
  <div class="footer-top">
    <div class="container">
      <div class="row gy-5">
        <!-- Brand Section -->
        <div class="col-lg-4 col-md-6">
          <a href="{{ url('/') }}" class="app-brand-link">
            <span class="app-brand-logo">

              <img src="{{ asset('storage/' . config('settings.site_logo')) }}"
                   alt="{{ config('settings.site_name') }} Logo"
                   width="45"
                   height="45"
                   loading="lazy" />
            </span>
            <span class="app-brand-text"style="color: white;">{{ config('settings.site_name') }}</span>
          </a>
          <p class="footer-logo-description">
            {{ config('settings.site_description') }}
          </p>

          <!-- Social Icons -->
          <div class="social-icons">
            @php
              $socialLinks = [
                'whatsapp' => [
                    'icon' => 'whatsapp.svg',
                    'name' => 'WhatsApp',
                    'url' => function($value) {
                        return 'https://wa.me/' . preg_replace('/[^0-9]/', '', $value);
                    }
                ],
                'facebook' => ['icon' => 'facebook.svg', 'name' => 'Facebook'],
                'twitter' => ['icon' => 'twitter.svg', 'name' => 'Twitter'],
                'instagram' => ['icon' => 'instagram.svg', 'name' => 'Instagram'],
                'linkedin' => ['icon' => 'linkedin.svg', 'name' => 'LinkedIn'],
                'tiktok' => ['icon' => 'tiktok.svg', 'name' => 'TikTok']
              ];
            @endphp

            @foreach ($socialLinks as $platform => $data)
              @if (config('settings.social_' . $platform))
                @php
                    $url = isset($data['url']) ?
                           $data['url'](config('settings.social_' . $platform)) :
                           config('settings.social_' . $platform);
                @endphp
                <a href="{{ $url }}"
                   class="social-icon-link"
                   target="_blank"
                   rel="noopener noreferrer"
                   aria-label="{{ $data['name'] }}">
                  <img src="{{ asset('assets/img/front-pages/icons/' . $data['icon']) }}"
                       alt="{{ $data['name'] }}"
                       class="social-icon-img"
                       width="20"
                       height="20"
                       loading="lazy" />
                </a>
              @endif
            @endforeach
          </div>
        </div>

        <!-- Quick Links -->
        <div class="col-lg-2 col-md-6">
          <h6 class="footer-title">{{ __('Quick Links') }}</h6>
          <a href="{{ url('/') }}" class="footer-link">{{ __('Home') }}</a>
          <a href="{{ url('/about-us') }}" class="footer-link">{{ __('About Us') }}</a>
          <a href="{{ url('/contact-us') }}" class="footer-link">{{ __('Contact') }}</a>
          <a href="{{ url('/news') }}" class="footer-link">{{ __('News') }}</a>
        </div>

        <!-- Legal Links -->
        <div class="col-lg-3 col-md-6">
          <h5 class="footer-title">{{ __('Legal Links') }}</h5>
          <ul class="list-unstyled">
            <li>
              <a href="{{ url('privacy-policy') }}" class="footer-link">
                <i class="bx bx-shield me-2"></i>{{ __('Privacy Policy') }}
              </a>
            </li>
            <li>
              <a href="{{ url('terms-of-service') }}" class="footer-link">
                <i class="bx bx-file me-2"></i>{{ __('Terms of Service') }}
              </a>
            </li>
            <li>
              <a href="{{ url('cookie-policy') }}" class="footer-link">
                <i class="bx bx-cookie me-2"></i>{{ __('Cookie Policy') }}
              </a>
            </li>
            <li>
              <a href="{{ url('disclaimer') }}" class="footer-link">
                <i class="bx bx-info-circle me-2"></i>{{ __('Disclaimer') }}
              </a>
            </li>
          </ul>
        </div>

        <!-- App Download -->
        <div class="col-lg-3 col-md-6">
          <div class="app-download-section">
            <h6 class="app-download-title">{{ __('Download Our App') }}</h6>
            <p class="app-download-text">{{ __('Get our mobile app for a better experience') }}</p>
            <div class="app-download-buttons">
              <a href="#" class="app-download-link" aria-label="Download on App Store">
                <img src="{{ asset('assets/img/front-pages/landing-page/apple-icon.webp') }}"
                     alt="App Store"
                     width="140"
                     loading="lazy" />
              </a>
              <a href="#" class="app-download-link" aria-label="Get it on Google Play">
                <img src="{{ asset('assets/img/front-pages/landing-page/google-play-icon.webp') }}"
                     alt="Google Play"
                     width="140"
                     loading="lazy" />
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Footer Bottom -->
  <div class="footer-bottom">
    <div class="container">
      <div class="d-flex flex-wrap justify-content-between align-items-center">
        <div class="footer-bottom-text">
          &copy; <span id="currentYear"></span>
          <a href="{{ url('/') }}">{{ config('settings.site_name') }}</a>
          {{ __('All rights reserved') }}.
        </div>
        <div class="footer-bottom-text">
          <span class="made-with-love">
            {{ __('Made with') }} <span class="heart-icon">❤️</span> {{ __('in Jordan') }}
          </span>
        </div>
      </div>
    </div>
  </div>
</footer>

@push('scripts')
<script>
  document.getElementById('currentYear').textContent = new Date().getFullYear();
</script>
@endpush
