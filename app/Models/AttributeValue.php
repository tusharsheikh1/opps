<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttributeValue extends Model
{
    use HasFactory;
    
    protected $guarded = ['id'];
    
    /**
     * Get the attribute that owns the attribute value.
     */
    public function attribute()
    {
        return $this->belongsTo(Attribute::class, 'attributes_id');
    }
    
    /**
     * Get all products that have this attribute value.
     */
    public function products()
    {
        return $this->belongsToMany(Product::class, 'attribute_product')
                    ->withPivot('qnty', 'price');
    }
}