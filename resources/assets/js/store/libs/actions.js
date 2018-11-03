
export default{

	_get({commit, state}, payload){
		return new Promise((resolve, reject) => {
			state.form.get(state.api_url, payload.token)
			.then(response => {
				commit('fill', response.data);
				commit('paginate', {meta: response.meta, links: response.links});
				resolve(response);
			})
			.catch(error => {
				reject(error);
			});
		});
	},
	
	_store({commit, state}, payload){
		return new Promise((resolve, reject) => {
			state.form.post(state.api_url, payload.token)
				.then(response => {
					commit('add', response.data);
					resolve(response);
				})
				.catch(error => {
					reject(error);
				});

			});
	},

	_update({commit, state}, payload){
		return new Promise((resolve, reject) => {
			let url = state.api_url+'/'+state.form.id;
			state.form.put(url, payload.token)
			.then(response => {
				commit('update', response.data);
				commit('resetEdit');
				resolve(response);
			})
			.catch(error => {
				reject(error);
			});
		});
	},

	_destroy({commit, state}, payload){
		return new Promise((resolve, reject) => {
			let url = state.api_url+'/'+payload.id;
			state.form.delete(url, payload.token)
				.then(response => {
					commit('delete', payload.index);
					resolve(response);
				})
				.catch(error => {
					reject(error);
				});

		});
	},


	navigate({commit}, payload){
		if(payload != null)
		{
			axios.get(payload)
				.then(response => {
					commit('fill', response.data.data);
					commit('paginate', {meta: response.data.meta, links: response.data.links});
					// resolve(response);
				})
				.catch(error => {
					// reject(error);
					console.log(error);
				});
		}
	},

}