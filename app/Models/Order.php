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
        'cancelled_by',
        'cancellation_reason',
        'cancellation_status',
        'return_proof_image',
        'refund_confirmed_at',
    ];

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
            'total_price'         => 'decimal:2',
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