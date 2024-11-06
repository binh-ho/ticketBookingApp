<?php

namespace App\Services;

use App\Repositories\MovieRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MovieService
{
    protected $movieRepo;

    public function __construct(MovieRepository $movieRepo)
    {
        $this->movieRepo = $movieRepo;
    }

    public function list()
    {
        $movies = $this->movieRepo->index();

        return response()->json(['status'=>200, 'movie'=>$movies], 200);
    }

    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
        ]);

        // Check if validation fails
        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'message' => $validator->errors(), // Use errors() to get validation messages
            ], 422);
        }

        $validatedData = $validator->validated();
        // Create a new user
        $movie = $this->movieRepo->insert($validatedData);

        return response()->json([
            'status' => 200,
            'message' => 'movie successfully added!',
            'movie' =>  $movie // Optionally return the user data
        ], 200);
    }

    public function findByID($id)
    {
        $movie = $this->movieRepo->findByID($id);

        return response()->json(['status'=>200, 'ticket'=>$movie], 200);
    }

    public function update(Request $request, $id)
    {
        // Validate the request
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);
    
        // Create a new user and hash the password
        $movie = $this->movieRepo->findByID($id);
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

    public function delete($id)
    {
        $movie = $this->movieRepo->findByID($id);

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