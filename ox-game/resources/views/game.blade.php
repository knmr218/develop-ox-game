<?php
$first = $player->cur_turn;
$roomId = $room->id;
?>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>OXゲーム</title>
    <link rel="stylesheet" href="{{asset('css/game.css')}}">
</head>
<body>
    <h1 id="game_title">○×ゲーム</h1>
    <div class="game">
        <table class="board">
            <tr>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td></td>
                <td></td>
                <td></td>
            </tr>
        </table>

        <button id="reset" onclick="resetGame()">リセット</button>
    </div>


    <script src="{{asset('js/game.js')}}"></script>
    <script>
        let first = '{{$first}}';
        let mark;
        if (first) {
            mark = '○';
            alert('あなたは先攻です');
            enableClick();
        } else {
            mark = '×';
            alert('あなたは後攻です');
        }

        let clientBoard = [
            [0, 0, 0],
            [0, 0, 0],
            [0, 0, 0]
        ];

        

        // GameStateUpdateイベントをリッスン
        Echo.channel('room.{{$roomId}}')
            .listen('GameStateUpdated', (event) => {
                console.log('Game state updated:', event.game);
                // ここでゲーム盤を更新する処理を実行
                updateGameBoard(event.game);
            });
        
        // GameEndイベントをリッスン
        Echo.channel('room.{{$roomId}}')
            .listen('GameEnd', (event) => {
                console.log('Game ended. Winner:', event.winner);
                // ゲーム終了時の処理を実行
                endGame(event.winner);
            });
    </script>
</body>
</html>