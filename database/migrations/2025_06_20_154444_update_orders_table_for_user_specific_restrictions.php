<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateOrdersTableForUserSpecificRestrictions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            // Ensure all tracking fields exist
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
        
        // Add optimized indexes for user-specific restriction queries
        Schema::table('orders', function (Blueprint $table) {
            try {
                // Primary index for user-specific restrictions (phone + created_at)
                $table->index(['phone', 'created_at', 'user_id'], 'idx_orders_user_restriction');
                
                // Index for guest orders
                $table->index(['user_id', 'created_at'], 'idx_orders_guest_time');
                
                // Index for abuse detection (IP + created_at)
                $table->index(['ip_address', 'created_at'], 'idx_orders_ip_abuse');
                
                // Index for fingerprint abuse detection
                $table->index(['browser_fingerprint', 'created_at'], 'idx_orders_fingerprint_abuse');
                
                // Composite index for phone restrictions
                $table->index(['phone', 'user_id', 'created_at'], 'idx_orders_phone_restriction');
                
            } catch (\Exception $e) {
                // Indexes might already exist, that's okay
                \Log::info('Some indexes might already exist during user-specific migration: ' . $e->getMessage());
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
                $table->dropIndex('idx_orders_user_restriction');
                $table->dropIndex('idx_orders_guest_time');
                $table->dropIndex('idx_orders_ip_abuse');
                $table->dropIndex('idx_orders_fingerprint_abuse');
                $table->dropIndex('idx_orders_phone_restriction');
            } catch (\Exception $e) {
                // Indexes might not exist
            }
        });
    }
}