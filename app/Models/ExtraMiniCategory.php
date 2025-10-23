<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExtraMiniCategory extends Model
{
    use HasFactory;
    
    protected $guarded = ['id'];
    
    /**
     * Get the mini category that owns this extra mini category
     * Foreign key: mini_category_id (in extra_mini_categories table)
     */
    public function miniCategory()
    {
        return $this->belongsTo(miniCategory::class, 'mini_category_id');
    }
    
    /**
     * Get all products associated with this extra mini category
     */
    public function products()
    {
        return $this->belongsToMany(Product::class);
    }
}