<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'bank_account_id', 
        'transaction_date', 
        'description', 
        'amount', 
        'type', 
        'reference_number', 
        'category', 
        'status', 
        'reconciliation_id', 
        'import_hash'
    ];

    protected $casts = [
        'transaction_date' => 'date',
        'amount' => 'decimal:2',
    ];

    public function bankAccount()
    {
        return $this->belongsTo(BankAccount::class);
    }

    public function reconciliation()
    {
        return $this->belongsTo(Reconciliation::class);
    }

    public function getFormattedAmountAttribute()
    {
        $prefix = $this->type === 'debit' ? '-' : '+';
        return $prefix . ' R$ ' . number_format($this->amount, 2, ',', '.');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeReconciled($query)
    {
        return $query->where('status', 'reconciled');
    }

    public function scopeInPeriod($query, $startDate, $endDate)
    {
        return $query->whereBetween('transaction_date', [$startDate, $endDate]);
    }

    public static function generateImportHash($data)
    {
        return md5(
            $data['bank_account_id'] . 
            $data['transaction_date'] . 
            $data['amount'] . 
            $data['description']
        );
    }
}