import htmx from 'htmx.org';
import {loadBoards} from './board';

window.htmx = htmx;

window.addEventListener("load", () => {
    loadBoards();
});