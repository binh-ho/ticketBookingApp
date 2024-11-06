<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ticket;
use Illuminate\Support\Facades\Validator;

class TicketController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($id)
    {
        $tickets = Ticket::where('user_id', $id)->get();


        return response()->json(['status'=>200, 'ticket'=>$tickets], 200);
    }

    public static function showTicket($id)
    {
        $tickets = Ticket::where('user_id', $id)->get();

        $filteredTickets = $tickets->map(function ($ticket) {
            return [
                'id' => $ticket->id,
                'name' => $ticket->name,
                'show_time' => $ticket->show_time,
                'class' => $ticket->class,
            ];
        });

        return response()->json(['status'=>200, 'ticket'=>$filteredTickets], 200);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        
    }

    /**
     * Store a newly created resource in storage.
     */
    public static function addTicket(Request $request, $userId)
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
        $ticket = new Ticket();

        $ticket->name = $request->name;
        $ticket->show_time = $request->show_time;
        $ticket->class = $request->class;
        $ticket->user_id = $userId;

        $ticket->save();

        return response()->json([
            'status' => 200,
            'message' => 'Ticket successfully purschased!',
            'ticket' => $ticket // Optionally return the user data
        ], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show( $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit( $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public static function updateTicket(Request $request, $id)
    {
        // Validate the request
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'show_time' => 'required|string|max:255',
            'class' => 'required|string|max:255',
        ]);
    
        // Create a new user and hash the password
        $tickets = Ticket::where('id' , $id)->first();
        // Use mass assignment but ensure the password is hashed
        $tickets->name = $validated['name'];
        $tickets->show_time = $validated['show_time'];
        $tickets->class = $validated['class'];
    
        $tickets->save();
    
        // Return a success response with the new user data (excluding password)
        return response()->json([
            'status' => 200,
            'message' => 'ticket add successfully',
            'ticket' => $tickets, // Only return non-sensitive data
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public static function destroyTicket($id)
    {
        $tickets = Ticket::where('id' , $id)->first();

        // Check if the user exists
        if (!$tickets) {
            return response()->json([
                'status' => 404,
                'message' => 'User not found',
            ], 404);
        }
    
        // Delete the user
        $tickets->delete();
    
        // Return a success response
        return response()->json([
            'status' => 200,
            'message' => 'User deleted successfully',
        ], 200);
    }
}
