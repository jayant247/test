<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItems extends Model
{
    use HasFactory;

    public function productVariable(){
        return $this->belongsTo(ProductVariables::class,'product_variable_id')->with('productDetails')
            ->select(['price',"mrp","color","size","type","primary_image",'id','product_id'])
            ;
    }
}
