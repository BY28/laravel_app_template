
export default{
	
	paginate(state, payload){
		let pagination = { // pagination object
			current_page: payload.meta.current_page,
			last_page: payload.meta.last_page,
			next_page_url: payload.links.next,
			prev_page_url: payload.links.prev,
			current_page_url: state.api_url+'?page='+payload.meta.current_page,
		}

		state.pagination = pagination; // assign pagination informations to the pagination state object
	},
	fill(state, payload){ // Fill the posts array
		state.data = payload;
	},

	add(state, payload){ // Adds an element to the first element of the posts array
		state.data.unshift(payload);
	},


	update(state, payload){ // Updates a specific element in the posts array
		state.data.splice(state.current_index, 1, payload);
	},

	delete(state, payload){ // Deletes a specific element of the posts array 
		state.data.splice(payload, 1);
	},
	
	edit(state, payload){
		state.isEdit = true; // setting edit state to true
			
		Object.assign(state.form, payload.element); // assign post values to post object

		state.current_index = payload.index;
	},

	resetEdit(state, payload)
	{
		state.isEdit = false;
		state.current_index = '';
		state.form.reset();
	},
}