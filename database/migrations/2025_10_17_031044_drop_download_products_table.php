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
        // Drop foreign keys first if they exist
        if (Schema::hasTable('download_products')) {
            Schema::table('download_products', function (Blueprint $table) {
                $sm = Schema::getConnection()->getDoctrineSchemaManager();
                $doctrineTable = $sm->listTableDetails('download_products');
                
                // Drop foreign keys
                foreach ($doctrineTable->getForeignKeys() as $foreignKey) {
                    $table->dropForeign($foreignKey->getName());
                }
            });
        }
        
        // Now drop the table
        Schema::dropIfExists('download_products');
        
        // Also drop the pivot table if it exists
        Schema::dropIfExists('product_user_downloads');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::create('download_products', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id');
            $table->string('name');
            $table->string('file')->nullable();
            $table->string('url')->nullable();
            $table->timestamps();
            
            $table->foreign('product_id')
                ->references('id')
                ->on('products')
                ->onDelete('cascade');
        });
        
        Schema::create('product_user_downloads', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('user_id');
            $table->integer('download_count')->default(0);
            $table->timestamps();
            
            $table->foreign('product_id')
                ->references('id')
                ->on('products')
                ->onDelete('cascade');
                
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
        });
    }
};