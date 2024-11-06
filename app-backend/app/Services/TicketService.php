<?php

namespace App\Services;

use App\Repositories\TicketRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TicketService
{
    protected $ticketRepo;

    public function __construct(TicketRepository $ticketRepo)
    {
        $this->ticketRepo = $ticketRepo;
    }

    public function list()
    {
        $tickets = $this->ticketRepo->index();

        return response()->json(['status'=>200, 'ticket'=>$tickets], 200);
    }

    public function listByID($id)
    {
        return $this->ticketRepo->listByID($id);
    }

    public function create(Request $request, $id)
    {
        // Validate the request
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'show_time' => 'required|string|max:255',
            'class' => 'required|string|max:255',
        ]);

        // Check if validation fails
        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'message' => $validator->errors(),
            ], 422);
        }

        // Get the validated data
        $validatedData = $validator->validated();

        // Create a new ticket using the repository
        $ticket = $this->ticketRepo->insert($validatedData, $id);

        return response()->json([
            'status' => 200,
            'message' => 'Movie successfully added!',
            'movie' => $ticket,
        ], 200);
    }

    public function findByID($id)
    {
        $ticket = $this->ticketRepo->findByID($id);

        return response()->json(['status'=>200, 'ticket'=>$ticket], 200);
    }

    public function update(Request $request, $id)
    {
        // Validate the request
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);
    
        // Create a new user and hash the password
        $ticket = $this->ticketRepo->findByID($id);
        // Use mass assignment but ensure the password is hashed
        $ticket->name = $validated['name'];
    
        $ticket->save();
    
        // Return a success response with the new user data (excluding password)
        return response()->json([
            'status' => 200,
            'message' => 'ticket add successfully',
            'ticket' => $ticket->only(['id', 'name']), // Only return non-sensitive data
        ], 200);
    }

    public function delete($id)
    {
        $ticket = $this->ticketRepo->findByID($id);

        // Check if the user exists
        if (!$ticket) {
            return response()->json([
                'status' => 404,
                'message' => 'User not found',
            ], 404);
        }
    
        // Delete the user
        $ticket->delete();
    
        // Return a success response
        return response()->json([
            'status' => 200,
            'message' => 'User deleted successfully',
        ], 200);
    }
}