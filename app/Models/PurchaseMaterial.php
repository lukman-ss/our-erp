<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class PurchaseMaterial extends Model
{
    use HasFactory;

    protected $table = 'purchase_materials';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'purchase_id',
        'material_id',
        'qty',
        'unit_cost',
        'total_cost',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'qty'        => 'decimal:4',
        'unit_cost'  => 'decimal:4',
        'total_cost' => 'decimal:2',
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
    public function purchase()
    {
        return $this->belongsTo(Purchase::class, 'purchase_id');
    }

    public function material()
    {
        return $this->belongsTo(Material::class, 'material_id');
    }

    public function batches()
    {
        return $this->hasMany(MaterialBatch::class, 'purchase_material_id');
    }
}
