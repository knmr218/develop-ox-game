<?php
$first = $player->cur_turn;
$playerId = $player->id;
$roomId = $room->id;
$turn = "あなた";
if ($first === 0) {
    $turn = "相手";
}
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
        <h2 id="game_text">{{$turn}}のターン</h2>
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
    </div>
    <div class="btn_box">
        <button type="button" onclick="window.location.href = '/game/onemore'">もう一度</button>
        <button type="button" onclick="window.location.href = '/game/end'">やめる</button>
    </div>


    <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
    <script src="{{asset('js/game.js')}}"></script>
    <script>
        let first = "{{$first}}";
        let playerId = "{{$playerId}}";
        if (first === "1") {
            enableClick();
        } else {
            disableClick();
        }

        // Enable pusher logging - don't include this in production
        Pusher.logToConsole = true;

        var pusher = new Pusher("{{ config('const.pusher.app_key') }}", {
            cluster: "{{ config('const.pusher.cluster') }}"
        });

        var channel = pusher.subscribe('room.{{$roomId}}');
        
        channel.bind('GameStateUpdate', function(data) {
            document.getElementById("game_text").textContent = "あなたのターン";
            clientBoard = data.board;
            updateBoard(clientBoard);
            enableClick();
        });

        channel.bind('GameEnd', function(data) {
            clientBoard = data.board;
            updateBoard(clientBoard);
            enableClick();
            document.querySelector('.btn_box').style.display = "block";
            if (data.status === 1) {
                if (data.winner != playerId) {
                    document.getElementById("game_text").textContent = "あなたの負けです";
                }
            } else if (data.status === 2) {
                document.getElementById("game_text").textContent = "引き分けです";
            }
        });

    </script>
</body>
</html>