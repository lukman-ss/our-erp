<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class MaterialMove extends Model
{
    use HasFactory;

    protected $table = 'material_moves';
    public $incrementing = false;
    protected $keyType = 'string';

    // Enum-like constants for direction
    public const DIR_IN  = 'in';
    public const DIR_OUT = 'out';

    /**
     * Mass-assignable fields (full fillable)
     */
    protected $fillable = [
        'id',
        'material_id',
        'batch_id',
        'direction',
        'qty',
        'unit_cost',
        'moved_at',
        'ref_type',
        'ref_id',
        'created_at',
        'updated_at',
    ];

    /**
     * Attribute casting
     *
     * Note: Laravel returns decimal casts as strings (preserves precision).
     */
    protected $casts = [
        'qty'       => 'decimal:4',
        'unit_cost' => 'decimal:4',
        'moved_at'  => 'datetime',
    ];

    /**
     * Auto-UUID on create
     */
    protected static function booted(): void
    {
        static::creating(function (self $model) {
            if (! $model->getKey()) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
        });
    }

    // =======================
    // Relationships
    // =======================

    /**
     * The material moved.
     */
    public function material()
    {
        return $this->belongsTo(Material::class, 'material_id');
    }

    /**
     * The batch involved (nullable, set null on delete).
     */
    public function batch()
    {
        return $this->belongsTo(MaterialBatch::class, 'batch_id');
    }

    /**
     * Polymorphic reference (e.g., Production, Purchase, Adjustment, etc.)
     * Uses ref_type + ref_id.
     */
    public function reference()
    {
        // Explicitly pass column names to match migration (not the default "reference_type/id" pair name)
        return $this->morphTo(__FUNCTION__, 'ref_type', 'ref_id');
    }

    // =======================
    // Query Scopes
    // =======================

    /**
     * Scope: only inbound moves.
     */
    public function scopeInbound($query)
    {
        return $query->where('direction', self::DIR_IN);
    }

    /**
     * Scope: only outbound moves.
     */
    public function scopeOutbound($query)
    {
        return $query->where('direction', self::DIR_OUT);
    }

    /**
     * Scope: by material.
     */
    public function scopeForMaterial($query, string $materialId)
    {
        return $query->where('material_id', $materialId);
    }

    /**
     * Scope: by batch.
     */
    public function scopeForBatch($query, string $batchId)
    {
        return $query->where('batch_id', $batchId);
    }

    /**
     * Scope: by polymorphic reference.
     *
     * @param  string  $type  Fully-qualified class name or your own alias used in ref_type
     * @param  string  $id    UUID of the referenced record
     */
    public function scopeForReference($query, string $type, string $id)
    {
        return $query->where('ref_type', $type)->where('ref_id', $id);
    }

    /**
     * Scope: between moved_at dates (inclusive).
     */
    public function scopeMovedBetween($query, $from, $to)
    {
        return $query->whereBetween('moved_at', [$from, $to]);
    }
}
