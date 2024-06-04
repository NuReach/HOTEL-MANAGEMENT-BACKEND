<?php

namespace App\Http\Controllers\api;

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
        $roomTypes = RoomType::where('user_id',$user_id)->get();
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
        ]);

        $roomType = RoomType::create($request->all());
       
        return response()->json([
            'roomtype'=>$roomType,
            'message'=>'Room type is created succesfully'
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

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $roomType = RoomType::findOrFail($id);

        $request->validate([
            'name' => 'required|string',
            'user_id' => 'required|integer|string',
        ]);

        $roomType->update($request->all());

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

        $roomType->delete();

        return response()->json(['message' => 'Room type deleted successfully'], 200);
    }
}
