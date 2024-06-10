<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookDetail extends Model
{
    use HasFactory;
    protected $gaurded = [];

    public function room ()
    {
        return $this->belongsTo(Room::class, 'room_id', 'id');
    }
}
