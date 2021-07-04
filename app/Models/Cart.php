<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cart extends Model
{
    use HasFactory,SoftDeletes;

    public function productVariable(){
        return $this->belongsTo(ProductVariables::class,'product_variable_id','id');
    }
}
