<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class FixAllOrdersTableDefaultValues extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Fix all problematic columns with appropriate defaults
        DB::statement("ALTER TABLE `orders` MODIFY COLUMN `first_name` VARCHAR(255) DEFAULT 'Guest'");
        DB::statement("ALTER TABLE `orders` MODIFY COLUMN `last_name` VARCHAR(255) DEFAULT ''");
        DB::statement("ALTER TABLE `orders` MODIFY COLUMN `country` VARCHAR(255) DEFAULT 'Bangladesh'");
        DB::statement("ALTER TABLE `orders` MODIFY COLUMN `address` VARCHAR(255) DEFAULT ''");
        DB::statement("ALTER TABLE `orders` MODIFY COLUMN `town` VARCHAR(255) DEFAULT ''");
        DB::statement("ALTER TABLE `orders` MODIFY COLUMN `district` VARCHAR(255) DEFAULT ''");
        DB::statement("ALTER TABLE `orders` MODIFY COLUMN `thana` VARCHAR(255) DEFAULT ''");
        DB::statement("ALTER TABLE `orders` MODIFY COLUMN `post_code` VARCHAR(255) DEFAULT ''");
        DB::statement("ALTER TABLE `orders` MODIFY COLUMN `phone` VARCHAR(255) DEFAULT ''");
        DB::statement("ALTER TABLE `orders` MODIFY COLUMN `shipping_method` VARCHAR(255) DEFAULT 'standard'");
        DB::statement("ALTER TABLE `orders` MODIFY COLUMN `payment_method` VARCHAR(255) DEFAULT 'cod'");
        DB::statement("ALTER TABLE `orders` MODIFY COLUMN `subtotal` DECIMAL(20,2) DEFAULT 0.00");
        DB::statement("ALTER TABLE `orders` MODIFY COLUMN `discount` DECIMAL(20,2) DEFAULT 0.00");
        DB::statement("ALTER TABLE `orders` MODIFY COLUMN `total` DECIMAL(20,2) DEFAULT 0.00");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Revert back to original state (nullable where possible)
        DB::statement("ALTER TABLE `orders` MODIFY COLUMN `first_name` VARCHAR(255) DEFAULT NULL");
        DB::statement("ALTER TABLE `orders` MODIFY COLUMN `last_name` VARCHAR(255)");
        DB::statement("ALTER TABLE `orders` MODIFY COLUMN `country` VARCHAR(255)");
        DB::statement("ALTER TABLE `orders` MODIFY COLUMN `address` VARCHAR(255)");
        DB::statement("ALTER TABLE `orders` MODIFY COLUMN `town` VARCHAR(255)");
        DB::statement("ALTER TABLE `orders` MODIFY COLUMN `district` VARCHAR(255)");
        DB::statement("ALTER TABLE `orders` MODIFY COLUMN `thana` VARCHAR(255)");
        DB::statement("ALTER TABLE `orders` MODIFY COLUMN `post_code` VARCHAR(255)");
        DB::statement("ALTER TABLE `orders` MODIFY COLUMN `phone` VARCHAR(255)");
        DB::statement("ALTER TABLE `orders` MODIFY COLUMN `shipping_method` VARCHAR(255)");
        DB::statement("ALTER TABLE `orders` MODIFY COLUMN `payment_method` VARCHAR(255)");
        DB::statement("ALTER TABLE `orders` MODIFY COLUMN `subtotal` DECIMAL(20,2)");
        DB::statement("ALTER TABLE `orders` MODIFY COLUMN `discount` DECIMAL(20,2)");
        DB::statement("ALTER TABLE `orders` MODIFY COLUMN `total` DECIMAL(20,2)");
    }
}