<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class Production extends Model
{
    use HasFactory;

    protected $table = 'productions';
    public $incrementing = false;
    protected $keyType = 'string';

    // FULL fillable sesuai migrasi
    protected $fillable = [
        'id',
        'code',
        'product_id',
        'qty_planned',
        'qty_produced',
        'status',
        'scheduled_at',
        'started_at',
        'finished_at',
        'notes',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'qty_planned' => 'decimal:4',
        'qty_produced'=> 'decimal:4',
        'scheduled_at'=> 'datetime',
        'started_at'  => 'datetime',
        'finished_at' => 'datetime',
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

    // Produk utama (finished good)
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    // Output lines (produk utama/byproduct)
    public function products()
    {
        return $this->hasMany(ProductionProduct::class, 'production_id');
    }

    // Material lines (kebutuhan & konsumsi)
    public function materials()
    {
        return $this->hasMany(ProductionMaterial::class, 'production_id');
    }

    // Jurnal pergerakan material yang terkait produksi ini
    // (tabel material_moves: ref_type='production', ref_id=productions.id)
    public function moves()
    {
        return $this->hasMany(MaterialMove::class, 'ref_id')
            ->where('ref_type', 'production');
    }
}
