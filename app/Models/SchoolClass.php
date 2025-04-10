<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;

class SchoolClass extends Model
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'grade_name',
        'grade_level',
        'country_id'
    ];

    /**
     * Get the country that owns the class.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    /**
     * Get the subjects for the class.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function subjects()
    {
        return $this->hasMany(Subject::class, 'grade_level', 'grade_level');
    }

    /**
     * Get the semesters for the class.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function semesters()
    {
        return $this->hasMany(Semester::class, 'grade_level', 'grade_level');
    }
}
