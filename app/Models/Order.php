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
        return $this->belongsTo(PaymentStatus::class,'payment_status');
    }
    public function giftCardUsed(){
        return $this->belongsTo(UserGiftCards::class,'gift_card_id');
    }

    public function addressDetails(){
        return $this->belongsTo(UserAddress::class,'address_id');
    }
    public function orderItems(){
       return $this->hasMany(OrderItems::class,'order_id');
    }
}
