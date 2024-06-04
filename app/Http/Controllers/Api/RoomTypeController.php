<?php

namespace App\Http\Controllers\api;

use App\Models\Room;
use App\Models\RoomType;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class RoomTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = auth()->user();
        $user_id = $user->id;
        $roomTypes = RoomType::where('user_id',$user_id)->with('room')->get();
        return response()->json($roomTypes, 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
    
        $request->validate([
            'name' => 'required|string',
            'user_id' => 'required|integer|string',
            'total_adult' => 'nullable|string',
            'total_child' => 'nullable|string',
            'room_capacity' => 'nullable|string',
            'price' => 'required|string', 
            'size' => 'nullable|string',
            'view' => 'nullable|string',
            'bed_style' => 'nullable|string',
            'discount' => 'nullable|integer',
            'short_desc' => 'nullable|string',
            'description' => 'nullable|string',
            'status' => 'nullable|integer|in:0,1',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $roomTypeId = RoomType::insertGetId([
            'name' => $request->name,
            'user_id' => $request->user_id
        ]);

        if (!$roomTypeId) {
            return response()->json(['message'=>'Something went wrong with creating room type!!'], 500);
        }

        $imageUrl = 'default.png';

        if ( $request->hasFile('image') ) {

            $user = auth()->user(); 
            $image = $request->file('image');
            $imageName = $user->name. 'room' . time() . '.' . $image->getClientOriginalExtension();

            $image->move(public_path('images'), $imageName);

            $imageUrl = asset('images/' . $imageName);

        }

        $room = new Room;
        $room->roomtype_id = $roomTypeId;
        $room->total_adult = $request->total_adult;
        $room->total_child = $request->total_child;
        $room->room_capacity = $request->room_capacity;
        $room->price = $request->price;
        $room->size = $request->size;
        $room->view = $request->view;
        $room->bed_style = $request->bed_style;
        $room->discount = $request->discount;
        $room->short_desc = $request->short_desc;
        $room->description = $request->description;
        $room->status = $request->status;
        $room->image = $imageUrl;

        $room->save();


        return response()->json([
            'roomtype'=>$room,
            'message'=>'Room  is created succesfully'
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $roomType = RoomType::findOrFail($id);

        return response()->json($roomType, 200);
    }


    public function update(Request $request, $id)
    {
        $roomType = RoomType::findOrFail($id);

        $room = $roomType->room;

        $request->validate([
            'name' => 'required|string',
            'user_id' => 'required|integer|string',
            'total_adult' => 'nullable|string',
            'total_child' => 'nullable|string',
            'room_capacity' => 'nullable|string',
            'price' => 'required|string', 
            'size' => 'nullable|string',
            'view' => 'nullable|string',
            'bed_style' => 'nullable|string',
            'discount' => 'nullable|integer',
            'short_desc' => 'nullable|string',
            'description' => 'nullable|string',
            'status' => 'nullable|integer|in:0,1',
        ]);

        $roomType->name = $request->name;
        $roomType->user_id = $request->user_id;

        $room->total_adult = $request->total_adult;
        $room->total_child = $request->total_child;
        $room->room_capacity = $request->room_capacity;
        $room->price = $request->price;
        $room->size = $request->size;
        $room->view = $request->view;
        $room->bed_style = $request->bed_style;
        $room->discount = $request->discount;
        $room->short_desc = $request->short_desc;
        $room->description = $request->description;
        $room->status = $request->status;
       

        if ($request->hasFile('image')) {
            //add image to folder
            $user = auth()->user(); 
            $image = $request->file('image');
            $imageName = $user->name. 'room' . time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('images'), $imageName);
            $imageUrl = asset('images/' . $imageName);
            //update image url
            $room->image = $imageUrl;
            //delete previous image
            $oldImageUrl = $room->image;
            if ($oldImageUrl) {
                $filename = basename($oldImageUrl);
                $image_path = public_path('images/' . $filename);
                if (file_exists($image_path)) {
                    unlink($image_path);
                }
            }

        }else{
            $room->image = $request->image;
        }

        $roomType->save();
        $room->save();


        return response()->json([
            'roomtype'=>$roomType,
            'message'=>'Room type is updated succesfully'
        ], 201);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $roomType = RoomType::findOrFail($id);
    
        Room::where('roomtype_id', $roomType->id)->delete();
    
        $roomType->delete();
    
        return response()->json(['message' => 'Room type and associated rooms deleted successfully'], 200); 
    }
}
