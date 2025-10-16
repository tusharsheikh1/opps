<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CheckAndAddMissingOrderFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            // Add ip_address first if it doesn't exist
            if (!Schema::hasColumn('orders', 'ip_address')) {
                $table->string('ip_address', 45)->nullable()->after('meet_time');
            }
            
            // Add missing columns only
            if (!Schema::hasColumn('orders', 'ip_subnet')) {
                $table->string('ip_subnet')->nullable()->after('ip_address');
            }
            
            if (!Schema::hasColumn('orders', 'user_agent')) {
                $table->text('user_agent')->nullable()->after('ip_subnet');
            }
            
            if (!Schema::hasColumn('orders', 'browser_fingerprint')) {
                $table->string('browser_fingerprint')->nullable()->after('user_agent');
            }
            
            if (!Schema::hasColumn('orders', 'device_info')) {
                $table->json('device_info')->nullable()->after('browser_fingerprint');
            }
            
            if (!Schema::hasColumn('orders', 'order_placed_at')) {
                $table->timestamp('order_placed_at')->nullable()->after('device_info');
            }
        });
        
        // Add indexes for performance (only if they don't exist)
        try {
            Schema::table('orders', function (Blueprint $table) {
                $table->index(['ip_address', 'created_at'], 'orders_ip_created_idx');
                $table->index(['browser_fingerprint', 'created_at'], 'orders_fingerprint_created_idx');
                $table->index(['phone', 'created_at'], 'orders_phone_created_idx');
                $table->index(['email', 'created_at'], 'orders_email_created_idx');
                $table->index(['ip_subnet', 'created_at'], 'orders_subnet_created_idx');
            });
        } catch (\Exception $e) {
            // Indexes might already exist, that's okay
            echo "Note: Some indexes might already exist - " . $e->getMessage() . "\n";
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            // Drop indexes first
            try {
                $table->dropIndex('orders_ip_created_idx');
                $table->dropIndex('orders_fingerprint_created_idx');
                $table->dropIndex('orders_phone_created_idx');
                $table->dropIndex('orders_email_created_idx');
                $table->dropIndex('orders_subnet_created_idx');
            } catch (\Exception $e) {
                // Indexes might not exist, that's okay
            }
            
            // Drop the columns we added
            if (Schema::hasColumn('orders', 'order_placed_at')) {
                $table->dropColumn('order_placed_at');
            }
            
            if (Schema::hasColumn('orders', 'device_info')) {
                $table->dropColumn('device_info');
            }
            
            if (Schema::hasColumn('orders', 'browser_fingerprint')) {
                $table->dropColumn('browser_fingerprint');
            }
            
            if (Schema::hasColumn('orders', 'user_agent')) {
                $table->dropColumn('user_agent');
            }
            
            if (Schema::hasColumn('orders', 'ip_subnet')) {
                $table->dropColumn('ip_subnet');
            }
            
            if (Schema::hasColumn('orders', 'ip_address')) {
                $table->dropColumn('ip_address');
            }
        });
    }
}