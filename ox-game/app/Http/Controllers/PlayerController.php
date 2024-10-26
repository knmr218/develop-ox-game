<?php

namespace App\Http\Controllers;

use App\Models\Player;
use Illuminate\Http\Request;

class PlayerController extends Controller
{
    public function initPlayer(Request $request) {
        $playerId = $request->session()->get('player_id');

        // ID未登録なら新規追加
        if (!Player::where('id', $playerId)->exists()) {
            Player::insert([
                'id' => $playerId,
            ]);
        }

        return view('title');
    }
}
