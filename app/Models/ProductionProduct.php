<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class ProductionProduct extends Model
{
    use HasFactory;

    protected $table = 'production_products';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'production_id',
        'product_id',
        'qty',
        'unit_cost',
        'total_cost',
        'produced_at',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'qty'        => 'decimal:4',
        'unit_cost'  => 'decimal:4',
        'total_cost' => 'decimal:2',
        'produced_at'=> 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $m) {
            if (!$m->getKey()) {
                $m->{$m->getKeyName()} = (string) Str::uuid();
            }
        });
    }

    // ===== RELATIONS =====
    public function production()
    {
        return $this->belongsTo(Production::class, 'production_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
