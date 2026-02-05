<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'total_amount',
        'delivery_charge',
        'status',
        'payment_status',
        'payment_method', 'shipping_address', 'order_code', 'media',
        'coupon_code', 'discount_amount', 'updated_by', 'traffic_source', 'order_portal',
        'transaction_id', 'payment_number', 'due_amount',
        'delivery_division_id', 'delivery_district_id', 'delivery_upazila_id',
        'delivery_postcode', 'delivery_address', 'delivery_phone'
    ];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->order_code = strtoupper(substr(md5(uniqid()), 0, 6)); // Random 6 char code
            if (empty($model->media)) {
                $model->media = 'web';
            }
        });
    }

    public function getRouteKeyName()
    {
        return 'order_code';
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function updater()
    {
        return $this->belongsTo(Admin::class, 'updated_by');
    }
}
