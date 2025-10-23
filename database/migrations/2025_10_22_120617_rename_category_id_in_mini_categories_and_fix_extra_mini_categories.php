<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // First, let's check what foreign keys actually exist
        $foreignKeys = $this->getForeignKeys('mini_categories');
        
        // Drop any existing foreign key on category_id column
        if (!empty($foreignKeys)) {
            Schema::table('mini_categories', function (Blueprint $table) use ($foreignKeys) {
                foreach ($foreignKeys as $fk) {
                    if ($fk['column'] === 'category_id') {
                        try {
                            $table->dropForeign($fk['name']);
                        } catch (\Exception $e) {
                            // Foreign key might not exist, continue
                        }
                    }
                }
            });
        }
        
        // Now rename the column
        Schema::table('mini_categories', function (Blueprint $table) {
            // Check if column exists before renaming
            if (Schema::hasColumn('mini_categories', 'category_id')) {
                $table->renameColumn('category_id', 'sub_category_id');
            }
        });
        
        // Add the new foreign key constraint
        Schema::table('mini_categories', function (Blueprint $table) {
            if (Schema::hasColumn('mini_categories', 'sub_category_id')) {
                $table->foreign('sub_category_id')
                      ->references('id')
                      ->on('sub_categories')
                      ->onDelete('cascade')
                      ->onUpdate('cascade');
            }
        });
        
        // Fix extra_mini_categories table if needed
        $extraForeignKeys = $this->getForeignKeys('extra_mini_categories');
        
        // Check if mini_category_id exists and has foreign key
        if (Schema::hasTable('extra_mini_categories')) {
            if (!Schema::hasColumn('extra_mini_categories', 'mini_category_id')) {
                // Add the column if it doesn't exist
                Schema::table('extra_mini_categories', function (Blueprint $table) {
                    $table->unsignedBigInteger('mini_category_id')->after('id');
                });
            }
            
            // Check if foreign key exists
            $hasForeignKey = false;
            foreach ($extraForeignKeys as $fk) {
                if ($fk['column'] === 'mini_category_id') {
                    $hasForeignKey = true;
                    break;
                }
            }
            
            // Add foreign key if it doesn't exist
            if (!$hasForeignKey) {
                Schema::table('extra_mini_categories', function (Blueprint $table) {
                    $table->foreign('mini_category_id')
                          ->references('id')
                          ->on('mini_categories')
                          ->onDelete('cascade')
                          ->onUpdate('cascade');
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Remove foreign key from mini_categories
        Schema::table('mini_categories', function (Blueprint $table) {
            try {
                $table->dropForeign(['sub_category_id']);
            } catch (\Exception $e) {
                // Foreign key might not exist
            }
        });
        
        // Rename column back
        if (Schema::hasColumn('mini_categories', 'sub_category_id')) {
            Schema::table('mini_categories', function (Blueprint $table) {
                $table->renameColumn('sub_category_id', 'category_id');
            });
        }
        
        // Add back the old foreign key
        Schema::table('mini_categories', function (Blueprint $table) {
            if (Schema::hasColumn('mini_categories', 'category_id')) {
                $table->foreign('category_id')
                      ->references('id')
                      ->on('categories')
                      ->onDelete('cascade');
            }
        });
    }
    
    /**
     * Get foreign keys for a table
     * 
     * @param string $table
     * @return array
     */
    private function getForeignKeys($table)
    {
        $databaseName = DB::getDatabaseName();
        
        $foreignKeys = DB::select("
            SELECT 
                CONSTRAINT_NAME as name,
                COLUMN_NAME as `column`,
                REFERENCED_TABLE_NAME as referenced_table,
                REFERENCED_COLUMN_NAME as referenced_column
            FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
            WHERE TABLE_SCHEMA = ?
            AND TABLE_NAME = ?
            AND REFERENCED_TABLE_NAME IS NOT NULL
        ", [$databaseName, $table]);
        
        return array_map(function($fk) {
            return [
                'name' => $fk->name,
                'column' => $fk->column,
                'referenced_table' => $fk->referenced_table,
                'referenced_column' => $fk->referenced_column
            ];
        }, $foreignKeys);
    }
};