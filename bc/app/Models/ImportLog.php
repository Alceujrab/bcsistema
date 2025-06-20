<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImportLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'filename',
        'path',
        'bank_account_id',
        'import_type',
        'total_records',
        'imported_records',
        'skipped_records',
        'format',
        'encoding',
        'delimiter',
        'detected_bank',
        'status',
        'error_message',
        'metadata'
    ];

    protected $casts = [
        'metadata' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Relacionamento com conta bancária
     */
    public function bankAccount()
    {
        return $this->belongsTo(BankAccount::class);
    }

    /**
     * Transações relacionadas a esta importação
     */
    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'import_hash', 'id');
    }

    /**
     * Verificar se a importação foi bem-sucedida
     */
    public function isSuccessful()
    {
        return $this->status === 'completed';
    }

    /**
     * Verificar se a importação falhou
     */
    public function isFailed()
    {
        return $this->status === 'failed';
    }

    /**
     * Verificar se está em processamento
     */
    public function isProcessing()
    {
        return $this->status === 'processing';
    }

    /**
     * Obter taxa de sucesso da importação
     */
    public function getSuccessRateAttribute()
    {
        if ($this->total_records == 0) {
            return 0;
        }
        
        return round(($this->imported_records / $this->total_records) * 100, 2);
    }

    /**
     * Marcar como concluída
     */
    public function markAsCompleted()
    {
        $this->update(['status' => 'completed']);
    }

    /**
     * Marcar como falhou
     */
    public function markAsFailed($errorMessage = null)
    {
        $this->update([
            'status' => 'failed',
            'error_message' => $errorMessage
        ]);
    }

    /**
     * Marcar como em processamento
     */
    public function markAsProcessing()
    {
        $this->update(['status' => 'processing']);
    }
}
