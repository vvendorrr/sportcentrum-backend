<?php

namespace App\Models;

use Database\Factories\LessonFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Lesson extends Model
{
    /** @use HasFactory<LessonFactory> */
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'instructor_id',
        'starts_at',
        'duration_minutes',
        'category',
        'sport',
        'max_participants',
    ];

    protected function casts(): array
    {
        return [
            'starts_at' => 'datetime',
            'duration_minutes' => 'integer',
            'max_participants' => 'integer',
        ];
    }

    public function instructor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }

    public function registrations(): HasMany
    {
        return $this->hasMany(LessonRegistration::class);
    }
}
