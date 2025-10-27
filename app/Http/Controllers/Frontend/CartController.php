<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use App\Models\Product;
use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Http\Request;
use App\Models\Attribute;
use App\Models\CartInfo;
use App\Models\CampaingProduct;
use App\Models\Color;
use App\Models\AttributeValue;
use App\Models\Size;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class CartController extends Controller
{
    /**
     * Display cart page with server-side data to prevent slow loading
     */
    public function cart()
    {
        $cartCollection = Cart::content();
        $subtotal = Cart::subtotal(2, '.', ''); 
        $count = Cart::count();

        return view('frontend.cart', [
            'carts' => $cartCollection->sortBy('weight'),
            'subtotal' => $subtotal,
            'count' => $count
        ]);
    }

    /**
     * Add product to cart - COMPREHENSIVE FIXED VERSION
     */
    public function addToCart(Request $request)
    {
        // Enhanced validation
        $this->validate($request, [
            'id'            => 'required|integer',
            'qty'           => 'required|integer|min:1',
            'color'         => 'nullable|string|max:50',
            'size'          => 'nullable|string|max:50',
            'dynamic_price' => 'nullable|numeric|min:0'
        ]);

        try {
            $product = Product::findOrFail($request->id);
            
            // Basic product availability check
            if (!$product || $product->status != 1) {
                return response()->json([
                    'alert'   => 'Warning',
                    'message' => 'This product is not available',
                ]);
            }

            // Get product variations to determine validation requirements
            $variations = $product->getVariationsWithStock();
            $hasColorSize = !empty($variations['color_size']);
            $hasSizeOnly = !empty($variations['size_only']) && empty($variations['color_size']);
            $hasAttributes = !empty($variations['attributes']) && empty($variations['color_size']) && empty($variations['size_only']);

            // Initialize variables
            $selectedColorId = null;
            $selectedSizeId = null;
            $selectedAttributeId = null;
            $additionalPrice = 0;
            $availableStock = 0;
            $attr = [];

            // === VALIDATION AND PROCESSING BY PRODUCT TYPE ===
            
            if ($hasColorSize) {
                // 1. COLOR-SIZE PRODUCTS
                $colorId = $request->input('color');
                $sizeId = $request->input('size');

                // Validate color selection
                if (empty($colorId) || $colorId == 'blank') {
                    return response()->json([
                        'alert'   => 'Warning',
                        'message' => 'Please select a color',
                    ]);
                }

                // Validate size selection
                if (empty($sizeId) || $sizeId == 'blank') {
                    return response()->json([
                        'alert'   => 'Warning',
                        'message' => 'Please select a size',
                    ]);
                }

                // Validate color ID exists
                $color = Color::find($colorId);
                if (!$color) {
                    return response()->json([
                        'alert'   => 'Warning',
                        'message' => 'Invalid color selection',
                    ]);
                }
                $selectedColorId = $colorId;
                $selectedSizeId = $sizeId;

                // Check color-size stock and price
                $colorSizeRecord = DB::table('color_size_product')
                    ->where('product_id', $product->id)
                    ->where('color_id', $selectedColorId)
                    ->where('size_id', $selectedSizeId)
                    ->first();

                if (!$colorSizeRecord) {
                    return response()->json([
                        'alert'   => 'Warning',
                        'message' => 'This color and size combination is not available',
                    ]);
                }

                $availableStock = $colorSizeRecord->quantity;
                $additionalPrice = $colorSizeRecord->price ?? 0;

                // Store selection for cart options
                $attr['color'] = $selectedColorId;
                $attr['size'] = $selectedSizeId;

            } elseif ($hasSizeOnly) {
                // 2. SIZE-ONLY PRODUCTS
                $sizeId = $request->input('size');

                if (empty($sizeId) || $sizeId == 'blank') {
                    return response()->json([
                        'alert'   => 'Warning',
                        'message' => 'Please select a size',
                    ]);
                }

                $selectedSizeId = $sizeId;

                // Check size-only stock and price
                $sizeRecord = DB::table('color_size_product')
                    ->where('product_id', $product->id)
                    ->where('size_id', $selectedSizeId)
                    ->whereNull('color_id')
                    ->first();

                if (!$sizeRecord) {
                    return response()->json([
                        'alert'   => 'Warning',
                        'message' => 'This size is not available',
                    ]);
                }

                $availableStock = $sizeRecord->quantity;
                $additionalPrice = $sizeRecord->price ?? 0;
                $attr['size'] = $selectedSizeId;

            } elseif ($hasAttributes) {
                // 3. ATTRIBUTE-BASED PRODUCTS
                $attributeValueId = $request->input('size'); // Size field is reused for attributes

                if (empty($attributeValueId) || $attributeValueId == 'blank') {
                    return response()->json([
                        'alert'   => 'Warning',
                        'message' => 'Please select an option',
                    ]);
                }

                $selectedAttributeId = $attributeValueId;

                // Check attribute stock and price
                $attributeRecord = DB::table('attribute_product')
                    ->where('product_id', $product->id)
                    ->where('attribute_value_id', $selectedAttributeId)
                    ->first();

                if (!$attributeRecord) {
                    return response()->json([
                        'alert'   => 'Warning',
                        'message' => 'This option is not available',
                    ]);
                }

                $availableStock = $attributeRecord->qnty;
                $additionalPrice = $attributeRecord->price ?? 0;
                $attr['attribute'] = $selectedAttributeId;

            } else {
                // 4. SIMPLE PRODUCTS
                $availableStock = $product->quantity;
                $additionalPrice = 0;
            }

            // === STOCK VALIDATION ===
            if ($availableStock <= 0) {
                return response()->json([
                    'alert'   => 'Warning',
                    'message' => 'Sorry, this product is out of stock',
                ]);
            }

            if ($request->qty > $availableStock) {
                return response()->json([
                    'alert'   => 'Warning',
                    'message' => "Only {$availableStock} items available in stock",
                ]);
            }

            // === PRICE CALCULATION ===
            $basePrice = 0;
            if (isset($request->camp)) {
                $camp = CampaingProduct::find($request->camp);
                $basePrice = $camp ? $camp->price : $product->regular_price;
            } elseif ($request->filled('dynamic_price')) {
                $basePrice = $request->dynamic_price;
            } elseif (!empty($product->discount_price)) {
                $basePrice = $product->discount_price;
            } else {
                $basePrice = $product->regular_price;
            }

            $finalPrice = $basePrice + $additionalPrice;

            // === PREPARE CART OPTIONS ===
            $cartOptions = [
                'slug'              => $product->slug,
                'image'             => $product->image,
                'color'             => $selectedColorId ? $selectedColorId : 'blank',
                'vendor'            => $product->user_id,
                'seller'            => $product->user->name ?? 'Unknown',
                'variation_type'    => $hasColorSize ? 'color_size' : ($hasSizeOnly ? 'size_only' : ($hasAttributes ? 'attributes' : 'simple')),
                'selected_color_id' => $selectedColorId,
                'selected_size_id'  => $selectedSizeId,
                'selected_attr_id'  => $selectedAttributeId,
                'attributes'        => $attr,
            ];

            // === ADD TO CART ===
            $cart = Cart::add([
                'id'      => $product->id,
                'name'    => $product->title,
                'qty'     => $request->qty,
                'price'   => $finalPrice,
                'weight'  => $product->user_id,
                'options' => $cartOptions,
            ]);

            // === SAVE TO DATABASE (for logged in users) ===
            if (auth()->id()) {
                CartInfo::updateOrCreate(
                    [
                        'user_id'    => auth()->id(),
                        'product_id' => $product->id,
                        'color'      => $selectedColorId ?? 'blank',
                    ],
                    [
                        'ser'        => $cart->rowId,
                        'qty'        => $request->qty,
                        'attributes' => json_encode($attr),
                    ]
                );
            }

            return response()->json([
                'alert'   => 'Success',
                'message' => 'Successfully added product to cart',
                'count'   => Cart::count(),
            ]);

        } catch (\Exception $e) {
            \Log::error('Add to Cart Error: ' . $e->getMessage());
            return response()->json([
                'alert'   => 'Error',
                'message' => 'Sorry, something went wrong. Please try again.',
            ], 500);
        }
    }

    /**
     * Get cart data with improved attribute formatting
     */
    public function getCart()
    {
        try {
            $cartCollection = Cart::content();
            
            // Transform cart items to include formatted attributes
            $data = $cartCollection->sortBy('weight')->map(function ($cartItem) {
                $cartItem->options->formatted_attributes = [];
                
                // Get variation type
                $variationType = $cartItem->options->variation_type ?? 'simple';
                
                switch ($variationType) {
                    case 'color_size':
                        $this->formatColorSizeAttributes($cartItem);
                        break;
                    case 'size_only':
                        $this->formatSizeOnlyAttributes($cartItem);
                        break;
                    case 'attributes':
                        $this->formatGenericAttributes($cartItem);
                        break;
                    default:
                        // Simple product - no attributes to format
                        break;
                }
                
                return $cartItem;
            });
            
            $subtotal = Cart::subtotal(2, '.', '');
            
            return response()->json([
                'count'    => Cart::count(),
                'carts'    => $data,
                'subtotal' => $subtotal
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Get Cart Error: ' . $e->getMessage());
            return response()->json([
                'count'    => 0,
                'carts'    => [],
                'subtotal' => '0.00'
            ], 500);
        }
    }

    /**
     * Format color-size attributes for cart display
     */
    private function formatColorSizeAttributes($cartItem)
    {
        try {
            // Format Color
            if (!empty($cartItem->options->selected_color_id)) {
                $color = Color::find($cartItem->options->selected_color_id);
                if ($color) {
                    $cartItem->options->formatted_attributes['Color'] = $color->name;
                }
            } elseif (!empty($cartItem->options->color) && $cartItem->options->color != 'blank') {
                // Handle both color ID and color slug for backward compatibility
                if (is_numeric($cartItem->options->color)) {
                    // It's a color ID
                    $color = Color::find($cartItem->options->color);
                } else {
                    // It's a color slug (for backward compatibility)
                    $color = Color::where('slug', $cartItem->options->color)->first();
                }
                if ($color) {
                    $cartItem->options->formatted_attributes['Color'] = $color->name;
                }
            }
            
            // Format Size
            if (!empty($cartItem->options->selected_size_id)) {
                $size = Size::find($cartItem->options->selected_size_id);
                if ($size) {
                    $cartItem->options->formatted_attributes['Size'] = $size->name;
                }
            }
        } catch (\Exception $e) {
            \Log::error('Format Color-Size Attributes Error: ' . $e->getMessage());
        }
    }

    /**
     * Format size-only attributes for cart display
     */
    private function formatSizeOnlyAttributes($cartItem)
    {
        try {
            if (!empty($cartItem->options->selected_size_id)) {
                $size = Size::find($cartItem->options->selected_size_id);
                if ($size) {
                    $cartItem->options->formatted_attributes['Size'] = $size->name;
                }
            }
        } catch (\Exception $e) {
            \Log::error('Format Size-Only Attributes Error: ' . $e->getMessage());
        }
    }

    /**
     * Format generic attributes for cart display
     */
    private function formatGenericAttributes($cartItem)
    {
        try {
            if (!empty($cartItem->options->selected_attr_id)) {
                $attributeValue = AttributeValue::with('attribute')->find($cartItem->options->selected_attr_id);
                if ($attributeValue) {
                    $attributeName = $attributeValue->attribute->name ?? 'Attribute';
                    $cartItem->options->formatted_attributes[$attributeName] = $attributeValue->value;
                }
            }
            
            // Fallback: Process legacy attributes array
            if (isset($cartItem->options->attributes) && is_array($cartItem->options->attributes)) {
                foreach ($cartItem->options->attributes as $slug => $attributeId) {
                    $attributeValue = AttributeValue::with('attribute')->find($attributeId);
                    if ($attributeValue) {
                        if ($slug === 'size') {
                            $cartItem->options->formatted_attributes['Size'] = $attributeValue->value;
                        } else {
                            $attributeName = $attributeValue->attribute->name ?? ucfirst($slug);
                            $cartItem->options->formatted_attributes[$attributeName] = $attributeValue->value;
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            \Log::error('Format Generic Attributes Error: ' . $e->getMessage());
        }
    }

    /**
     * Update cart quantity with improved error handling
     */
    public function updateCart($rowId, $qty)
    {
        try {
            $cartItem = Cart::get($rowId);
            if (!$cartItem) {
                return response()->json([
                    'alert'   => 'Error',
                    'message' => 'Cart item not found',
                ], 404);
            }

            // Validate stock for the updated quantity
            $product = Product::find($cartItem->id);
            if (!$product) {
                return response()->json([
                    'alert'   => 'Error',
                    'message' => 'Product not found',
                ], 404);
            }

            // Check stock based on variation type
            $availableStock = $this->getAvailableStock($product, $cartItem->options);
            
            if ($qty > $availableStock) {
                return response()->json([
                    'alert'   => 'Warning',
                    'message' => "Only {$availableStock} items available in stock",
                ]);
            }

            Cart::update($rowId, ['qty' => $qty]);
            
            // Sync with database
            if (auth()->id()) {
                CartInfo::where('ser', $rowId)
                        ->where('user_id', auth()->id())
                        ->update(['qty' => $qty]);
            }
            
            return response()->json([
                'alert'   => 'Success',
                'message' => 'Quantity updated successfully',
                'count'   => Cart::count()
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Update Cart Error: ' . $e->getMessage());
            return response()->json([
                'alert'   => 'Error',
                'message' => 'Failed to update cart',
            ], 500);
        }
    }

    /**
     * Get available stock for a specific product variation
     */
    private function getAvailableStock($product, $options)
    {
        $variationType = $options->variation_type ?? 'simple';
        
        switch ($variationType) {
            case 'color_size':
                if (!empty($options->selected_color_id) && !empty($options->selected_size_id)) {
                    $record = DB::table('color_size_product')
                        ->where('product_id', $product->id)
                        ->where('color_id', $options->selected_color_id)
                        ->where('size_id', $options->selected_size_id)
                        ->first();
                    return $record ? $record->quantity : 0;
                }
                break;
                
            case 'size_only':
                if (!empty($options->selected_size_id)) {
                    $record = DB::table('color_size_product')
                        ->where('product_id', $product->id)
                        ->where('size_id', $options->selected_size_id)
                        ->whereNull('color_id')
                        ->first();
                    return $record ? $record->quantity : 0;
                }
                break;
                
            case 'attributes':
                if (!empty($options->selected_attr_id)) {
                    $record = DB::table('attribute_product')
                        ->where('product_id', $product->id)
                        ->where('attribute_value_id', $options->selected_attr_id)
                        ->first();
                    return $record ? $record->qnty : 0;
                }
                break;
                
            default:
                return $product->quantity;
        }
        
        return 0;
    }

    /**
     * Remove product from cart with improved error handling
     */
    public function destroyCart($rowId)
    {
        try {
            Cart::remove($rowId);
            
            // Remove from database
            if (auth()->id()) {
                CartInfo::where('ser', $rowId)
                        ->where('user_id', auth()->id())
                        ->delete();
            }
            
            return response()->json([
                'alert'   => 'Success',
                'message' => 'Product successfully removed from cart',
                'count'   => Cart::count()
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Destroy Cart Error: ' . $e->getMessage());
            return response()->json([
                'alert'   => 'Error',
                'message' => 'Failed to remove product from cart',
            ], 500);
        }
    }
    
    /**
     * Apply coupon to cart
     */
    public function applyCoupon($code, $stotal)
    {
        return $this->validateAndApplyCoupon($code, $stotal);
    }
    
    /**
     * Apply coupon for buy now products
     */
    public function applyCouponBuyNow($code, $id, $qty, $dynamic)
    {
        $product = Product::find($id);
        if (!$product) {
            return response()->json([
                'message' => 'Product not found',
                'alert'   => 'error'
            ]);
        }

        if ($qty >= 6 && $product->whole_price > 0) {
            $subtotal = $product->whole_price * $qty;
        } else {
            $subtotal = $dynamic * $qty;
        }
        
        return $this->validateAndApplyCoupon($code, $subtotal);
    }

    /**
     * Shared coupon validation and application logic
     */
    private function validateAndApplyCoupon($code, $subtotal)
    {
        try {
            $coupon = Coupon::where('code', $code)
                            ->where('status', true)
                            ->where('expire_date', '>=', date('Y-m-d'))
                            ->first();
            
            if (!$coupon) {
                return response()->json([
                    'message' => 'Invalid Coupon Code!',
                    'alert'   => 'error'
                ]);
            }

            if ($coupon->available_limit <= 0) {
                return response()->json([
                    'message' => 'Coupon limit not available',
                    'alert'   => 'error'
                ]);
            }

            // Check user usage limit
            if (auth()->id()) {
                $coupon_limit = DB::table('coupon_user')
                                  ->where('user_id', auth()->id())
                                  ->where('coupon_id', $coupon->id)
                                  ->count();

                if ($coupon_limit >= $coupon->limit_per_user) {
                    return response()->json([
                        'message' => "Your coupon use limit not available, already used {$coupon_limit} time(s)",
                        'alert'   => 'error',
                    ]);
                }
            }

            if (Session::has('coupon')) {
                return response()->json([
                    'message' => 'Already applied a coupon code',
                    'alert'   => 'error',
                ]);
            }

            // Calculate discount
            if ($coupon->discount_type == 'percent') {
                $discount = (floatval($coupon->discount) / 100) * $subtotal;
            } else {
                $discount = $coupon->discount;
            }
            
            // Apply coupon
            Session::put('coupon', [
                'name'     => $coupon->code,
                'discount' => $discount
            ]);

            if (auth()->id()) {
                $coupon->users()->attach(auth()->id());
            }
            $coupon->decrement('available_limit');

            return response()->json([
                'message'  => 'Successfully applied coupon',
                'alert'    => 'success',
                'total'    => $subtotal - $discount,
                'discount' => $discount
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Apply Coupon Error: ' . $e->getMessage());
            return response()->json([
                'message' => 'Failed to apply coupon',
                'alert'   => 'error'
            ], 500);
        }
    }
}