<?php

namespace App\Http\Controllers\api;

use App\Models\MultiImage;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class MultiImageController extends Controller
{
    public function deleteImageGallary(  $gallaryId ) {

        $image = MultiImage::findOrFail($gallaryId);
        $image->delete();
        return response()->json(['message' => 'Gallary is deleted '], 200);    
        
    }

}
