<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sale extends Model
{
    use SoftDeletes;

    protected $fillable = ['category_id', 'price', 'qty', 'purchased_at', 'note', 'delete_reason'];

    protected $casts = ['purchased_at' => 'datetime'];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}