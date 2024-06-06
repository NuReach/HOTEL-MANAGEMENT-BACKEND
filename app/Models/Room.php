<?php

namespace App\Models;

use App\Models\Facility;
use App\Models\MultiImage;
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
}
