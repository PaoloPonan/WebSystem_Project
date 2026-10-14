<?php

namespace App\Observers;

use App\Models\InventoryTransaction;
use App\Models\User;
use App\Notifications\LowStockNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Validation\ValidationException;

class InventoryTransactionObserver
{
    /**
     * Handle the InventoryTransaction "creating" event.
     * Validate stock availability before the record is saved.
     */
    public function creating(InventoryTransaction $transaction): void
    {
        $product = $transaction->product;

        if (!$product) {
            $product = \App\Models\Product::find($transaction->product_id);
        }

        if (!$product) {
            throw ValidationException::withMessages([
                'product_id' => 'Selected product does not exist.',
            ]);
        }

        // For stock_out, ensure sufficient quantity
        if ($transaction->type === InventoryTransaction::TYPE_STOCK_OUT) {
            if ($transaction->quantity > $product->quantity) {
                throw ValidationException::withMessages([
                    'quantity' => "Insufficient stock. Available: {$product->quantity}. Requested: {$transaction->quantity}.",
                ]);
            }
        }
    }

    /**
     * Handle the InventoryTransaction "created" event.
     * Update product quantity atomically and trigger low-stock alerts.
     */
    public function created(InventoryTransaction $transaction): void
    {
        DB::transaction(function () use ($transaction) {
            $product = $transaction->product()->lockForUpdate()->first();

            if (!$product) {
                return;
            }

            $delta = match ($transaction->type) {
                InventoryTransaction::TYPE_STOCK_IN => abs($transaction->quantity),
                InventoryTransaction::TYPE_STOCK_OUT => -abs($transaction->quantity),
                InventoryTransaction::TYPE_ADJUSTMENT => $transaction->quantity, // can be + or -
                default => 0,
            };

            $newQuantity = $product->quantity + $delta;

            // Safety: never allow negative inventory
            if ($newQuantity < 0) {
                throw ValidationException::withMessages([
                    'quantity' => 'Operation would result in negative inventory. Transaction cancelled.',
                ]);
            }

            $product->quantity = $newQuantity;
            $product->save();

            // Trigger low stock notification if needed
            if ($product->isLowStock()) {
                $this->sendLowStockAlert($product);
            }
        });
    }

    /**
     * Handle the InventoryTransaction "deleted" event.
     * Reverse the stock change when a transaction is deleted.
     */
    public function deleted(InventoryTransaction $transaction): void
    {
        DB::transaction(function () use ($transaction) {
            $product = $transaction->product()->lockForUpdate()->first();

            if (!$product) {
                return;
            }

            // Reverse the original delta
            $delta = match ($transaction->type) {
                InventoryTransaction::TYPE_STOCK_IN => -abs($transaction->quantity),
                InventoryTransaction::TYPE_STOCK_OUT => abs($transaction->quantity),
                InventoryTransaction::TYPE_ADJUSTMENT => -$transaction->quantity,
                default => 0,
            };

            $newQuantity = max(0, $product->quantity + $delta);
            $product->quantity = $newQuantity;
            $product->save();
        });
    }

    /**
     * Send low stock notification to all users (or admins).
     */
    protected function sendLowStockAlert($product): void
    {
        // Avoid flooding: only notify if no unread low-stock notification exists for this product in last 24h
        $users = User::all();

        foreach ($users as $user) {
            $recent = $user->unreadNotifications()
                ->where('type', LowStockNotification::class)
                ->where('data->product_id', $product->id)
                ->where('created_at', '>=', now()->subDay())
                ->exists();

            if (!$recent) {
                $user->notify(new LowStockNotification($product));
            }
        }
    }
}
