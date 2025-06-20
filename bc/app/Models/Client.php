<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'document',
        'document_type',
        'address',
        'city',
        'state',
        'zip_code',
        'notes',
        'active'
    ];

    protected $casts = [
        'active' => 'boolean',
    ];

    // Relacionamentos
    public function accountReceivables()
    {
        return $this->hasMany(AccountReceivable::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    // Acessors
    public function getFormattedDocumentAttribute()
    {
        if (!$this->document) return null;
        
        if ($this->document_type === 'cpf') {
            return preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $this->document);
        }
        
        if ($this->document_type === 'cnpj') {
            return preg_replace('/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/', '$1.$2.$3/$4-$5', $this->document);
        }
        
        return $this->document;
    }
}
