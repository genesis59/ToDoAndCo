/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
require('bootstrap');
import './styles/app.css';

const buttonSectionCreate = document.getElementById('btn-section-create');
const buttonSectionTodo = document.getElementById('btn-section-todo');
const buttonSectionFinished = document.getElementById('btn-section-finished');
buttonSectionCreate?.addEventListener("click", function() {
    window.location.href = buttonSectionCreate.getAttribute("data-url");
});
buttonSectionTodo?.addEventListener("click", function() {
    window.location.href = buttonSectionTodo.getAttribute("data-url");
});
buttonSectionFinished?.addEventListener("click", function() {
    window.location.href = buttonSectionFinished.getAttribute("data-url");
});
