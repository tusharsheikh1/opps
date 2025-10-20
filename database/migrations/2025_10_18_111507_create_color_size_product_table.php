<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateColorSizeProductTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Check if the table already exists before creating it
        if (!Schema::hasTable('color_size_product')) {
            Schema::create('color_size_product', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('product_id');
                $table->unsignedBigInteger('color_id');
                $table->unsignedBigInteger('size_id');
                $table->integer('quantity')->default(0);
                $table->decimal('price', 10, 2)->default(0.00);
                $table->timestamps();

                // Optional: Add foreign key constraints if needed
                // $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
                // $table->foreign('color_id')->references('id')->on('colors')->onDelete('cascade');
                // $table->foreign('size_id')->references('id')->on('sizes')->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // The dropIfExists method already checks if the table exists
        Schema::dropIfExists('color_size_product');
    }
}