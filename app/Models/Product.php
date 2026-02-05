<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Category;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id', 'sub_category_id', 'name', 'slug', 'description', 'price', 'stock', 
        'image', 'is_active', 'is_featured', 'specifications', 'code',
        'discount_price', 'discount_percentage', 'is_active', 'is_featured', 
        'views', 'is_free_delivery', 'commission_amount', 'commission_percentage'];

    protected $casts = [
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'specifications' => 'array',
        'discount_start' => 'datetime',
        'discount_end' => 'datetime',
        'discount_price' => 'decimal:2',
    ];

    // Discount Accessors
    public function getHasDiscountAttribute()
    {
        if (!$this->discount_price) {
            return false;
        }

        $now = now();
        
        if ($this->discount_start && $now->lt($this->discount_start)) {
            return false;
        }

        if ($this->discount_end && $now->gt($this->discount_end)) {
            return false;
        }

        return true;
    }

    public function getEffectivePriceAttribute()
    {
        if ($this->has_discount) {
            return $this->discount_price;
        }
        return $this->price;
    }

    public function getDiscountPercentageAttribute()
    {
        if (!$this->has_discount || $this->price <= 0) {
            return 0;
        }
        
        return round((($this->price - $this->effective_price) / $this->price) * 100);
    }

    public static function boot()
    {
        parent::boot();
        
        static::creating(function ($model) {
            $model->code = strtoupper(substr(md5(uniqid()), 0, 6));
        });
    }

    public function getRouteKeyName()
    {
        return 'code';
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function subCategory()
    {
        return $this->belongsTo(Category::class, 'sub_category_id');
    }
    public function images()
    {
        return $this->hasMany(ProductImage::class);
    }

    public function updater()
    {
        return $this->belongsTo(Admin::class, 'updated_by');
    }
}
