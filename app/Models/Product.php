<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Category;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id', 'name', 'slug', 'description', 'price', 'stock', 
        'image', 'is_active', 'is_featured', 'specifications', 'code'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'specifications' => 'array',
    ];

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
}
