<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Drop foreign keys from products table first if they exist
        if (Schema::hasTable('products') && Schema::hasColumn('products', 'author_id')) {
            Schema::table('products', function (Blueprint $table) {
                // Try to drop foreign key if it exists
                try {
                    $table->dropForeign(['author_id']);
                } catch (\Exception $e) {
                    // Foreign key might not exist, continue
                }
            });
        }
        
        // Now drop the authors table
        Schema::dropIfExists('authors');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::create('authors', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('image')->nullable();
            $table->boolean('status')->default(true);
            $table->timestamps();
        });
        
        // Recreate foreign key in products table
        if (Schema::hasTable('products') && Schema::hasColumn('products', 'author_id')) {
            Schema::table('products', function (Blueprint $table) {
                $table->foreign('author_id')
                    ->references('id')
                    ->on('authors')
                    ->onDelete('set null');
            });
        }
    }
};