<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductImage extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    /**
     * Get the product that owns the image.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the color associated with this image.
     */
    public function color()
    {
        return $this->belongsTo(Color::class, 'color_attri');
    }

    /**
     * Check if this image is tied to a specific color
     */
    public function hasColor()
    {
        return !is_null($this->color_attri);
    }

    /**
     * Scope to get only general images (not tied to colors)
     */
    public function scopeGeneral($query)
    {
        return $query->whereNull('color_attri');
    }

    /**
     * Scope to get images for a specific color
     */
    public function scopeForColor($query, $colorId)
    {
        return $query->where('color_attri', $colorId);
    }
}