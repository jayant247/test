<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductReview extends Model
{
    use HasFactory, SoftDeletes;

    public function userInfo(){
        return $this->belongsTo(User::class,'user_id','id')->select('name','id','mobile_no');
    }

    public function review_images(){
        return $this->hasMany(ReviewImages::class,'product_review_id','id');
    }
}
