<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Kyslik\ColumnSortable\Sortable;

class Order extends Model
{
    use HasFactory, SoftDeletes;
    use Sortable;

    public $sortable = [
        'id',
        'orderRefNo',
        'total',
        'created_at',
        'updated_at'];

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
        return $this->belongsTo(UserAddress::class,'address_id')->withTrashed();
    }
    public function orderItems(){
       return $this->hasMany(OrderItems::class,'order_id');
    }

    public function customer(){
        return $this->belongsTo(User::class,'user_id');
    }
}

