<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class AccountPayable extends Model
{
    use HasFactory;

    protected $fillable = [
        'supplier_id',
        'description',
        'amount',
        'due_date',
        'payment_date',
        'paid_amount',
        'status',
        'document_number',
        'category',
        'notes'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'due_date' => 'date',
        'payment_date' => 'date',
    ];

    // Relacionamentos
    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeOverdue($query)
    {
        return $query->where('status', 'overdue')
                    ->orWhere(function($q) {
                        $q->where('status', 'pending')
                          ->where('due_date', '<', now());
                    });
    }

    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    // Acessors
    public function getFormattedAmountAttribute()
    {
        return 'R$ ' . number_format($this->amount, 2, ',', '.');
    }

    public function getFormattedPaidAmountAttribute()
    {
        return 'R$ ' . number_format($this->paid_amount, 2, ',', '.');
    }

    public function getRemainingAmountAttribute()
    {
        return $this->amount - $this->paid_amount;
    }

    public function getFormattedRemainingAmountAttribute()
    {
        return 'R$ ' . number_format($this->remaining_amount, 2, ',', '.');
    }

    public function getStatusBadgeAttribute()
    {
        return match($this->status) {
            'pending' => '<span class="badge bg-warning">Pendente</span>',
            'partial' => '<span class="badge bg-info">Parcial</span>',
            'paid' => '<span class="badge bg-success">Pago</span>',
            'overdue' => '<span class="badge bg-danger">Vencido</span>',
            default => '<span class="badge bg-secondary">N/A</span>'
        };
    }

    public function getDaysUntilDueAttribute()
    {
        return now()->diffInDays($this->due_date, false);
    }
}
