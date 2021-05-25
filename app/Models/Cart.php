<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;

    public function productVariable(){
        return $this->belongsTo(ProductVariables::class,'product_variable_id','id');
    }
}
