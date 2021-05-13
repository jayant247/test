<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

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
