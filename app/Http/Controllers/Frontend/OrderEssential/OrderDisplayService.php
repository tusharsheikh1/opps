<?php

namespace App\Http\Controllers\Frontend\OrderEssential;

use App\Models\Commission;
use App\Models\DownloadProduct;
use App\Models\DownloadUserProduct;
use App\Models\Order;
use App\Models\Product;
use App\Models\Review;
use App\Models\User;
use App\Models\VendorAccount;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class OrderDisplayService
{
    /**
     * Customer order show
     */
    public function order()
    {
        $orders = Order::where('user_id', auth()->id())->latest('id')->get();
        return view('frontend.order', compact('orders'));
    }

    /**
     * Show returns page
     */
    public function returns()
    {
        $orders = Order::where('user_id', auth()->id())
            ->whereIn('status', [6, 7, 8])
            ->latest('id')
            ->get();
        return view('frontend.returns_order', compact('orders'));
    }

    /**
     * Order invoice print
     */
    public function orderInvoice($id)
    {
        $order = Order::where('id', $id)->where('user_id', auth()->id())->firstOrFail();
        return view('frontend.invoice', compact('order'));
    }

    /**
     * Cancel order
     */
    public function cancel($id)
    {
        $order = Order::where('id', $id)->where('user_id', auth()->id())->firstOrFail();
        
        if ($order->status == 0 || $order->status == 1) {
            foreach ($order->orderDetails as $item) {
                $product = Product::find($item->product_id);
                if ($product) {
                    $vendor = User::find($product->user_id);
                    if ($vendor && $vendor->role_id == 1) {
                        $amount = $vendor->vendorAccount->pending_amount;
                        $vendor->vendorAccount()->update([
                            'pending_amount' => $amount - $item->g_total
                        ]);
                    } else {
                        $grand_total = $item->g_total;
                        $vendor_amount = $grand_total;
                        $admin_amount = Commission::where('order_id', $order->id)->first();
                        $adminAccount = VendorAccount::where('vendor_id', 1)->first();
                        $amount = $adminAccount->pending_amount;

                        $vendor->vendorAccount()->update([
                            'pending_amount' => $vendor->vendorAccount->pending_amount - $vendor_amount
                        ]);
                        $adminAccount->update([
                            'pending_amount' => $amount - $admin_amount->amount
                        ]);
                    }
                    
                    // === UPDATED STOCK INCREASE LOGIC ===
                    // Increase stock based on product variation type
                    $this->increaseProductStock($product, $item);
                }
            }
           
            $order->status = 2;
            $order->save();
            $user = User::find($order->user_id);
            $user->pen_point -= $order->point;
            if ($order->payment_method == 'wallate') {
                $user->wallate = $user->wallate + $order->total;
            }
            if ($user->cancel_attempt == 3) {
                $user->status = 0;
            } else {
                $user->cancel_attempt += 1;
            }
            $user->update();
            notify()->success("Order Cancel", "Congratulations");
            return redirect()->route('order');
        }
    }

    /**
     * Return command by user
     */
    public function return_req($id)
    {
        $order = Order::where('id', $id)->where('user_id', auth()->id())->firstOrFail();

        if ($order->status == 3) {
            foreach ($order->orderDetails as $item) {
                $product = Product::find($item->product_id);
                if ($product) {
                    $vendor = User::find($product->user_id);
                    if ($vendor && $vendor->role_id == 1) {
                        $amount = $vendor->vendorAccount->pending_amount;
                        $vendor->vendorAccount()->update([
                            'pending_amount' => $amount - $item->g_total
                        ]);
                    } else {
                        $grand_total = $item->g_total;
                        $vendor_amount = $grand_total;
                        $admin_amount = Commission::where('order_id', $order->id)->first();
                        $adminAccount = VendorAccount::where('vendor_id', 1)->first();
                        $amount = $adminAccount->pending_amount;

                        $vendor->vendorAccount()->update([
                            'pending_amount' => $vendor->vendorAccount->pending_amount - $vendor_amount
                        ]);
                        $adminAccount->update([
                            'pending_amount' => $amount - $admin_amount->amount
                        ]);
                    }

                    // === UPDATED STOCK INCREASE LOGIC ===
                    // Increase stock based on product variation type
                    $this->increaseProductStock($product, $item);
                }
            }

            $order->status = 6; // return status
            $order->save();
            $user = User::find($order->user_id);
            $user->pen_point -= $order->point;
            if ($order->payment_method == 'wallate') {
                $user->wallate = $user->wallate + $order->total;
            }
            if ($user->cancel_attempt == 3) {
                $user->status = 0;
            } else {
                $user->cancel_attempt += 1;
            }
            $user->update();
            notify()->success("Order Return", "Congratulations");
            return redirect()->route('order');
        }
    }

    /**
     * Ordered product review
     */
    public function review($orderId)
    {
        $order = Order::where('user_id', auth()->id())->where('order_id', $orderId)->firstOrFail();
        return view('frontend.review', compact('order'));
    }

    /**
     * Store product review
     */
    public function storeReview(Request $request, $id)
    {
        $this->validate($request, [
            'rating' => 'required|integer',
            'review' => 'required|string'
        ]);
        
        $check = Review::where('user_id', auth()->id())->where('product_id', $id)->first();
        if ($check) {
            notify()->warning("You are already review this product", "Sorry");
            return back();
        }

        $bookName = $bookName2 = $bookName3 = $bookName4 = $bookName5 = '';

        $book = $request->file('report');
        if ($book) {
            $bookName = $this->upload($book);
        }
        $book2 = $request->file('report2');
        if ($book2) {
            $bookName2 = $this->upload($book2);
        }
        $book3 = $request->file('report3');
        if ($book3) {
            $bookName3 = $this->upload($book3);
        }
        $book4 = $request->file('report4');
        if ($book4) {
            $bookName4 = $this->upload($book4);
        }
        $book5 = $request->file('report5');
        if ($book5) {
            $bookName5 = $this->upload($book5);
        }

        Review::create([
            'user_id'    => auth()->id(),
            'product_id' => $id,
            'rating'     => $request->rating,
            'file'       => $bookName,
            'file1'      => '',
            'file2'      => $bookName2,
            'file3'      => $bookName3,
            'file4'      => $bookName4,
            'file5'      => $bookName5,
            'body'       => $request->review
        ]);

        notify()->success("For your awesome review, enjoy and shopping now", "Thanks!!");
        return back();
    }

    /**
     * Upload file helper
     */
    public function upload($book)
    {
        $currentDate = Carbon::now()->toDateString();
        $bookName = $currentDate . '-' . uniqid() . '.' . $book->getClientOriginalExtension();
        if (!file_exists('uploads/review')) {
            mkdir('uploads/review', 0777, true);
        }
        $book->move(public_path('uploads/review'), $bookName);
        return $bookName;
    }

    /**
     * Show download view file
     */
    public function download()
    {
        $orders = auth()->user()->orders;
        $items = [];
        foreach ($orders as $order) {
            foreach ($order->orderDetails as $item) {
                $items[] = $item;
            }
        }
        return view('frontend.download', compact('items'));
    }

    /**
     * Download product file
     */
    public function downloadProductFile($pro_id, $id)
    {
        $download = DownloadProduct::findOrFail($id);
        $product  = Product::where('id', $pro_id)->where('download_able', true)->firstOrFail();
        $check    = DownloadUserProduct::where('user_id', auth()->id())
                                    ->where('product_id', $product->id)
                                    ->where('download_id', $id)
                                    ->count();
                                    
        if ($product->download_expire < date('Y-m-d')) {
            notify()->warning("Download date expired", "Date Expired");
            return back();
        }
        
        if ($check < $product->download_limit) {
            if ($download->url == NULL) {
                if (file_exists('uploads/product/download/'.$download->file)) {
                    DownloadUserProduct::create([
                        'user_id'     => auth()->id(),
                        'product_id'  => $product->id,
                        'download_id' => $id
                    ]);
                    return response()->download(public_path('uploads/product/download/'.$download->file));
                } else {
                    notify()->error("File not found", "Not Found");
                    return back();
                }
            } else {
                DownloadUserProduct::create([
                    'user_id'     => auth()->id(),
                    'product_id'  => $product->id,
                    'download_id' => $id
                ]);
                return redirect()->to($download->url);
            }
        } else {
            notify()->warning("Already you have been download ".$product->download_limit." time", "Download Expired");
            return back();
        }
    }

    /**
     * Validation helper
     */
    protected function validate($request, $rules)
    {
        return $request->validate($rules);
    }

    /**
     * Increase stock for product based on variation type (for cancellations/returns)
     * Handles: Color-Size, Size-Only, Attributes, and Simple products
     */
    private function increaseProductStock($product, $item)
    {
        $quantity = $item->qty;
        
        // Decode size/color data from order item
        $colorId = $item->color ?? null;
        $sizeData = json_decode($item->size, true);
        
        // Priority 1: Check for Color-Size variation
        if ($colorId && !empty($sizeData)) {
            foreach ($sizeData as $sizeInfo) {
                if (isset($sizeInfo['size_id'])) {
                    $sizeId = $sizeInfo['size_id'];
                    
                    // Increase in color_size_product table
                    DB::table('color_size_product')
                        ->where('product_id', $product->id)
                        ->where('color_id', $colorId)
                        ->where('size_id', $sizeId)
                        ->increment('quantity', $quantity);
                    
                    \Log::info('Stock increased: Color-Size (Cancel/Return)', [
                        'product_id' => $product->id,
                        'color_id' => $colorId,
                        'size_id' => $sizeId,
                        'quantity' => $quantity
                    ]);
                    break;
                }
            }
        }
        // Priority 2: Check for Size-Only variation (no color)
        elseif (empty($colorId) && !empty($sizeData)) {
            foreach ($sizeData as $sizeInfo) {
                if (isset($sizeInfo['size_id'])) {
                    $sizeId = $sizeInfo['size_id'];
                    
                    // Increase in color_size_product table where color_id IS NULL
                    DB::table('color_size_product')
                        ->where('product_id', $product->id)
                        ->where('size_id', $sizeId)
                        ->whereNull('color_id')
                        ->increment('quantity', $quantity);
                    
                    \Log::info('Stock increased: Size-Only (Cancel/Return)', [
                        'product_id' => $product->id,
                        'size_id' => $sizeId,
                        'quantity' => $quantity
                    ]);
                    break;
                }
            }
        }
        // Priority 3: Check for Attribute variation
        elseif (!empty($sizeData)) {
            foreach ($sizeData as $attrInfo) {
                if (isset($attrInfo['attribute_value_id'])) {
                    $attributeValueId = $attrInfo['attribute_value_id'];
                    
                    // Increase in attribute_product table
                    DB::table('attribute_product')
                        ->where('product_id', $product->id)
                        ->where('attribute_value_id', $attributeValueId)
                        ->increment('qnty', $quantity);
                    
                    \Log::info('Stock increased: Attribute (Cancel/Return)', [
                        'product_id' => $product->id,
                        'attribute_value_id' => $attributeValueId,
                        'quantity' => $quantity
                    ]);
                    break;
                }
            }
        }
        
        // Always increase main product quantity (for all product types)
        $product->increment('quantity', $quantity);
        
        \Log::info('Stock increased: Main product quantity (Cancel/Return)', [
            'product_id' => $product->id,
            'quantity' => $quantity,
            'new_stock' => $product->fresh()->quantity
        ]);
    }

}