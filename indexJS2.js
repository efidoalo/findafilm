/*======================;
 *
 * File: indexJS2.js
 * Content: Javascript functionality
 * for search_results.php
 * Date: 10/11/2021
 *
 ***********************/

function decrement_page()
{
	document.getElementById("decrement_page_form").submit();
}

function increment_page()
{
        document.getElementById("increment_page_form").submit();
}

window.onload = function() {
	document.getElementById("decrement_page_button").addEventListener("click", function() {decrement_page();});
	document.getElementById("increment_page_button").addEventListener("click", function() {increment_page();});
}
