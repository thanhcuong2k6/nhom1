<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'description',
        'price',
        'discount_price',
        'stock',
        'views',
        'is_featured',
        'is_active',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function cartItems()
    {
        return $this->hasMany(CartItem::class);
    }

    public function getAverageRating()
    {
        return $this->reviews()->avg('rating') ?? 0;
    }

    public function getReviewCount()
    {
        return $this->reviews()->count();
    }

    public function getImageUrl()
    {
        if ($this->images()->exists()) {
            return asset('storage/' . $this->images()->first()->image);
        }
        // Placeholder image - sử dụng placeholder service
        return 'https://via.placeholder.com/400x300?text=' . urlencode($this->name);
    }

    public function getMainImage()
    {
        return $this->images()->first();
    }
}
