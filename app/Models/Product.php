<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'sku',
        'name',
        'unit',
        'sell_price',
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

    public function materials()
    {
        return $this->belongsToMany(Material::class, 'product_materials')
            ->withPivot('id','qty','cost_price') // include pivot cost if table has it
            ->withTimestamps();
    }
}
