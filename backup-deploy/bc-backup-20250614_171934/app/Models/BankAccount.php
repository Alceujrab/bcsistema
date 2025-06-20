<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BankAccount extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 
        'bank_name', 
        'account_number', 
        'agency', 
        'type', 
        'balance', 
        'active'
    ];

    protected $casts = [
        'balance' => 'decimal:2',
        'active' => 'boolean',
    ];

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function reconciliations()
    {
        return $this->hasMany(Reconciliation::class);
    }

    public function imports()
    {
        return $this->hasMany(StatementImport::class);
    }

    public function getTypeNameAttribute()
    {
        return [
            'checking' => 'Conta Corrente',
            'savings' => 'Poupança',
            'credit_card' => 'Cartão de Crédito'
        ][$this->type] ?? $this->type;
    }

    public function updateBalance()
    {
        $credits = $this->transactions()
            ->where('type', 'credit')
            ->where('status', 'reconciled')
            ->sum('amount');
            
        $debits = $this->transactions()
            ->where('type', 'debit')
            ->where('status', 'reconciled')
            ->sum('amount');
            
        $this->balance = $credits - $debits;
        $this->save();
    }
}