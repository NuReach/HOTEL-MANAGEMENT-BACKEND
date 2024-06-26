<?php

namespace App\Models;

use App\Models\Facility;
use App\Models\RoomType;
use App\Models\MultiImage;
use App\Models\RoomNumber;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Room extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function facilities()
    {
        return $this->hasMany(Facility::class);
    }

    public function gallary()
    {
        return $this->hasMany(MultiImage::class);
    }

    public function roomNumbers()
    {
        return $this->hasMany(RoomNumber::class);
    }

    public function roomType(){
        return $this->belongsTo(RoomType::class, 'roomtype_id' , 'id');
    }
}
