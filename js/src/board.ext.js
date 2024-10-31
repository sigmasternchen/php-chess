import htmx from 'htmx.org';

const loadBoard = (board) => {
    const boardId = board.getAttribute("data-board");
    const getSquare = square => board.getElementsByClassName(square)[0];
    const moveSelected = event => {
        const move = event.target.getAttribute("data-move");
        console.log(move);
        board.setAttribute("data-hx-vals", JSON.stringify({"move": move}));
        htmx.trigger(document.body, "chess-move-" + boardId, {});
    };
    const clearSelection = () => {
        board.classList.remove("moveSelection");
        board.querySelectorAll(".source").forEach(element => {
            element.classList.remove("source");
        });
        board.querySelectorAll(".movePossible").forEach(element => {
            element.classList.remove("movePossible");
            element.removeEventListener("click", moveSelected);
        });
    };
    const enterSelection = (moves) => {
        board.classList.add("moveSelection");
        for (const move of moves) {
            const target = getSquare(move.target);
            target.classList.add("movePossible");
            target.setAttribute("data-move", move.encoded);
            target.addEventListener("click", moveSelected);
            getSquare(move.source).classList.add("source");
        }
    }

    board.querySelectorAll(".piece.hasMoves").forEach(element => {
        element.addEventListener("click", event => {
            clearSelection();
            const moves = element.getAttribute("data-moves").split(";").map(move => ({
                encoded: move,
                source: move.split(",")[0].split("-")[2],
                target: move.split(",")[1]
            }));
            enterSelection(moves);
            event.stopPropagation();
        });
    });
    board.addEventListener("click", () => {
        clearSelection();
    });
}

htmx.defineExtension('board', {
    onEvent : function(name, event) {
        console.log(name);
        if (name !== "htmx:afterProcessNode" ||
            (
                event?.target?.getAttribute("hx-ext") ??
                event?.target?.getAttribute("data-hx-ext" ?? "")
            ) !== "board"
        ) {
            return;
        }

        loadBoard(event.target);
    }
})