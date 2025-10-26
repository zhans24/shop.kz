<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, HasMany};

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'order_number', 'ordered_at', 'paid_at',
        'delivery_method_id','delivery_method_name','shipping_total',
        'payment_method_id','payment_method_name',
        'customer_type','contact_name','phone','address',
        'items_count','items_subtotal','total',
        'status',
    ];

    protected $casts = [
        'items_count'     => 'int',
        'items_subtotal'  => 'decimal:2',
        'shipping_total'  => 'decimal:2',
        'total'           => 'decimal:2',
        'ordered_at'      => 'datetime',
        'paid_at'         => 'datetime',
    ];

    public function user(): BelongsTo           { return $this->belongsTo(User::class); }
    public function items(): HasMany            { return $this->hasMany(OrderItem::class); }
    public function deliveryMethod(): BelongsTo { return $this->belongsTo(DeliveryMethod::class); }
    public function paymentMethod(): BelongsTo  { return $this->belongsTo(PaymentMethod::class); }

    public function scopeNumber($q, string $number) { return $q->where('order_number', $number); }
    public function scopePaid($q) { return $q->whereNotNull('paid_at'); }

    public function recalcTotals(): void
    {
        $subtotal = (float) $this->items()->sum('total');

        $this->forceFill([
            'items_count'    => (int) $this->items()->sum('qty'),
            'items_subtotal' => $subtotal,
            'total'          => $subtotal + (float) $this->shipping_total,
        ])->saveQuietly();
    }

    protected static function booted(): void
    {
        static::creating(function (self $o) {
            $o->status         ??= 'new';
            $o->ordered_at     ??= now();
            $o->shipping_total ??= 0;
        });

        static::created(function (self $o) {
            if (empty($o->order_number)) {
                $o->order_number = sprintf('ORD-%s-%06d', now()->format('Ym'), $o->id);
                $o->saveQuietly();
            }
        });
    }
}
