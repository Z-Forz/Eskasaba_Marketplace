<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'seller_id',
        'invoice_number',
        'total_price',
        'pickup_location',
        'note',
        'status',
        'previous_status',
        'cancelled_by',
        'cancellation_reason',
        'cancellation_status',
        'return_proof_image',
        'refund_confirmed_at',
    ];

    /**
     * Auto complete orders that have been marked ready_for_pickup (diserahkan) >= 3 days ago.
     */
    public static function autoCompleteExpiredOrders(): int
    {
        $cutoff = now()->subDays(3);
        $expiredOrders = static::with(['seller.user', 'user', 'payment'])
            ->whereIn('status', ['delivered', 'ready_for_pickup'])
            ->where('updated_at', '<=', $cutoff)
            ->get();

        $count = 0;
        foreach ($expiredOrders as $order) {
            $order->update(['status' => 'completed']);

            if ($order->payment && $order->payment->status === 'pending') {
                $order->payment->update([
                    'status' => 'verified',
                    'verified_at' => now(),
                ]);
            }

            Notification::create([
                'user_id' => $order->user_id,
                'title' => 'Pesanan Otomatis Selesai 📦',
                'message' => 'Pesanan #'.($order->invoice_number ?? $order->id).' telah otomatis dikonfirmasi Selesai oleh sistem (3 hari setelah diserahkan oleh penjual).',
                'type' => 'order_completed',
                'link' => route('buyer.orders.show', $order),
            ]);

            Notification::create([
                'user_id' => $order->seller->user_id,
                'title' => 'Pesanan Otomatis Selesai 📦',
                'message' => 'Pesanan #'.($order->invoice_number ?? $order->id).' telah otomatis dikonfirmasi Selesai oleh sistem (3 hari setelah diserahkan).',
                'type' => 'order_completed',
                'link' => route('seller.orders.show', $order),
            ]);

            $count++;
        }

        return $count;
    }

    /**
     * Restore stock for all products and variants in this order when cancelled.
     */
    public function restoreStock(): void
    {
        $this->loadMissing('items.product');

        foreach ($this->items as $item) {
            $product = $item->product;
            if (! $product) {
                continue;
            }

            $variantName = $item->variant_name;
            $qty = (int) $item->quantity;

            if (! empty($variantName) && is_array($product->variants)) {
                $variants = $product->variants;
                $variantFound = false;

                foreach ($variants as $idx => $var) {
                    if (isset($var['name']) && strcasecmp(trim($var['name']), trim($variantName)) === 0) {
                        $variantFound = true;
                        $varStock = isset($var['stock']) ? (int) $var['stock'] : 0;
                        $variants[$idx]['stock'] = $varStock + $qty;
                        break;
                    }
                }

                if ($variantFound) {
                    $product->variants = $variants;
                    if (array_filter($variants, fn ($v) => isset($v['stock']))) {
                        $product->stock = array_sum(array_column($variants, 'stock'));
                    } else {
                        $product->increment('stock', $qty);
                    }
                    $product->save();

                    continue;
                }
            }

            $product->increment('stock', $qty);
        }
    }

    protected function casts(): array
    {
        return [
            'total_price' => 'decimal:2',
            'refund_confirmed_at' => 'datetime',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    // Pembeli
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Pembeli (Alias for backward compatibility)
    public function buyer()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Penjual
    public function seller()
    {
        return $this->belongsTo(Seller::class);
    }

    // Detail pesanan
    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    // Pembayaran
    public function payment()
    {
        return $this->hasOne(Payment::class);
    }

    // Jadwal pengambilan
    public function pickupSchedule()
    {
        return $this->hasOne(PickupSchedule::class);
    }
}
