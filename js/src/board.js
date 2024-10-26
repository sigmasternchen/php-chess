
const loadBoard = (board) => {
    const getSquare = square => board.getElementsByClassName(square)[0];
    const clearSelection = () => {
        board.classList.remove("moveSelection");
        board.querySelectorAll(".source").forEach(element => {
            element.classList.remove("source")
        });
        board.querySelectorAll(".movePossible").forEach(element => {
            element.classList.remove("movePossible")
        });
    };
    const enterSelection = (moves) => {
        board.classList.add("moveSelection");
        for (const move of moves) {
            getSquare(move.target).classList.add("movePossible");
            getSquare(move.source).classList.add("source");
        }
    }
    const moveSelected = (move) => {
        // TODO
    };

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

export const loadBoards = () => {
    document.querySelectorAll(".board.interactive").forEach(loadBoard);
};