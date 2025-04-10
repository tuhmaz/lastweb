<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\User;
class VisitorTracking extends Model
{
    protected $table = 'visitors_tracking';
    protected $guarded = [];

    protected $fillable = [
        'ip_address',
        'user_agent',
        'country',
        'city',
        'browser',
        'os',
        'url',
        'latitude',
        'longitude',
        'user_id',
        'status_code',
        'last_activity'
    ];
    protected $casts = [
        'last_activity' => 'datetime'
    ];
     


    /**
     * Get the user associated with the visitor.
     */
    

    /**
     * Get the page visits for the visitor.
     */
    public function pageVisits(): HasMany
    {
        return $this->hasMany(PageVisit::class, 'visitor_id');
    }

    /**
     * Scope a query to only include online visitors.
     */
    public function scopeOnline($query)
    {
        return $query->where('last_activity', '>=', now()->subMinutes(5));
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Force the VisitorTracking model to always use the main connection
    public function getConnectionName()
    {
        return 'jo';
    }
}
