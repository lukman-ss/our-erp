<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class ProductionMaterial extends Model
{
    use HasFactory;

    protected $table = 'production_materials';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'production_id',
        'material_id',
        'qty_required',
        'qty_issued',
        'unit_cost',
        'total_cost',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'qty_required' => 'decimal:4',
        'qty_issued'   => 'decimal:4',
        'unit_cost'    => 'decimal:4',
        'total_cost'   => 'decimal:2',
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

    public function material()
    {
        return $this->belongsTo(Material::class, 'material_id');
    }
}
