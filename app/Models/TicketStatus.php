<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TicketStatus extends Model
{
    use HasFactory, SoftDeletes;
    public function tickets(){
        return $this->hasMany(Ticket::class,'ticket_status_id','id');
    }
}
