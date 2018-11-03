
window._ = require('lodash');
window.Popper = require('popper.js').default;

/**
 * We'll load jQuery and the Bootstrap jQuery plugin which provides support
 * for JavaScript based Bootstrap features such as modals and tabs. This
 * code may be modified to fit the specific needs of your application.
 */

try {
    window.$ = window.jQuery = require('jquery');

    require('bootstrap');
} catch (e) {}

/**
 * We'll load the axios HTTP library which allows us to easily issue requests
 * to our Laravel back-end. This library automatically handles sending the
 * CSRF token as a header based on the value of the "XSRF" token cookie.
 */


window.Vue = require('vue');

window.axios = require('axios');

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

import VueRouter from 'vue-router';

Vue.use(VueRouter);

import Vuex from 'vuex';

Vue.use(Vuex);

import {mapGetters} from 'vuex';

window.mapGetters = mapGetters;

import Form from './utilities/Form';

window.Form = Form;

import M from 'materialize-css';

window.M = M;

import VueProgressBar from 'vue-progressbar';

const options = {
  color: '#bcaaa4',
  failedColor: '#874b4b',
  thickness: '5px',
  transition: {
    speed: '0.2s',
    opacity: '0.6s',
    termination: 300
  },
  autoRevert: true,
  location: 'left',
  inverse: false
}
 
Vue.use(VueProgressBar,  {
  color: '#d7ccc8 ',
  failedColor: '#ef5350',
  height: '2px',
  thickness: '5px',
});

/**
 * Next we will register the CSRF Token as a common header with Axios so that
 * all outgoing HTTP requests automatically have it attached. This is just
 * a simple convenience so we don't have to attach every token manually.
 */

let token = document.head.querySelector('meta[name="csrf-token"]');

if (token) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
} else {
    console.error('CSRF token not found: https://laravel.com/docs/csrf#csrf-x-csrf-token');
}

/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allows your team to easily build robust real-time web applications.
 */

import Echo from 'laravel-echo'

window.Pusher = require('pusher-js');

window.Echo = new Echo({
    broadcaster: 'pusher',
    key: '1338315a22c20a6f9d0e',
    cluster: 'eu',
    encrypted: true,
    // authEndpoint: '/broadcasting/auth?token=' + localStorage.getItem('token'),
    //  auth: {
    //     headers: {
    //         Authorization: 'Bearer ' + localStorage.getItem('token')
    //     },
    // },
});
