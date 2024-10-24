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
</body>
</html>