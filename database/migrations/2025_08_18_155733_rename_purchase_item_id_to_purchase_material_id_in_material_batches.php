<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('material_batches', function (Blueprint $table) {
            // Rename kolom
            $table->renameColumn('purchase_item_id', 'purchase_material_id');
        });
    }

    public function down(): void
    {
        Schema::table('material_batches', function (Blueprint $table) {
            // Balikin lagi kalau rollback
            $table->renameColumn('purchase_material_id', 'purchase_item_id');
        });
    }
};
