<?php

namespace App\Models;

use Database\Factories\LessonRegistrationFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LessonRegistration extends Model
{
    /** @use HasFactory<LessonRegistrationFactory> */
    use HasFactory;

    protected $fillable = [
        'lesson_id',
        'user_id',
        'status',
        'registered_at',
        'attendance',
        'canceled_at',
    ];

    protected function casts(): array
    {
        return [
            'registered_at' => 'datetime',
            'attendance' => 'string',
            'canceled_at' => 'datetime',
        ];
    }

    public function lesson(): BelongsTo
    {
        return $this->belongsTo(Lesson::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
