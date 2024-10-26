<?php
$key = env('PUSHER_APP_KEY');
$cluster = env('PUSHER_APP_CLUSTER');
?>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>standby</title>
    <script type="module" src="https://172.18.0.3:5173/@vite/client"></script>
    <script type="module" src="https://172.18.0.3:5173/resources/js/app.js"></script>

</head>
<body>
    

    <script>
        // Echo.private(`rooms.{{$roomId}}`)
        //     .listen('PlayerMatched', (event) => {
        //         console.log('Player matched!');
        //         // ゲーム開始処理をここに追加
        //     });

        // Echo.private(`rooms.{{$roomId}}`)
        //     .listen('GameStart', (event) => {
        //         console.log('Game start!');
        //         // ゲーム開始処理をここに追加
        //         window.location.href = '/game/start';
        //     });

        Pusher.logToConsole = true;
        
        let pusher = new Pusher("{{ $key }}", {
            cluster: "{{ $cluster }}"
        });

        let channel = pusher.subscribe('rooms.{{$roomId}}');
        channel.bind('PlayerMatched', function (data) {
            console.log('Player matched!');
        });
        channel.bind('GameStart', function (data) {
            console.log('Game start!');
            window.location.href = '/game/start';
        });

    </script>
</body>
</html>