import state from '../libs/state';
import mutations from '../libs/mutations';
import actions from '../libs/actions';
import getters from '../libs/getters';

export default {

	namespaced: true,

	state: {
		// tags: [],
		...state,
		form: new Form({
			id: '',
			name: '',
			tags: ''
		}),
		isEdit: false,
		api_url: 'api/tags',
		chips: '',
	}, 

	mutations:{

		fill: mutations.fill,
		update: mutations.update,
		delete: mutations.delete,
		edit: mutations.edit,
		resetEdit: mutations.resetEdit,
		paginate: mutations.paginate,

		add(state, payload){
			for (var i = 0; i < payload.length; i++) { 
				state.data.unshift(payload[i]);
			}
		},

		setChipsAutocomplete(state, payload){ // Adds the tags autocomplete data to the chips instance
			Object.assign(state.chips.options.autocompleteOptions.data, payload);
		},

		setTagsFromChips(state, payload){ // Get the chips data and assigns them to the tags array
			state.form.tags = state.chips.chipsData.map(function(obj){ // getting the tags into an array
				return obj.tag;
			});
		},

		setChips(state, payload){ // Sets the chips instance
			state.chips = payload;
		},

		resetChips(state, payload){ // Deletes all the chips
			while(state.chips.chipsData.length)
			{
				state.chips.deleteChip(0);
			}
		},

		// store(state, payload){
		// 	for (var i = 0; i < payload.length; i++) { 
		// 		state.tags.unshift(payload[i]);
		// 	}
		// },

		// update(state, payload){
		// 	state.tags.splice(payload.index, 1, payload.data);
		// },

		// destroy(state, payload){
		// 	state.tags.splice(payload, 1);
		// }

	},

	actions:{
		// store(context, payload){
		// 	context.commit('store', payload);
		// }
		
		_get: actions._get,
		_update: actions._update,
		_destroy: actions._destroy,
		navigate: actions.navigate,

		_store({commit, dispatch, state}, payload){
			return new Promise((resolve, reject) => {
				commit('setTagsFromChips');
				
					state.form.post(state.api_url, true) // AJAX API post
					.then(response => {
						commit('resetChips'); // reset the tags data
						console.log(response.data);
						commit('add', response.data);
						resolve(response);
					})
					.catch(error => {
						console.log(error.errors)
						reject(error);
					});
			});
		},


	},

	getters:{
		...getters,
		isEdit(state){
			return state.isEdit;
		},
		// tags(state){
		// 	return state.tags;
		// },
		tagsName(state)
		{
			let tagsName = new Object();

			for(var k in state.tags)
			{
				tagsName[state.tags[k].name]= null;
			}

			return tagsName;
		}
	}
	
}