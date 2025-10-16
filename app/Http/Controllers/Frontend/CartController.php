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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class CartController extends Controller
{
    public function cart()
    {
        return view('frontend.cart');
    }

    // add to cart - FIXED VERSION
    public function addToCart(Request $request)
    {
        // Basic validation - only validate required fields
        $this->validate($request, [
            'id'    => 'required|integer',
            'qty'   => 'required|integer|min:1',
            'color' => 'nullable|string|max:50'
        ]);

        try {
            $attr = [];
            $price = 0;
            $product = Product::findOrFail($request->id);
            
            // Check if product exists and has stock
            if ($product->quantity <= 0) {
                return response()->json([
                    'alert'    => 'Warning',
                    'message'  => 'Sorry, this product is out of stock',
                ]);
            }

            // Get attributes that actually exist for this product
            $productAttributes = DB::table('attribute_product')
                ->join('attribute_values', 'attribute_values.id', '=', 'attribute_product.attribute_value_id')
                ->join('attributes', 'attributes.id', '=', 'attribute_values.attributes_id')
                ->where('attribute_product.product_id', $product->id)
                ->select('attributes.slug', 'attributes.name', 'attribute_product.attribute_value_id', 'attribute_product.price')
                ->get()
                ->groupBy('slug');

            // Validate color selection if product has colors
            if ($product->colors->count() > 0 && ($request->color == 'blank' || empty($request->color))) {
                return response()->json([
                    'alert'    => 'Warning',
                    'message'  => 'Please choose a color',
                ]);
            }

            // Validate required attributes
            foreach ($productAttributes as $slug => $attributeValues) {
                $requestValue = $request->input($slug);
                
                // If multiple options exist for this attribute, selection is required
                if ($attributeValues->count() > 1 && ($requestValue == 'blank' || empty($requestValue))) {
                    $attributeName = $attributeValues->first()->name;
                    return response()->json([
                        'alert'    => 'Warning',
                        'message'  => "Please select {$attributeName}",
                    ]);
                }
            }

            // Process attributes and calculate additional price
            foreach ($productAttributes as $slug => $attributeValues) {
                $requestValue = $request->input($slug);
                
                if (!empty($requestValue) && $requestValue != 'blank') {
                    $attr[$slug] = $requestValue;
                    
                    // Find the attribute product record to get additional price
                    $attrProduct = DB::table('attribute_product')
                        ->where('product_id', $request->id)
                        ->where('attribute_value_id', $requestValue)
                        ->first();
                    
                    if ($attrProduct) {
                        $price += $attrProduct->price;
                    }
                }
            }

            // Process color selection and pricing
            if (!empty($request->color) && $request->color != 'blank') {
                $color = Color::where('slug', $request->color)->first();
                if ($color) {
                    $colorProduct = DB::table('color_product')
                        ->where('product_id', $request->id)
                        ->where('color_id', $color->id)
                        ->first();
                    
                    if ($colorProduct) {
                        $price += $colorProduct->price;
                    }
                }
            }

            // Determine base price
            if (isset($request->camp)) {
                $camp = CampaingProduct::find($request->camp);
                $basePrice = $camp ? $camp->price : $product->regular_price;
            } elseif (!empty($product->discount_price)) {
                $basePrice = $product->discount_price;
            } else {
                $basePrice = $product->regular_price;
            }

            $finalPrice = $basePrice + $price;

            // Add to cart
            $cart = Cart::add([
                'id'        => $product->id, 
                'name'      => $product->title, 
                'qty'       => $request->qty, 
                'price'     => $finalPrice,
                'weight'    => $product->user_id,
                'options'   => [
                    'slug'       => $product->slug, 
                    'image'      => $product->image, 
                    'attributes' => $attr,
                    'color'      => $request->color ?? 'blank',
                    'vendor'     => $product->user_id, 
                    'seller'     => $product->user->name ?? 'Unknown', 
                ],
            ]);

            // Save cart info to database if user is logged in
            if (auth()->id()) {
                CartInfo::create([
                    'user_id'    => auth()->id(),
                    'vendor'     => $product->user_id, 
                    'ser'        => $cart->rowId,
                    'product_id' => $product->id,
                    'qty'        => $request->qty,
                    'price'      => $finalPrice,
                    'color'      => $request->color ?? 'blank',
                    'attr'       => json_encode($attr),
                ]);
            }

            return response()->json([
                'alert'    => 'Success',
                'message'  => 'Product added to cart successfully',
                'subtotal' => Cart::subtotal(),
                'count'    => Cart::count(),
            ]);

        } catch (\Exception $e) {
            // Log the error for debugging
            \Log::error('Add to cart error: ' . $e->getMessage());
            
            return response()->json([
                'alert'    => 'Error',
                'message'  => 'Failed to add product to cart. Please try again.',
            ], 500);
        }
    }

    // get cart data
    public function getCart() {
        
         $cartCollection= Cart::content();
        $data= $cartCollection->sortBy('weight');
         
        
        return response()->json([
            'count' => Cart::count(),
            'carts' => $data
        ]);
    }

    // update cart quantity
    public function updateCart($rowId, $qty)
    {
        Cart::update($rowId, ['qty' => $qty]);
        return response()->json([
            'alert'   => 'Success',
            'message' => 'Quantity update successfully',
        ]);
    }

    // remove product to cart
    public function destroyCart($rowId)
    {
        Cart::remove($rowId);
        $cart=CartInfo::where('ser',$rowId)->delete();
        return response()->json([
            'alert'   => 'Success',
            'message' => 'Product successfully remove to cart',
        ]);
    }
    
    /**
     * apply coupon
     *
     * @param  mixed $code
     * @return void
     */
    public function applyCoupon($code,$stotal)
    {
        $coupon = Coupon::where('code', $code)->where('status', true)->where('expire_date', '>=', date('Y-m-d'))->first();
        
        if($coupon){
            if ($coupon->available_limit > 0) {
                $coupon_limit = DB::table('coupon_user')->where('user_id', auth()->id())->where('coupon_id', $coupon->id)->get();
                
                if ($coupon_limit->count() < $coupon->limit_per_user) {
                    
                    if(Session::has('coupon')){
                        return response()->json([
                            'message' => 'Already applied this coupon code.',
                            'alert'   => 'error',
                        ]);
                    }

                    $subtotal = $stotal;
                    if ($coupon->discount_type == 'percent') {
                        $discount  = (floatval($coupon->discount) / 100) * $subtotal;
                    } 
                    else {
                        $discount = $coupon->discount;
                    }
                    
                    Session::put('coupon', [
                        'name'     => $coupon->code,
                        'discount' => $discount
                    ]);
                    $coupon->users()->attach(auth()->id());
                    $coupon->update([
                        'available_limit' => $coupon->available_limit - 1
                    ]);
                    return response()->json([
                        'message'  => 'Successfully apply coupon',
                        'alert'    => 'success',
                        'total'    => $subtotal - $discount,
                        'discount' => $discount
                    ]);
                }
                return response()->json([
                    'message' => 'Your coupon use limit not available, already use '.auth()->user()->coupons()->count().' time',
                    'alert'   => 'error',
                ]);
            }
            return response()->json([
                'message' => 'Coupon Limit Not Available',
                'alert'   => 'error',
            ]);
        }
        return response()->json([
            'message' => 'Invalid Coupon Code!!',
            'alert' => 'error',
        ]);
    }
    
    /**
     * apply coupon buy now product
     *
     * @param  mixed $code
     * @return void
     */
    public function applyCouponBuyNow($code, $id, $qty,$dynamic)
    {
        $coupon = Coupon::where('code', $code)->where('status', true)->where('expire_date', '>=', date('Y-m-d'))->first();
        
        if($coupon){
            if ($coupon->available_limit > 0) {
                $coupon_limit = DB::table('coupon_user')->where('user_id', auth()->id())->where('coupon_id', $coupon->id)->get();
                
                if ($coupon_limit->count() < $coupon->limit_per_user) {
                    
                    if(Session::has('coupon')){
                        return response()->json([
                            'message' => 'Already applied this coupon code.',
                            'alert'   => 'error'
                        ]);
                    }
                    $product = Product::find($id);
                    if ($product) {
                        if($qty>=6 && $product->whole_price >0){
                            $subtotal = $product->whole_price * $qty;
                        }else{
                            $subtotal = $dynamic * $qty;
                        }
                        if ($coupon->discount_type == 'percent') {
                            $discount  = (floatval($coupon->discount) / 100) * $subtotal;
                        } 
                        else {
                            $discount = $coupon->discount;
                        }
                        
                        Session::put('coupon', [
                            'name'     => $coupon->code,
                            'discount' => $discount
                        ]);
                        $coupon->users()->attach(auth()->id());
                        $coupon->update([
                            'available_limit' => $coupon->available_limit - 1
                        ]);
                        return response()->json([
                            'message'  => 'Successfully apply coupon',
                            'alert'    => 'success',
                            'total'    => $subtotal - $discount,
                            'discount' => $discount
                        ]);
                    }
                    return response()->json([
                        'message' => 'Sorry something wrong'.$id,
                        'alert'   => 'error'
                    ]);
                }
                return response()->json([
                    'message' => 'Your coupon use limit not available, already use '.auth()->user()->coupons()->count().' time',
                    'alert'   => 'error'
                ]);
            }
            return response()->json([
                'message' => 'Coupon Limit Not Available',
                'alert'   => 'error'
            ]);
        }
        return response()->json([
            'message' => 'Invalid Coupon Code!!',
            'alert' => 'error'
        ]);
    }
}