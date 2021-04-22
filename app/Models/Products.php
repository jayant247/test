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
        return $this->hasMany(ProductDescription::class,'product_id','id')->select('property_name','property_value','product_id');
    }

    public function productVariables(){
        return $this->hasMany(ProductVariables::class,'product_id','id');
    }

    public function productImages(){
        return $this->hasMany(ProductImages::class,'product_id','id')->select('imagePath','product_id');
    }



}
