<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('ALTER TABLE `users` MODIFY `id` BIGINT UNSIGNED NOT NULL');
        DB::statement('ALTER TABLE `users` DROP PRIMARY KEY');
        DB::statement('ALTER TABLE `users` MODIFY `id` CHAR(36) NOT NULL');
        DB::statement('UPDATE `users` SET `id` = UUID()');
        DB::statement('ALTER TABLE `users` ADD PRIMARY KEY (`id`)');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE `users` DROP PRIMARY KEY');
        DB::statement('ALTER TABLE `users` MODIFY `id` BIGINT UNSIGNED NOT NULL');
        DB::statement('ALTER TABLE `users` ADD PRIMARY KEY (`id`)');
        DB::statement('ALTER TABLE `users` MODIFY `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT');
    }
};
