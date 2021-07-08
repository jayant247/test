<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TicketMessage extends Model
{
    use HasFactory,SoftDeletes;
    public function ticket(){
        return $this->belongsTo(Ticket::class,'ticket_id','id');
    }

    public function supportAgent(){
        return $this->belongsTo(User::class,'admin_id','id');
    }
}
