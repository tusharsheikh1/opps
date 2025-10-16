<?php
// File: app/Console/Commands/SmsManagementCommand.php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\SmsService;
use App\Models\SmsLog;
use App\Models\Order;

class SmsManagementCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sms:manage 
                            {action : The action to perform (test|stats|clean|config)}
                            {--phone= : Phone number for test SMS}
                            {--days=30 : Number of days for cleaning old logs}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Manage SMS functionality - test, view stats, clean logs, check config';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $action = $this->argument('action');

        switch ($action) {
            case 'test':
                return $this->testSms();
            case 'stats':
                return $this->showStats();
            case 'clean':
                return $this->cleanOldLogs();
            case 'config':
                return $this->checkConfig();
            default:
                $this->error("Invalid action. Available actions: test, stats, clean, config");
                return 1;
        }
    }

    /**
     * Test SMS configuration
     */
    private function testSms()
    {
        $phone = $this->option('phone');
        
        if (!$phone) {
            $phone = $this->ask('Enter phone number to test');
        }

        if (!$phone) {
            $this->error('Phone number is required for testing');
            return 1;
        }

        $this->info('Testing SMS configuration...');
        
        // Check configuration first
        if (!$this->checkSmsConfig()) {
            return 1;
        }

        $smsService = new SmsService();
        $testMessage = "Test SMS from " . (setting('APP_NAME') ?? 'Your Website') . " at " . now()->format('Y-m-d H:i:s');
        
        $this->info("Sending test SMS to: {$phone}");
        $this->info("Message: {$testMessage}");
        
        // Get the first admin user or use null for console testing
        $adminUser = \App\Models\User::where('role_id', 1)->first();
        $userId = $adminUser ? $adminUser->id : null;
        
        $result = $smsService->sendSms($phone, $testMessage, 'custom', null, $userId);
        
        if ($result) {
            $this->info('✅ SMS sent successfully!');
            $this->info('Check the SMS logs in admin panel for delivery status.');
        } else {
            $this->error('❌ Failed to send SMS. Check your configuration and logs.');
        }

        return $result ? 0 : 1;
    }

    /**
     * Show SMS statistics
     */
    private function showStats()
    {
        $this->info('SMS Statistics:');
        $this->info('================');

        $smsService = new SmsService();
        $stats = $smsService->getSmsStats();

        $this->table(
            ['Metric', 'Count'],
            [
                ['Total SMS', $stats['total']],
                ['Sent', $stats['sent']],
                ['Failed', $stats['failed']],
                ['Pending', $stats['pending']],
            ]
        );

        // Success rate
        if ($stats['total'] > 0) {
            $successRate = round(($stats['sent'] / $stats['total']) * 100, 2);
            $this->info("Success Rate: {$successRate}%");
        }

        // Recent activity
        $recent = SmsLog::orderBy('created_at', 'desc')->limit(5)->get();
        
        if ($recent->count() > 0) {
            $this->info("\nRecent SMS Activity:");
            $this->table(
                ['Date', 'Phone', 'Type', 'Status'],
                $recent->map(function ($log) {
                    return [
                        $log->created_at->format('M d, H:i'),
                        $log->formatted_phone,
                        ucfirst($log->type),
                        ucfirst($log->status)
                    ];
                })->toArray()
            );
        }

        return 0;
    }

    /**
     * Clean old SMS logs
     */
    private function cleanOldLogs()
    {
        $days = $this->option('days');
        
        $cutoffDate = now()->subDays($days);
        $count = SmsLog::where('created_at', '<', $cutoffDate)->count();
        
        if ($count === 0) {
            $this->info("No SMS logs older than {$days} days found.");
            return 0;
        }

        $this->info("Found {$count} SMS logs older than {$days} days.");
        
        if ($this->confirm("Are you sure you want to delete these logs?")) {
            $deleted = SmsLog::where('created_at', '<', $cutoffDate)->delete();
            $this->info("✅ Deleted {$deleted} old SMS logs.");
        } else {
            $this->info("Operation cancelled.");
        }

        return 0;
    }

    /**
     * Check SMS configuration
     */
    private function checkConfig()
    {
        $this->info('SMS Configuration Status:');
        $this->info('==========================');

        $checks = [
            'SMS Service' => setting('sms_config_status') == 1,
            'API URL' => !empty(setting('SMS_API_URL')),
            'API Key' => !empty(setting('SMS_API_KEY')),
            'Sender ID' => !empty(setting('SMS_API_SENDER_ID')),
            'Queue Connection' => config('queue.default') === 'database',
        ];

        foreach ($checks as $check => $status) {
            $icon = $status ? '✅' : '❌';
            $statusText = $status ? 'OK' : 'NOT SET';
            $this->info("{$icon} {$check}: {$statusText}");
        }

        // Additional info
        $this->info("\nCurrent Settings:");
        $this->info("API URL: " . (setting('SMS_API_URL') ?: 'Not set'));
        $this->info("Sender ID: " . (setting('SMS_API_SENDER_ID') ?: 'Not set'));
        $this->info("Queue Connection: " . config('queue.default'));

        // Check if all required settings are configured
        $allConfigured = array_reduce($checks, function ($carry, $status) {
            return $carry && $status;
        }, true);

        if (!$allConfigured) {
            $this->warn("\n⚠️  Some configuration is missing. Please complete SMS setup in admin panel.");
        } else {
            $this->info("\n✅ SMS configuration looks good!");
        }

        return 0;
    }

    /**
     * Check if SMS configuration is valid
     */
    private function checkSmsConfig()
    {
        if (setting('sms_config_status') != 1) {
            $this->error('SMS service is disabled. Enable it in admin panel.');
            return false;
        }

        if (empty(setting('SMS_API_URL'))) {
            $this->error('SMS API URL is not configured.');
            return false;
        }

        if (empty(setting('SMS_API_KEY'))) {
            $this->error('SMS API Key is not configured.');
            return false;
        }

        if (empty(setting('SMS_API_SENDER_ID'))) {
            $this->error('SMS Sender ID is not configured.');
            return false;
        }

        return true;
    }
}

// Register this command in app/Console/Kernel.php:
/*
protected $commands = [
    Commands\SmsManagementCommand::class,
];
*/