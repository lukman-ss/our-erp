<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class MaterialBatch extends Model
{
    use HasFactory;

    protected $table = 'material_batches';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'material_id',
        'qty_initial',
        'qty_remaining',
        'unit_cost',
        'received_at',
        'purchase_id',
        'purchase_material_id', // hasil rename dari purchase_item_id
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'qty_initial'    => 'decimal:4',
        'qty_remaining'  => 'decimal:4',
        'unit_cost'      => 'decimal:4',
        'received_at'    => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $model) {
            if (!$model->id) {
                $model->id = (string) Str::uuid();
            }
        });
    }

    // ========== RELATIONS ==========
    public function material()
    {
        return $this->belongsTo(Material::class, 'material_id');
    }

    public function purchase()
    {
        return $this->belongsTo(Purchase::class, 'purchase_id');
    }

    public function purchaseMaterial()
    {
        return $this->belongsTo(PurchaseMaterial::class, 'purchase_material_id');
    }
}
