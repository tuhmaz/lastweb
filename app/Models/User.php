<?php

namespace App\Models;

use App\Notifications\CustomVerifyEmail;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Support\Facades\Log;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens;
    use HasFactory;
    use Notifiable;
    use TwoFactorAuthenticatable;
    use HasRoles;

    protected $guard_name = 'sanctum';

    /**
     * التحقق مما إذا كان المستخدم مدير
     *
     * @return bool
     */
    public function isAdmin()
    {
        return $this->hasRole('Admin');
    }

    /**
     * إرسال إشعار تأكيد البريد الإلكتروني.
     *
     * @return void
     */
    public function sendEmailVerificationNotification()
    {
        Log::info('Sending email verification notification', [
            'user_id' => $this->id,
            'email' => $this->email
        ]);

        $this->notify(new CustomVerifyEmail);

        Log::info('Email verification notification sent successfully');
    }

    /**
     * الحصول على رابط الصورة الشخصية للمستخدم
     *
     * @return string
     */
    public function getProfilePhotoUrlAttribute()
    {
        // التحقق من وجود حقل profile_photo_path أولاً
        if ($this->profile_photo_path) {
            return asset('storage/' . $this->profile_photo_path);
        }
        
        // التحقق من وجود حقل avatar كبديل (للتوافق مع الإصدارات السابقة)
        if (isset($this->attributes['avatar']) && $this->attributes['avatar']) {
            $avatar = $this->attributes['avatar'];
            // التحقق مما إذا كان المسار يحتوي على URL كامل
            if (filter_var($avatar, FILTER_VALIDATE_URL)) {
                return $avatar;
            }
            // التحقق مما إذا كان المسار يبدأ بـ /storage
            if (strpos($avatar, '/storage') === 0) {
                return asset($avatar);
            }
            // إذا كان المسار يحتوي على مسار نسبي
            return asset('storage/' . $avatar);
        }

        // استخدام صورة افتراضية إذا لم يتم العثور على صورة
        $randomNumber = ($this->id % 8) + 1;
        return asset("assets/img/avatars/{$randomNumber}.png");
    }

    /**
     * تحقق مما إذا كان المستخدم متصل حالياً
     *
     * @return bool
     */
    public function isOnline()
    {
        return $this->last_activity && $this->last_activity->gt(now()->subMinutes(5));
    }

    /**
     * تحديث آخر نشاط للمستخدم
     *
     * @return void
     */
    public function updateLastActivity()
    {
        $this->last_activity = now();
        $this->save();
    }

    /**
     * الحقول التي يمكن تعبئتها جماعياً
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'api_token',
        'phone',
        'job_title',
        'gender',
        'country',
        'bio',
        'social_links',
        'profile_photo_path',
        'avatar', // للتوافق مع الإصدارات السابقة
        'status',
        'last_activity',
        'current_team_id'
    ];

    /**
     * الحقول المخفية عند التحويل إلى مصفوفة أو JSON
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * الحقول التي يجب تحويلها إلى أنواع بيانات محددة
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'social_links' => 'array',
        'last_activity' => 'datetime'
    ];

    /**
     * الخصائص المضافة إلى نموذج المستخدم
     *
     * @var array<int, string>
     */
    protected $appends = [
        'profile_photo_url',
    ];
}
