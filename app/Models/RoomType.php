<?php

namespace App\Models;

use App\Models\User;
use App\Models\RoomNumber;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RoomType extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function room()
    {
        return $this->belongsTo(Room::class,'id','roomtype_id');
    }

    
    public function roomNumbers()
    {
        return $this->hasMany(RoomNumber::class,'roomtype_id','id');
    }

    public function user(){
        return $this->belongsTo(User::class, 'user_id' , 'id');
    }
    
}
