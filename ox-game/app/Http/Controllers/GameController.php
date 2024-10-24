<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class GameController extends Controller
{
    public function game(Request $request) {
        // セッションを使ってボードを管理
        if (!Session::has('board')) {
            Session::put('board', [
                [0, 0, 0],
                [0, 0, 0],
                [0, 0, 0]
            ]);
        }

        $board = Session::get('board');

        // リクエストで送信された行と列を取得
        $row = $request->input('row');
        $col = $request->input('col');

        // プレイヤーの動き
        if ($board[$row][$col] == 0) {
            $board[$row][$col] = 1; // プレイヤーの手は「○」
        }

        // 勝利判定関数
        if ($this->checkWinner(1, $board)) {
            return response()->json(['board' => $board, 'winner' => 1]);
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
            return response()->json(['board' => $board, 'winner' => 2]);
        }

        // ゲームの状態をセッションに保存
        Session::put('board', $board);

        // 勝者がいない場合は、更新されたボードを返す
        return response()->json(['board' => $board, 'winner' => 0]);
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
        return view('title');
    }

}
