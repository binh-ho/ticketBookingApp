<?php

namespace App\Http\Controllers;
use App\Models\Movie;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MovieController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public static function index()
    {
        $movies = Movie::all();

        $filteredMoviess = $movies->map(function ($movies) {
            return [
                'id' => $movies->id,
                'name' => $movies->name,
            ];
        });

        return response()->json(['status'=>200, 'movie'=>$filteredMoviess], 200);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public static function addMovie(Request $request)
    {
        $validated = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
        ]);

        // Check if validation fails
        if ($validated->fails()) {
            return response()->json([
                'status' => 422,
                'message' => $validated->errors(), // Use errors() to get validation messages
            ], 422);
        }

        // Create a new user
        $movie = new Movie();

        $movie->name = $request->name;
        $movie->save();

        return response()->json([
            'status' => 200,
            'message' => 'movie successfully added!',
            'movie' =>  $movie // Optionally return the user data
        ], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public static function updateMovie(Request $request, $id)
    {
        // Validate the request
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);
    
        // Create a new user and hash the password
        $movie = Movie::where('id' , $id)->first();
        // Use mass assignment but ensure the password is hashed
        $movie->name = $validated['name'];
    
        $movie->save();
    
        // Return a success response with the new user data (excluding password)
        return response()->json([
            'status' => 200,
            'message' => 'ticket add successfully',
            'ticket' => $movie->only(['id', 'name']), // Only return non-sensitive data
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public static function destroyMovie($id)
    {
        $movie = Movie::where('id' , $id)->first();

        // Check if the user exists
        if (!$movie) {
            return response()->json([
                'status' => 404,
                'message' => 'User not found',
            ], 404);
        }
    
        // Delete the user
        $movie->delete();
    
        // Return a success response
        return response()->json([
            'status' => 200,
            'message' => 'User deleted successfully',
        ], 200);
    }
}
