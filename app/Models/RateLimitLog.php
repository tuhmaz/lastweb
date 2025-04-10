<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RateLimitLog extends Model
{
    use HasFactory;

    /**
     * اسم الجدول المرتبط بالنموذج
     *
     * @var string
     */
    protected $table = 'rate_limit_logs';

    /**
     * الخصائص التي يمكن تعيينها بشكل جماعي
     *
     * @var array
     */
    protected $fillable = [
        'ip_address',
        'user_id',
        'route',
        'method',
        'user_agent',
        'attempts',
        'limit',
        'blocked_until',
    ];

    /**
     * الخصائص التي يجب تحويلها إلى أنواع بيانات محددة
     *
     * @var array
     */
    protected $casts = [
        'blocked_until' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * العلاقة مع نموذج المستخدم
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * تسجيل محاولة تجاوز الحد المسموح به من الطلبات
     *
     * @param array $data بيانات المحاولة
     * @return \App\Models\RateLimitLog
     */
    public static function logAttempt(array $data)
    {
        return self::create([
            'ip_address' => $data['ip_address'] ?? request()->ip(),
            'user_id' => $data['user_id'] ?? (auth()->check() ? auth()->id() : null),
            'route' => $data['route'] ?? request()->route()->getName() ?? request()->path(),
            'method' => $data['method'] ?? request()->method(),
            'user_agent' => $data['user_agent'] ?? request()->userAgent(),
            'attempts' => $data['attempts'] ?? 1,
            'limit' => $data['limit'] ?? 0,
            'blocked_until' => $data['blocked_until'] ?? now()->addMinutes(5),
        ]);
    }
}
