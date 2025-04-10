<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class PermissionsTableSeeder extends Seeder
{
  public function run()
  {
    $timestamp = now();
    
    $permissions = [
      // صلاحيات لوحة التحكم
      ['name' => 'access dashboard', 'guard_name' => 'sanctum'],
      ['name' => 'view analytics', 'guard_name' => 'sanctum'],
      
      // صلاحيات إدارة المستخدمين
      ['name' => 'manage users', 'guard_name' => 'sanctum'],
      ['name' => 'admin users', 'guard_name' => 'sanctum'],
      ['name' => 'manage roles', 'guard_name' => 'sanctum'],
      ['name' => 'manage permissions', 'guard_name' => 'sanctum'],
      
      // صلاحيات إدارة المحتوى
      ['name' => 'manage articles', 'guard_name' => 'sanctum'],
      ['name' => 'manage news', 'guard_name' => 'sanctum'],
      ['name' => 'manage categories', 'guard_name' => 'sanctum'],
      ['name' => 'manage files', 'guard_name' => 'sanctum'],
      
      // صلاحيات إدارة المدرسة
      ['name' => 'manage school classes', 'guard_name' => 'sanctum'],
      ['name' => 'manage subjects', 'guard_name' => 'sanctum'],
      ['name' => 'manage semesters', 'guard_name' => 'sanctum'],
      
      // صلاحيات الرسائل
      ['name' => 'manage messages', 'guard_name' => 'sanctum'],
      ['name' => 'view messages', 'guard_name' => 'sanctum'],
      ['name' => 'send messages', 'guard_name' => 'sanctum'],
      
      // صلاحيات الأمان والمراقبة
      ['name' => 'manage monitoring', 'guard_name' => 'sanctum'],
      ['name' => 'manage security', 'guard_name' => 'sanctum'],
      ['name' => 'view activity', 'guard_name' => 'sanctum'],
      ['name' => 'manage performance', 'guard_name' => 'sanctum'],
      ['name' => 'manage redis', 'guard_name' => 'sanctum'],
      ['name' => 'view redis', 'guard_name' => 'sanctum'],
      ['name' => 'monitor redis', 'guard_name' => 'sanctum'],
      ['name' => 'view redis stats', 'guard_name' => 'sanctum'],
      ['name' => 'view security', 'guard_name' => 'sanctum'],
      ['name' => 'view security logs', 'guard_name' => 'sanctum'],
      ['name' => 'view security analytics', 'guard_name' => 'sanctum'],
      ['name' => 'manage blocked ips', 'guard_name' => 'sanctum'],

      // صلاحيات التقويم
      ['name' => 'manage calendar', 'guard_name' => 'sanctum'],

      // صلاحيات الخريطة
      ['name' => 'manage sitemap', 'guard_name' => 'sanctum'],
      
      // صلاحيات الإعدادات
      ['name' => 'manage settings', 'guard_name' => 'sanctum'],
      
      // صلاحيات النظام
      ['name' => 'manage redis', 'guard_name' => 'sanctum'],
      ['name' => 'manage cache', 'guard_name' => 'sanctum']
    ];

    foreach ($permissions as $permission) {
      // التحقق من وجود الصلاحية قبل إنشائها
      if (!DB::table('permissions')->where('name', $permission['name'])->where('guard_name', $permission['guard_name'])->exists()) {
        $permission['created_at'] = $timestamp;
        $permission['updated_at'] = $timestamp;
        DB::table('permissions')->insert($permission);
      }
    }
  }
}
