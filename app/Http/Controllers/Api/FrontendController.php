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

        $occupiedRoomNumberIds = $bookings->pluck('roomnumber_id'); 

        $availableRoomNumbersId = RoomNumber::whereHas('room', function ($query) use ($occupiedRoomNumberIds, $request) {
            $query->where('total_adult', '>=', $request->total_adult);
        })
        ->whereNotIn('id', $occupiedRoomNumberIds)
        ->distinct()
        ->pluck('room_id');

        $availableRoom = Room::whereIn('id',$availableRoomNumbersId)->get();

        $remainRoom = [];

        foreach ($availableRoom as $key => $item) {
            $occupiedRoomNumber = RoomNumber::where('room_id',$item->id)
            ->whereIn('id',$occupiedRoomNumberIds)->get();
            $remainRoom[] = [
              'room' => $item,
              'totalRoom' => sizeof($item->roomNumbers),
              'occupied' => sizeof($occupiedRoomNumber)
            ];
          }

        return response()->json($remainRoom, 200 );
    }
}
