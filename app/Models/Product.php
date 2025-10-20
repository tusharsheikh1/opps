<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

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
     * Get color-size stock combinations for this product
     */
    public function colorSizeStock()
    {
        return DB::table('color_size_product')
            ->select('color_size_product.*', 'colors.name as color_name', 'colors.code as color_code', 'sizes.name as size_name')
            ->leftJoin('colors', 'colors.id', '=', 'color_size_product.color_id')
            ->join('sizes', 'sizes.id', '=', 'color_size_product.size_id')
            ->where('color_size_product.product_id', $this->id)
            ->get();
    }
    
    /**
     * Get the stock and pricing data for size-only variations (where color_id is NULL).
     */
    public function sizeOnlyStock()
    {
        return DB::table('color_size_product')
            ->join('sizes', 'color_size_product.size_id', '=', 'sizes.id')
            ->where('color_size_product.product_id', $this->id)
            ->whereNull('color_size_product.color_id')
            ->select(
                'sizes.id as size_id',
                'sizes.name as size_name',
                'color_size_product.quantity as stock',
                'color_size_product.price as price'
            )
            ->get();
    }

    /**
     * Get all unique colors that have stock
     */
    public function getAvailableColors()
    {
        return DB::table('color_size_product')
            ->select('colors.id', 'colors.name', 'colors.code', 'colors.slug')
            ->join('colors', 'colors.id', '=', 'color_size_product.color_id')
            ->where('color_size_product.product_id', $this->id)
            ->whereNotNull('color_size_product.color_id') // Exclude size-only
            ->where('color_size_product.quantity', '>', 0)
            ->groupBy('colors.id', 'colors.name', 'colors.code', 'colors.slug')
            ->get();
    }

    /**
     * Get all unique sizes that have stock
     */
    public function getAvailableSizes()
    {
        return DB::table('color_size_product')
            ->select('sizes.id', 'sizes.name', 'sizes.slug')
            ->join('sizes', 'sizes.id', '=', 'color_size_product.size_id')
            ->where('color_size_product.product_id', $this->id)
            ->where('color_size_product.quantity', '>', 0)
            ->groupBy('sizes.id', 'sizes.name', 'sizes.slug')
            ->get();
    }

    /**
     * Get available sizes for a specific color
     */
    public function getSizesForColor($colorId)
    {
        return DB::table('color_size_product')
            ->select('sizes.id', 'sizes.name', 'sizes.slug', 'color_size_product.quantity', 'color_size_product.price')
            ->join('sizes', 'sizes.id', '=', 'color_size_product.size_id')
            ->where('color_size_product.product_id', $this->id)
            ->where('color_size_product.color_id', $colorId)
            ->whereNotNull('color_size_product.color_id') // Ensure it's not a size-only record
            ->where('color_size_product.quantity', '>', 0)
            ->get();
    }

    /**
     * Get stock for a specific color and size combination
     */
    public function getColorSizeStock($colorId, $sizeId)
    {
        $result = DB::table('color_size_product')
            ->where('product_id', $this->id)
            ->where('color_id', $colorId)
            ->where('size_id', $sizeId)
            ->first();
            
        return $result ? $result->quantity : 0;
    }

    /**
     * Get price for a specific color and size combination
     */
    public function getColorSizePrice($colorId, $sizeId)
    {
        $result = DB::table('color_size_product')
            ->where('product_id', $this->id)
            ->where('color_id', $colorId)
            ->where('size_id', $sizeId)
            ->first();
            
        return $result ? $result->price : 0;
    }

    // === MODIFIED START ===
    /**
     * Get total available stock.
     * This value is pre-calculated and stored in the 'quantity' column
     * for both simple and variable products (in Admin\ProductController).
     */
    public function getTotalStockAttribute()
    {
        // The 'quantity' attribute is now the single source of truth.
        return $this->quantity;
    }
    // === MODIFIED END ===

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
     * Check if product has color-size variations
     */
    public function hasColorSizeVariations()
    {
        return DB::table('color_size_product')
            ->where('product_id', $this->id)
            ->whereNotNull('color_id')
            ->exists();
    }

    /**
     * Check if product has any variations
     */
    public function hasVariations()
    {
        return DB::table('color_size_product')->where('product_id', $this->id)->exists() || $this->attributes_values()->count() > 0;
    }

    /**
     * Get all variations with stock info in a structured format
     */
    public function getVariationsWithStock()
    {
        $variations = [
            'color_size' => [],
            'size_only' => [], // ADDED: Key for size-only variations
            'attributes' => []
        ];

        // 1. Get color-size variations grouped by color (color_id IS NOT NULL)
        $colorSizeData = DB::table('color_size_product')
            ->select('color_size_product.*', 'colors.name as color_name', 'colors.code as color_code', 'sizes.name as size_name')
            ->join('colors', 'colors.id', '=', 'color_size_product.color_id')
            ->join('sizes', 'sizes.id', '=', 'color_size_product.size_id')
            ->where('color_size_product.product_id', $this->id)
            ->whereNotNull('color_size_product.color_id') // Filter: Only include color-size variants
            ->get();

        foreach ($colorSizeData as $item) {
            if (!isset($variations['color_size'][$item->color_id])) {
                $variations['color_size'][$item->color_id] = [
                    'color_id' => $item->color_id,
                    'color_name' => $item->color_name,
                    'color_code' => $item->color_code,
                    'sizes' => [],
                    'total_stock' => 0,
                    'images' => $this->getColorImages($item->color_id)
                ];
            }
            
            $variations['color_size'][$item->color_id]['sizes'][] = [
                'size_id' => $item->size_id,
                'size_name' => $item->size_name,
                'stock' => $item->quantity,
                'price' => $item->price
            ];
            
            $variations['color_size'][$item->color_id]['total_stock'] += $item->quantity;
        }

        // PRIORITY CHECK: If color-size exists, return it, skipping size-only and attributes
        if (!empty($variations['color_size'])) {
             return $variations;
        }

        // 2. Get Size-Only Variations (color_id IS NULL)
        $sizeOnlyData = $this->sizeOnlyStock(); // Use the new dedicated method

        if ($sizeOnlyData->isNotEmpty()) {
            $variations['size_only'] = $sizeOnlyData->map(function ($item) {
                return [
                    'id' => $item->size_id,
                    'name' => $item->size_name,
                    'stock' => $item->stock,
                    'price' => $item->price
                ];
            })->values()->toArray();
            
            // PRIORITY CHECK: If size-only exists, return it, skipping attributes
            return $variations;
        }

        // 3. Get attribute variations (if neither above exists)
        foreach($this->attributes_values as $attr) {
            $variations['attributes'][] = [
                'id' => $attr->id,
                'name' => $attr->name,
                'attribute_name' => $attr->attribute->name ?? 'N/A',
                'stock' => $attr->pivot->qnty,
                'price' => $attr->pivot->price,
            ];
        }

        return $variations;
    }

    /**
     * Reduce stock for a specific color-size combination
     */
    public function reduceColorSizeStock($colorId, $sizeId, $quantity)
    {
        DB::table('color_size_product')
            ->where('product_id', $this->id)
            ->where('color_id', $colorId)
            ->where('size_id', $sizeId)
            ->decrement('quantity', $quantity);
            
        // Update product total quantity
        $this->decrement('quantity', $quantity);
    }

    /**
     * Increase stock for a specific color-size combination
     */
    public function increaseColorSizeStock($colorId, $sizeId, $quantity)
    {
        DB::table('color_size_product')
            ->where('product_id', $this->id)
            ->where('color_id', $colorId)
            ->where('size_id', $sizeId)
            ->increment('quantity', $quantity);
            
        // Update product total quantity
        $this->increment('quantity', $quantity);
    }

    /**
     * Check if a specific color-size combination is available
     */
    public function isColorSizeAvailable($colorId, $sizeId, $requestedQuantity = 1)
    {
        $stock = $this->getColorSizeStock($colorId, $sizeId);
        return $stock >= $requestedQuantity;
    }

    /**
     * Get the color-size matrix for display
     */
    public function getColorSizeMatrix()
    {
        $matrix = [];
        $colorSizeData = DB::table('color_size_product')
            ->select('color_size_product.*', 'colors.name as color_name', 'colors.code as color_code', 'sizes.name as size_name')
            ->join('colors', 'colors.id', '=', 'color_size_product.color_id')
            ->join('sizes', 'sizes.id', '=', 'color_size_product.size_id')
            ->where('color_size_product.product_id', $this->id)
            ->whereNotNull('color_size_product.color_id') // Ensure only color-size is returned for the matrix
            ->get();
        
        foreach ($colorSizeData as $item) {
            if (!isset($matrix[$item->color_id])) {
                $matrix[$item->color_id] = [
                    'color' => [
                        'id' => $item->color_id,
                        'name' => $item->color_name,
                        'code' => $item->color_code
                    ],
                    'sizes' => []
                ];
            }
            
            $matrix[$item->color_id]['sizes'][$item->size_id] = [
                'id' => $item->size_id,
                'name' => $item->size_name,
                'quantity' => $item->quantity,
                'price' => $item->price
            ];
        }
        
        return $matrix;
    }
}