<?php

namespace App\Http\Controllers;

use App\Events\GameStart;
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
            if ($random) {
                $player1->update([
                    'cur_turn' => 1
                ]);

                $player2->update([
                    'cur_turn' => 0
                ]);
            } else {
                $player1->update([
                    'cur_turn' => 0
                ]);

                $player2->update([
                    'cur_turn' => 1
                ]);
            }
        }

        // マスを初期化
        $game = Game::find($room->game_id);
        $game->update([
            'board' => '000000000'
        ]);

        $player = Player::find($playerId);
        broadcast(new GameStart($room));
        return view('game', ['player' => $player, 'room' => $room]);
    }

    public function startGame(Request $request) {
        $playerId = $request->session()->get('player_id');
        $room = Room::where('player_1', '=', $playerId)
                    ->orWhere('player_2', '=', $playerId)
                    ->first();
        $player = Player::find($playerId);
        return view('game', ['player' => $player, 'room' => $room]);
    }

    public function game(Request $request) {
        // セッションを使ってボードを管理
        if (!Session::has('board')) {
            Session::put('board', [
                [0, 0, 0],
                [0, 0, 0],
                [0, 0, 0]
            ]);
        }

        // バリデーション
        $request->validate([
            'row' => 'required|integer|between:0,2',
            'col' => 'required|integer|between:0,2',
        ]);

        $board = Session::get('board');

        // リクエストで送信された行と列を取得
        $row = $request->input('row');
        $col = $request->input('col');

        // 入力値の検証
        if ($board[$row][$col] != 0) {
            return response()->json(['board' => $board, 'winner' => 0, 'Invalid' => true]);
        }

        // プレイヤーの動き
        $board[$row][$col] = 1; // プレイヤーの手は「○」

        // 勝利判定関数
        if ($this->checkWinner(1, $board)) {
            return response()->json(['board' => $board, 'winner' => 1, 'Invalid' => false]);
        }

        // 敵の動き（ランダムに×を置く）
        $emptyCells = [];
        for ($i = 0; $i < 3; $i++) {
            for ($j = 0; $j < 3; $j++) {
                if ($board[$i][$j] == 0) {
                    $emptyCells[] = ['row' => $i, 'col' => $j];
                }
            }
        }

        if (count($emptyCells) > 0) {
            $randomIndex = array_rand($emptyCells);
            $move = $emptyCells[$randomIndex];
            $board[$move['row']][$move['col']] = 2; // 敵の手は「×」
        }

        // 敵の勝利判定
        if ($this->checkWinner(2, $board)) {
            return response()->json(['board' => $board, 'winner' => 2, 'Invalid' => false]);
        }

        // ゲームの状態をセッションに保存
        Session::put('board', $board);

        // 勝者がいない場合は、更新されたボードを返す
        return response()->json(['board' => $board, 'winner' => 0, 'Invalid' => false]);
    }

    // 勝利判定関数
    private function checkWinner($player, $board)
    {
        // 行のチェック
        for ($i = 0; $i < 3; $i++) {
            if ($board[$i][0] == $player && $board[$i][1] == $player && $board[$i][2] == $player) {
                return true;
            }
        }
        // 列のチェック
        for ($i = 0; $i < 3; $i++) {
            if ($board[0][$i] == $player && $board[1][$i] == $player && $board[2][$i] == $player) {
                return true;
            }
        }
        // 対角線のチェック
        if ($board[0][0] == $player && $board[1][1] == $player && $board[2][2] == $player) {
            return true;
        }
        if ($board[0][2] == $player && $board[1][1] == $player && $board[2][0] == $player) {
            return true;
        }
        return false;
    }

    public function resetGame() {
        Session::forget('board');
        return response()->json(['status' => 'reset']); 
    }

    public function endGame() {
        Session::forget('board');
        return redirect('/');
    }

}
