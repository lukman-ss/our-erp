<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_materials', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('product_id');
            $table->uuid('material_id');
            $table->decimal('qty', 16, 4)->default(0);
            $table->decimal('cost_price', 16, 2)->default(0); // add cost price for this material usage
            $table->timestamps();

            $table->unique(['product_id', 'material_id']);

            $table->foreign('product_id')
                  ->references('id')->on('products')
                  ->onUpdate('cascade')->onDelete('cascade');

            $table->foreign('material_id')
                  ->references('id')->on('materials')
                  ->onUpdate('cascade')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_materials');
    }
};
