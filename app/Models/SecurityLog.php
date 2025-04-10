<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cache;
use App\Models\User;
use Carbon\Carbon;

class SecurityLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'ip_address',
        'user_agent',
        'event_type',
        'description',
        'user_id',
        'route',
        'request_data',
        'severity',
        'is_resolved',
        'resolved_at',
        'resolved_by',
        'resolution_notes',
        'country_code',
        'city',
        'attack_type',
        'risk_score',
    ];

    protected $casts = [
        'request_data' => 'encrypted:array',
        'is_resolved' => 'boolean',
        'resolved_at' => 'datetime',
        'risk_score' => 'integer',
    ];

    // أنواع الأحداث
    const EVENT_TYPES = [
        'LOGIN_SUCCESS' => 'login',
        'LOGIN_FAILED' => 'failed_login',
        'LOGOUT' => 'logout',
        'PASSWORD_RESET' => 'password_reset',
        'PROFILE_UPDATE' => 'profile_update',
        'SETTINGS_CHANGE' => 'settings_change',
        'API_ACCESS' => 'api_access',
        'SUSPICIOUS_ACTIVITY' => 'suspicious_activity',
        'BLOCKED_ACCESS' => 'blocked_access',
        'DATA_EXPORT' => 'data_export',
        'PERMISSION_CHANGE' => 'permission_change',
        'FILE_ACCESS' => 'file_access',
        'ADMIN_ACTION' => 'admin_action',
    ];

    // مستويات الخطورة
    const SEVERITY_LEVELS = [
        'INFO' => 'info',
        'WARNING' => 'warning',
        'DANGER' => 'danger',
        'CRITICAL' => 'critical',
    ];

    // العلاقة مع المستخدم
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // العلاقة مع المستخدم الذي قام بحل المشكلة
    public function resolvedByUser()
    {
        return $this->belongsTo(User::class, 'resolved_by');
    }

    // الحصول على لون نوع الحدث
    public function getEventTypeColorAttribute()
    {
        return match($this->event_type) {
            self::EVENT_TYPES['LOGIN_SUCCESS'] => 'success',
            self::EVENT_TYPES['LOGOUT'] => 'info',
            self::EVENT_TYPES['LOGIN_FAILED'] => 'danger',
            self::EVENT_TYPES['PASSWORD_RESET'] => 'warning',
            self::EVENT_TYPES['PROFILE_UPDATE'] => 'primary',
            self::EVENT_TYPES['SETTINGS_CHANGE'] => 'secondary',
            self::EVENT_TYPES['SUSPICIOUS_ACTIVITY'] => 'danger',
            self::EVENT_TYPES['BLOCKED_ACCESS'] => 'dark',
            self::EVENT_TYPES['DATA_EXPORT'] => 'info',
            default => 'dark'
        };
    }

    // Scope للأحداث غير المحلولة
    public function scopeUnresolved(Builder $query)
    {
        return $query->where('is_resolved', false);
    }

    // Scope للأحداث الحرجة
    public function scopeCritical(Builder $query)
    {
        return $query->where('severity', self::SEVERITY_LEVELS['CRITICAL']);
    }

    // Scope للأحداث الأخيرة
    public function scopeRecent(Builder $query, $hours = 24)
    {
        return $query->where('created_at', '>=', now()->subHours($hours));
    }
}