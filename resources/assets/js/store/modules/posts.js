import actions from '../libs/actions';
export default {
      
    namespaced: true,

	state: {
		data: [],
		tags: [],
		sections: [],
		form: new Form({
			id: '',
			title: '',
			body: '',
			section: '',
			tags: ''
		}),
		current_index: '',
		formData: '',
		selectedFiles: null,
		imageFiles: [],
		chips: '',
		isEdit: false, // EDIT IN VUE!!!
		pagination: {},
		api_url: 'api/posts',
		sections_api: 'api/sections',
		tags_api: 'api/tags',
	}, 

	mutations:{

		paginate(state, payload){
			let pagination = { // pagination object
					current_page: payload.current_page,
					last_page: payload.last_page,
					next_page_url: payload.next_page_url,
					prev_page_url: payload.prev_page_url,
					current_page_url: state.api_url+'?page='+payload.current_page,
				}

				state.pagination = pagination; // assign pagination informations to the pagination state object
		},

		edit(state, payload){

			state.isEdit = true; // setting edit state to true
			
			Object.assign(state.form, payload.element); // assign post values to post object

			state.form.section = state.form.section.id; // adding section id to the post object

			for(var k in payload.element.tags)
			{
				state.chips.addChip({tag: payload.element.tags[k].name}); // adding tags to the chip instance
			}
			
			state.current_index = payload.index; // setting the current_index variable
		},

		resetEdit(state, payload){ // Clear selected post index and reset isEdit state to false after submitting edit form
			state.current_index = '';
			state.isEdit = false;
			state.form.reset();
		},

		onFileSelected(state, payload)
		{
			state.formData = new FormData();

			state.selectedFiles = payload.target.files; // gets all the selected files
			for(let i=0; i<state.selectedFiles.length; i++){
				state.imageFiles.push(state.selectedFiles[i]); // assigns the files to the imagesFIles Array	
			}
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

		setSections(state, payload){ // Fill the sections array
			state.sections = payload;
		},

		setTags(state, payload){ // Fill the tags array
			state.tags = payload;
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

		updateFile(state, payload){ // Updates a specific file in the posts.images array
			state.data[payload.current_index].images.splice(payload.img_index, 1, payload.image);
		},

		deleteFile(state, payload){ // Deletes a specific file in the posts.images array
			state.data[payload.current_index].images.splice(payload.img_index, 1);
		}

	},

	actions:{
		/*...actions,*/

		index({commit, dispatch, state}, payload){
			dispatch('getSections');
			// dispatch('getTags');
			dispatch('getPosts');
		},

		getSections({commit, state}, payload){
			return new Promise((resolve, reject) => {
				axios.get(state.sections_api)
				.then(response => {
					commit('setSections', response.data.data);
					resolve(response);
				})
				.catch(error => {
					reject(error);
				});
			});
		},

		getTags({commit, getters, state}, payload){
			return new Promise((resolve, reject) => {
				axios.get(state.tags_api) 
				.then(response => {
					commit('setTags', response.data.data);
					commit('setChipsAutocomplete', getters.tagsName);
					resolve(response);
				})
				.catch(error => {
					reject(error);
				});
			});
		},

		getPosts({commit, state}, payload){
			return new Promise((resolve, reject) => {
				axios.get(state.api_url)
				.then(response => {
					console.log(response);
					// console.log(response.data.data[0].tags.find(tags => tags.id == 88))
					commit('fill', response.data.data);
					commit('paginate', response.data);
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
						commit('paginate', response.data);
						// resolve(response);
					})
					.catch(error => {
						// reject(error);
						console.log(error);
					});
			}
		},

		store({commit, dispatch, state}, payload){
			return new Promise((resolve, reject) => {
				commit('setTagsFromChips');
				
				let _post ={ // creating a temporary _post object
					images: {},
					tags: state.form.tags,
				};
				
				if(!state.isEdit) // If not in edit mode
				{
					state.form.post(state.api_url, true) // AJAX API post
					.then(response => {

						commit('resetChips'); // reset the tags data

						Object.assign(_post, response.data); // assign stored data to _post
						
						if(state.imageFiles.length) // if files selected
						{
							let images = dispatch('storeFiles', {post_id: response.data.id})
												.then(response => {
														Object.assign(_post.images, response.images);
														commit('add', _post);
													})
												.catch(error => {
														console.log(error)
														reject(error);
													}); // store and return the stored image files

						}
						else
						{
							commit('add', _post);
						}
						resolve(response);
					})
					.catch(error => {
						console.log(error.errors)
						reject(error);
					});
				}
			});
		},

		update({commit, dispatch, state}, payload){
			return new Promise((resolve, reject) => {
				commit('setTagsFromChips');
				
				let _post ={ // creating a temporary _post object
					images: {},
					tags: state.form.tags,
				};

				let url = state.api_url+'/'+state.form.id;
				state.form.put(url, true)
				.then(response => {

					commit('resetChips');

					Object.assign(_post, response.data);
					
					if(state.imageFiles.length) // if files selected
					{
						let images = dispatch('storeFiles', {post_id: response.data.id})
											.then(response => {
													for(let image in response.images)
													{
														_post.images.push(response.images[image]);
													}
													commit('update', _post);
													commit('resetEdit');
												})
												.catch(error => {
														console.log(error)
														reject(error);
													}); // store and return the stored image files

					}
					else
					{
						commit('update', _post);
						commit('resetEdit');
					}
					resolve(response);
					
				})
				.catch(error => {
					console.log(error.errors)
					reject(error);
				});
			});
		},

		destroy({commit, state}, payload){
			return new Promise((resolve, reject) => {

				if(confirm('Are you sure?')){
					let url = state.api_url+'/'+payload.id;
					state.form.delete(url, true)
					.then(response => {
						alert('Destroyed');
						commit('delete', payload.index);
						resolve(response);
					})
					.catch(error => {
						console.log(error.errors)
						reject(error);
					});
				}

			});
		},

		storeFiles({state}, payload){

			return new Promise((resolve, reject) => {

				state.fomrData = new FormData();

				state.formData.append('post', payload.post_id); // add the post_id to the formData
			
				for(let i=0; i<state.imageFiles.length; i++){
					state.formData.append('images[]', state.imageFiles[i]); // add image files to the formData
				}

				state.form.files('post', 'api/images', state.formData, true) // API AJAX post image files
					.then(response => {
						state.imageFiles = []; // reset imageFiles array
						resolve(response);
					})
					.catch(error => {
						reject(error);
					});

			});
				
		},

		updateFile({commit, state}, payload){
			return new Promise((resolve, reject) => {

				let form = new FormData();

				let selectedFile = payload.event.target.files[0];
				console.log(selectedFile)
				form.append('image', selectedFile, selectedFile.name);
				
				let url = 'api/images/'+payload.image.id;
				state.form.files('post', url, form, true)
				.then(response => {
					console.log(response);
					alert('Updated');
					commit('updateFile', {current_index: payload.current_index, img_index: payload.img_index, image: response.image});
					resolve(response);
				})
					.catch(error => {
						console.log(error.errors)
						reject(error);
					});
			});
		},

		deleteFile({commit, state}, payload){
			return new Promise((resolve, reject) => {

				let url = 'api/images/'+payload.id;
				state.form.delete(url, true)
				.then(response => {
					alert('Destroyed');
					commit('deleteFile', {current_index: payload.current_index, img_index: payload.img_index});
					resolve(response);
				})
				.catch(error => {
					console.log(error.errors)
					reject(error);
				});

			});
			
		},

		show({state}, payload)
		{
			return new Promise((resolve, reject) => {

				axios.get('/api/post/' + payload.slug)
					.then(response => {
						resolve(response);
					})
					.catch(error => {
						reject(error);
					});

			});
		}

	},

	getters:{
		data(state){
			return state.data;
		},

		form(state){
			return state.form;
		},
		
		current_index(state){
			return state.current_index;
		},
		
		formData(state){
			return state.formData;
		},
		
		selectedFiles(state){
			return state.selectedFiles;
		},
		
		imageFiles(state){
			return state.imageFiles;
		},
		
		chips(state){
			return state.chips;
		},
		
		isEdit(state){
			return state.isEdit;
		},
		
		pagination(state){
			return state.pagination;
		},
		
		api_url(state){
			return state.api_url;
		},

		sections(state){
			return state.sections;
		},
		
		tagsName(state) // get tags from state and adds them to the tagsName object as keys with no data tagsName{'kay': null};
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

