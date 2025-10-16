<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Order;
use App\Services\SteadfastCourierService;
use Illuminate\Support\Facades\Log;

class SyncDeliveryStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'orders:sync-delivery-status {--order_id=} {--limit=50}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync delivery status from Steadfast courier for orders';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Starting delivery status sync...');

        $steadfastService = new SteadfastCourierService();
        
        if (!$steadfastService->isConfigured()) {
            $this->error('Steadfast courier service is not configured properly');
            return 1;
        }

        // Get orders to sync
        $query = Order::sentToCourier()
                     ->whereNotIn('delivery_status', ['delivered', 'cancelled', 'partial_delivered']);

        // If specific order ID is provided
        if ($this->option('order_id')) {
            $query->where('id', $this->option('order_id'));
        } else {
            // Limit the number of orders to process
            $query->limit($this->option('limit'));
        }

        $orders = $query->get();

        if ($orders->isEmpty()) {
            $this->info('No orders found to sync');
            return 0;
        }

        $this->info("Found {$orders->count()} orders to sync");

        $successCount = 0;
        $errorCount = 0;

        $progressBar = $this->output->createProgressBar($orders->count());

        foreach ($orders as $order) {
            try {
                $this->line("\nSyncing order: {$order->invoice}");

                // Get status from Steadfast
                $response = $steadfastService->getStatusByConsignmentId($order->consignment_id);
                
                if ($response['status'] !== 200) {
                    // Try by invoice as fallback
                    $response = $steadfastService->getStatusByInvoice($order->invoice);
                }

                if ($response['status'] === 200 && isset($response['delivery_status'])) {
                    $oldStatus = $order->delivery_status;
                    $newStatus = $response['delivery_status'];

                    $order->update([
                        'delivery_status' => $newStatus,
                        'delivery_status_updated_at' => now(),
                        'delivery_response' => $response,
                    ]);

                    if ($oldStatus !== $newStatus) {
                        $this->info("  Status updated: {$oldStatus} → {$newStatus}");
                        
                        // Log status change
                        Log::info('Delivery status updated', [
                            'order_id' => $order->id,
                            'invoice' => $order->invoice,
                            'old_status' => $oldStatus,
                            'new_status' => $newStatus,
                            'consignment_id' => $order->consignment_id,
                        ]);

                        // Update internal order status based on delivery status
                        $this->updateInternalOrderStatus($order, $newStatus);
                    } else {
                        $this->line("  Status unchanged: {$newStatus}");
                    }

                    $successCount++;
                } else {
                    $this->error("  Failed to get status: " . ($response['message'] ?? 'Unknown error'));
                    $errorCount++;
                }

            } catch (\Exception $e) {
                $this->error("  Error syncing order {$order->invoice}: " . $e->getMessage());
                $errorCount++;
                
                Log::error('Delivery status sync error', [
                    'order_id' => $order->id,
                    'invoice' => $order->invoice,
                    'error' => $e->getMessage(),
                ]);
            }

            $progressBar->advance();
            
            // Small delay to avoid overwhelming the API
            usleep(500000); // 0.5 seconds
        }

        $progressBar->finish();

        $this->line('');
        $this->info("Sync completed!");
        $this->info("Successfully synced: {$successCount}");
        $this->info("Errors: {$errorCount}");

        return 0;
    }

    /**
     * Update internal order status based on delivery status
     */
    private function updateInternalOrderStatus($order, $deliveryStatus)
    {
        $statusMapping = [
            'delivered' => Order::STATUS_DELIVERED,
            'cancelled' => Order::STATUS_CANCELLED,
            'partial_delivered' => Order::STATUS_DELIVERED, // You might want to handle this differently
            'in_review' => Order::STATUS_SHIPPING,
            'pending' => Order::STATUS_SHIPPING,
        ];

        if (isset($statusMapping[$deliveryStatus]) && $order->status !== $statusMapping[$deliveryStatus]) {
            $oldInternalStatus = $order->status;
            $order->update(['status' => $statusMapping[$deliveryStatus]]);
            
            $this->info("  Internal status updated: {$oldInternalStatus} → {$statusMapping[$deliveryStatus]}");
            
            Log::info('Internal order status updated based on delivery status', [
                'order_id' => $order->id,
                'invoice' => $order->invoice,
                'delivery_status' => $deliveryStatus,
                'old_internal_status' => $oldInternalStatus,
                'new_internal_status' => $statusMapping[$deliveryStatus],
            ]);
        }
    }
}