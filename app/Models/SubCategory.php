<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubCategory extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    /**
     * Get all of the products that are assigned this sub category.
     */
    public function products()
    {
        return $this->belongsToMany(Product::class);
    }

    /**
     * Get the category that owns the sub category.
     * Foreign key: category_id (in sub_categories table)
     */
    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }
    
    /**
     * Get all mini categories under this sub category
     * 
     * IMPORTANT: Based on migration 2025_10_22_120617, the column in mini_categories
     * was renamed from 'category_id' to 'sub_category_id'
     * 
     * Foreign key: sub_category_id (in mini_categories table)
     */
    public function miniCategory()
    {
        return $this->hasMany(miniCategory::class, 'sub_category_id');
    }
}