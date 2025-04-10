<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Article extends Model
{
    use HasFactory, LogsActivity;

    // Constants for article status
    const STATUS_DRAFT = 0;
    const STATUS_PUBLISHED = 1;

    // Fillable attributes to protect against mass assignment
    protected $fillable = [
        'title',
        'content',
        'grade_level',
        'subject_id',
        'semester_id',
        'meta_description',
        'author_id',
        'status',
        'keywords',
        'views_count',
        'published_at'
    ];

    protected $casts = [
        'status' => 'boolean',
        'published_at' => 'datetime',
        'views_count' => 'integer'
    ];

    protected $with = ['subject', 'semester', 'schoolClass'];

    /**
     * Get the list of status options
     *
     * @return array
     */
    public static function getStatusOptions()
    {
        return [
            self::STATUS_DRAFT => 'Draft',
            self::STATUS_PUBLISHED => 'Published'
        ];
    }

    /**
     * Check if the article is published
     *
     * @return bool
     */
    public function isPublished()
    {
        return $this->status === self::STATUS_PUBLISHED;
    }

    /**
     * Check if the article is draft
     *
     * @return bool
     */
    public function isDraft()
    {
        return $this->status === self::STATUS_DRAFT;
    }

    /**
     * Scope a query to only include published articles
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePublished($query)
    {
        return $query->where('status', self::STATUS_PUBLISHED);
    }

    /**
     * Scope a query to only include draft articles
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeDraft($query)
    {
        return $query->where('status', self::STATUS_DRAFT);
    }

    /**
     * Relationship with Subject model
     */
    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    /**
     * Relationship with Semester model
     */
    public function semester()
    {
        return $this->belongsTo(Semester::class);
    }

    /**
     * Relationship with SchoolClass based on grade_level
     */
    public function schoolClass()
    {
        return $this->belongsTo(SchoolClass::class, 'grade_level', 'id');
    }

    /**
     * Relationship with File model
     */
    public function files()
    {
        return $this->hasMany(File::class);
    }

    /**
     * Relationship with User model (as author)
     */
    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    /**
     * Polymorphic relationship for comments
     * Use default connection 'jo' for comments
     */
    public function comments()
    {
        $database = session('database', 'jo');

        return $this->morphMany(Comment::class, 'commentable')
                    ->where('database', $database)
                    ->with(['user', 'reactions' => function($q) use ($database) {
                        $q->where('database', $database);
                    }]);
    }

    /**
     * Relationship with Country model
     */
    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    /**
     * Relationship with Keyword model using a pivot table
     */
    public function keywords()
    {
        return $this->belongsToMany(Keyword::class, 'article_keyword', 'article_id', 'keyword_id');
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['title', 'status', 'published_at'])
            ->logOnlyDirty()
            ->setDescriptionForEvent(function(string $eventName) {
                return "تم {$eventName} مقال: {$this->title}";
            });
    }
}
