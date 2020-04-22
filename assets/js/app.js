*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import '../css/app.scss';
import 'bootstrap';

// Need jQuery? Install it with "yarn add jquery", then uncomment to import it.
import $ from 'jquery';
import 'select2';
import 'select2/dist/css/select2.css';

import './home.js';
import './figure/comment.js';
import './figure/figure.js';
import './picture/picture.js';
import './videos/videos.js';

// Add smooth scrolling on all links inside the navbar
$('.scroll').on('click', function(event) {
	// Make sure this.hash has a value before overriding default behavior
	if (this.hash !== '') {
		// Prevent default anchor click behavior
		event.preventDefault();

		// Store hash
		var hash = this.hash;

		// Using jQuery's animate() method to add smooth page scroll
		// The optional number (800) specifies the number of milliseconds it takes to scroll to the specified area
		$('html, body').animate(
			{
				scrollTop: $(hash).offset().top - 100,
			},
			800,
			function() {
				// Add hash (#) to URL when done scrolling (default click behavior)
				window.location.hash = hash;
			}
		);
	} // End if
});

