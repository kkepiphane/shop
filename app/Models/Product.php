<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'price',
        'description',
        'image_path'
    ];

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }
}