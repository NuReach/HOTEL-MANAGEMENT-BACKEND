<?php

namespace App\Http\Controllers\api;

use App\Models\Room;
use App\Models\Facility;
use App\Models\RoomType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
        $roomTypes = RoomType::where('user_id',$user_id)
                    ->with('room')
                    ->with('room.facilities')
                    ->get();
        return response()->json($roomTypes, 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
           
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
                'facilites' => 'nullable|array'
            ]);

            $roomType = RoomType::create([
                'name' => $request->name,
                'user_id' => $request->user_id
            ]);
    
            if (!$roomType->id) {
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
            $room->roomtype_id = $roomType->id;
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
    
            if ($room->save()) {
                $facilityCount = Count($request->facilities);
                for ($i=0; $i < $facilityCount ; $i++) { 
                    $facility = new Facility;
                    $facility->room_id = $room->id;
                    $facility->facility_name = $request->facilities[$i];
                    $facility->save(); 
                }
            }
    
            DB::commit();
            return response()->json([
                'room' => $room,
                'roomtype'=>$roomType,
                'message' => 'Room is created successfully'
            ], 201);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['message' => 'Something went wrong with creating room type or room'], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $roomType = RoomType::findOrFail($id);

        return response()->json($roomType->with('room')->with('room.facility'), 200);
    }


    public function update(Request $request, $id)
    {
        DB::beginTransaction();
        try {

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
                'facilites' => 'nullable|array'
            ]);

            //update room type
            $roomType->name = $request->name;
            $roomType->user_id = $request->user_id;
    
            //update room
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

            //update image
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

        if ($request->facilites !== null ) {
            Facility::where('room_id',$room->id)->delete();
            $facilityCount = Count($request->facilities);   
            for ($i=0; $i < $facilityCount ; $i++) { 
                $facility = new Facility;
                $facility->room_id = $room->id;
                $facility->name = $request->facilities[$i];
                $facility->save(); 
            }
        }
        
        $roomType->save();
        $room->save();

        DB::commit();

        return response()->json([
            'roomtype'=>$roomType->with('room')->with('room.facility'),
            'message'=>'Room type is updated succesfully'
        ], 201);
        
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['message' => 'Something went wrong with updating room'], 500);    
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $roomType = RoomType::findOrFail($id);
        $oldImageUrl = $roomType->room->image;
        if ($oldImageUrl) {
            $filename = basename($oldImageUrl);
            $image_path = public_path('images/' . $filename);
            if (file_exists($image_path)) {
                unlink($image_path);
            }
        }
        $roomType->delete();
    
        return response()->json(['message' => 'Room type and associated rooms and facilities have been deleted successfully'], 200); 
    }
    
}
