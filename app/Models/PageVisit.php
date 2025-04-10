<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PageVisit extends Model
{
    protected $table = 'page_visits';

    protected $fillable = [
        'visitor_id',
        'page_url'
    ];

    /**
     * Get the visitor that owns the page visit.
     */
    public function visitor(): BelongsTo
    {
        return $this->belongsTo(VisitorTracking::class, 'visitor_id');
    }
}
