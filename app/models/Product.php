<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    // Вказуємо точну назву вашої таблиці з товарами
    protected $table = 'products'; 
    
    // Вимикаємо стандартні ларавелівські поля часу, бо у старій базі їх немає
    public $timestamps = false;

    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'description',
        'price',
        'old_price',
        'image',
        'stock',
        'is_featured',
    ];
}