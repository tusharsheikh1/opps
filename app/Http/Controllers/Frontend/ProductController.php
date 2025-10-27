<?php

namespace App\Http\Controllers\Frontend;

use App\Helper\Sorting;
use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\ExtraMiniCategory;
use App\Models\Collection;
use App\Models\Color;
use App\Models\Comment;
use App\Models\Product;
use App\Models\CampaingProduct;
use App\Models\Attribute;
use App\Models\AttributeValue;
use App\Models\Size;
use App\Models\SubCategory;
use App\Models\miniCategory;
use Illuminate\Http\Request;
use View;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    // show products by category
    public function showProductByCategory($slug, Request $request)
    {
        $i = 1;
        if ($request->ajax()) {
            $skip = $request->skip / 2;
        } else {
            $skip = 0;
        }

        $category = Category::with(['products' => function($query) use ($skip) {
            return $query->where('status', true)->latest('id')->take(15)->skip($skip);
        }])
        ->where('slug', $slug)
        ->where('status', true)
        ->firstOrFail();

        $products = $category;
        $data = '';
        $data2 = '';
       
        if ($request->ajax()) {
            if($category->products->count() > 0) {
                foreach ($category->products as $product) {
                    $data .= View::make("components.product-grid-view")
                        ->with("product", $product)
                        ->render();
                    $data2 .= View::make("components.product-list-view")
                        ->with("product", $product)
                        ->render();
                }
            }
            return json_encode(array($data, $data2));
        }
        return view('frontend.category-product', compact('category', 'slug'));
    }
    
    public function showProductByBrand($slug, Request $request)
    {
        $i = 1;
        if ($request->ajax()) {
            $skip = $request->skip / 2;
        } else {
            $skip = 0;
        }
        $brand = Brand::where('slug', $slug)->first();
        $products = Product::where('status', '1')->where('brand_id', $brand->id)
            ->skip($skip)
            ->take(16)->get();

        $data = '';
        $data2 = '';
       
        if ($request->ajax()) {
            if($products->count() > 0) {
                foreach ($products as $product) {
                    $data .= View::make("components.product-grid-view")
                        ->with("product", $product)
                        ->render();
                    $data2 .= View::make("components.product-list-view")
                        ->with("product", $product)
                        ->render();
                }
            }
            return json_encode(array($data, $data2));
        }
        return view('frontend.product', compact('slug', 'products'));
    }

    // show products by sub category
    public function showProductBySubCategory($slug, Request $request)
    {
        $i = 1;
        if ($request->ajax()) {
            $skip = $request->skip / 2;
        } else {
            $skip = 0;
        }
        $data = '';
        $data2 = '';
        $subCategory = SubCategory::with(['products' => function($query) use ($skip) {
            return $query->where('status', true)->latest('id')->take(16)->skip($skip);
        }])
        ->where('slug', $slug)
        ->where('status', true)
        ->firstOrFail();
        
        if ($request->ajax()) {
            if($subCategory->products->count() > 0) {
                foreach ($subCategory->products as $product) {
                    $data .= View::make("components.product-grid-view")
                        ->with("product", $product)
                        ->render();
                    $data2 .= View::make("components.product-list-view")
                        ->with("product", $product)
                        ->render();
                }
            }
            return json_encode(array($data, $data2));
        }
        $type = '0';
        return view('frontend.sub-category-product', compact('subCategory', 'slug', 'type'));
    }
    
    public function showProductByMiniCategory($slug, Request $request)
    {
        $type = '1';
        $i = 1;
        if ($request->ajax()) {
            $skip = $request->skip / 2;
        } else {
            $skip = 0;
        }
        $data = '';
        $data2 = '';
        $subCategory = miniCategory::with(['products' => function($query) use ($skip) {
            return $query->where('status', true)->latest('id')->take(16)->skip($skip);
        }])
        ->where('slug', $slug)
        ->where('status', true)
        ->firstOrFail();
        
        if ($request->ajax()) {
            if($subCategory->products->count() > 0) {
                foreach ($subCategory->products as $product) {
                    $data .= View::make("components.product-grid-view")
                        ->with("product", $product)
                        ->render();
                    $data2 .= View::make("components.product-list-view")
                        ->with("product", $product)
                        ->render();
                }
            }
            return json_encode(array($data, $data2));
        }
        return view('frontend.sub-category-product', compact('subCategory', 'slug', 'type'));
    }
    
    public function showProductByextraCategory($slug, Request $request)
    {
        $type = '2';
        $i = 1;
        if ($request->ajax()) {
            $skip = $request->skip / 2;
        } else {
            $skip = 0;
        }
        $data = '';
        $data2 = '';
        $subCategory = ExtraMiniCategory::with(['products' => function($query) use ($skip) {
            return $query->where('status', true)->latest('id')->take(16)->skip($skip);
        }])
        ->where('slug', $slug)
        ->where('status', true)
        ->firstOrFail();
        
        if ($request->ajax()) {
            if($subCategory->products->count() > 0) {
                foreach ($subCategory->products as $product) {
                    $data .= View::make("components.product-grid-view")
                        ->with("product", $product)
                        ->render();
                    $data2 .= View::make("components.product-list-view")
                        ->with("product", $product)
                        ->render();
                }
            }
            return json_encode(array($data, $data2));
        }
        return view('frontend.sub-category-product', compact('subCategory', 'type', 'slug'));
    }
    
    /**
     * show products by collection
     */
    public function showProductByCollection($slug, Request $request)
    {
        $i = 1;
        if ($request->ajax()) {
            $skip = $request->skip / 2;
        } else {
            $skip = 0;
        }

        $collection = Collection::where('slug', $slug)->where('status', true)->firstOrFail();
        $categoryIds = $collection->categories->pluck('id');
        $productIds = DB::table('category_product')->whereIn('category_id', $categoryIds)->get()->pluck('product_id');
        $products = Product::whereIn('id', $productIds)->where('status', true)->latest('id')->take(4)->skip($skip)->get();
        $data = '';
        $data2 = '';
        
        if ($request->ajax()) {
            if($products->count() > 0) {
                foreach ($products as $product) {
                    $data .= View::make("components.product-grid-view")
                        ->with("product", $product)
                        ->render();
                    $data2 .= View::make("components.product-list-view")
                        ->with("product", $product)
                        ->render();
                }
            }
            return json_encode(array($data, $data2));
        }
        return view('frontend.collection-product', compact('products', 'collection'));
    }

    public function showAllProduct(Request $request)
    {
        $i = 1;
        if ($request->ajax()) {
            $skip = $request->skip / 2;
        } else {
            $skip = 0;
        }
        $products = Product::where('status', '1')
            ->skip($skip)->orderBy('id', 'desc')
            ->take(16)->get();

        $data = '';
        $data2 = '';
        if ($request->ajax()) {
            if($products->count() > 0) {
                foreach ($products as $product) {
                    $data .= View::make("components.product-grid-view")
                        ->with("product", $product)
                        ->render();
                    $data2 .= View::make("components.product-list-view")
                        ->with("product", $product)
                        ->render();
                }
            }
            return json_encode(array($data, $data2));
        }
        return view('frontend.product', compact('products')); 
    }

    public function productSearch(Request $request)
    {
        $i = 1;
        if ($request->ajax()) {
            $skip = $request->skip / 2;
        } else {
            $skip = 0;
        }
        $data = '';
        $data2 = '';
        $products = Product::whereLike(['title', 'full_description', 'tags.name'], $request->keyword)
            ->filter('status', true)
            ->latest('id')
            ->skip($skip)
            ->take(16)->get();
            
        if ($request->ajax()) {
            if($products->count() > 0) {
                foreach ($products as $product) {
                    $data .= View::make("components.product-grid-view")
                        ->with("product", $product)
                        ->render();
                    $data2 .= View::make("components.product-list-view")
                        ->with("product", $product)
                        ->render();
                }
            }
            return json_encode(array($data, $data2));
        }
        $key = $request->keyword;
        return view('frontend.search-product', compact('products', 'key'));
    }
    
    public function advanceSearch(Request $request)
    {
        $products = Product::whereLike(['title', 'full_description', 'tags.name'], $request->key)
            ->filter('status', true)
            ->latest('id')
            ->paginate(20);
        $data = '';
        
        if($products->count() > 0) {
            foreach ($products as $product) {
                $data .= '<style>.pin:hover{background:gainsboro !important}</style><div class="product col-lg-12" style="height: initial;">
                            <div class="product-wrapper list-comp">
                            <a href="'.route('product.details', $product->slug).'">
                                <div class="pin" style="display:flex;margin-bottom: 0;background: white;padding: 5px;border-bottom: 1px solid gainsboro;">
                                        <div class="thumbnail">
                                            <img style="object-fit:fill;width: 60px;height: 60px;max-width: 100px;" src="'.asset('uploads/product/'.$product->image).'" alt="Product Image">
                                        </div>
                                    <div class="detaisls">
                                            <h5 style="font-size:15px">'.$product->title.'</h5>
                                             <h5 style="font-size:15px">by:'.$product->user->shop_info->name.'</h5>
                                    </div>
                                </div>
                                </a>
                            </div>
                        </div>';
            }
        }
        return json_encode($data);
    }
    
    // show product details
    public function productDetails($slug)
    {
        $product = Product::with([
            'comments', 
            'reviews',
            'brand',
            'categories', 
            'attributes_values.attribute', // Eager load attribute relationship
            'images'
        ])
        ->where('slug', $slug)
        ->where('status', true)
        ->firstOrFail();
            
        $product->reach += 1;
        $product->update();
        
        // Get structured variations data from the Product model
        $variations = $product->getVariationsWithStock();
        
        // Get only attribute definitions that are actually assigned to this product
        $productAttributeIds = $product->attributes_values->pluck('attribute.id')->unique()->filter();
        $attributes = Attribute::whereIn('id', $productAttributeIds)->get();

        // Get all active sizes to display all options
        $allSizes = Size::where('status', true)->orderBy('id')->get();

        return view('frontend.single-product', compact('product', 'attributes', 'variations', 'allSizes'));
    }
    
    // show product details for campaign
    public function productDetails1($slug)
    {
        $campaigns_product = CampaingProduct::find($slug);
        $product = Product::with([
            'comments', 
            'reviews', 
            'brand',
            'categories',
            'attributes_values.attribute', // Eager load attribute relationship
            'images'
        ])
        ->where('id', $campaigns_product->product_id)
        ->where('status', true)
        ->firstOrFail();
            
        $product->reach += 1;
        $product->update();
        
        // Get structured variations data from the Product model
        $variations = $product->getVariationsWithStock();

        // Get only attribute definitions that are actually assigned to this product
        $productAttributeIds = $product->attributes_values->pluck('attribute.id')->unique()->filter();
        $attributes = Attribute::whereIn('id', $productAttributeIds)->get();
        
        // Get all active sizes to display all options
        $allSizes = Size::where('status', true)->orderBy('id')->get();

        return view('frontend.single-product', compact('product', 'attributes', 'variations', 'campaigns_product', 'allSizes'));
    }
    
    /**
     * === NEW METHOD: Product Info for Add to Cart Modal ===
     * This method returns product information for the add to cart modal
     */
    public function productInfo($slug)
    {
        try {
            $product = Product::with([
                'brand',
                'categories', 
                'attributes_values.attribute',
                'images'
            ])
            ->where('slug', $slug)
            ->where('status', true)
            ->firstOrFail();
            
            // Get structured variations data
            $variations = $product->getVariationsWithStock();
            
            // Get all active sizes
            $allSizes = Size::where('status', true)->orderBy('id')->get();
            
            // Get only attribute definitions that are actually assigned to this product
            $productAttributeIds = $product->attributes_values->pluck('attribute.id')->unique()->filter();
            $attributes = Attribute::whereIn('id', $productAttributeIds)->get();
            
            // Prepare response data
            $response = [
                'success' => true,
                'product' => [
                    'id' => $product->id,
                    'title' => $product->title,
                    'slug' => $product->slug,
                    'image' => asset('uploads/product/' . $product->image),
                    'regular_price' => $product->regular_price,
                    'discount_price' => $product->discount_price,
                    'total_stock' => $product->getTotalStockAttribute(),
                    'has_variations' => $product->hasVariations(),
                ],
                'variations' => $variations,
                'attributes' => $attributes,
                'allSizes' => $allSizes,
                'images' => $product->images->map(function($img) {
                    return [
                        'name' => $img->name,
                        'url' => asset('uploads/product/' . $img->name),
                        'color_attri' => $img->color_attri
                    ];
                })
            ];
            
            return response()->json($response);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found or error occurred: ' . $e->getMessage()
            ], 404);
        }
    }
    
    /**
     * === NEW METHOD: Quick View ===
     * This method returns the quick view modal content
     */
    public function quickView($slug)
    {
        try {
            $product = Product::with([
                'brand',
                'categories', 
                'attributes_values.attribute',
                'images',
                'reviews'
            ])
            ->where('slug', $slug)
            ->where('status', true)
            ->firstOrFail();
            
            // Get structured variations data
            $variations = $product->getVariationsWithStock();
            
            // Get all active sizes
            $allSizes = Size::where('status', true)->orderBy('id')->get();
            
            // Get only attribute definitions that are actually assigned to this product
            $productAttributeIds = $product->attributes_values->pluck('attribute.id')->unique()->filter();
            $attributes = Attribute::whereIn('id', $productAttributeIds)->get();
            
            // If it's an AJAX request, return JSON
            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'html' => view('components.quick-view-modal', compact('product', 'variations', 'attributes', 'allSizes'))->render()
                ]);
            }
            
            // Otherwise, return the view directly (fallback)
            return view('components.quick-view-modal', compact('product', 'variations', 'attributes', 'allSizes'));
            
        } catch (\Exception $e) {
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Product not found: ' . $e->getMessage()
                ], 404);
            }
            abort(404);
        }
    }
    
    /**
     * === NEW METHOD: Get Attribute Price ===
     * Returns price for specific color/size/attribute combination
     */
    public function getAttrPrice(Request $request)
    {
        try {
            $product = Product::findOrFail($request->product_id);
            $basePrice = $product->discount_price > 0 ? $product->discount_price : $product->regular_price;
            $additionalPrice = 0;
            $stock = 0;
            
            // Check for color-size combination
            if ($request->filled('color_id') && $request->filled('size_id')) {
                $result = DB::table('color_size_product')
                    ->where('product_id', $product->id)
                    ->where('color_id', $request->color_id)
                    ->where('size_id', $request->size_id)
                    ->first();
                    
                if ($result) {
                    $additionalPrice = $result->price ?? 0;
                    $stock = $result->quantity ?? 0;
                }
            }
            // Check for size-only
            elseif ($request->filled('size_id') && !$request->filled('color_id')) {
                $result = DB::table('color_size_product')
                    ->where('product_id', $product->id)
                    ->where('size_id', $request->size_id)
                    ->whereNull('color_id')
                    ->first();
                    
                if ($result) {
                    $additionalPrice = $result->price ?? 0;
                    $stock = $result->quantity ?? 0;
                }
            }
            // Check for attribute
            elseif ($request->filled('attribute_value_id')) {
                $attrProduct = DB::table('attribute_product')
                    ->where('product_id', $product->id)
                    ->where('attribute_value_id', $request->attribute_value_id)
                    ->first();
                    
                if ($attrProduct) {
                    $additionalPrice = $attrProduct->price ?? 0;
                    $stock = $attrProduct->qnty ?? 0;
                }
            }
            // Simple product
            else {
                $stock = $product->quantity;
            }
            
            $finalPrice = $basePrice + $additionalPrice;
            
            return response()->json([
                'success' => true,
                'price' => $finalPrice,
                'stock' => $stock,
                'formatted_price' => number_format($finalPrice, 0)
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching price: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * comment by product
     */
    public function comment(Request $request, $slug)
    {
        $this->validate($request, [
            'comment' => 'required|string'
        ]);

        $product = Product::where('slug', $slug)->first();
        
        Comment::create([
            'user_id'    => auth()->id(),
            'product_id' => $product->id,
            'body'       => $request->comment
        ]);

        notify()->success("Your comment successful", "Success");
        return back();
    }
    
    /**
     * product comment reply
     */
    public function reply(Request $request, $slug, $id)
    {
        $this->validate($request, [
            'reply' => 'required|string'
        ]);

        $product = Product::where('slug', $slug)->first();
        
        Comment::create([
            'user_id'    => auth()->id(),
            'product_id' => $product->id,
            'parent_id'  => $id,
            'body'       => $request->reply
        ]);

        notify()->success("Your reply successful", "Success");
        return back();
    }

    /**
     * product filtering by requested data
     */
    public function productFilter(Request $request)
    {
        $products = Product::where('status', '1');

        $unr = $request->unr;
        
        // Pricing level string to int
        if(isset($request->amount)) {
            if (setting('CURRENCY_CODE_MIN')) {
                $currency_code_min = setting('CURRENCY_CODE_MIN');
            } else {
                $currency_code_min = "Tk";
            }
            $min = (int)str_replace($currency_code_min, '', substr($request->amount, 0, strpos($request->amount, '-')));
            $max = (int)str_replace($currency_code_min, '', substr($request->amount, strpos($request->amount, "-") + 1));
            $products = $products->whereBetween('regular_price', [$min, $max]);
        } else {
            $min = 0;
            $max = 9999999999999999999999999;
        }

        // check category
        if ($request->extra_category != '') {
            $sub_category = ExtraMiniCategory::where('slug', $request->extra_category)->pluck('id');
            $sub_category_product_ids = DB::table('extra_mini_category_product')->where('extra_mini_category_id', $sub_category)->get()->pluck('product_id');
            $products = $products->whereIn('id', $sub_category_product_ids);
        } elseif ($request->mini_category != '') {
            $sub_category = miniCategory::where('slug', $request->mini_category)->pluck('id');
            $sub_category_product_ids = DB::table('mini_category_product')->where('mini_category_id', $sub_category)->get()->pluck('product_id');
            $products = $products->whereIn('id', $sub_category_product_ids);
        } elseif ($request->sub_category != '') {
            $sub_category = SubCategory::where('slug', $request->sub_category)->pluck('id');
            $sub_category_product_ids = DB::table('product_sub_category')->where('sub_category_id', $sub_category)->get()->pluck('product_id');
            $products = $products->whereIn('id', $sub_category_product_ids);
        } elseif ($request->category != '') {
            $category = Category::where('slug', $request->category)->pluck('id');
            $category_product_ids = DB::table('category_product')->where('category_id', $category)->get()->pluck('product_id');
            $products = $products->whereIn('id', $category_product_ids);
        }

        // check request collection
        if ($request->collection != '') {
            $collection = Collection::where('slug', $request->collection)->first();
            $categoryIds = $collection->categories->pluck('id');
            $collection_product_ids = DB::table('category_product')->whereIn('category_id', $categoryIds)->get()->pluck('product_id');
            $products = $products->whereIn('id', $collection_product_ids);
        }
        
        // check request rating
        if ($request->rating != '') {
            $rating_product_ids = DB::table('reviews')->where('rating', $request->rating)->get()->pluck('product_id');
            $products = $products->whereIn('id', $rating_product_ids);
        }
        
        // Filter by color
        if ($request->colors != '') {
            $colors = Color::whereIn('slug', $request->colors)->pluck('id');
            $color_product_ids = DB::table('color_product')->whereIn('color_id', $colors)->get()->pluck('product_id');
            $products = $products->whereIn('id', $color_product_ids);
        }

        // Filter by attributes
        $s_attri = $request->input('attri');
        if (!empty($s_attri)) {
            $attributeValues = AttributeValue::whereIn('slug', $s_attri)->pluck('id');
            $attributeProductIds = DB::table('attribute_product')->whereIn('attribute_value_id', $attributeValues)->pluck('product_id');
            if ($attributeProductIds->count() > 0) {
                $products = $products->whereIn('id', $attributeProductIds->toArray());
            } else {
                $products = $products->where('id', 0);
            }
        }

        // Filter by brands
        $brands = $request->input('brands');
        if (!empty($brands)) {
            $brandIds = Brand::whereIn('slug', $brands)->pluck('id');
            if ($brandIds->count() > 0) {
                $products = $products->whereIn('brand_id', $brandIds);
            }
        }
        
        // Filter out products with zero stock
        $products = $products->where('quantity', '>', 0);

        // sorting
        $sort = new Sorting();
        $value = $sort->getValue($request->sort);
        if ($value == $sort->oldToNew) {
            $products = $products->orderBy('id', 'asc')->get();
        } elseif ($value == $sort->best) {
            $products = $products->orderBy('reach', 'desc')->get();
        } elseif ($value == $sort->highToLow) {
            $products = $products->orderByRaw('CONVERT(regular_price, SIGNED) desc')->get();
        } elseif ($value == $sort->lowToHigh) {
            $products = $products->orderByRaw('CONVERT(regular_price, SIGNED) asc')->get();
        } elseif ($value == $sort->dhighToLow) {
            $products = $products->orderByRaw('CONVERT(discount_price, SIGNED) desc')->get();
        } elseif ($value == $sort->dlowToHigh) {
            $products = $products->orderByRaw('CONVERT(discount_price, SIGNED) asc')->get();
        } else {
            $products = $products->orderBy('id', 'desc')->get();
        }

        return view('frontend.filter-product', compact('products', 'request', 'min', 'max', 'unr'));
    }
    
    public function allBrand()
    {
        $brands = Brand::where('status', '1')->get();
        return view('frontend.brands', compact('brands'));
    }

    /**
     * Show all available collections (for route 'collection.list').
     */
    public function allCollection()
    {
        // Fetch all active collections
        $collections = Collection::where('status', true)->get();
        // Return a view to display them, similar to 'frontend.brands'
        return view('frontend.collections', compact('collections'));
    }
    
    /**
     * Check stock availability for a specific variation
     */
    public function checkStock(Request $request)
    {
        $product = Product::find($request->product_id);
        
        if (!$product) {
            return response()->json(['available' => false, 'message' => 'Product not found']);
        }
        
        $requestedQty = $request->quantity ?? 1;
        $availableStock = 0;

        // Check for Color-Size Variation
        if ($request->filled('color_id') && $request->filled('size_id')) {
            $availableStock = $product->getColorSizeStock($request->color_id, $request->size_id);
        }
        // Check for Size-Only Variation
        else if ($request->filled('size_id') && !$request->filled('color_id')) {
            $result = DB::table('color_size_product')
                        ->where('product_id', $product->id)
                        ->where('size_id', $request->size_id)
                        ->whereNull('color_id')
                        ->first();
            $availableStock = $result ? $result->quantity : 0;
        }
        // Check for Attribute Variation
        else if ($request->filled('attribute_value_id')) {
            $availableStock = $product->getAttributeStock($request->attribute_value_id);
        }
        // Check for Simple Product Stock
        else if (!$request->filled('color_id') && !$request->filled('size_id') && !$request->filled('attribute_value_id')) {
            $availableStock = $product->quantity;
        }
        else {
            $availableStock = 0; 
            return response()->json([
                'available' => false,
                'stock' => 0,
                'message' => 'Please select all product options.'
            ]);
        }
        
        return response()->json([
            'available' => $availableStock >= $requestedQty,
            'stock' => $availableStock,
            'message' => $availableStock >= $requestedQty 
                ? 'In stock' 
                : ($availableStock > 0 ? "Only $availableStock available" : 'Out of stock')
        ]);
    }
}