<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Movie;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\Services\TicketService;
use App\Services\MovieService;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    protected $ticketService;
    protected $movieService;

    public function __construct(TicketService $ticketService, MovieService $movieService)
    {
        $this->ticketService = $ticketService;
        $this->movieService = $movieService;
    }

    public function index()
    {
        $user = User::all();

        return response()->json(['status'=>200, 'user'=>$user], 200);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate the request using the request's built-in validation
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|unique:users,email',
            'password' => 'required|string|min:8', // Password must be at least 8 characters
        ]);
    
        // Create a new user and hash the password
        $user = new User;
        
        // Use mass assignment but ensure the password is hashed
        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->password = bcrypt($validated['password']); // Hash the password
    
        $user->save();
    
        // Return a success response with the new user data (excluding password)
        return response()->json([
            'status' => 200,
            'message' => 'User registered successfully',
            'user' => $user->only(['id', 'name', 'email']), // Only return non-sensitive data
        ], 200);
    }
    
    /**
     * Show the form for editing the specified resource.
     */
    public function edit( $id)
    {
        
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        // Validate the request
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|unique:users,email',
            'password' => 'required|string|min:8', // Password must be at least 8 characters
        ]);
    
        // Create a new user and hash the password
        $user = User::find($id);
        
        // Use mass assignment but ensure the password is hashed
        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->password = bcrypt($validated['password']); // Hash the password
    
        $user->save();
    
        // Return a success response with the new user data (excluding password)
        return response()->json([
            'status' => 200,
            'message' => 'User registered successfully',
            'user' => $user->only(['id', 'name', 'email']), // Only return non-sensitive data
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $user = User::find($id);

        // Check if the user exists
        if (!$user) {
            return response()->json([
                'status' => 404,
                'message' => 'User not found',
            ], 404);
        }
    
        // Delete the user
        $user->delete();
    
        // Return a success response
        return response()->json([
            'status' => 200,
            'message' => 'User deleted successfully',
        ], 200);
    }

    public function haveTicket(){
        $user = Auth::user();
        $tickets = $this->ticketService->listByID($user->id);

        return response()->json(['status'=>200, 'ticket'=>$tickets], 200);
    }

    public function buyTicket(Request $request){
        $user = Auth::user();
        return $this->ticketService->create($request, $user->id);
    }

    public function updateTicket(Request $request, $id){
        return $this->ticketService->update($request, $id);
    }

    public function dropTicket($id){
        return $this->ticketService->delete($id);
    }

    public function getMovie(){
        return $this->movieService->list();
    }

    public function addMovie(Request $request){
        if (! Gate::allows('movie-post')) {
            return response()->json([
                'status' => 201,
                'message' => 'you are not Admin!',
            ], 201);;
        }

        return $this->movieService->create($request);       
    }

    public function updateMovie(Request $request, $id){
        if (! Gate::allows('movie-post')) {
            return response()->json([
                'status' => 201,
                'message' => 'you are not Admin!',
            ], 201);;
        }

        return $this->movieService->update($request, $id);
    }

    public function dropMovie($id){
        if (! Gate::allows('movie-post')) {
            return response()->json([
                'status' => 201,
                'message' => 'you are not Admin!',
            ], 201);;
        }

        return $this->movieService->delete($id);
    }
}
