function sendMoveToServer(row, col) {
    // マス目の座標をサーバーに送信
    fetch('/game/move', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ row: row, col: col }) // 行と列をサーバーに送信
    })
    .then(response => response.json()) // サーバーからのレスポンスをJSONで受け取る
    .then(data => {
        // サーバーから返ってきたデータを使ってクライアント側のボードを更新
        clientBoard = data.board;
        updateBoard(clientBoard);
        
        if (data.winner == 1) {
            alert("あなたの勝ちです");
            window.location.href = "/game/end";
        } else if (data.winner == 2) {
            alert("あなたの負けです");
            window.location.href = "/game/end";
        }

        if (data.Invalid) {
            alert('無効な操作');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('エラーが発生しました');
        window.location.href = '/game/end';
    });
}

function updateBoard(board) {
    // サーバーから送られたボード状態をもとにクライアントのボードを更新
    for (let row = 0; row < board.length; row++) {
        for (let col = 0; col < board[row].length; col++) {
            const cell = tableCells[row * 3 + col]; // 対応する<td>要素を取得
            let content;
            switch(board[row][col]) {
                case 0:
                    content = '';
                    break;
                case 1:
                    content = '○';
                    break;
                case 2:
                    content = '×';
                    break;
            }
            cell.textContent = content;
        }
    }
}

function resetGame() {
    fetch('/game/reset', {
        method: 'GET',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json()) // サーバーからのレスポンスをJSONで受け取る
    .then(data => {
        if (data.status === 'reset') {
            clientBoard = [
                [0, 0, 0],
                [0, 0, 0],
                [0, 0, 0]
            ];
            updateBoard(clientBoard);
            alert("リセットしました");
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
}

let clientBoard = [
    [0, 0, 0],
    [0, 0, 0],
    [0, 0, 0]
];

const tableCells = document.querySelectorAll('.board td');
tableCells.forEach(cell => {
    cell.addEventListener('click', () => {
        const row = cell.parentElement.rowIndex; // 行
        const col = cell.cellIndex % 3; // 列

        if (clientBoard[row][col] === 0) {
            clientBoard[row][col] = 1;
            updateBoard(clientBoard);

            sendMoveToServer(row,col);
        } else {
            alert("無効な操作");
        }

        
    });
});

