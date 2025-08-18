<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('productions', function (Blueprint $table) {
            $table->uuid('id')->primary();                   // char(36)
            $table->string('code')->unique();                // WO/Production code
            $table->uuid('product_id')->index();             // products.id (main finished good)
            $table->decimal('qty_planned', 16, 4);
            $table->decimal('qty_produced', 16, 4)->default(0);
            $table->string('status', 50)->default('draft');  // draft|in_progress|done|cancelled
            $table->timestamp('scheduled_at')->nullable()->index();
            $table->timestamp('started_at')->nullable()->index();
            $table->timestamp('finished_at')->nullable()->index();
            $table->text('notes')->nullable();
            $table->timestamps();

            // FKs
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('productions');
    }
};
