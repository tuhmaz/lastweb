<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reaction extends Model
{
    protected $fillable = [
        'user_id',
        'comment_id',
        'type',
        'database'
    ];

    // الاتصال الديناميكي بقاعدة البيانات
    public function getConnectionName()
    {
        return $this->database ?? config('database.default');
    }

    // العلاقة مع التعليقات
    public function comment()
    {
        return $this->belongsTo(Comment::class)->withDefault();
    }

    // العلاقة مع المستخدم
    public function user()
    {
        // نقوم بإنشاء نموذج User مباشرة مع تحديد الاتصال
        $userModel = (new User())->setConnection(config('database.default'));
        
        return $this->belongsTo(get_class($userModel), 'user_id')
                    ->withDefault();
    }
}
