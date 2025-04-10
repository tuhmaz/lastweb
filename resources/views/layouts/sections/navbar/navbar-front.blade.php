@php
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

$currentRouteName = Route::currentRouteName();
$randomAvatarNumber = rand(1, 5);
$defaultAvatar = 'assets/img/avatars/' . $randomAvatarNumber . '.png';
$defaultLogo = 'assets/img/logo/logo.png';
$defaultFavicon = 'assets/img/favicon/favicon.ico';
@endphp

<nav class="layout-navbar navbar-expand-lg shadow-sm py-0">
  <div class="container">
    <div class="navbar navbar-expand-lg landing-navbar px-3 px-md-8">
      <!-- Logo -->
      <a href="{{ url('/') }}" class="navbar-brand app-brand demo py-0 py-lg-2 me-0 me-lg-4">
        <div class="app-brand-link d-flex align-items-center gap-2">
          <img src="{{ asset('storage/' . config('settings.site_logo', $defaultLogo)) }}"
               alt="{{ config('settings.site_name', 'My Website') }}"
               class="app-brand-logo"
               width="32"
               height="32">
          <span class="app-brand-text menu-text fw-bold d-none d-md-inline-block" style="color: #000;">{{ config('settings.site_name', 'My Website') }}</span>
        </div>
      </a>

      <!-- Toggle Button for Mobile -->
      <button class="navbar-toggler border-0 shadow-none px-2" type="button" data-bs-toggle="collapse" 
              data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" 
              aria-expanded="false" aria-label="Toggle navigation">
        <i class="ti ti-menu-2 ti-md"></i>
      </button>

      <!-- Navbar Content -->
      <div class="collapse navbar-collapse landing-nav-menu" id="navbarSupportedContent">
        <!-- Navigation Links - Add your menu items here -->
        <ul class="navbar-nav me-auto mb-2 mb-lg-0">
          <!-- Add your navigation links here -->
        </ul>

        <!-- Right Side Elements -->
        <div class="d-flex align-items-center gap-2">
          <!-- Country Selector -->
          <div class="dropdown">
            <form method="POST" action="{{ route('setDatabase') }}" id="databaseForm" class="mb-0">
              @csrf
              <input type="hidden" name="database" id="databaseInput" value="{{ session('database', 'jo') }}">
              <button type="button" class="btn btn-outline-warning btn-icon rounded-pill dropdown-toggle hide-arrow p-1 p-sm-2" 
                      data-bs-toggle="dropdown" aria-expanded="false">
                @php
                $currentCountry = session('database', 'jo');
                $flag = match ($currentCountry) {
                    'sa' => 'saudi.svg',
                    'eg' => 'egypt.svg',
                    'ps' => 'palestine.svg',
                    default => 'jordan.svg',
                };
                @endphp
                <img alt="Current Country" src="{{ asset('assets/img/flags/' . $flag) }}" 
                     style="width: 20px; height: 20px;" loading="lazy">
              </button>
              <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButton">
                <li>
                  <a class="dropdown-item d-flex align-items-center" href="#" onclick="setDatabase('jo')">
                    <img alt="Jordan" src="{{ asset('assets/img/flags/jordan.svg') }}" 
                         style="width: 20px; height: 20px;" class="me-2" loading="lazy"> 
                    <span>الأردن</span>
                  </a>
                </li>
                <li>
                  <a class="dropdown-item d-flex align-items-center" href="#" onclick="setDatabase('sa')">
                    <img alt="Saudi Arabia" src="{{ asset('assets/img/flags/saudi.svg') }}" 
                         style="width: 20px; height: 20px;" class="me-2" loading="lazy"> 
                    <span>السعودية</span>
                  </a>
                </li>
                <li>
                  <a class="dropdown-item d-flex align-items-center" href="#" onclick="setDatabase('eg')">
                    <img alt="Egypt" src="{{ asset('assets/img/flags/egypt.svg') }}" 
                         style="width: 20px; height: 20px;" class="me-2" loading="lazy"> 
                    <span>مصر</span>
                  </a>
                </li>
                <li>
                  <a class="dropdown-item d-flex align-items-center" href="#" onclick="setDatabase('ps')">
                    <img alt="Palestine" src="{{ asset('assets/img/flags/palestine.svg') }}" 
                         style="width: 20px; height: 20px;" class="me-2" loading="lazy"> 
                    <span>فلسطين</span>
                  </a>
                </li>
              </ul>
            </form>
          </div>

          <!-- User Menu -->
          @auth
            <div class="nav-item navbar-dropdown dropdown-user dropdown">
              <a class="nav-link dropdown-toggle hide-arrow p-0 ms-1" href="#" data-bs-toggle="dropdown">
                <div class="avatar avatar-online">
                  <img src="{{ Auth::check() && Auth::user()->profile_photo_path ? asset('storage/' . Auth::user()->profile_photo_path) : asset($defaultAvatar) }}"
                       alt="{{ Auth::check() ? Auth::user()->name : 'Default Avatar' }}"
                       class="rounded-circle w-px-30 h-px-30">
                </div>
              </a>
              <ul class="dropdown-menu dropdown-menu-end mt-1">
                <li>
                  <a class="dropdown-item" href="{{ route('dashboard.users.show', Auth::user()->id ?? '') }}">
                    <div class="d-flex align-items-center">
                      <div class="flex-shrink-0 me-3">
                        <div class="avatar avatar-online">
                          <img src="{{ Auth::check() && Auth::user()->profile_photo_path ? asset('storage/' . Auth::user()->profile_photo_path) : asset($defaultAvatar) }}"
                               alt="{{ Auth::check() ? Auth::user()->name : 'Default Avatar' }}"
                               class="rounded-circle w-px-30 h-px-30">
                        </div>
                      </div>
                      <div class="flex-grow-1">
                        <span class="fw-semibold d-block">{{ Auth::check() ? Auth::user()->name : '' }}</span>
                        <small class="text-muted">{{ Auth::check() ? Auth::user()->email : '' }}</small>
                      </div>
                    </div>
                  </a>
                </li>
                <li><hr class="dropdown-divider"></li>
                <li>
                  <a class="dropdown-item d-flex align-items-center" href="{{ route('dashboard.index') }}">
                    <i class="ti ti-dashboard me-2 ti-sm"></i>
                    <span class="align-middle">{{ __('Dashboard') }}</span>
                  </a>
                </li>
                <li>
                  <form method="POST" action="{{ route('logout') }}" id="logout-form" class="mb-0">
                    @csrf
                    <a class="dropdown-item d-flex align-items-center" href="#"
                       onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                      <i class="ti ti-logout me-2 ti-sm"></i>
                      <span class="align-middle">{{ __('Logout') }}</span>
                    </a>
                  </form>
                </li>
              </ul>
            </div>
          @else
            <a href="{{ route('login') }}" class="btn btn-primary btn-sm d-flex align-items-center">
              <i class="ti ti-login me-1 ti-sm"></i>
              <span class="d-none d-sm-inline-block">{{ __('Login/Register') }}</span>
            </a>
          @endauth
        </div>
      </div>
    </div>
  </div>
</nav>

<script>
  function setDatabase(database) {
    document.getElementById('databaseInput').value = database;
    document.getElementById('databaseForm').submit();
  }
</script>

<style>
  @media (max-width: 991.98px) {
    .landing-nav-menu {
      position: absolute;
      top: 100%;
      left: 0;
      right: 0;
      background: var(--bs-body-bg);
      padding: 1rem;
      box-shadow: 0 0.25rem 1rem rgba(0, 0, 0, 0.15);
      z-index: 1000;
    }
    
    .landing-nav-menu .d-flex {
      flex-direction: column;
      align-items: flex-start;
      gap: 1rem;
    }
    
    .dropdown-menu {
      position: static !important;
      width: 100%;
      margin-top: 0.5rem !important;
      transform: none !important;
    }
  }
  
  @media (min-width: 992px) {
    .landing-nav-menu {
      display: flex !important;
      justify-content: flex-end;
      flex-grow: 1;
    }
  }
</style>
