/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import './styles/app.css';

// start the Stimulus application
import './bootstrap';
//Adds global jQuery for usage
import Cookies from 'js-cookie'

const $ = require("jquery");
global.$ = global.jQuery = $;
global.Cookies = Cookies;
