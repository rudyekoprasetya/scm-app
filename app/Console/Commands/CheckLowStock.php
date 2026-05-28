<?php

namespace App\Console\Commands;

use App\Models\Product;
use App\Models\User;
use App\Notifications\LowStockNotification;
use Illuminate\Console\Command;

class CheckLowStock extends Command
{
    protected $signature = 'app:check-low-stock';

    protected $description = 'Check for low stock products and send notifications';

    public function handle()
    {
        $lowStockProducts = Product::where('is_active', true)
            ->whereColumn('stock_quantity', '<=', 'low_stock_threshold')
            ->get();

        if ($lowStockProducts->isEmpty()) {
            $this->info('No low stock products found.');

            return;
        }

        $recipients = User::role(['admin', 'manager', 'warehouse'])->get();

        $sent = 0;
        foreach ($lowStockProducts as $product) {
            foreach ($recipients as $user) {
                $user->notify(new LowStockNotification($product));
                $sent++;
            }
        }

        $this->info("Sent {$sent} notifications for {$lowStockProducts->count()} low stock products to {$recipients->count()} users.");
    }
}
