<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class FixOrdersTableFirstNameColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Use raw SQL to modify the column - no doctrine/dbal required
        DB::statement("ALTER TABLE `orders` MODIFY COLUMN `first_name` VARCHAR(255) DEFAULT 'Guest'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Revert back to nullable without default
        DB::statement("ALTER TABLE `orders` MODIFY COLUMN `first_name` VARCHAR(255) DEFAULT NULL");
    }
}