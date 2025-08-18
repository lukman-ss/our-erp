<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('production_materials', function (Blueprint $table) {
            $table->uuid('id')->primary();             // char(36)
            $table->uuid('production_id')->index();
            $table->uuid('material_id')->index();      // materials.id
            $table->decimal('qty_required', 16, 4);    // sesuai BOM
            $table->decimal('qty_issued', 16, 4)->default(0); // aktual dikeluarkan
            $table->decimal('unit_cost', 16, 4)->nullable();
            $table->decimal('total_cost', 16, 2)->nullable();
            $table->timestamps();

            $table->foreign('production_id')->references('id')->on('productions')->onDelete('cascade');
            $table->foreign('material_id')->references('id')->on('materials')->onDelete('cascade');

            // Helps when summarizing/validating consumption vs requirement
            $table->index(['production_id', 'material_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('production_materials');
    }
};
