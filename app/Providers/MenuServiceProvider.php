<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Routing\Route;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Collection;
use App\Services\Menu;
use Illuminate\Support\Facades\Auth;

class MenuServiceProvider extends ServiceProvider
{
  /**
   * Register services.
   */
  public function register(): void
  {
    $this->app->singleton('menu', function ($app) {
      return new Menu();
    });
  }

  /**
   * Bootstrap services.
   */
  public function boot(): void
  {
    // تسجيل حدث عند اكتمال تحميل التطبيق
    $this->app->booted(function () {
      $this->buildMenu();
    });

    // مشاركة بيانات القائمة مع جميع العروض
    View::composer('*', function ($view) {
      $menuData = $this->getMenuData();
      $view->with('menuData', $menuData);
    });
  }

  private function buildMenu()
  {
    try {
      $menuData = $this->getMenuData();
      app('menu')->setMenu($menuData);
    } catch (\Exception $e) {
      // تم إزالة تسجيل الخطأ
    }
  }

  private function filterMenuItems($menuData)
  {
    if (!is_array($menuData) && !is_object($menuData)) {
      return $menuData;
    }

    // استخدام Auth facade مباشرة
    $user = Auth::user();
    $authCheck = Auth::check();

    // إذا لم يكن المستخدم مسجل الدخول، نعرض فقط العناصر التي لا تتطلب صلاحيات
    return collect($menuData)->map(function ($item) use ($user, $authCheck) {
      if (isset($item->menuHeader)) {
        return $item;
      }

      // إذا لم تكن هناك صلاحيات مطلوبة، نعرض العنصر
      if (!isset($item->permissions)) {
        return $item;
      }

      // إذا كان المستخدم مسجل الدخول ولديه الصلاحيات المطلوبة
      if ($authCheck && $user && $user->can($item->permissions)) {
        if (isset($item->submenu)) {
          $filteredSubmenu = collect($item->submenu)
            ->map(function ($subItem) use ($user) {
              if (!isset($subItem->permissions) || $user->can($subItem->permissions)) {
                return $subItem;
              }
              return null;
            })
            ->filter()
            ->values()
            ->all();

          if (empty($filteredSubmenu)) {
            return null;
          }

          $item->submenu = $filteredSubmenu;
        }
        return $item;
      }

      // تم إزالة تسجيل الخطأ
      return null;
    })->filter()->values();
  }

  public function getMenuData(): array
  {
    try {
      $menuPath = resource_path('menu/verticalMenu.json');
      $verticalMenuJson = json_decode(file_get_contents($menuPath));

      // تحقق من وجود القائمة قبل تصفيتها
      if (isset($verticalMenuJson->menu)) {
        $verticalMenuJson->menu = $this->filterMenuItems($verticalMenuJson->menu);
      } else {
        // تم إزالة تسجيل التحذير
        $verticalMenuJson = new \stdClass();
        $verticalMenuJson->menu = [];
      }

      return [$verticalMenuJson];
    } catch (\Exception $e) {
      // تم إزالة تسجيل الخطأ
      $emptyMenu = new \stdClass();
      $emptyMenu->menu = [];
      return [$emptyMenu];
    }
  }
}
