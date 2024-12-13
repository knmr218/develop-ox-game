function sendMoveToServer(row, col) {
    // マス目の座標をサーバーに送信
    fetch('/game/move', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ "row": row, "col": col }) // 行と列をサーバーに送信
    })
    .then(response => response.json()) // サーバーからのレスポンスをJSONで受け取る
    .then(data => {
        if (data.Invalid) {
            alert('無効な操作');
            return;
        }
        
        // サーバーから返ってきたデータを使ってクライアント側のボードを更新
        clientBoard = data.board;
        updateBoard(clientBoard);
        
        if (data.winner !== 0) {
            if (data.winner === 1) {
                document.getElementById("game_text").textContent = "あなたの勝ちです";
            } else if (data.winner === 2) {
                document.getElementById("game_text").textContent = "引き分けです";
            }
            disableClick();
            document.querySelector('.btn_box').style.display = "block";
            return;
        }

        document.getElementById("game_text").textContent = "相手のターン";
        disableClick();
    })
    .catch(error => {
        console.log('Error:', error);
        alert('エラーが発生しました');
        window.location.href = '/game/end';
    });
}

function updateBoard(board) {
    // サーバーから送られたボード状態をもとにクライアントのボードを更新
    for (let i = 0; i < board.length; i++) {
        const cell = tableCells[i]; // 対応する<td>要素を取得
        let content;
        switch(board[i]) {
            case "0":
                content = '';
                break;
            case "1":
                content = '○';
                break;
            case "2":
                content = '×';
                break;
        }
        cell.textContent = content;
    }
}

// マスをクリックしたときの処理
function handleClick(cell) {
    const row = cell.parentElement.rowIndex; // 行
    const col = cell.cellIndex % 3; // 列

    if (clientBoard[row * 3 + col] === "0") {
        clientBoard[row * 3 + col] = "1";

        disableClick();
        sendMoveToServer(row,col);
    } else {
        alert("無効な操作");
    }
}

const tableCells = document.querySelectorAll('.board td');
let clientBoard = "000000000";

tableCells.forEach(cell => {
    cell.addEventListener('click', () => {
        handleClick(cell);
    });
});

// 無効化する関数
function disableClick() {
    tableCells.forEach(cell => {
        cell.style.pointerEvents = "none";
    });
}

// 有効化する関数
function enableClick() {
    tableCells.forEach(cell => {
        cell.style.pointerEvents = "auto";
    });
}

