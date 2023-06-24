/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (admin.css in this case)
import 'choices.js/public/assets/styles/choices.css';
import './styles/admin.sass';

// start the Stimulus application
import './bootstrap';

// require jQuery normally
import $ from 'jquery';
// create global $ and jQuery variables
global.$ = global.jQuery = $;
