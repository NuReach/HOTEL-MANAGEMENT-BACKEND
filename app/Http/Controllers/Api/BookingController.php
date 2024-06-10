<?php

namespace App\Http\Controllers\api;

use Carbon\Carbon;
use App\Models\BookDetail;
use App\Models\RoomBooked;
use App\Models\RoomNumber;
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

    
    public function updateBooking ( Request $request , $booking_id ) {
        $bookingDetail = BookDetail::with('bookedRoomNumbers')
        ->with('room.roomType')
        ->with('room.roomType.user')
        ->findOrFail($booking_id);
        if ($request->has('check_in') && $request->has('check_out')) {
                $checkIn = Carbon::parse($request->check_in);
                $checkOut = Carbon::parse($request->check_out);
                //delete old booked room number
                $roomsBooked = RoomBooked::where('booking_id',$bookingDetail->id)
                ->delete();  
                //map dates
                $dayMapping = [];
                for ($date = $checkIn; $date->lte($checkOut); $date->addDay()) {
                $dayMapping[] = $date->format('Y-m-d');
                }
                $bookings = RoomBooked::whereBetween('book_date', [$request->check_in, $request->check_out])
                ->get();
        
                $occupiedRoomNumberIds = $bookings->pluck('roomnumber_id'); 
        
                $availableRoomNumbers = RoomNumber::where('room_id',$bookingDetail->room_id)
                ->whereNotIn('id', $occupiedRoomNumberIds)
                ->get();
        
                if (sizeof($availableRoomNumbers) == 0) {
                    return response()->json(['message'=>'There is no remain room'], 500);
                }

                if ($bookingDetail) {
                    foreach ($dayMapping as $key => $item) {
                        $roomBook = new RoomBooked();
                        $roomBook->booking_id = $bookingDetail->id;
                        $roomBook->room_id = $bookingDetail->room_id;
                        $roomBook->roomnumber_id = $availableRoomNumbers[0]->id;
                        $roomBook->book_date = $item;
                        $roomBook->save();
                    }                      
                }
                }

        if ($request->has('roomnumber_id')) {
            $roomsBooked = RoomBooked::where('booking_id',$bookingDetail->id)->get();
            $availableRoomNumbersOfRoomType = BookingController::checkAvailableRooms( $bookingDetail->check_in , $bookingDetail->check_out , $bookingDetail->room_id );
            if (sizeof($availableRoomNumbersOfRoomType) == 0) {
                return response()->json(['message'=>'There is no remain room'], 500);
            }
            foreach ($roomsBooked as $key => $item) {
                $item->roomnumber_id = $request->roomnumber_id;
                $item->save();
            }
            return response()->json([
                'message'=>'Room number changed successfully',
            ], 200); 
        }
        $bookingDetail->save();
        return response()->json([
            'message'=>'Booking is updated',
        ], 200);
    }

    public function checkAvailableRooms ( $check_in , $check_out , $room_id  ){
        $bookings = RoomBooked::whereBetween('book_date', [$check_in, $check_out])
        ->get();
        $occupiedRoomNumberIds = $bookings->pluck('roomnumber_id'); 
        $availableRoomNumbers = RoomNumber::where('room_id',$room_id)
        ->whereNotIn('id', $occupiedRoomNumberIds)
        ->get();
       return $availableRoomNumbers;
    }
}
