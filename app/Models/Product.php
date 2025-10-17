<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    /**
     * Get the user that owns the product.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    /**
     * Get the images for the product.
     */
    public function images()
    {
        return $this->hasMany(ProductImage::class);
    }

    /**
     * Get the brand that owns the product.
     */
    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    /**
     * Get all of the tags for the product.
     */
    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }

    /**
     * Get all of the categories for the product.
     */
    public function categories()
    {
        return $this->belongsToMany(Category::class);
    }
    
    /**
     * Get all of the active categories for the product.
     */
    public function categories2()
    {
        return $this->belongsToMany(Category::class)->where('status', 1);
    }

    /**
     * Get all of the sub_categories for the product.
     */
    public function sub_categories()
    {
        return $this->belongsToMany(SubCategory::class);
    }
    
    /**
     * Get all of the mini_categories for the product.
     */
    public function mini_categories()
    {
        return $this->belongsToMany(miniCategory::class);
    }
    
    /**
     * Get all of the extra_categories for the product.
     */
    public function extra_categories()
    {
        return $this->belongsToMany(ExtraMiniCategory::class);
    }
    
    /**
     * The campaigns that belong to the product.
     */
    public function campaigns()
    {
        return $this->belongsToMany(Campaign::class,'campaing_products');
    }
    
    /**
     * Get all of the sizes for the product.
     */
    public function sizes()
    {
        return $this->belongsToMany(Size::class)->withPivot('qnty', 'price');
    }

    /**
     * Get all of the colors for the product.
     */
    public function colors()
    {
        return $this->belongsToMany(Color::class)->withPivot('qnty', 'price');
    }
    
    /**
     * Get all of the attribute values for the product.
     */
    public function attributes_values()
    {
        return $this->belongsToMany(AttributeValue::class, 'attribute_product')
                    ->withPivot('qnty', 'price')
                    ->with('attribute'); // Eager load the parent attribute
    }

    /**
     * Get the order details for the product.
     */
    public function orderDetails()
    {
        return $this->hasMany(OrderDetails::class);
    }
    
    /**
     * Get the campaign product entries for the product.
     */
    public function campaingProduct()
    {
        return $this->hasMany(CampaingProduct::class);
    }

    /**
     * Get the comments for the product.
     */
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * Get the reviews for the product.
     */
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    /**
     * Get total available stock across all variations
     */
    public function getTotalStockAttribute()
    {
        $colorStock = $this->colors()->sum('qnty');
        $attributeStock = $this->attributes_values()->sum('qnty');
        
        return $colorStock + $attributeStock;
    }

    /**
     * Get stock for a specific color
     */
    public function getColorStock($colorId)
    {
        $color = $this->colors()->where('color_id', $colorId)->first();
        return $color ? $color->pivot->qnty : 0;
    }

    /**
     * Get stock for a specific attribute value
     */
    public function getAttributeStock($attributeValueId)
    {
        $attribute = $this->attributes_values()->where('attribute_value_id', $attributeValueId)->first();
        return $attribute ? $attribute->pivot->qnty : 0;
    }

    /**
     * Get images for a specific color
     */
    public function getColorImages($colorId)
    {
        return $this->images()->where('color_attri', $colorId)->get();
    }

    /**
     * Get all general images (not tied to any color)
     */
    public function getGeneralImages()
    {
        return $this->images()->whereNull('color_attri')->get();
    }

    /**
     * Check if product has variations
     */
    public function hasVariations()
    {
        return $this->colors()->count() > 0 || $this->attributes_values()->count() > 0;
    }

    /**
     * Get all variations with stock info
     */
    public function getVariationsWithStock()
    {
        $variations = [];

        // Add color variations
        foreach($this->colors as $color) {
            $variations[] = [
                'type' => 'color',
                'id' => $color->id,
                'name' => $color->name,
                'code' => $color->code,
                'stock' => $color->pivot->qnty,
                'price' => $color->pivot->price,
                'images' => $this->getColorImages($color->id)
            ];
        }

        // Add attribute variations
        foreach($this->attributes_values as $attr) {
            $variations[] = [
                'type' => 'attribute',
                'id' => $attr->id,
                'name' => $attr->name,
                'attribute_name' => $attr->attribute->name ?? 'N/A',
                'stock' => $attr->pivot->qnty,
                'price' => $attr->pivot->price,
            ];
        }

        return $variations;
    }
}