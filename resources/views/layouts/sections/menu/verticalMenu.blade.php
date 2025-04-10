@php
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
$configData = Helper::appClasses();
@endphp

<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">

  @if(!isset($navbarFull))
    <div class="app-brand demo">
      <a href="{{ url('/') }}" class="app-brand-link">
        <span class="app-brand-logo">
          <img src="{{ asset('storage/' . config('settings.site_logo')) }}"
               alt="{{ config('settings.site_name') }} Logo"
               width="45"
               height="45"
               loading="lazy" />
        </span>
        <span class="app-brand-text" style="color: white;">{{ config('settings.site_name') }}</span>
      </a>
      <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto">
        <i class="ti menu-toggle-icon d-none d-xl-block align-middle"></i>
        <i class="ti ti-x d-block d-xl-none ti-md align-middle"></i>
      </a>
    </div>
  @endif

  <div class="menu-inner-shadow"></div>

  <div class="menu-inner-content" style="height: calc(100vh - 80px); overflow-y: auto; overflow-x: hidden;">
    <ul class="menu-inner py-1">
      @foreach ($menuData[0]->menu as $menu)
        @if (isset($menu->menuHeader))
          <li class="menu-header small">
            <span class="menu-header-text">{{ __($menu->menuHeader) }}</span>
          </li>
        @else
          @php
          $activeClass = '';
          $currentRouteName = Route::currentRouteName();

          // Check for exact route match
          if (isset($menu->slug) && $currentRouteName === $menu->slug) {
              $activeClass = 'active';
          }
          // Check submenu items
          elseif (isset($menu->submenu)) {
              foreach ($menu->submenu as $subItem) {
                  if (isset($subItem->slug) && $currentRouteName === $subItem->slug) {
                      $activeClass = 'active open';
                      break;
                  }
              }
          }
          @endphp

          <li class="menu-item {{ $activeClass }}">
            <a href="{{ $menu->url ?? 'javascript:void(0);' }}" class="{{ isset($menu->submenu) ? 'menu-link menu-toggle' : 'menu-link' }}">
              @isset($menu->icon)
                <i class="{{ $menu->icon }}"></i>
              @endisset
              <div>{{ $menu->name ?? '' }}</div>
              @isset($menu->badge)
                <div class="badge bg-{{ $menu->badge[0] }} rounded-pill ms-auto">{{ $menu->badge[1] }}</div>
              @endisset
            </a>
            @isset($menu->submenu)
              @php
                $submenuActive = isset($menu->url) && Str::startsWith(url()->current(), $menu->url);
              @endphp
              @include('layouts.sections.menu.submenu', ['menu' => $menu->submenu, 'submenuActive' => $submenuActive])
            @endisset
          </li>
        @endif
      @endforeach
    </ul>
  </div>
</aside>
