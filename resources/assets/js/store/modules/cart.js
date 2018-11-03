
export default{
	namespaced: true,

	state:{
		data: [],
	},

	mutations:{
		add(state, payload){
			let record = state.data.find(item => item.id == payload.id);
			if(!record)
			{
				// let item = {};
				// item.id = ^payload.id;
				// item.name = payload.name;
				// item.price = payload.price;
				// item.quantity = 1;
				// state.data.unshift(item);
				console.log(payload)
				state.data.unshift({ ...payload, quantity: 1});
			}
			else
			{
				record.quantity++;
			}
		},

		delete(state, payload){
			state.data.splice(payload, 1);
		},

		save(state, payload){
			window.localStorage.setItem('cart', JSON.stringify(state.data));
		},

		init(state, payload){
			let cart = window.localStorage.getItem('cart');
			state.data = cart ? JSON.parse(cart) : [];
		}
	},

	actions:{
		store({commit}, payload)
		{
			commit('add', payload);
			commit('save');
		},
		destroy({commit}, payload)
		{
			commit('delete', payload);
			commit('save'); 
		}
	},

	getters:{
		data(state)
		{
			return state.data;
		},
		item_number(state)
		{
			return (state.data) ? state.data.length : 0;
		},
		item_quantity(state)
		{
			return state.data.reduce((accumulator, item) => { return accumulator+item.quantity; }, 0);
		},
		total_price(state)
		{
			return state.data.reduce((accumulator, item) => { return accumulator+(item.price*item.quantity); }, 0);
		},
	}
}