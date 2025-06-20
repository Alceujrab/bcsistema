<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'icon',
        'color',
        'keywords',
        'type',
        'active',
        'sort_order'
    ];

    protected $casts = [
        'active' => 'boolean',
        'keywords' => 'array'
    ];

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public static function boot()
    {
        parent::boot();

        static::creating(function ($category) {
            if (empty($category->slug)) {
                $category->slug = Str::slug($category->name);
            }
        });
    }

    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    public function scopeForExpenses($query)
    {
        return $query->whereIn('type', ['expense', 'both']);
    }

    public function scopeForIncome($query)
    {
        return $query->whereIn('type', ['income', 'both']);
    }

    public function matchesDescription($description)
    {
        if (empty($this->keywords)) {
            return false;
        }

        $description = strtolower($description);
        
        foreach ($this->keywords as $keyword) {
            if (strpos($description, strtolower($keyword)) !== false) {
                return true;
            }
        }

        return false;
    }
}
