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
        'bank_code',
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
            'investment' => 'Investimento',
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
    
    /**
     * Verifica se é cartão de crédito
     */
    public function isCreditCard()
    {
        return $this->type === 'credit_card';
    }
    
    /**
     * Verifica se é conta de investimento
     */
    public function isInvestment()
    {
        return $this->type === 'investment';
    }
    
    /**
     * Obtém o ícone baseado no tipo de conta
     */
    public function getIconAttribute()
    {
        return [
            'checking' => 'fas fa-university',
            'savings' => 'fas fa-piggy-bank',
            'investment' => 'fas fa-chart-line',
            'credit_card' => 'fas fa-credit-card'
        ][$this->type] ?? 'fas fa-university';
    }
    
    /**
     * Obtém cor baseada no tipo de conta
     */
    public function getColorAttribute()
    {
        return [
            'checking' => 'primary',
            'savings' => 'success',
            'investment' => 'warning',
            'credit_card' => 'danger'
        ][$this->type] ?? 'primary';
    }
    
    public function getTypeIconAttribute() 
    {
        return [
            'checking' => 'fas fa-university',
            'savings' => 'fas fa-piggy-bank',
            'investment' => 'fas fa-chart-line',
            'credit_card' => 'fas fa-credit-card'
        ][$this->type] ?? 'fas fa-wallet';
    }

    public function getTypeColorAttribute()
    {
        return [
            'checking' => 'primary',
            'savings' => 'success',
            'investment' => 'warning',
            'credit_card' => 'danger'
        ][$this->type] ?? 'secondary';
    }

    public function canHaveNegativeBalance()
    {
        return $this->type === 'credit_card';
    }
}