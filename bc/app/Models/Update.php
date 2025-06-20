<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Update extends Model
{
    use HasFactory;

    protected $fillable = [
        'version',
        'name',
        'description',
        'release_date',
        'download_url',
        'file_path',
        'file_size',
        'file_hash',
        'changes',
        'requirements',
        'status',
        'applied_at',
        'error_message',
        'backup_path'
    ];

    protected $casts = [
        'changes' => 'array',
        'requirements' => 'array',
        'release_date' => 'datetime',
        'applied_at' => 'datetime',
    ];

    const STATUS_AVAILABLE = 'available';
    const STATUS_DOWNLOADING = 'downloading';
    const STATUS_READY = 'ready';
    const STATUS_APPLYING = 'applying';
    const STATUS_APPLIED = 'applied';
    const STATUS_FAILED = 'failed';

    public function isApplied()
    {
        return $this->status === self::STATUS_APPLIED;
    }

    public function canApply()
    {
        return in_array($this->status, [self::STATUS_AVAILABLE, self::STATUS_READY, self::STATUS_FAILED]);
    }

    public function getFormattedFileSizeAttribute()
    {
        return $this->formatBytes($this->file_size);
    }

    private function formatBytes($size, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        for ($i = 0; $size > 1024 && $i < count($units) - 1; $i++) {
            $size /= 1024;
        }
        
        return round($size, $precision) . ' ' . $units[$i];
    }

    public function scopeAvailable($query)
    {
        return $query->where('status', self::STATUS_AVAILABLE);
    }

    public function scopeApplied($query)
    {
        return $query->where('status', self::STATUS_APPLIED);
    }

    public function scopeLatest($query)
    {
        return $query->orderBy('release_date', 'desc');
    }
}
