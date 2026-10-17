<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    protected $fillable = ['category_id', 'buyer_name', 'price', 'qty', 'purchased_at', 'note'];

    protected $casts = ['purchased_at' => 'datetime'];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}