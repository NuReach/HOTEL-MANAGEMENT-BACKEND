<?php

namespace App\Http\Controllers\api;

use App\Models\BookDetail;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class BookingController extends Controller
{
    function getAllBookings($search , $sortBy , $sortDir , $size ) {
        $user_id = auth()->user()->id;
        if ($search == "all") {
            $bookings = BookDetail::with('room')
            ->with('bookedRoomNumbers')
            ->with('room.roomType')
            ->with('room.roomType.user')
            ->whereHas('room.roomType.user', function ($query) use ($user_id) {
                $query->where('id', $user_id);
            })
            ->orderBy($sortBy, $sortDir)
            ->paginate($size);
        }else{
            $bookings = BookDetail::with('room')
            ->with('bookedRoomNumbers')
            ->with('room.roomType')
            ->with('room.roomType.user')
            ->whereHas('room.roomType.user', function ($query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%');
            })
            ->orWhereHas('room.roomType', function ($query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%');
            })
            ->orderBy($sortBy, $sortDir)
            ->paginate($size);
        return response()->json($bookings, 200);
        }
        return response()->json($bookings, 200);
    }

    public function getBookingById ( $booking_id ){
        $bookingDetail = BookDetail::with('bookedRoomNumbers')
        ->with('room.roomType')
        ->with('room.roomType.user')
        ->findOrFail($booking_id);
        return response()->json($bookingDetail, 200);
    }
}
