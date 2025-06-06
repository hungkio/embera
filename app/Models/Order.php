<?php

namespace App\Models;

use App\Domain\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Order extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $fillable = [
        'order_number', // Order ID
        'payment_id', // Payment ID
        'payment_failure_reason', // Payment failure reason
        'user_id', // User ID
        'rental_equipment_id', // Rental Equipment ID
        'return_equipment_id', // Return Equipment ID
        'rental_time', // Rental time
        'return_time', // Return time
        'rental_shop_id', // Rental Shop ID
        'rental_shop', // Rental Shop
        'rental_shop_type', // Rental Shop Type
        'rental_shop_address', // Rental Shop Address
        'return_shop', // Return Shop
        'duration_of_use', // Duration of use
        'currency', // Currency
        'order_amount', // Order Amount
        'fees', // Fees
        'order_status', // Order Status
        'orders_belong_to_merchants', // Orders belong to merchants
        'merchant_id', // Merchant ID
        'merchant_name', // Merchant Name
        'service_provider_id', // Service Provider ID
        'employee_id', // Employee ID
        'employee_name', // Employee Name
        'order_source', // Order source
        'payment_time', // Payment Time
        'payment_channels', // Payment channels
        'refund_status', // Refund Status
        'refund_amount', // Refund amount
        'refund_fee', // Refund Fee
        'agent_share_ratio', // Agent share ratio
        'franchisee_share_ratio', // Franchisee share ratio
        'service_provider_share_ratio', // Service Provider share ratio
        'merchant_share_ratio', // Merchant share ratio
        'charging_strategy', // Charging strategy,
        'region', 'city', 'area'
    ];

    protected $hidden = [
    ];

    protected $casts = [
    ];
}
