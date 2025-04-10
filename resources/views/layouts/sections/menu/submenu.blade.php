@php
use Illuminate\Support\Facades\Route;
@endphp

<ul class="menu-sub">
  @if (isset($menu))
    @foreach ($menu as $submenu)
      {{-- active menu method --}}
      @php
        $activeClass = '';
        $currentRouteName = Route::currentRouteName();
        
        if (isset($submenu->slug) && $currentRouteName === $submenu->slug) {
            $activeClass = 'active';
        } elseif (isset($submenu->submenu)) {
            foreach($submenu->submenu as $subItem) {
                if (isset($subItem->slug) && $currentRouteName === $subItem->slug) {
                    $activeClass = 'active open';
                    break;
                }
            }
        }
      @endphp

      <li class="menu-item {{ $activeClass }}">
        <a href="{{ isset($submenu->url) ? url($submenu->url) : 'javascript:void(0)' }}" 
           class="menu-link {{ isset($submenu->submenu) ? 'menu-toggle' : '' }}"
           @if(isset($submenu->target) && !empty($submenu->target)) target="_blank" @endif>
          @if (isset($submenu->icon))
            <i class="{{ $submenu->icon }}"></i>
          @endif
          <div>{{ isset($submenu->name) ? __($submenu->name) : '' }}</div>
          @isset($submenu->badge)
            <div class="badge bg-{{ $submenu->badge[0] }} rounded-pill ms-auto">{{ $submenu->badge[1] }}</div>
          @endisset
        </a>
        {{-- Recursive inclusion for nested submenus --}}
        @if (isset($submenu->submenu))
          @include('layouts.sections.menu.submenu', ['menu' => $submenu->submenu])
        @endif
      </li>
    @endforeach
  @endif
</ul>
