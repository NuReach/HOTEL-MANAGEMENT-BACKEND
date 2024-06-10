<?php

namespace App\Models;

use App\Models\RoomBooked;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BookDetail extends Model
{
    use HasFactory;
    protected $gaurded = [];

    public function room ()
    {
        return $this->belongsTo(Room::class, 'room_id', 'id');
    }

    public function bookedRoomNumbers(){
        return $this->hasMany(RoomBooked::class,'booking_id');
    }
}
