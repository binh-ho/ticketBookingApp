<?php

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\AuthenticationController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    //return $request->user();
});

Route::post('register', [AuthenticationController::class, 'register']);

Route::get('hello/{id}', [TicketController::class, 'showTicket']);

Route::post('login', [AuthenticationController::class, 'login']);

Route::get('checkUser', [AuthenticationController::class, 'checkUser']);

Route::middleware('auth:api')->group(function () {

    Route::get('checkUser', [AuthenticationController::class, 'checkUser']);

    Route::get('/users', [UserController::class, 'index']);

    Route::post('/users/post/id', [UserController::class, 'store']);

    Route::put('/users/put/id', [UserController::class, 'edit']);

    Route::delete('/users/delete/id', [UserController::class, 'delete']);

    Route::post('/users/buyTicket', [UserController::class, 'buyTicket']);

    Route::get('/users/haveTicket', [UserController::class, 'haveTicket']);

    Route::put('/users/updateTicket/{id}', [UserController::class, 'updateTicket']);

    Route::delete('/users/dropTicket/{id}', [UserController::class, 'dropTicket']);

    Route::post('/users/addMovie', [UserController::class, 'addMovie']);

    Route::get('/users/getMovie', [UserController::class, 'getMovie']);

    Route::put('/users/updateMovie/{id}', [UserController::class, 'updateMovie']);

    Route::delete('/users/dropMovie/{id}', [UserController::class, 'dropMovie']);

    Route::post('/users/logout', [AuthenticationController::class, 'logout']);
});

