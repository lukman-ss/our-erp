<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('sales', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('code')->unique()->index();
            $table->uuid('customer_id')->nullable()->index();
            $table->date('sale_date');
            $table->decimal('total_amount', 16, 2)->default(0); // SALES ONLY TOTAL
            $table->string('status')->default('draft'); // draft|confirmed|paid|cancelled
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};
