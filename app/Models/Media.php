<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Media extends Model
{
    use HasFactory;

    protected $table = 'media';

    protected $fillable = [
        'title',
        'type',
        'genre',
        'duration_minutes',
        'release_year',
        'director',
        'age_rating',
        'artist',
        'album',
    ];

    protected $casts = [
        'duration_minutes' => 'integer',
        'release_year' => 'integer',
    ];

    /**
     * Relasi Many-to-Many: Media dapat dimasukkan ke koleksi banyak pengguna.
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'media_user')
                    ->withTimestamps();
    }

    /**
     * Accessor untuk format durasi (jam & menit).
     */
    public function getFormattedDurationAttribute(): string
    {
        $hours = intdiv((int) $this->duration_minutes, 60);
        $minutes = (int) $this->duration_minutes % 60;

        if ($hours > 0) {
            return "{$hours} jam {$minutes} menit";
        }
        return "{$minutes} menit";
    }
}
