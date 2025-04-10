<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class News extends Model
{
    use HasFactory, LogsActivity;

    protected $table = 'news';

    protected $fillable = [
        'category_id',
        'title',
        'slug',
        'content',
        'image',
        'alt',
        'is_active',
        'is_featured',
        'views',
        'country',
        'keywords',
        'meta_description',
        'author_id'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'views' => 'integer'
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['title', 'is_active'])
            ->logOnlyDirty()
            ->setDescriptionForEvent(function(string $eventName) {
                $action = match($eventName) {
                    'created' => 'إضافة',
                    'updated' => 'تحديث',
                    'deleted' => 'حذف',
                    default => $eventName
                };
                return "تم {$action} خبر: {$this->title}";
            });
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function comments(): MorphMany
    {
        $database = session('database', 'jo');

        return $this->morphMany(Comment::class, 'commentable')
                    ->where('database', $database)
                    ->with(['user', 'reactions' => function($q) use ($database) {
                        $q->where('database', $database);
                    }]);
    }

    public function keywords()
    {
        return $this->belongsToMany(Keyword::class, 'news_keyword', 'news_id', 'keyword_id')
                    ->withTimestamps();
    }

    public function getKeywordsArrayAttribute()
    {
        return $this->keywords instanceof \Illuminate\Database\Eloquent\Collection
            ? $this->keywords->pluck('keyword')->toArray()
            : [];
    }

    public function getKeywordsStringAttribute()
    {
        return !empty($this->keywords_array)
            ? implode(',', $this->keywords_array)
            : '';
    }
}
