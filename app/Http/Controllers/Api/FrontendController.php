<?php

namespace App\Http\Controllers\api;

use App\Models\Room;
use App\Models\BookDetail;
use App\Models\RoomBooked;
use App\Models\RoomNumber;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class FrontendController extends Controller
{
    public function searchAvailableRoomType ( Request $request ) {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'total_adult' => 'required|string',
        ]);

        $bookings = RoomBooked::whereBetween('book_date', [$request->start_date, $request->end_date])
        ->get();

        $occupiedRoomIds = $bookings->pluck('roomnumber_id'); 

        $availableRoomNumbers = RoomNumber::whereHas('room', function ($query) use ($occupiedRoomIds, $request) {
            $query->where('total_adult', '>=', $request->total_adult);
        })
        ->whereNotIn('id', $occupiedRoomIds)
        ->distinct()
        ->pluck('room_id');

        $availableRoom = Room::whereIn('id',$availableRoomNumbers)->get();

        return response()->json($availableRoom, 200 );
    }
}
