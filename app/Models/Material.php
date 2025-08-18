<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Material extends Model
{
    use HasFactory;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'code',
        'name',
        'unit',
        'cost_price',
        'stock',
        'description',
    ];

    protected static function booted()
    {
        static::creating(function ($model) {
            if (! $model->getKey()) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
        });
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_materials')
            ->withPivot('id','qty','cost_price')
            ->withTimestamps();
    }

    public function purchaseMaterials()
    {
        return $this->hasMany(PurchaseMaterial::class, 'material_id');
    }
    
    public function batches()
    {
        return $this->hasMany(MaterialBatch::class, 'material_id');
    }

}
