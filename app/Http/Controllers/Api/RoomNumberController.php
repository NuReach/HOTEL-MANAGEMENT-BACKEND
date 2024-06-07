<?php

namespace App\Http\Controllers\api;

use App\Models\Room;
use App\Models\RoomType;
use App\Models\RoomNumber;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class RoomNumberController extends Controller
{

    public function createRoomNumber ( Request $request , $roomtype_id , $room_id ) {
        $request->validate([
            'room_no' => 'required|string|unique:room_numbers',
            'status' => 'required|string',
        ]);
        $roomType = RoomType::findOrFail($roomtype_id);
        $room = Room::findOrFail($room_id);
        $roomNumber = new RoomNumber();
        $roomNumber->roomtype_id = $roomtype_id;
        $roomNumber->room_id = $room_id;
        $roomNumber->room_no = $request->room_no;
        $roomNumber->status = $request->status;
        $roomNumber->save();
        return response()->json([
            'roomNumber' => $roomNumber,
            'message' => 'Room number is created',
        ], 201);
    }

    public function updateRoomNumber(Request $request, $roomtype_id, $room_id, $roomnumber_id)
    {
        $request->validate([
            'room_no' => 'required|string', 
            'status' => 'required|string',
        ]);

        $roomNumber = RoomNumber::findOrFail($roomnumber_id);

        $roomNumber->roomtype_id = $roomtype_id; 
        $roomNumber->room_id = $room_id;         
        $roomNumber->room_no = $request->room_no;
        $roomNumber->status = $request->status;

        $roomNumber->save();

        return response()->json([
            'roomNumber' => $roomNumber,
            'message' => 'Room number is updated',
        ], 200); 
    }

    public function deleteRoomNumber( $roomnumber_id)
    {
        $roomNumber = RoomNumber::findOrFail($roomnumber_id);
        $roomNumber->delete();

        return response()->json([
            'message' => 'Room number is deleted',
        ], 200);
    }
}
