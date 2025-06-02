<?php

namespace App\Models;

use App\Domain\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Order extends Model implements HasMedia
{
    use InteractsWithMedia;
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'order_number',
        'payment_order_id',
        'reason_for_payment_failure',
        'user_id',
        'rent_out_from',
        'return_to',
        'when_to_rent',
        'when_to_return',
        'merchant_id_rent_out_from',
        'merchant_rent_out_from',
        'merchant_return_to',
        'renting_time',
        'order_bills',
        'order_bills_vnd',
        'commission_fees',
        'commission_fees_vnd',
        'status_of_order',
        'order_belongs_to',
        'merchant_id',
        'name_of_merchant',
        'staff_id',
        'staff_name',
        'order_comes_from',
        'when_to_pay',
        'payment_channel',
        'status_of_refund',
        'refund',
        'commission_of_refunds',
        'profit_sharing_to_dealer',
        'revenue_to_dealer',
        'revenue_to_merchant',
        'billing_strategy',
        'shop_name',
        'shop_type',
        'location',
        'region',
        'city',
        'area',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
    ];
}
