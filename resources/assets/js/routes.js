import VueRouter from 'vue-router';


let routes = [
	{
		path: '/',
		name: 'home',
		component: require('./views/home.vue'),
	},
	{
		path: '/signup',
		name: 'signup',
		component: require('./views/signup.vue'),
		meta:{
			visitors: true,
		}
	},
	{
		path: '/signin',
		name: 'signin',
		component: require('./views/signin.vue'),
		meta:{
			visitors: true,
		}
	},
	{
		path: '/password',
		name: 'password-actions',
		redirect: '/',
		component: require('./views/password-actions.vue'),
		children:[
			{
				path: 'email',
				name: 'password-email',
				component: require('./views/password-email.vue'),
				meta:{
					visitors: true,
				}
			},
			{
				path: 'reset/:token',
				name: 'password-reset',
				component: require('./views/password-reset.vue'),
				props: true,
				meta:{
					visitors: true,
				}
			},
		],
	},
	// {
	// 	path: '/password/reset/:token', // /password-reset
	// 	name: 'password-reset',
	// 	component: require('./views/password-reset.vue'),
	// 	meta:{
	// 		visitors: true,
	// 	}
	// },
	// {
	// 	path: '/password/reset',
	// 	name: 'password-email',
	// 	component: require('./views/password-email.vue'),
	// 	meta:{
	// 		visitors: true,
	// 	}
	// },
	{
		path: '/posts',
		name: 'posts',
		component: require('./views/posts.vue'),
		meta:{
			auth: true,
		}
	},
	{
		path: '/post/:slug',
		name: 'post',
		component: require('./views/post.vue'),
		props: true,
		meta:{
			auth: true,
		}
	},
	{
		path: '/sections',
		name: 'sections',
		component: require('./views/sections.vue'),
		meta:{
			auth: true,
		}
	},
	{
		path: '/tags',
		name: 'tags',
		component: require('./views/tags.vue'),
		meta:{
			auth: true,
		}
	},
	{
		path: '/products',
		name: 'products',
		component: require('./views/products.vue'),
		// meta:{
		// 	auth: true,
		// }
	},
	{
		path: '/cart',
		name: 'cart',
		component: require('./views/cart.vue'),
		// meta:{
		// 	auth: true,
		// }
	},
	{
		path: '/schedule',
		name: 'schedule',
		component: require('./views/schedule.vue'),
		// meta:{
		// 	auth: true,
		// }
	},
	{
		path: '/chat',
		name: 'chat',
		component: require('./views/chat.vue'),
		meta:{
			auth: true,
		}
	},
	{
		path: '/conversations',
		name: 'conversations',
		component: require('./views/conversations.vue'),
		meta:{
			auth: true,
		}
	},
];


export default new VueRouter({
	routes,
	linkActiveClass: 'active',
	mode: 'history',
});