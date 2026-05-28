<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shipment extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'shipment_number',
        'carrier',
        'tracking_number',
        'shipping_cost',
        'status',
        'origin',
        'destination',
        'estimated_delivery_date',
        'delivered_at',
        'user_id',
        'notes',
    ];

    protected $casts = [
        'shipping_cost' => 'decimal:2',
        'estimated_delivery_date' => 'date',
        'delivered_at' => 'datetime',
        'status' => 'string',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    public function trackingEvents()
    {
        return $this->hasMany(TrackingEvent::class);
    }
}