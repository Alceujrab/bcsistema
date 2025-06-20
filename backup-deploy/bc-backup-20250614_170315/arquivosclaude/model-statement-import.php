<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StatementImport extends Model
{
    use HasFactory;

    protected $fillable = [
        'bank_account_id',
        'filename',
        'file_type',
        'total_transactions',
        'imported_transactions',
        'duplicate_transactions',
        'error_transactions',
        'status',
        'import_log',
        'imported_by'
    ];

    protected $casts = [
        'import_log' => 'array',
        'total_transactions' => 'integer',
        'imported_transactions' => 'integer',
        'duplicate_transactions' => 'integer',
        'error_transactions' => 'integer',
    ];

    public function bankAccount()
    {
        return $this->belongsTo(BankAccount::class);
    }

    public function importer()
    {
        return $this->belongsTo(User::class, 'imported_by');
    }

    public function getSuccessRateAttribute()
    {
        if ($this->total_transactions == 0) {
            return 0;
        }
        
        return round(($this->imported_transactions / $this->total_transactions) * 100, 2);
    }

    public function getStatusColorAttribute()
    {
        return [
            'processing' => 'warning',
            'completed' => 'success',
            'failed' => 'danger'
        ][$this->status] ?? 'secondary';
    }
}