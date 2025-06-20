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
        $colors = [
            'completed' => 'success',
            'processing' => 'warning',
            'failed' => 'danger',
            'pending' => 'secondary'
        ];
        
        return $colors[$this->status] ?? 'secondary';
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'bank_account_id', 'bank_account_id')
            ->where('created_at', '>=', $this->created_at)
            ->where('created_at', '<=', $this->created_at->addMinutes(5));
    }

    public function canBeDeleted()
    {
        return $this->transactions()
            ->where('status', 'reconciled')
            ->count() == 0;
    }

    public function getNonReconciledTransactionsCountAttribute()
    {
        return $this->transactions()
            ->where('status', '!=', 'reconciled')
            ->count();
    }
}