<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Reconciliações criadas pelo usuário
     */
    public function createdReconciliations()
    {
        return $this->hasMany(Reconciliation::class, 'created_by');
    }

    /**
     * Reconciliações aprovadas pelo usuário
     */
    public function approvedReconciliations()
    {
        return $this->hasMany(Reconciliation::class, 'approved_by');
    }

    /**
     * Importações realizadas pelo usuário
     */
    public function imports()
    {
        return $this->hasMany(StatementImport::class, 'imported_by');
    }
}