<?php

namespace App\Notifications;

use App\Models\Product;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LowStockNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public int $count;

    public function __construct(public Product $product)
    {
        $this->count = $product->stock_quantity;
    }

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Peringatan Stok Menipis: ' . $this->product->name)
            ->greeting('Halo ' . $notifiable->name . ',')
            ->line('Produk **' . $this->product->name . '** (SKU: ' . $this->product->sku . ') sedang menipis.')
            ->line('Stok tersisa: **' . $this->count . '**')
            ->line('Batas minimum: **' . $this->product->low_stock_threshold . '**')
            ->action('Lihat Produk', url('/products/' . $this->product->id))
            ->line('Segera lakukan pengadaan stok.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'product_id' => $this->product->id,
            'product_name' => $this->product->name,
            'product_sku' => $this->product->sku,
            'stock_quantity' => $this->count,
            'low_stock_threshold' => $this->product->low_stock_threshold,
            'message' => "Stok {$this->product->name} tersisa {$this->count} (threshold: {$this->product->low_stock_threshold})",
        ];
    }
}
