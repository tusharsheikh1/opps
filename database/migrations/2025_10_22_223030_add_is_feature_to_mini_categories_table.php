<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsFeatureToMiniCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mini_categories', function (Blueprint $table) {
            // Check if column doesn't exist before adding
            if (!Schema::hasColumn('mini_categories', 'is_feature')) {
                $table->boolean('is_feature')->default(false)->after('status');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('mini_categories', function (Blueprint $table) {
            if (Schema::hasColumn('mini_categories', 'is_feature')) {
                $table->dropColumn('is_feature');
            }
        });
    }
}