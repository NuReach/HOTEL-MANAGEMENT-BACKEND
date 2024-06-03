<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function updateProfilePicture ( Request $request ) {

         $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048', // Adjust max file size as needed
        ]);

        if ($request->file('image')->isValid()) {
            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();

            $image->move(public_path('images'), $imageName);

            $imageUrl = asset('images/' . $imageName);

            $user = auth()->user(); // Assuming you are using authentication
            $user->image_url = $imageUrl;
            $user->save();


            return response()->json([
                'message' => 'Image updated successfully',
                'image_url' => $imageUrl,
            ]);
        }

        return response()->json(['message' => 'Invalid image'], 400);
    }
}
