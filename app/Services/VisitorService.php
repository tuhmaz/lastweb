<?php

namespace App\Services;

use App\Models\VisitorTracking;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use GeoIp2\Database\Reader;
use DeviceDetector\DeviceDetector;
use Carbon\Carbon;

/**
 * Class VisitorService
 *
 * خدمة مسؤولة عن إحصائيات الزوار وعمليات تحليل الـUser-Agent والموقع الجغرافي.
 */
class VisitorService
{
    /**
     * @var Reader
     */
    protected $geoReader;

    /**
     * VisitorService constructor.
     * يقوم بتحميل قاعدة بيانات GeoLite2-City من مجلد التخزين.
     */
    public function __construct()
    {
        // تعديل المسار وفقاً للمكان الذي وضعت فيه قاعدة MaxMind
        $this->geoReader = new Reader(storage_path('geoip/GeoLite2-City.mmdb'));
    }

    /**
     * إحصائيات الزوار (الحاليين، اليوم، نسبة التغير، والسجل خلال آخر 24 ساعة).
     *
     * @return array
     */
    public function getVisitorStats()
    {
        try {
            // عدد الزوار الحاليين (آخر 5 دقائق)
            $currentVisitors = Cache::remember('current_visitors', 300, function () {
                return VisitorTracking::where('last_activity', '>=', now()->subMinutes(5))->count();
            });

            // عدد زيارات اليوم
            $totalToday = Cache::remember('total_today_visitors', 86400, function () {
                return VisitorTracking::whereDate('created_at', today())->count();
            });

            // الزوار في آخر ساعة
            $lastHour = VisitorTracking::where('created_at', '>=', now()->subHour())->count();
            // الزوار في الساعة السابقة
            $previousHour = VisitorTracking::where('created_at', '>=', now()->subHours(2))
                ->where('created_at', '<', now()->subHour())
                ->count();

            // نسبة التغيير
            $change = $previousHour > 0
                ? round((($lastHour - $previousHour) / $previousHour) * 100)
                : 0;

            // سجل الزيارات خلال آخر 24 ساعة، مجمّعة بالساعة
            $history = Cache::remember('visitor_history', 3600, function () {
                return DB::table('visitors_tracking')
                    ->select(
                        DB::raw('DATE_FORMAT(created_at, "%Y-%m-%d %H:00:00") as timestamp'),
                        DB::raw('COUNT(*) as count')
                    )
                    ->where('created_at', '>=', now()->subDay())
                    ->groupBy('timestamp')
                    ->orderBy('timestamp')
                    ->get()
                    ->map(function ($item) {
                        return [
                            'timestamp' => $item->timestamp,
                            'count'     => (int) $item->count,
                        ];
                    })
                    ->toArray();
            });

            return [
                'current'     => $currentVisitors,
                'total_today' => $totalToday,
                'change'      => $change,
                'history'     => $history,
            ];
        } catch (\Exception $e) {
            Log::error('Error getting visitor stats: ' . $e->getMessage());
            return [
                'current'     => 0,
                'total_today' => 0,
                'change'      => 0,
                'history'     => [],
            ];
        }
    }

    /**
     * يعيد بيانات المواقع الجغرافية للزوار خلال آخر 24 ساعة.
     *
     * @return array
     */
    public function getVisitorLocations()
    {
        try {
            // جلب جميع عناوين IP من آخر 24 ساعة مع عدد الزيارات لكل IP
            $visitors = DB::table('visitors_tracking')
                ->select('ip_address', DB::raw('COUNT(*) as count'))
                ->whereNotNull('ip_address')
                ->where('created_at', '>=', now()->subDay())
                ->groupBy('ip_address')
                ->get();

            // تحويل كل IP إلى بيانات جغرافية
            $locations = $visitors->map(function ($visitor) {
                $geoData = $this->getGeoDataFromIP($visitor->ip_address);

                return [
                    'country' => $geoData['country'] ?? 'Unknown',
                    'city'    => $geoData['city']    ?? 'Unknown',
                    'lat'     => $geoData['lat']     ?? null,
                    'lng'     => $geoData['lon']     ?? null,
                    'count'   => (int) $visitor->count,
                ];
            });

            // تجميع النتائج حسب (الدولة-المدينة)
            $groupedLocations = $locations->groupBy(function ($item) {
                return $item['country'] . '-' . $item['city'];
            })->map(function ($group) {
                return [
                    'country' => $group->first()['country'],
                    'city'    => $group->first()['city'],
                    'lat'     => $group->first()['lat'],
                    'lng'     => $group->first()['lng'],
                    'count'   => $group->sum('count'),
                ];
            })->values();

            if ($groupedLocations->isEmpty()) {
                Log::warning('No visitor locations found via IP in the last 24 hours.');
            } else {
                Log::info('Visitor Locations Data via MaxMind:', $groupedLocations->toArray());
            }

            return $groupedLocations->toArray();
        } catch (\Exception $e) {
            Log::error('Error resolving IP locations: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * يحلّل الـUser-Agent ويعيد معلومات مفصلة باستخدام DeviceDetector.
     *
     * @param  string  $userAgent
     * @return array
     */
    public function analyzeUserAgent($userAgent)
    {
        // أنشئ كائن DeviceDetector
        $dd = new DeviceDetector($userAgent);
        $dd->parse();

        // التحقق إن كان بوت
        if ($dd->isBot()) {
            // مثال: ['name'=>'Googlebot','category'=>'Search bot','url'=>'http://...','producer'=>...]
            return [
                'isBot'   => true,
                'botInfo' => $dd->getBot(),
                'client'  => null,
                'os'      => null,
                'device'  => null,
                'brand'   => null,
                'model'   => null,
            ];
        }

        // ليس بوت => جلب معلومات المتصفح/النظام/الجهاز
        return [
            'isBot'   => false,
            'botInfo' => null,
            'client'  => $dd->getClient(),      // ['type'=>'browser','name'=>'Chrome','version'=>'...']
            'os'      => $dd->getOs(),          // ['name'=>'Windows','version'=>'...']
            'device'  => $dd->getDeviceName(),  // 'desktop','smartphone','tablet','tv'...
            'brand'   => $dd->getBrandName(),   // الشركة المصنّعة
            'model'   => $dd->getModel(),       // موديل الجهاز
        ];
    }

    /**
     * يعيد قائمة الزوار النشطين خلال آخر X دقائق.
     *
     * @param  int  $minutes
     * @return array
     */
    public function getActiveVisitors($minutes = 5)
    {
        try {
            $activeVisitors = VisitorTracking::where('last_activity', '>=', now()->subMinutes($minutes))
                ->orderBy('last_activity', 'desc')
                ->get();

            return $activeVisitors->map(function ($visitor) {
                return [
                    'ip'          => $visitor->ip_address ?? 'Unknown',
                    'country'     => $visitor->country ?? 'Unknown',
                    'city'        => $visitor->city ?? 'Unknown',
                    'browser'     => $visitor->browser ?? 'Unknown',
                    'os'          => $visitor->os ?? 'Unknown',
                    'last_active' => $visitor->last_activity,
                ];
            })->toArray();
        } catch (\Exception $e) {
            Log::error('Error getting active visitors: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * جلب بيانات الموقع الجغرافي من عنوان IP عبر قاعدة MaxMind، مع التخزين المؤقت.
     *
     * @param  string  $ip
     * @return array
     */
    public function getGeoDataFromIP($ip)
    {
        try {
            // تجاهل IP المحلي أو فارغ
            if ($ip === '127.0.0.1' || !$ip) {
                return [
                    'country' => 'Localhost',
                    'city'    => 'Local',
                    'lat'     => null,
                    'lon'     => null,
                ];
            }

            // التحقق من التخزين المؤقت
            $cachedLocation = Cache::get('geo_ip_' . $ip);
            if ($cachedLocation) {
                return $cachedLocation;
            }

            // استدعاء MaxMind GeoLite2
            $record = $this->geoReader->city($ip);
            $data = [
                'country' => $record->country->name ?? 'Unknown',
                'city'    => $record->city->name ?? 'Unknown',
                'lat'     => $record->location->latitude ?? null,
                'lon'     => $record->location->longitude ?? null,
            ];

            // تخزين البيانات في الكاش لمدة يوم
            Cache::put('geo_ip_' . $ip, $data, 86400);

            return $data;
        } catch (\Exception $e) {
            Log::error("Error fetching geolocation for IP $ip: " . $e->getMessage());
            return [
                'country' => 'Unknown',
                'city'    => 'Unknown',
                'lat'     => null,
                'lon'     => null,
            ];
        }
    }
}
