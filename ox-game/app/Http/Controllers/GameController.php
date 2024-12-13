<?php

namespace App\Http\Controllers;

use App\Events\GameEnd;
use App\Events\GameStart;
use App\Events\GameStateUpdate;
use App\Models\Game;
use App\Models\Player;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class GameController extends Controller
{   
    public function initGame(Request $request) {
        $playerId = $request->session()->get('player_id');
        $room = Room::where('player_1', '=', $playerId)
                    ->orWhere('player_2', '=', $playerId)
                    ->first();
        $player1 = Player::find($room->player_1);
        $player2 = Player::find($room->player_2);

        // 先攻後攻決め
        if (is_null($player1->cur_turn)) {
            $random = rand(0,1);
            if ($random === 1) {
                $player1->update([
                    'cur_turn' => 1,
                    'first' => 1
                ]);

                $player2->update([
                    'cur_turn' => 0,
                    'first' => 0
                ]);
            } else {
                $player1->update([
                    'cur_turn' => 0,
                    'first' => 0
                ]);

                $player2->update([
                    'cur_turn' => 1,
                    'first' => 1
                ]);
            }
        }

        // マスを初期化
        $game = Game::find($room->game_id);
        $game->update([
            'board' => '000000000'
        ]);

        $player = Player::find($playerId);
        event(new GameStart($room));
        return view('game', ['player' => $player, 'room' => $room, 'game' => $game]);
    }

    public function startGame(Request $request) {
        $playerId = $request->session()->get('player_id');
        $room = Room::where('player_1', '=', $playerId)
                    ->orWhere('player_2', '=', $playerId)
                    ->first();
        $player = Player::find($playerId);
        $game = Game::find($room->game_id);
        return view('game', ['player' => $player, 'room' => $room, 'game' => $game]);
    }

    public function game(Request $request) {
        $playerId = $request->session()->get('player_id');
        $room = Room::where('player_1', '=', $playerId)
                    ->orWhere('player_2', '=', $playerId)
                    ->first();
        $player1 = Player::find($room->player_1);
        $player2 = Player::find($room->player_2);
        $player = Player::find($playerId);
        $game = Game::find($room->game_id);
        $board = $game->board;

        if ($player->cur_turn === 0) {
            return response()->json(['board' => $board, 'winner' => 0, 'Invalid' => true]);
        }

        // バリデーション
        $request->validate([
            'row' => 'required|integer|between:0,2',
            'col' => 'required|integer|between:0,2',
        ]);


        // リクエストで送信された行と列を取得
        $row = $request->input('row');
        $col = $request->input('col');

        // 入力値の検証
        if ($board[$row * 3 + $col] != "0") {
            return response()->json(['board' => $board, 'winner' => 0, 'Invalid' => true]);
        }

        $mark = 1; // プレイヤーの手は「○」か「×」
        if ($player->first === 0) {
            $mark = 2;
        }

        // プレイヤーの動き
        $board[$row * 3 + $col] = $mark; // プレイヤーの手は「○」

        $game->update([
            'board' => $board
        ]);

        // 勝利判定関数
        if ($this->checkWinner($mark, $board)) {
            $game->update([
                'status' => 1
            ]);
            event(new GameEnd($room,$game,$player->id));
            return response()->json(['board' => $board, 'winner' => 1, 'Invalid' => false]);
        }

        // 引き分け判定
        if (strpos($board, '0') === false) {
            $game->update([
                'status' => 2
            ]);
            event(new GameEnd($room,$game,null));
            return response()->json(['board' => $board, 'winner' => 2, 'Invalid' => false]);
        }

        $player1->update([
            'cur_turn' => 1,
        ]);

        $player2->update([
            'cur_turn' => 1,
        ]);
        
        $player->update([
            'cur_turn' => 0,
        ]);
        
        // 勝者がいない場合は、更新されたボードを返す
        event(new GameStateUpdate($room, $game));
        return response()->json(['board' => $board, 'winner' => 0, 'Invalid' => false]);
    }

    // 勝利判定関数
    private function checkWinner($player, $board)
    {
        // 行のチェック
        for ($i = 0; $i < 3; $i++) {
            if ($board[$i * 3 + 0] == $player && $board[$i * 3 + 1] == $player && $board[$i * 3 + 2] == $player) {
                return true;
            }
        }
        // 列のチェック
        for ($i = 0; $i < 3; $i++) {
            if ($board[0 * 3 + $i] == $player && $board[1 * 3 + $i] == $player && $board[2 * 3 + $i] == $player) {
                return true;
            }
        }
        // 対角線のチェック
        if ($board[0 * 3 + 0] == $player && $board[1 * 3 + 1] == $player && $board[2 * 3 + 2] == $player) {
            return true;
        }
        if ($board[0 * 3 + 2] == $player && $board[1 * 3 + 1] == $player && $board[2 * 3 + 0] == $player) {
            return true;
        }
        return false;
    }

    public function endGame() {
        Session::forget('board');
        return redirect('/');
    }

}
