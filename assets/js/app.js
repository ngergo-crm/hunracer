/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import 'bootstrap';
import '../css/app.scss';
import $ from 'jquery';
import Vue from 'vue';
import { BootstrapVue, IconsPlugin } from 'bootstrap-vue';
import Vuesax from 'vuesax';
import UUID from 'vue-uuid';
import 'vuesax/dist/vuesax.css';
import VueSweetalert2 from 'vue-sweetalert2';

// If you don't need the styles, do not connect
//import 'sweetalert2/dist/sweetalert2.min.css';

Vue.use(VueSweetalert2);
Vue.use(UUID);
//Vuesax styles
Vue.use(Vuesax);

Vue.use(BootstrapVue);

// Need jQuery? Install it with "yarn add jquery", then uncomment to import it.
//import $ from 'jquery';
