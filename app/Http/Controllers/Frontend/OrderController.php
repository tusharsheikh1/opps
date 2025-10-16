<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Frontend\OrderEssential\OrderDisplayService;
use App\Http\Controllers\Frontend\OrderEssential\OrderCreationService;
use App\Http\Controllers\Frontend\OrderEssential\BuyNowService;
use App\Http\Controllers\Frontend\OrderEssential\PaymentService;
use App\Http\Controllers\Frontend\OrderEssential\OrderHelperService;
use App\Services\OrderIntervalService;
use App\Jobs\SendOrderConfirmationSms;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class OrderController extends Controller
{
    protected $orderDisplayService;
    protected $orderCreationService;
    protected $buyNowService;
    protected $paymentService;
    protected $orderHelperService;
    protected $orderIntervalService;

    public function __construct(
        OrderDisplayService $orderDisplayService,
        OrderCreationService $orderCreationService,
        BuyNowService $buyNowService,
        PaymentService $paymentService,
        OrderHelperService $orderHelperService,
        OrderIntervalService $orderIntervalService
    ) {
        $this->orderDisplayService = $orderDisplayService;
        $this->orderCreationService = $orderCreationService;
        $this->buyNowService = $buyNowService;
        $this->paymentService = $paymentService;
        $this->orderHelperService = $orderHelperService;
        $this->orderIntervalService = $orderIntervalService;
    }

    /**
     * Enhanced device-based order interval check for guest users (AJAX endpoint)
     * PREVENTS phone number changes from bypassing restrictions
     * NEW: Also checks product-specific restrictions (same product within 10 days using device fingerprint and IP)
     */
    public function checkOrderInterval(Request $request)
    {
        // Only for guests
        if (auth()->check()) {
            return response()->json(['restricted' => false]);
        }

        $request->validate([
            'phone' => 'required|string|min:10|max:15',
            'email' => 'nullable|email|string|max:255',
            'product_id' => 'nullable|integer' // New field for product restriction check
        ]);

        $phone = $request->phone;
        $email = $request->email ?: 'noreply@lems.shop';
        $productId = $request->product_id;

        // First check: Product-specific restriction (10 days for same product using device/IP/phone)
        if ($productId) {
            $productRestriction = $this->orderIntervalService->checkProductRestriction($phone, $productId, $email);
            
            if ($productRestriction['restricted']) {
                \Log::info('Product-specific order restriction applied', [
                    'phone' => $phone,
                    'product_id' => $productId,
                    'ip_address' => request()->ip(),
                    'restriction_type' => 'product',
                    'restriction_reason' => $productRestriction['restriction_reason'],
                    'remaining_days' => $productRestriction['remaining_days'],
                    'last_order_date' => $productRestriction['last_order_date'],
                    'device_matched' => $productRestriction['device_matched'] ?? false,
                    'ip_matched' => $productRestriction['ip_matched'] ?? false,
                ]);

                // Dynamic message based on restriction reason
                $message = "দুঃখিত! ";
                
                switch ($productRestriction['restriction_reason']) {
                    case 'device':
                        if ($productRestriction['last_order_phone'] !== $phone) {
                            $message .= "এই ডিভাইস থেকে {$productRestriction['last_order_phone']} নম্বর দিয়ে এই পণ্যটি {$productRestriction['last_order_date']} তারিখে অর্ডার করা হয়েছে। ফোন নম্বর পরিবর্তন করেও একই পণ্য অর্ডার করা যাবে না।";
                        } else {
                            $message .= "আপনি এই পণ্যটি {$productRestriction['last_order_date']} তারিখে অর্ডার করেছেন।";
                        }
                        break;
                    case 'ip':
                        $message .= "এই নেটওয়ার্ক থেকে এই পণ্যটি {$productRestriction['last_order_date']} তারিখে অর্ডার করা হয়েছে।";
                        break;
                    default: // phone
                        $message .= "আপনি এই পণ্যটি {$productRestriction['last_order_date']} তারিখে অর্ডার করেছেন।";
                }
                
                $message .= " একই পণ্য {$productRestriction['remaining_days']} দিন পর অর্ডার করতে পারবেন। নতুন অর্ডারের জন্য আমাদের WhatsApp {$productRestriction['whatsapp_number']} এ যোগাযোগ করুন।";
                
                return response()->json([
                    'restricted' => true,
                    'restriction_type' => 'product',
                    'restriction_reason' => $productRestriction['restriction_reason'],
                    'remaining_days' => $productRestriction['remaining_days'],
                    'last_order_date' => $productRestriction['last_order_date'],
                    'next_allowed_date' => $productRestriction['next_allowed_date'],
                    'whatsapp_number' => $productRestriction['whatsapp_number'],
                    'message' => $message,
                    'order_id' => $productRestriction['order_id'] ?? null,
                    'last_order_phone' => $productRestriction['last_order_phone'] ?? null,
                    'device_matched' => $productRestriction['device_matched'] ?? false,
                    'ip_matched' => $productRestriction['ip_matched'] ?? false,
                ]);
            }
        }

        // Second check: Device-based restriction (prevents phone number changes)
        $restriction = $this->orderIntervalService->checkOrderRestriction($phone, $email);
        
        if ($restriction['restricted']) {
            // Enhanced logging for device-based restrictions
            \Log::info('Device-based order restriction applied', [
                'phone' => $phone,
                'email' => $email,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'reason' => $restriction['restriction_reason'] ?? 'Device-based interval restriction',
                'remaining_seconds' => $restriction['remaining_seconds'],
                'phone_changed' => $restriction['phone_changed'] ?? false,
                'last_phone' => $restriction['last_phone'] ?? null,
                'current_phone' => $restriction['current_phone'] ?? null,
            ]);

            $remainingMinutes = ceil($restriction['remaining_seconds'] / 60);
            
            // Dynamic message based on phone change detection
            $message = "অপেক্ষা করুন! ";
            
            if (isset($restriction['phone_changed']) && $restriction['phone_changed']) {
                $lastPhone = $restriction['last_phone'] ?? 'অজানা';
                $message .= "এই ডিভাইস থেকে সম্প্রতি {$lastPhone} নম্বর দিয়ে অর্ডার করা হয়েছে। ফোন নম্বর পরিবর্তন করে অর্ডার করা যাবে না। ";
            } else {
                $message .= "এই ডিভাইস থেকে ইতিমধ্যে একটা অর্ডার করা হয়েছে। ";
            }
            
            $message .= "আপনি {$remainingMinutes} মিনিট পর আবার অর্ডার করতে পারবেন। এটি ভুয়া অর্ডার প্রতিরোধের জন্য। অর্ডারের যেকোন পরিবর্তনের জন্য আমাদের WhatsApp {$restriction['whatsapp_number']} এ নক করুন।";
            
            return response()->json([
                'restricted' => true,
                'restriction_type' => 'device',
                'remaining_seconds' => $restriction['remaining_seconds'],
                'whatsapp_number' => $restriction['whatsapp_number'],
                'phone_changed' => $restriction['phone_changed'] ?? false,
                'last_phone' => $restriction['last_phone'] ?? null,
                'current_phone' => $restriction['current_phone'] ?? null,
                'message' => $message
            ]);
        }

        return response()->json(['restricted' => false]);
    }

    /**
     * Enhanced device-based spam detection endpoint
     */
    public function checkSpamRisk(Request $request)
    {
        // Only for guests
        if (auth()->check()) {
            return response()->json(['risk_level' => 'low']);
        }

        $request->validate([
            'phone' => 'required|string',
            'email' => 'nullable|email'
        ]);

        $identifiers = [
            'phone' => $request->phone,
            'email' => $request->email ?: 'noreply@lems.shop',
            'ip_address' => request()->ip(),
            'fingerprint' => $this->orderIntervalService->generateUserFingerprint(),
        ];

        $spamCheck = $this->orderIntervalService->checkSpammerFlags($identifiers);

        $riskLevel = 'low';
        if ($spamCheck['risk_score'] > 70) {
            $riskLevel = 'high';
        } elseif ($spamCheck['risk_score'] > 40) {
            $riskLevel = 'medium';
        }

        // Log high-risk attempts with device information
        if ($riskLevel === 'high') {
            \Log::warning('High-risk device activity detected', [
                'phone' => $request->phone,
                'email' => $request->email,
                'ip_address' => request()->ip(),
                'risk_score' => $spamCheck['risk_score'],
                'flags' => $spamCheck['flags'],
                'user_agent' => request()->userAgent(),
                'device_order_count' => $spamCheck['device_order_count'] ?? 0,
                'phone_variations' => $spamCheck['phone_variations'] ?? 0,
            ]);
        }

        return response()->json([
            'risk_level' => $riskLevel,
            'risk_score' => $spamCheck['risk_score'],
            'flags' => $spamCheck['flags'],
            'is_flagged' => $spamCheck['is_flagged'],
            'device_order_count' => $spamCheck['device_order_count'] ?? 0,
            'phone_variations' => $spamCheck['phone_variations'] ?? 0,
        ]);
    }

    /**
     * Customer order show
     */
    public function order()
    {
        return $this->orderDisplayService->order();
    }

    /**
     * Show returns page
     */
    public function returns()
    {
        return $this->orderDisplayService->returns();
    }

    /**
     * Store guest order (minimal) with device-based tracking
     */
    public function orderStore_minimal(Request $request)
    {
        // Device-based pre-order validation for guests
        if (!auth()->check()) {
            $restriction = $this->orderIntervalService->checkOrderRestriction(
                $request->phone, 
                $request->email ?: 'noreply@lems.shop'
            );
            
            if ($restriction['restricted']) {
                $remainingMinutes = ceil($restriction['remaining_seconds'] / 60);
                
                // Dynamic message based on phone change detection
                $message = "অপেক্ষা করুন! ";
                
                if (isset($restriction['phone_changed']) && $restriction['phone_changed']) {
                    $lastPhone = $restriction['last_phone'] ?? 'অজানা';
                    $message .= "এই ডিভাইস থেকে সম্প্রতি {$lastPhone} নম্বর দিয়ে অর্ডার করা হয়েছে। ফোন নম্বর পরিবর্তন করে অর্ডার করা যাবে না। ";
                } else {
                    $message .= "এই ডিভাইস থেকে ইতিমধ্যে একটা অর্ডার করা হয়েছে। ";
                }
                
                $message .= "আপনি {$remainingMinutes} মিনিট পর আবার অর্ডার করতে পারবেন।";
                
                notify()->warning($message, "ডিভাইস সীমাবদ্ধতা");
                return redirect()->back()->withInput();
            }
        }

        $result = $this->orderCreationService->orderStore_minimal($request);
        
        // Record device-based order placement
        if (!auth()->check()) {
            $this->orderIntervalService->recordOrderPlacement(
                $request->phone,
                $request->email ?: 'noreply@lems.shop'
            );
        }

        return $result;
    }

    /**
     * Store guest order with device-based tracking
     */
    public function orderStore_guest(Request $request)
    {
        // Device-based pre-order validation for guests
        $restriction = $this->orderIntervalService->checkOrderRestriction(
            $request->phone, 
            $request->email ?: 'noreply@lems.shop'
        );
        
        if ($restriction['restricted']) {
            $remainingMinutes = ceil($restriction['remaining_seconds'] / 60);
            
            // Dynamic message based on phone change detection
            $message = "অপেক্ষা করুন! ";
            
            if (isset($restriction['phone_changed']) && $restriction['phone_changed']) {
                $lastPhone = $restriction['last_phone'] ?? 'অজানা';
                $message .= "এই ডিভাইস থেকে সম্প্রতি {$lastPhone} নম্বর দিয়ে অর্ডার করা হয়েছে। ফোন নম্বর পরিবর্তন করে অর্ডার করা যাবে না। ";
            } else {
                $message .= "এই ডিভাইস থেকে ইতিমধ্যে একটা অর্ডার করা হয়েছে। ";
            }
            
            $message .= "আপনি {$remainingMinutes} মিনিট পর আবার অর্ডার করতে পারবেন।";
            
            notify()->warning($message, "ডিভাইস সীমাবদ্ধতা");
            return redirect()->back()->withInput();
        }

        $result = $this->orderCreationService->orderStore_guest($request);
        
        // Record device-based order placement
        $this->orderIntervalService->recordOrderPlacement(
            $request->phone,
            $request->email ?: 'noreply@lems.shop'
        );

        return $result;
    }

    /**
     * Store customer order
     */
    public function orderStore(Request $request)
    {
        return $this->orderCreationService->orderStore($request);
    }

    /**
     * Store customer buy now order (minimal) with device-based tracking
     */
    public function orderBuyNowStore_minimal(Request $request)
    {
        // Device-based pre-order validation for guests
        if (!auth()->check()) {
            $restriction = $this->orderIntervalService->checkOrderRestriction(
                $request->phone, 
                $request->email ?: 'noreply@lems.shop'
            );
            
            if ($restriction['restricted']) {
                $remainingMinutes = ceil($restriction['remaining_seconds'] / 60);
                
                // Dynamic message based on phone change detection
                $message = "অপেক্ষা করুন! ";
                
                if (isset($restriction['phone_changed']) && $restriction['phone_changed']) {
                    $lastPhone = $restriction['last_phone'] ?? 'অজানা';
                    $message .= "এই ডিভাইস থেকে সম্প্রতি {$lastPhone} নম্বর দিয়ে অর্ডার করা হয়েছে। ফোন নম্বর পরিবর্তন করে অর্ডার করা যাবে না। ";
                } else {
                    $message .= "এই ডিভাইস থেকে ইতিমধ্যে একটা অর্ডার করা হয়েছে। ";
                }
                
                $message .= "আপনি {$remainingMinutes} মিনিট পর আবার অর্ডার করতে পারবেন।";
                
                notify()->warning($message, "ডিভাইস সীমাবদ্ধতা");
                return redirect()->back()->withInput();
            }
        }

        $result = $this->buyNowService->orderBuyNowStore_minimal($request);
        
        // Record device-based order placement
        if (!auth()->check()) {
            $this->orderIntervalService->recordOrderPlacement(
                $request->phone,
                $request->email ?: 'noreply@lems.shop'
            );
        }

        return $result;
    }

    /**
     * Store customer buy now order (guest) with device-based tracking
     */
    public function orderBuyNowStore_guest(Request $request)
    {
        // Device-based pre-order validation for guests
        $restriction = $this->orderIntervalService->checkOrderRestriction(
            $request->phone, 
            $request->email ?: 'noreply@lems.shop'
        );
        
        if ($restriction['restricted']) {
            $remainingMinutes = ceil($restriction['remaining_seconds'] / 60);
            
            // Dynamic message based on phone change detection
            $message = "অপেক্ষা করুন! ";
            
            if (isset($restriction['phone_changed']) && $restriction['phone_changed']) {
                $lastPhone = $restriction['last_phone'] ?? 'অজানা';
                $message .= "এই ডিভাইস থেকে সম্প্রতি {$lastPhone} নম্বর দিয়ে অর্ডার করা হয়েছে। ফোন নম্বর পরিবর্তন করে অর্ডার করা যাবে না। ";
            } else {
                $message .= "এই ডিভাইস থেকে ইতিমধ্যে একটা অর্ডার করা হয়েছে। ";
            }
            
            $message .= "আপনি {$remainingMinutes} মিনিট পর আবার অর্ডার করতে পারবেন।";
            
            notify()->warning($message, "ডিভাইস সীমাবদ্ধতা");
            return redirect()->back()->withInput();
        }

        $result = $this->buyNowService->orderBuyNowStore_guest($request);
        
        // Record device-based order placement
        $this->orderIntervalService->recordOrderPlacement(
            $request->phone,
            $request->email ?: 'noreply@lems.shop'
        );

        return $result;
    }

    /**
     * Store customer buy now order
     */
    public function orderBuyNowStore(Request $request)
    {
        return $this->buyNowService->orderBuyNowStore($request);
    }

    /**
     * Buy product
     */
    public function buyProduct(Request $request)
    {
        return $this->buyNowService->buyProduct($request);
    }

    /**
     * Order invoice print
     */
    public function orderInvoice($id)
    {
        return $this->orderDisplayService->orderInvoice($id);
    }

    /**
     * Cancel order
     */
    public function cancel($id)
    {
        return $this->orderDisplayService->cancel($id);
    }

    /**
     * Return request by user
     */
    public function return_req($id)
    {
        return $this->orderDisplayService->return_req($id);
    }

    /**
     * Ordered product review
     */
    public function review($orderId)
    {
        return $this->orderDisplayService->review($orderId);
    }

    /**
     * Store product review
     */
    public function storeReview(Request $request, $id)
    {
        return $this->orderDisplayService->storeReview($request, $id);
    }

    /**
     * Show download view file
     */
    public function download()
    {
        return $this->orderDisplayService->download();
    }

    /**
     * Download product file
     */
    public function downloadProductFile($pro_id, $id)
    {
        return $this->orderDisplayService->downloadProductFile($pro_id, $id);
    }

    /**
     * Payment form
     */
    public function payform($slug)
    {
        return $this->paymentService->payform($slug);
    }

    /**
     * Create payment
     */
    public function payCreate(Request $request, $slug)
    {
        return $this->paymentService->payCreate($request, $slug);
    }

    /**
     * UddoktaPay processing
     */
    public function uddokpay($request, $amn, $id)
    {
        return $this->paymentService->uddokpay($request, $amn, $id);
    }

    /**
     * AamarPay processing
     */
    public function paynow($request, $amn, $id)
    {
        return $this->paymentService->paynow($request, $amn, $id);
    }

    /**
     * UddoktaPay webhook
     */
    public function webhook(Request $request)
    {
        return $this->paymentService->webhook($request);
    }

    /**
     * UddoktaPay success
     */
    public function success2(Request $request)
    {
        return $this->paymentService->success2($request);
    }

    /**
     * AamarPay success
     */
    public function success(Request $request)
    {
        return $this->paymentService->success($request);
    }

    /**
     * UddoktaPay cancel
     */
    public function cancel2()
    {
        return $this->paymentService->cancel2();
    }

    /**
     * AamarPay fail
     */
    public function fail(Request $request)
    {
        return $this->paymentService->fail($request);
    }

    /**
     * Admin function to get device-based order restriction statistics
     */
    public function getOrderRestrictionStats()
    {
        if (!auth()->check() || !auth()->user()->hasRole('admin')) {
            abort(403);
        }

        $stats = $this->orderIntervalService->getRestrictionStats();
        
        // Add device-specific statistics
        $cutoffTime = Carbon::now()->subHours(24);
        
        $deviceStats = [
            'devices_with_multiple_phones' => Order::whereNull('user_id')
                ->where('created_at', '>=', $cutoffTime)
                ->select('ip_address', 'browser_fingerprint', DB::raw('COUNT(DISTINCT phone) as phone_count'))
                ->groupBy('ip_address', 'browser_fingerprint')
                ->having('phone_count', '>', 1)
                ->count(),
                
            'most_active_device' => Order::whereNull('user_id')
                ->where('created_at', '>=', $cutoffTime)
                ->select('ip_address', 'browser_fingerprint', DB::raw('COUNT(*) as order_count'), DB::raw('COUNT(DISTINCT phone) as phone_count'))
                ->groupBy('ip_address', 'browser_fingerprint')
                ->orderBy('order_count', 'desc')
                ->first(),
                
            'phone_change_attempts' => Order::whereNull('user_id')
                ->where('created_at', '>=', $cutoffTime)
                ->select('ip_address', 'browser_fingerprint', DB::raw('COUNT(DISTINCT phone) as phone_count'))
                ->groupBy('ip_address', 'browser_fingerprint')
                ->having('phone_count', '>', 2)
                ->sum('phone_count'),
        ];
        
        return response()->json(array_merge($stats, $deviceStats));
    }

    /**
     * Admin function to remove device-based restriction
     */
    public function removeOrderRestriction(Request $request)
    {
        if (!auth()->check() || !auth()->user()->hasRole('admin')) {
            abort(403);
        }

        $request->validate([
            'identifier' => 'required|string',
            'type' => 'required|in:device,phone,ip,fingerprint'
        ]);

        $result = $this->orderIntervalService->removeRestriction(
            $request->identifier,
            $request->type
        );

        $message = $result ? 'Device restriction removed successfully' : 'Failed to remove device restriction';
        
        if ($result) {
            \Log::info('Device-based order restriction removed by admin', [
                'admin_id' => auth()->id(),
                'admin_name' => auth()->user()->name,
                'identifier' => $request->identifier,
                'type' => $request->type,
                'timestamp' => now(),
            ]);
        }

        return response()->json([
            'success' => $result,
            'message' => $message
        ]);
    }

    /**
     * Admin function to get device activity report
     */
    public function getDeviceActivityReport(Request $request)
    {
        if (!auth()->check() || !auth()->user()->hasRole('admin')) {
            abort(403);
        }

        $hours = $request->get('hours', 24);
        $cutoffTime = Carbon::now()->subHours($hours);
        
        $deviceActivity = Order::whereNull('user_id')
            ->where('created_at', '>=', $cutoffTime)
            ->select(
                'ip_address',
                'browser_fingerprint',
                DB::raw('COUNT(*) as total_orders'),
                DB::raw('COUNT(DISTINCT phone) as unique_phones'),
                DB::raw('MIN(created_at) as first_order'),
                DB::raw('MAX(created_at) as last_order'),
                DB::raw('GROUP_CONCAT(DISTINCT phone ORDER BY created_at) as phone_sequence')
            )
            ->groupBy('ip_address', 'browser_fingerprint')
            ->orderBy('unique_phones', 'desc')
            ->limit(50)
            ->get();
            
        $report = [
            'summary' => [
                'total_devices' => $deviceActivity->count(),
                'devices_with_phone_changes' => $deviceActivity->where('unique_phones', '>', 1)->count(),
                'highest_phone_variation' => $deviceActivity->max('unique_phones'),
                'period_hours' => $hours,
            ],
            'suspicious_devices' => $deviceActivity->where('unique_phones', '>', 2)->values(),
            'active_devices' => $deviceActivity->where('total_orders', '>', 5)->values(),
        ];
        
        return response()->json($report);
    }
}