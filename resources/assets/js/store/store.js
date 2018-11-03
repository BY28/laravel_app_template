import Vuex from 'vuex';
import posts from './modules/posts';
import sections from './modules/sections';
import tags from './modules/tags';
import products from './modules/products';
import cart from './modules/cart';
import user from './modules/user';

export const store = new Vuex.Store({

	modules:{
		posts: posts,
		sections: sections,
		tags: tags,
		products: products,
		cart: cart,
		user: user,
	}

});