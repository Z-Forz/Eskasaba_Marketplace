<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WhatsAppBroadcast extends Model
{
    use HasFactory;

    protected $table = 'whatsapp_broadcasts';

    protected $fillable = [
        'title',
        'target_type',
        'message',
        'total_recipients',
        'sent_count',
        'failed_count',
        'delay_seconds',
        'status',
        'created_by',
        'started_at',
        'completed_at',
    ];

    protected $casts = [
        'total_recipients' => 'integer',
        'sent_count' => 'integer',
        'failed_count' => 'integer',
        'delay_seconds' => 'integer',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function logs(): HasMany
    {
        return $this->hasMany(WhatsAppBroadcastLog::class, 'whatsapp_broadcast_id');
    }

    public function getProgressPercentageAttribute(): int
    {
        if ($this->total_recipients <= 0) {
            return 0;
        }

        $processed = $this->sent_count + $this->failed_count;
        $percentage = (int) round(($processed / $this->total_recipients) * 100);

        return min(100, max(0, $percentage));
    }

    public function getTargetLabelAttribute(): string
    {
        return match ($this->target_type) {
            'teacher' => 'Guru & Staf',
            'student_10' => 'Siswa Kelas 10 (X)',
            'student_11' => 'Siswa Kelas 11 (XI)',
            'student_12' => 'Siswa Kelas 12 (XII)',
            'all' => 'Semua Users (Guru & Siswa)',
            default => 'Semua Users',
        };
    }
}
