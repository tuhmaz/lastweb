<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class File extends Model
{
    use HasFactory;

    protected $fillable = [
        'article_id',
        'file_path',
        'file_type',
        'file_category',
        'file_name',
        'file_size',
        'mime_type'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the article that owns the file.
     */
    public function article(): BelongsTo
    {
        return $this->belongsTo(Article::class);
    }

    /**
     * Delete the file from storage when the model is deleted.
     */
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($file) {
            if (Storage::disk('public')->exists($file->file_path)) {
                Storage::disk('public')->delete($file->file_path);
            }
        });
    }

    public function getFileNameAttribute()
    {
        // If file_Name is empty, get the name from the file path
        if (empty($this->attributes['file_Name'])) {
            return basename($this->attributes['file_path']);
        }
        return $this->attributes['file_Name'];
    }
}
