<?php

namespace App\Http\Controllers\api;

use Stripe\Token;
use Carbon\Carbon;
use Stripe\Charge;
use Stripe\Stripe;
use App\Models\Room;
use App\Models\BookDetail;
use App\Models\RoomBooked;
use App\Models\RoomNumber;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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

    public function CreateBooking ( Request $request ) {
        $request->validate([
            'room_id' => 'required|exists:rooms,id', 
            'check_in' => 'required|date',
            'check_out' => 'required|date|after:check_in', 
            'person' => 'required|string', 
            'number_of_rooms' => 'required|integer|min:1', 
        ]);

        $user_id = auth()->user()->id;
        $checkIn = Carbon::parse($request->check_in);
        $checkOut = Carbon::parse($request->check_out);

        $room = Room::findOrFail($request->room_id);

        
        Stripe::setApiKey(env('STRIPE_KEY_SECRET'));

        //map dates
        $dayMapping = [];
        for ($date = $checkIn; $date->lte($checkOut); $date->addDay()) {
        $dayMapping[] = $date->format('Y-m-d');
        }
        
        //check available room numbers

        $bookings = RoomBooked::whereBetween('book_date', [$request->check_in, $request->check_out])
        ->get();

        $occupiedRoomNumberIds = $bookings->pluck('roomnumber_id'); 

        $availableRoomNumbers = RoomNumber::where('room_id',$request->room_id)
        ->whereNotIn('id', $occupiedRoomNumberIds)
        ->get();

        if (sizeof($availableRoomNumbers) == 0) {
            return response()->json(['message'=>'There is no remain room'], 500);
        }


        DB::beginTransaction();
        try {
            //create booking

            $totalNights = sizeof($dayMapping)-1;

            $number_of_rooms = $request->number_of_rooms;
            $actual_price = $room->price;
            $subtotal = $totalNights*$actual_price*$number_of_rooms;
            $discount = $room->discount;
            $total_price = $subtotal*(1-$discount/100);

            $bookDetails = new BookDetail();

            $bookDetails->room_id = $request->room_id;
            $bookDetails->user_id = $user_id;
            $bookDetails->check_in = $request->check_in;
            $bookDetails->check_out = $request->check_out;
            $bookDetails->person = $request->person;
            $bookDetails->number_of_rooms = $number_of_rooms ;

            $bookDetails->total_night = $totalNights; 
            $bookDetails->actual_price = $actual_price;
            $bookDetails->subtotal = $subtotal;
            $bookDetails->discount = $discount;
            $bookDetails->total_price = $total_price;

            $charge = Charge::create([
                'amount' => $total_price * 100, 
                'currency' => 'usd',
                'source' => 'tok_visa',
                'description' => 'Payment for booking',
            ]);

            $transactionId = $charge->id; 
            $paymentStatus = $charge->status; 

            $bookDetails->payment_method = $request->payment_method;
            $bookDetails->transation_id = $transactionId;
            $bookDetails->payment_status = $paymentStatus;

            $bookDetails->code = $paymentStatus;
            $bookDetails->status = 1;

            $bookDetails->save();

            $bookedRooms = [];
            if ($bookDetails->save() && $paymentStatus=="succeeded") {
                for ($i=0; $i < $number_of_rooms ; $i++) { 
                    foreach ($dayMapping as $key => $item) {
                        $roomBook = new RoomBooked();
                        $roomBook->booking_id = $bookDetails->id;
                        $roomBook->room_id = $request->room_id;
                        $roomBook->roomnumber_id = $availableRoomNumbers[$i]->id;
                        $roomBook->book_date = $item;
                        $roomBook->save();
                        $bookedRooms[] = $roomBook;
                    }                                
                }
            }
            DB::commit();
            return response()->json([
                'message' => 'Your booking is successful',
                'booking_detail' => $bookDetails,
                'booking_room' => $bookedRooms,
            ], 200);

        } catch (\Throwable $th) {
            DB::rollback();
            return response()->json(['message'=>'Something went wrong with booking system'], 500);
        }

    }
}
