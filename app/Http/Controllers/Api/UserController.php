<?php

namespace App\Http\Controllers\api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::all();
        return response()->json($users, 200);
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
    public function show(Request $request)
    {
        $user = auth()->user();
        return response()->json($user, 200);
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

    public function uploadProfilePicture ( Request $request ) {

         $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048', // Adjust max file size as needed
        ]);

        if ($request->file('image')->isValid()) {
            $user = auth()->user(); // Assuming you are using authentication
            $image = $request->file('image');
            $imageName = $user->name . time() . '.' . $image->getClientOriginalExtension();

            $image->move(public_path('images'), $imageName);

            $imageUrl = asset('images/' . $imageName);

            $user->image_url = $imageUrl;
            $user->save();


            return response()->json([
                'message' => 'Image updated successfully',
                'image_url' => $imageUrl,
            ]);
        }

        return response()->json(['message' => 'Invalid image'], 400);
    }

    public function updateProfilePicture(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048', // Adjust max file size as needed
        ]);

        $user = auth()->user();
        $oldImageUrl = $user->image_url;
        $filename = basename($oldImageUrl);
        $image_path = public_path('images/'.$filename);
        
        if ($request->file('image')->isValid()) {

            $imageName = $user->name . time() . '.' . $request->image->getClientOriginalExtension();
            $request->image->move(public_path('/images'), $imageName);
            $imageUrl = asset('images/' . $imageName);
            
            if (file_exists($image_path)) {
                unlink($image_path);
            }

            $user->image_url = $imageUrl;
            $user->save();
            
            
            return response()->json([
                'message' => 'Image updated successfully',
                'new_image_url' => $user->image_url,
            ]);

        }

        return response()->json(['message' => 'Invalid image'], 400);
    }
}
