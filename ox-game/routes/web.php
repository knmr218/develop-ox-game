<?php

use App\Http\Controllers\GameController;
use App\Http\Controllers\PlayerController;
use App\Http\Controllers\RoomController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', [
    PlayerController::class,
    'initPlayer'
]);

Route::get('/game/npc', function () {
    return view('game');
});

Route::get('/room/search', [
    RoomController::class,
    'searchRoom'
]);

Route::get('/game/init', [
    GameController::class,
    'initGame'
]);

Route::get('/game/start', [
    GameController::class,
    'startGame'
]);

Route::post('/game/move', [
    GameController::class,
    'game'
]);

Route::get('/game/reset', [
    GameController::class,
    'resetGame'
]);

Route::get('/game/end', [
    GameController::class,
    'endGame'
]);