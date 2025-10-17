<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddColorSupportToProductImages extends Migration
{
    public function up()
    {
        // Step 1: Add the column if it doesn't exist
        if (!Schema::hasColumn('product_images', 'color_attri')) {
            DB::statement('ALTER TABLE product_images ADD color_attri BIGINT UNSIGNED NULL AFTER name');
        } else {
            // Make sure existing column is correct type
            DB::statement('ALTER TABLE product_images MODIFY color_attri BIGINT UNSIGNED NULL');
        }

        // Step 2: Add index
        $indexes = DB::select("SHOW INDEXES FROM product_images WHERE Key_name = 'product_images_product_id_color_attri_index'");
        if (empty($indexes)) {
            DB::statement('CREATE INDEX product_images_product_id_color_attri_index ON product_images (product_id, color_attri)');
        }

        // Step 3: Add foreign key
        // First check if foreign key already exists
        $foreignKeys = DB::select("
            SELECT CONSTRAINT_NAME 
            FROM information_schema.KEY_COLUMN_USAGE 
            WHERE TABLE_SCHEMA = DATABASE() 
            AND TABLE_NAME = 'product_images' 
            AND COLUMN_NAME = 'color_attri' 
            AND REFERENCED_TABLE_NAME IS NOT NULL
        ");

        if (empty($foreignKeys)) {
            DB::statement('
                ALTER TABLE product_images 
                ADD CONSTRAINT product_images_color_attri_foreign 
                FOREIGN KEY (color_attri) 
                REFERENCES colors(id) 
                ON DELETE SET NULL
            ');
        }
    }

    public function down()
    {
        // Drop foreign key
        DB::statement('ALTER TABLE product_images DROP FOREIGN KEY product_images_color_attri_foreign');
        
        // Drop index
        DB::statement('DROP INDEX product_images_product_id_color_attri_index ON product_images');
    }
}