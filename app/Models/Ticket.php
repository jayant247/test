<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Ticket extends Model
{
    use HasFactory;
    use Softdeletes;

    public function ticketStatus(){
        return $this->belongsTo(TicketStatus::class,'ticket_status_id','id');
    }

    public function customer(){
        return $this->belongsTo(User::class,'customer_id','id');
    }

    public function admin(){
        return $this->belongsTo(User::class,'assigned_to','id');
    }

    public function message(){
        return $this->hasMany(TicketMessage::class,'ticket_id','id');
    }

    public function orders(){
        return $this->belongsTo(Order::class,'order_id','id');
    }
}
