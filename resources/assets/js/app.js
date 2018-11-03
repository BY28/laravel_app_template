
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

import './bootstrap'

import router from './routes';

import {store} from './store/store';


/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

Vue.component('example-component', require('./components/ExampleComponent.vue'));
// Vue.component('posts', require('./components/posts.vue'));
Vue.component('navbar', require('./views/navbar.vue'));

router.beforeEach(

	(to, from, next) => {

		// VueProgressBarEventBus.$Progress.start();
		if(to.matched.some(record => record.meta.visitors))
		{
			store.dispatch('user/checkUser')
				.then(response => {
					next({
						path: '/'
					});
				})
				.catch(error => {
					next();
				});
		}
		else if(to.matched.some(record => record.meta.auth))
		{
			store.dispatch('user/checkUser')
				.then(response => {
					next();
				})
				.catch(error => {
					console.log(error)
					next({
						path: '/signin'
					});
				});
		}
		else
		{
			store.dispatch('user/checkUser');
			next();
		}

	}

);

// router.afterEach((to, from) => {
//       //  finish the progress bar
//       VueProgressBarEventBus.$Progress.finish();
// })


const app = new Vue({
    el: '#app',
    router: router,
    store: store,
});
