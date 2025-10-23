<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class miniCategory extends Model
{
    use HasFactory;
    
    protected $guarded = ['id'];
    
    /**
     * Get all products associated with this mini category
     */
    public function products()
    {
        return $this->belongsToMany(Product::class);
    }
    
    /**
     * Get the sub category that owns this mini category
     * 
     * IMPORTANT: Based on migration 2025_10_22_120617, the column in mini_categories
     * was renamed from 'category_id' to 'sub_category_id'
     * 
     * Foreign key: sub_category_id (in mini_categories table)
     */
    public function subCategory()
    {
        return $this->belongsTo(SubCategory::class, 'sub_category_id');
    }
    
    /**
     * Get all extra mini categories under this mini category
     */
    public function extraCategory()
    {
        return $this->hasMany(ExtraMiniCategory::class, 'mini_category_id');
    }
}