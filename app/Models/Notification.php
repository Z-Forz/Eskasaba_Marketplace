<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\MassPrunable;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory, MassPrunable;

    protected $fillable = [
        'user_id',
        'title',
        'message',
        'type',
        'link',
        'is_read',
    ];

    protected function casts(): array
    {
        return [
            'is_read' => 'boolean',
        ];
    }

    /**
     * Get the prunable model query (notifications older than 7 days).
     */
    public function prunable(): Builder
    {
        return static::where('created_at', '<', now()->subDays(7));
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

