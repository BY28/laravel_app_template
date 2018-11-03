import state from '../libs/state';
import mutations from '../libs/mutations';
import actions from '../libs/actions';
import getters from '../libs/getters';

export default {

	namespaced: true,

	state: {
		...state,
		form: new Form({
			id: '',
			name: '',
		}),
		api_url: 'api/sections',
		isEdit: false,
	}, 

	mutations:{
		...mutations,
	},

	actions:{
		...actions,
	},

	getters:{
		...getters,
		isEdit(state){
			return state.isEdit;
		},
	}
	
}