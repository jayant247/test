<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Products extends Model
{
    use HasFactory, SoftDeletes;

    public function subCategories()
    {
        return $this->belongsToMany(Category::class, 'product_has_category',
            'product_id','sub_category_id');
    }

    public function categories(){
        return $this->belongsToMany(Category::class, 'product_has_category',
            'product_id','category_id');
    }

    public function productDescriptions(){
        return $this->hasMany(ProductDescription::class,'product_id','id');
    }
}
