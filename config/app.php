<?php

return [

  /*
  |--------------------------------------------------------------------------
  | API Authentication
  |--------------------------------------------------------------------------
  |
  | These values are used for API authentication. The API key is the primary
  | method of authentication, while the API token is maintained for legacy support.
  |
  */

  'api_key' => env('API_KEY'),
  'api_token' => env('API_TOKEN'),

  /*
  |--------------------------------------------------------------------------
  | Application Name
  |--------------------------------------------------------------------------
  |
  | This value is the name of your application, which will be used when the
  | framework needs to place the application's name in a notification or
  | other UI elements where an application name needs to be displayed.
  |
  */

  'name' => env('APP_NAME', 'alemedu'),

  /*
  |--------------------------------------------------------------------------
  | Application Environment
  |--------------------------------------------------------------------------
  |
  | This value determines the "environment" your application is currently
  | running in. This may determine how you prefer to configure various
  | services the application utilizes. Set this in your ".env" file.
  |
  */

  'env' => env('APP_ENV', 'production'),

  /*
  |--------------------------------------------------------------------------
  | Application Debug Mode
  |--------------------------------------------------------------------------
  |
  | When your application is in debug mode, detailed error messages with
  | stack traces will be shown on every error that occurs within your
  | application. If disabled, a simple generic error page is shown.
  |
  */

  'debug' => env('APP_DEBUG', false),

  /*
  |--------------------------------------------------------------------------
  | Application URL
  |--------------------------------------------------------------------------
  |
  | This URL is used by the console to properly generate URLs when using
  | the Artisan command line tool. You should set this to the root of
  | the application so that it's available within Artisan commands.
  |
  */

  'url' => env('APP_URL', 'https://alemedu.com'),

  /*
  |--------------------------------------------------------------------------
  | Application Timezone
  |--------------------------------------------------------------------------
  |
  | Here you may specify the default timezone for your application, which
  | will be used by the PHP date and date-time functions. The timezone
  | is set to "UTC" by default as it is suitable for most use cases.
  |
  */

  'timezone' => env('APP_TIMEZONE', 'UTC'),

  /*
  |--------------------------------------------------------------------------
  | Application Locale Configuration
  |--------------------------------------------------------------------------
  |
  | The application locale determines the default locale that will be used
  | by Laravel's translation / localization methods. This option can be
  | set to any locale for which you plan to have translation strings.
  |
  */

  'locale' => env('APP_LOCALE', 'ar'),

  'fallback_locale' => env('APP_FALLBACK_LOCALE', 'en'),

  'faker_locale' => env('APP_FAKER_LOCALE', 'ar_SA'),

  /*
  |--------------------------------------------------------------------------
  | Encryption Key
  |--------------------------------------------------------------------------
  |
  | This key is utilized by Laravel's encryption services and should be set
  | to a random, 32 character string to ensure that all encrypted values
  | are secure. You should do this prior to deploying the application.
  |
  */

  'cipher' => 'AES-256-CBC',

  'key' => env('APP_KEY'),

  'previous_keys' => [
    ...array_filter(
      explode(',', env('APP_PREVIOUS_KEYS', ''))
    ),
  ],

  /*
  |--------------------------------------------------------------------------
  | Maintenance Mode Driver
  |--------------------------------------------------------------------------
  |
  | These configuration options determine the driver used to determine and
  | manage Laravel's "maintenance mode" status. The "cache" driver will
  | allow maintenance mode to be controlled across multiple machines.
  |
  | Supported drivers: "file", "cache"
  |
  */

  'maintenance' => [
    'driver' => env('APP_MAINTENANCE_DRIVER', 'file'),
    'store' => env('APP_MAINTENANCE_STORE', 'database'),
  ],

  /*
  |--------------------------------------------------------------------------
  | Compression Settings
  |--------------------------------------------------------------------------
  |
  | Here you may configure compression settings for your application.
  | Level ranges from 1 (lowest) to 9 (highest).
  | Threshold is the minimum size in bytes before compression is applied.
  |
  */

  'compression' => [
    'enabled' => env('COMPRESSION_ENABLED', true),
    'zlib_output' => env('ZLIB_OUTPUT_COMPRESSION', 'On'),
    'level' => env('COMPRESSION_LEVEL', 7), 
    'handler' => env('COMPRESSION_HANDLER', 'ob_gzhandler'),
    'threshold' => env('COMPRESSION_THRESHOLD', 1024),
    'cache_max_age' => env('COMPRESSION_CACHE_MAX_AGE', 86400),
    'brotli' => [
      'enabled' => env('BROTLI_COMPRESSION_ENABLED', true),
      'level' => env('BROTLI_COMPRESSION_LEVEL', 6), 
    ],
    'security' => [
      'enable_security_headers' => true,
      'enable_etag' => true,
      'enable_cache_control' => true,
      'hsts' => [
        'enabled' => env('SECURITY_HSTS_ENABLED', true),
        'max_age' => env('SECURITY_HSTS_MAX_AGE', 31536000), 
        'include_subdomains' => env('SECURITY_HSTS_INCLUDE_SUBDOMAINS', true),
        'preload' => env('SECURITY_HSTS_PRELOAD', true),
      ],
    ],
    'types' => [
      'text/html',
      'text/plain',
      'text/css',
      'text/javascript',
      'application/javascript',
      'application/json',
      'application/xml',
      'application/x-httpd-php',
      'application/ld+json',
      'application/x-yaml',
      'application/yaml',
      'image/svg+xml',
      'application/vnd.api+json',
      'text/xml',
      'application/graphql',
      'font/woff',
      'font/woff2',
      'font/ttf',
      'font/otf',
    ],
    'static_max_age' => env('STATIC_CACHE_MAX_AGE', 604800), 
    'dynamic_max_age' => env('DYNAMIC_CACHE_MAX_AGE', 3600), 
  ],

  /*
  |--------------------------------------------------------------------------
  | Image Optimization Settings
  |--------------------------------------------------------------------------
  |
  | Here you may configure image optimization settings for your application.
  | These settings control how images are processed when uploaded.
  |
  */

  'image_optimization' => [
    'enabled' => env('IMAGE_OPTIMIZATION_ENABLED', true),
    'max_width' => env('IMAGE_MAX_WIDTH', 1920),
    'quality' => env('IMAGE_QUALITY', 85),
    'convert_to_webp' => env('IMAGE_CONVERT_TO_WEBP', true),
    'create_responsive' => env('IMAGE_CREATE_RESPONSIVE', true),
    'temp_directory' => env('IMAGE_TEMP_DIRECTORY', 'temp'),
    'storage_disk' => env('IMAGE_STORAGE_DISK', 'public'),
    'responsive_sizes' => [
      'lg' => ['width' => 1280, 'height' => null],
      'md' => ['width' => 768, 'height' => null],
      'sm' => ['width' => 480, 'height' => null],
    ],
    'formats' => [
      'jpg', 'jpeg', 'png', 'gif', 'webp'
    ],
  ],

  'providers' => [
    /*
     * Laravel Framework Service Providers...
     */
    Illuminate\Auth\AuthServiceProvider::class,
    Illuminate\Broadcasting\BroadcastServiceProvider::class,
    Illuminate\Bus\BusServiceProvider::class,
    Illuminate\Cache\CacheServiceProvider::class,
    Illuminate\Foundation\Providers\ConsoleSupportServiceProvider::class,
    Illuminate\Cookie\CookieServiceProvider::class,
    Illuminate\Database\DatabaseServiceProvider::class,
    Illuminate\Encryption\EncryptionServiceProvider::class,
    Illuminate\Filesystem\FilesystemServiceProvider::class,
    Illuminate\Foundation\Providers\FoundationServiceProvider::class,
    Illuminate\Hashing\HashServiceProvider::class,
    Illuminate\Mail\MailServiceProvider::class,
    Illuminate\Notifications\NotificationServiceProvider::class,
    Illuminate\Pagination\PaginationServiceProvider::class,
    Illuminate\Pipeline\PipelineServiceProvider::class,
    Illuminate\Queue\QueueServiceProvider::class,
    Illuminate\Redis\RedisServiceProvider::class,
    Illuminate\Auth\Passwords\PasswordResetServiceProvider::class,
    Illuminate\Session\SessionServiceProvider::class,
    Illuminate\Translation\TranslationServiceProvider::class,
    Illuminate\Validation\ValidationServiceProvider::class,
    Illuminate\View\ViewServiceProvider::class,

    /*
     * Package Service Providers...
     */
    Berkayk\OneSignal\OneSignalServiceProvider::class,

    /*
     * Application Service Providers...
     */
    App\Providers\AppServiceProvider::class,
    App\Providers\RouteServiceProvider::class,
    App\Providers\OneSignalServiceProvider::class,
  ],

  'aliases' => [
    'App' => Illuminate\Support\Facades\App::class,
    'Artisan' => Illuminate\Support\Facades\Artisan::class,
    'Auth' => Illuminate\Support\Facades\Auth::class,
    'Blade' => Illuminate\Support\Facades\Blade::class,
    'Broadcast' => Illuminate\Support\Facades\Broadcast::class,
    'Bus' => Illuminate\Support\Facades\Bus::class,
    'Cache' => Illuminate\Support\Facades\Cache::class,
    'Config' => Illuminate\Support\Facades\Config::class,
    'Cookie' => Illuminate\Support\Facades\Cookie::class,
    'Crypt' => Illuminate\Support\Facades\Crypt::class,
    'DB' => Illuminate\Support\Facades\DB::class,
    'Eloquent' => Illuminate\Database\Eloquent\Model::class,
    'Event' => Illuminate\Support\Facades\Event::class,
    'File' => Illuminate\Support\Facades\File::class,
    'Gate' => Illuminate\Support\Facades\Gate::class,
    'Hash' => Illuminate\Support\Facades\Hash::class,
    'Helper' => App\Helpers\Helpers::class,
    'Http' => Illuminate\Support\Facades\Http::class,
    'Lang' => Illuminate\Support\Facades\Lang::class,
    'Log' => Illuminate\Support\Facades\Log::class,
    'Mail' => Illuminate\Support\Facades\Mail::class,
    'Notification' => Illuminate\Support\Facades\Notification::class,
    'Password' => Illuminate\Support\Facades\Password::class,
    'Queue' => Illuminate\Support\Facades\Queue::class,
    'Redirect' => Illuminate\Support\Facades\Redirect::class,
    'Redis' => Illuminate\Support\Facades\Redis::class,
    'Request' => Illuminate\Support\Facades\Request::class,
    'Response' => Illuminate\Support\Facades\Response::class,
    'Route' => Illuminate\Support\Facades\Route::class,
    'Schema' => Illuminate\Support\Facades\Schema::class,
    'Session' => Illuminate\Support\Facades\Session::class,
    'Storage' => Illuminate\Support\Facades\Storage::class,
    'URL' => Illuminate\Support\Facades\URL::class,
    'Validator' => Illuminate\Support\Facades\Validator::class,
    'View' => Illuminate\Support\Facades\View::class,
    'Image' => Intervention\Image\Facades\Image::class,
  ],
];
