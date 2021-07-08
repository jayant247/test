<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use HasFactory, SoftDeletes;

    public function orderStatus(){
        return $this->belongsTo(OrderStatus::class,'order_status');
    }
    public function paymentStatus(){
        return $this->belongsTo(OrderStatus::class,'payment_status');
    }

    public function orderItems(){
       return $this->hasMany(OrderItems::class,'order_id');
    }
}

