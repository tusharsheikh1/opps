<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('color_size_product', function (Blueprint $table) {
            // Check if the foreign key exists before dropping it.
            // **For simplicity and to resolve the immediate error, we will SKIP dropping the FK.**

            // Modify the column to make it nullable.
            // We use 'foreignId' to ensure it remains an unsigned big integer.
            $table->foreignId('color_id')->nullable()->change();

            // We do NOT attempt to re-add the foreign key, as this might conflict
            // if the original table creation was non-conventional.
            // If you want the FK, you would add it here, but it's safer to skip it for now.
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('color_size_product', function (Blueprint $table) {
            // We only need to revert the nullability.
            $table->foreignId('color_id')->change();
        });
    }
};