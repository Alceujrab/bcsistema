<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reconciliation extends Model
{
    use HasFactory;

    protected $fillable = [
        'bank_account_id', 
        'start_date', 
        'end_date',
        'starting_balance', 
        'ending_balance', 
        'calculated_balance',
        'difference', 
        'status', 
        'created_by', 
        'approved_by',
        'approved_at', 
        'notes'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'approved_at' => 'datetime',
        'starting_balance' => 'decimal:2',
        'ending_balance' => 'decimal:2',
        'calculated_balance' => 'decimal:2',
        'difference' => 'decimal:2',
    ];

    public function bankAccount()
    {
        return $this->belongsTo(BankAccount::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function calculate()
    {
        $credits = $this->transactions()
            ->where('type', 'credit')
            ->sum('amount');
            
        $debits = $this->transactions()
            ->where('type', 'debit')
            ->sum('amount');
            
        $this->calculated_balance = $this->starting_balance + $credits - $debits;
        $this->difference = $this->ending_balance - $this->calculated_balance;
        $this->save();
        
        return $this;
    }

    public function isBalanced()
    {
        return abs($this->difference) < 0.01;
    }
}