<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug', 'image', 'is_active', 'code'];

    public static function boot()
    {
        parent::boot();
        
        static::creating(function ($model) {
            $model->code = strtoupper(substr(md5(uniqid()), 0, 6)); // Random 6 digit/char code
        });
    }

    public function getRouteKeyName()
    {
        return 'code';
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }
    public function updater()
    {
        return $this->belongsTo(Admin::class, 'updated_by');
    }
}
