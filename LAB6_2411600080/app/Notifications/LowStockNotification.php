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

    public function __construct(public Product $product)
    {
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Low Stock Alert: ' . $this->product->name)
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line("The product **{$this->product->name}** (SKU: {$this->product->sku}) is running low on stock.")
            ->line("Current Quantity: {$this->product->quantity}")
            ->line("Reorder Level: {$this->product->reorder_level}")
            ->action('View Product', url('/products/' . $this->product->id))
            ->line('Please consider reordering soon to avoid stockouts.');
    }

    /**
     * Get the array representation of the notification (database channel).
     */
    public function toArray(object $notifiable): array
    {
        return [
            'product_id' => $this->product->id,
            'product_name' => $this->product->name,
            'sku' => $this->product->sku,
            'quantity' => $this->product->quantity,
            'reorder_level' => $this->product->reorder_level,
            'message' => "Low stock alert: {$this->product->name} (Qty: {$this->product->quantity})",
            'url' => '/products/' . $this->product->id,
        ];
    }
}
