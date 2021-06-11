<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductVariables extends Model
{
    use HasFactory, SoftDeletes;

    public function productVariablesImages(){
        return $this->hasMany(ProductImages::class,'product_variable_id','id')->select('imagePath','product_variable_id');
    }

    public function product(){
        return $this->belongsTo(Products::class,'product_id');
    }

    public function productDetails(){
        return $this->belongsTo(Products::class,'product_id')->select('id','product_name');
    }
}
