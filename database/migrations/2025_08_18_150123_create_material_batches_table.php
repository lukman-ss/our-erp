<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('material_batches', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('material_id')->index();
            $table->decimal('qty_initial', 16, 4);
            $table->decimal('qty_remaining', 16, 4);
            $table->decimal('unit_cost', 16, 4);
            $table->timestamp('received_at')->index();
            $table->uuid('purchase_id')->nullable()->index();
            $table->uuid('purchase_item_id')->nullable()->index();
            $table->timestamps();

            $table->foreign('material_id')->references('id')->on('materials')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('material_batches');
    }
};
