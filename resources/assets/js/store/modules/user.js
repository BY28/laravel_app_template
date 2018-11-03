
export default{

    namespaced: true,
	
	state:{
		
		form: new Form(),
		user: {},
		logged_in: false,
		// token: localStorage.getItem('token') || ''
	},	

	mutations:{
		
		// signin(state, payload){
		// 	if(payload.token)
		// 	{
		// 		const token = payload.token;
		// 		const base64Url = token.split('.')[1];
		// 		const base64 = base64Url.replace('-', '+').replace('_', '/');
		// 		console.log(JSON.parse(window.atob(base64)));
		// 		localStorage.setItem('token', token);
		// 	}
			
		// 	this.commit('user/getUser');

		// 	state.logged_in = true;

		// },

		signout(state){
			state.user = {};
			state.logged_in = false;
			localStorage.removeItem('token');
		},

		getUser(state){
			 return new Promise((resolve, reject) => {
				state.form.get('api/user', true)
				.then(response => {
					Object.assign(state.user, response.user);
					state.logged_in = true;
					 resolve(response);
				})
				.catch(error => {
					console.log(error)
					state.logged_in = false;
					 reject(error);
				});
			 });
		},

		setUser(state, payload){
			Object.assign(state.user, payload.user);
		},

		setLoggedIn(state, payload){
			state.logged_in = payload.logged_in;
		},

		setToken(state, payload){
			const token = payload.token;
			const base64Url = token.split('.')[1];
			const base64 = base64Url.replace('-', '+').replace('_', '/');
			console.log(JSON.parse(window.atob(base64)));
			localStorage.setItem('token', token);
		},

	},

	actions:{
		signin({state, commit}, payload){
			return new Promise((resolve, reject) => {
				
				payload.user.post('api/user/signin')
					.then(response => { 
	                    commit('setToken', {token: response.token});
	                    commit('setUser', {user: response.user});
	                    commit('setLoggedIn',  {logged_in: true});
						resolve(response);
                     })
					.catch(error => {
						reject(error);
					});
			 });
		},

		signout({state, commit}){
			return new Promise((resolve, reject) => {
				
				state.form.post('api/user/signout', true)
					.then(response => {
						commit('signout');
					 	resolve(response);
					})
					.catch(error => {
							reject(error);
						}
					);
			
			 });
		},

		checkUser({state, commit}){
			 return new Promise((resolve, reject) => {
				state.form.get('/api/user', true)
				.then(response => {
					Object.assign(state.user, response.user);
					
					if(response.token)
					{
						commit('setToken', {token: response.token});
					}

					commit('setLoggedIn',  {logged_in: true});
					resolve(response);
				})
				.catch(error => {
					console.log(error)
					commit('setLoggedIn',  {logged_in: false});
					 reject(error);
				});
			 });
		},
	},

	getters:{
		user(state){
			return state.user;
		},

		logged_in(state){
			return state.logged_in;
		},

		// isAuthenticated(state){
		// 	return !!state.token;
		// }
	}

}