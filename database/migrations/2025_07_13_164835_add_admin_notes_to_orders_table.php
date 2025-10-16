<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAdminNotesToOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            // Check if admin_notes column doesn't exist before adding
            if (!Schema::hasColumn('orders', 'admin_notes')) {
                // Add admin_notes after meet_time if it exists, otherwise add at end
                if (Schema::hasColumn('orders', 'meet_time')) {
                    $table->text('admin_notes')->nullable()->after('meet_time');
                } else {
                    $table->text('admin_notes')->nullable();
                }
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
        Schema::table('orders', function (Blueprint $table) {
            if (Schema::hasColumn('orders', 'admin_notes')) {
                $table->dropColumn('admin_notes');
            }
        });
    }
}