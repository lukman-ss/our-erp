<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('sale_products', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('sale_id')->index();
            $table->uuid('product_id')->index();
            $table->decimal('qty', 16, 4);
            $table->decimal('unit_price', 16, 2);
            // Discounts live on LINE:
            $table->decimal('discount_percentage', 5, 2)->default(0); // %
            $table->decimal('discount_amount', 16, 2)->default(0);    // nominal (rupiah)
            // Computed final line total (gross - discounts):
            $table->decimal('line_total', 16, 2);
            $table->timestamps();

            $table->foreign('sale_id')->references('id')->on('sales')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sale_products');
    }
};
