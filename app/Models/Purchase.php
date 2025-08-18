<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class Purchase extends Model
{
    use HasFactory;

    protected $table = 'purchases';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'code',
        'supplier_id',
        'purchase_date',
        'total_amount',
        'status',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'purchase_date' => 'date',
        'total_amount'  => 'decimal:2',
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
    public function materials()
    {
        return $this->hasMany(PurchaseMaterial::class, 'purchase_id');
    }

    public function materialBatches()
    {
        return $this->hasMany(MaterialBatch::class, 'purchase_id');
    }
}
