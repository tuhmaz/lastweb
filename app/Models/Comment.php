<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class Comment extends Model
{
    protected $fillable = [
        'body',
        'user_id',
        'commentable_id',
        'commentable_type',
        'database'
    ];

    protected $with = ['user'];

    // الاتصال الديناميكي بقاعدة البيانات
    public function getConnectionName()
    {
        return 'jo';
    }

    // العلاقة متعددة الأشكال
    public function commentable()
    {
        return $this->morphTo();
    }

    // العلاقة مع المستخدم (يظل في قاعدة البيانات الرئيسية)
    public function user()
    {
        // Always use 'jo' connection for users
        return $this->setConnection('jo')
                    ->belongsTo(User::class)
                    ->withDefault([
                        'name' => 'Unknown User',
                        'email' => 'unknown@example.com'
                    ]);
    }

    // العلاقة مع ردود الأفعال
    public function reactions()
    {
        return $this->hasMany(Reaction::class);
    }

    // Boot method to add global scope
    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope('database_scope', function (Builder $builder) {
            $database = session('database', 'jo');
            $builder->where('database', $database);
        });

        // Always set the database field when creating a new comment
        static::creating(function ($comment) {
            if (!$comment->database) {
                $comment->database = session('database', 'jo');
            }
        });
    }
}
