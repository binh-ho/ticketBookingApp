<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Ticket;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;

use App\Http\Controllers\TicketController;

class AuthenticationController extends Controller
{
    public function checkUser()
    {
        $user = Auth::user();

        $tickets = Ticket::where('user_id', $user->id)->get();

        return TicketController::showTicket($user->id);
        // return response()->json(['user' => $user], 401);
    }

    public function register(request $request)
    {
        // return response()->json(["status"=>200, "message"=>"login..."]);
        // Validate login credentials
        $validated = $request->validate([
            'name' => 'required|string',
            'email' => 'required|string',
            'password' => 'required|string|min:8',
        ]);

        $user = User::where('email' , $request['email'])->where( 'name' , $request['name'])->first();
        // Get the authenticated user
        if(!$user){
            $newUser = new User;
            $newUser->name = $validated['name']; 
            $newUser->email = $validated['email'];
            $newUser->password = $validated['password'];
            $newUser->save();
        
            return response()->json([
                'status' => 200,
                'message' => 'User registered successfully',
            ], 200);
        }

        return response()->json([
            'status' => 201,
            'message' => 'User already registered',
        ], 201);
    }

    public function login(request $request)
    {
        // return response()->json(["status"=>200, "message"=>"login..."]);
        // Validate login credentials
        $request->validate([
            'email' => 'required|string',
            'password' => 'required|string|min:8',
        ]);

        $user = User::where('email' , $request['email'])->where( 'password' , $request['password'])->first();
        // Get the authenticated user
        if($user){
            $token =  $user->createToken('MyApp')->accessToken;
            return response()->json(['token' => $token], 200);
        }else{
            return response()->json(['error'=>'Unauthorised'], 401);
        }
        return response()->json(['token' => 'acess token'], 200);
    }

    public function logout (Request $request) {
        $token = $request->user()->token();
        $token->revoke();
        $response = ['message' => 'You have been successfully logged out!'];
        return response($response, 200);
    }
}
