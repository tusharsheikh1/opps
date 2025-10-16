<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOrderTrackingFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            // Add the missing columns after existing ones
            if (!Schema::hasColumn('orders', 'ip_address')) {
                $table->string('ip_address', 45)->nullable()->after('meet_time');
            }
            
            if (!Schema::hasColumn('orders', 'ip_subnet')) {
                $table->string('ip_subnet', 45)->nullable()->after('ip_address');
            }
            
            if (!Schema::hasColumn('orders', 'browser_fingerprint')) {
                $table->string('browser_fingerprint')->nullable()->after('ip_subnet');
            }
            
            if (!Schema::hasColumn('orders', 'user_agent')) {
                $table->text('user_agent')->nullable()->after('browser_fingerprint');
            }
            
            if (!Schema::hasColumn('orders', 'device_info')) {
                $table->json('device_info')->nullable()->after('user_agent');
            }
            
            if (!Schema::hasColumn('orders', 'order_placed_at')) {
                $table->timestamp('order_placed_at')->nullable()->after('device_info');
            }
        });
        
        // Add indexes for better performance
        Schema::table('orders', function (Blueprint $table) {
            try {
                $table->index(['ip_address', 'created_at'], 'idx_orders_ip_created');
                $table->index(['phone', 'created_at'], 'idx_orders_phone_created');
                $table->index(['browser_fingerprint', 'created_at'], 'idx_orders_fingerprint_created');
                $table->index(['ip_subnet', 'created_at'], 'idx_orders_subnet_created');
                $table->index(['email', 'created_at'], 'idx_orders_email_created');
            } catch (\Exception $e) {
                // Indexes might already exist
                \Log::info('Some indexes might already exist: ' . $e->getMessage());
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
            // Drop indexes first
            try {
                $table->dropIndex('idx_orders_ip_created');
                $table->dropIndex('idx_orders_phone_created');
                $table->dropIndex('idx_orders_fingerprint_created');
                $table->dropIndex('idx_orders_subnet_created');
                $table->dropIndex('idx_orders_email_created');
            } catch (\Exception $e) {
                // Indexes might not exist
            }
            
            // Drop columns
            $table->dropColumn([
                'order_placed_at',
                'device_info', 
                'user_agent',
                'browser_fingerprint',
                'ip_subnet',
                'ip_address'
            ]);
        });
    }
}