<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;
use Exception;
use App\Models\Order;

class SteadfastCourierService
{
    protected $baseUrl = 'https://portal.packzy.com/api/v1';
    protected $apiKey;
    protected $secretKey;

    public function __construct($apiKey = null, $secretKey = null)
    {
        $this->apiKey = $apiKey ?? setting('STEADFAST_API_KEY');
        $this->secretKey = $secretKey ?? setting('STEADFAST_SECRET_KEY');
    }

    /**
     * Create a single order
     */
    public function createOrder($invoice, $recipientName, $recipientPhone, $recipientAddress, $codAmount, $note = null)
    {
        try {
            $client = new Client();

            $response = $client->post($this->baseUrl . '/create_order', [
                'headers' => [
                    'Api-Key' => $this->apiKey,
                    'Secret-Key' => $this->secretKey,
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'invoice' => $invoice,
                    'recipient_name' => $recipientName,
                    'recipient_phone' => $recipientPhone,
                    'recipient_address' => $recipientAddress,
                    'cod_amount' => $codAmount,
                    'note' => $note,
                ],
                'timeout' => 30,
            ]);

            $result = json_decode($response->getBody(), true);
            
            Log::info('Steadfast Order Created', [
                'invoice' => $invoice,
                'response' => $result
            ]);

            return $result;

        } catch (Exception $e) {
            Log::error('Steadfast Order Creation Failed', [
                'invoice' => $invoice,
                'error' => $e->getMessage()
            ]);
            
            return [
                'status' => 500,
                'message' => 'Failed to create order: ' . $e->getMessage(),
                'consignment' => null
            ];
        }
    }

    /**
     * Check delivery status by consignment ID
     */
    public function getStatusByConsignmentId($consignmentId)
    {
        try {
            $client = new Client();

            $response = $client->get($this->baseUrl . '/status_by_cid/' . $consignmentId, [
                'headers' => [
                    'Api-Key' => $this->apiKey,
                    'Secret-Key' => $this->secretKey,
                    'Content-Type' => 'application/json',
                ],
                'timeout' => 30,
            ]);

            $result = json_decode($response->getBody(), true);
            
            Log::info('Steadfast Status Check by Consignment ID', [
                'consignment_id' => $consignmentId,
                'response' => $result
            ]);

            return $result;

        } catch (Exception $e) {
            Log::error('Steadfast Status Check Failed', [
                'consignment_id' => $consignmentId,
                'error' => $e->getMessage()
            ]);
            
            return [
                'status' => 500,
                'delivery_status' => 'unknown',
                'message' => 'Failed to check status: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Check delivery status by invoice
     */
    public function getStatusByInvoice($invoice)
    {
        try {
            $client = new Client();

            $response = $client->get($this->baseUrl . '/status_by_invoice/' . $invoice, [
                'headers' => [
                    'Api-Key' => $this->apiKey,
                    'Secret-Key' => $this->secretKey,
                    'Content-Type' => 'application/json',
                ],
                'timeout' => 30,
            ]);

            $result = json_decode($response->getBody(), true);
            
            Log::info('Steadfast Status Check by Invoice', [
                'invoice' => $invoice,
                'response' => $result
            ]);

            return $result;

        } catch (Exception $e) {
            Log::error('Steadfast Status Check by Invoice Failed', [
                'invoice' => $invoice,
                'error' => $e->getMessage()
            ]);
            
            return [
                'status' => 500,
                'delivery_status' => 'unknown',
                'message' => 'Failed to check status: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Check delivery status by tracking code
     */
    public function getStatusByTrackingCode($trackingCode)
    {
        try {
            $client = new Client();

            $response = $client->get($this->baseUrl . '/status_by_trackingcode/' . $trackingCode, [
                'headers' => [
                    'Api-Key' => $this->apiKey,
                    'Secret-Key' => $this->secretKey,
                    'Content-Type' => 'application/json',
                ],
                'timeout' => 30,
            ]);

            $result = json_decode($response->getBody(), true);
            
            Log::info('Steadfast Status Check by Tracking Code', [
                'tracking_code' => $trackingCode,
                'response' => $result
            ]);

            return $result;

        } catch (Exception $e) {
            Log::error('Steadfast Status Check by Tracking Code Failed', [
                'tracking_code' => $trackingCode,
                'error' => $e->getMessage()
            ]);
            
            return [
                'status' => 500,
                'delivery_status' => 'unknown',
                'message' => 'Failed to check status: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Get current balance
     */
    public function getCurrentBalance()
    {
        try {
            $client = new Client();

            $response = $client->get($this->baseUrl . '/get_balance', [
                'headers' => [
                    'Api-Key' => $this->apiKey,
                    'Secret-Key' => $this->secretKey,
                    'Content-Type' => 'application/json',
                ],
                'timeout' => 30,
            ]);

            $result = json_decode($response->getBody(), true);
            
            Log::info('Steadfast Balance Check', [
                'response' => $result
            ]);

            return $result;

        } catch (Exception $e) {
            Log::error('Steadfast Balance Check Failed', [
                'error' => $e->getMessage()
            ]);
            
            return [
                'status' => 500,
                'current_balance' => 0,
                'message' => 'Failed to check balance: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Create bulk orders
     */
    public function bulkCreate($ordersData)
    {
        try {
            $client = new Client();

            $response = $client->post($this->baseUrl . '/create_order/bulk-order', [
                'headers' => [
                    'Api-Key' => $this->apiKey,
                    'Secret-Key' => $this->secretKey,
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'data' => $ordersData,
                ],
                'timeout' => 60, // Longer timeout for bulk operations
            ]);

            $result = json_decode($response->getBody(), true);
            
            Log::info('Steadfast Bulk Order Created', [
                'orders_count' => count($ordersData),
                'response' => $result
            ]);

            return $result;

        } catch (Exception $e) {
            Log::error('Steadfast Bulk Order Creation Failed', [
                'orders_count' => count($ordersData),
                'error' => $e->getMessage()
            ]);
            
            return [
                'status' => 500,
                'message' => 'Failed to create bulk orders: ' . $e->getMessage(),
                'data' => []
            ];
        }
    }

    /**
     * Fetch delivery status by trying consignment ID, tracking code, then invoice
     * and map to raw and friendly text
     *
     * @param Order $order
     * @return array ['raw' => string|null, 'text' => string]
     */
    public function fetchAndMapStatus(Order $order): array
    {
        $response = [];

        if (!empty($order->consignment_id)) {
            $response = $this->getStatusByConsignmentId($order->consignment_id);
        }

        if ((empty($response['status']) || $response['status'] !== 200) && !empty($order->tracking_code)) {
            $response = $this->getStatusByTrackingCode($order->tracking_code);
        }

        if ((empty($response['status']) || $response['status'] !== 200) && !empty($order->invoice)) {
            $response = $this->getStatusByInvoice($order->invoice);
        }

        $rawStatus = $response['delivery_status'] ?? null;
        $text      = self::getDeliveryStatusText($rawStatus);

        return ['raw' => $rawStatus, 'text' => $text];
    }

    /**
     * Get human-readable delivery status
     */
    public static function getDeliveryStatusText($status)
    {
        $statuses = [
            'pending' => 'Pending',
            'delivered_approval_pending' => 'Delivered (Approval Pending)',
            'partial_delivered_approval_pending' => 'Partially Delivered (Approval Pending)',
            'cancelled_approval_pending' => 'Cancelled (Approval Pending)',
            'unknown_approval_pending' => 'Unknown (Approval Pending)',
            'delivered' => 'Delivered',
            'partial_delivered' => 'Partially Delivered',
            'cancelled' => 'Cancelled',
            'hold' => 'On Hold',
            'in_review' => 'In Review',
            'unknown' => 'Unknown',
        ];

        return $statuses[$status] ?? 'Unknown Status';
    }

    /**
     * Get status badge HTML for display
     */
    public static function getStatusBadge($status)
    {
        $badges = [
            'pending' => '<span class="badge badge-warning">Pending</span>',
            'delivered_approval_pending' => '<span class="badge badge-info">Delivered (Approval Pending)</span>',
            'partial_delivered_approval_pending' => '<span class="badge badge-info">Partially Delivered (Approval Pending)</span>',
            'cancelled_approval_pending' => '<span class="badge badge-secondary">Cancelled (Approval Pending)</span>',
            'unknown_approval_pending' => '<span class="badge badge-secondary">Unknown (Approval Pending)</span>',
            'delivered' => '<span class="badge badge-success">Delivered</span>',
            'partial_delivered' => '<span class="badge badge-primary">Partially Delivered</span>',
            'cancelled' => '<span class="badge badge-danger">Cancelled</span>',
            'hold' => '<span class="badge badge-warning">On Hold</span>',
            'in_review' => '<span class="badge badge-info">In Review</span>',
            'unknown' => '<span class="badge badge-secondary">Unknown</span>',
        ];

        return $badges[$status] ?? '<span class="badge badge-secondary">Unknown Status</span>';
    }

    /**
     * Check if service is properly configured
     */
    public function isConfigured()
    {
        return !empty($this->apiKey) && !empty($this->secretKey);
    }
}