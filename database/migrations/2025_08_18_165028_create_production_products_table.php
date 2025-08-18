<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('production_products', function (Blueprint $table) {
            $table->uuid('id')->primary();             // char(36)
            $table->uuid('production_id')->index();
            $table->uuid('product_id')->index();       // products.id
            $table->decimal('qty', 16, 4);
            $table->decimal('unit_cost', 16, 4)->nullable();
            $table->decimal('total_cost', 16, 2)->nullable();
            $table->timestamp('produced_at')->nullable()->index();
            $table->timestamps();

            $table->foreign('production_id')->references('id')->on('productions')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');

            // If you often query by (production_id, product_id)
            $table->index(['production_id', 'product_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('production_products');
    }
};
