
export default {
      
    namespaced: true,

	state: {
		posts: [],
		tags: [],
		sections: [],
		post: new Form({
			id: '',
			title: '',
			body: '',
			section: '',
			tags: ''
		}),
		post_index: '',
		formData: '',
		selectedFiles: null,
		imageFiles: [],
		chips: '',
		isEdit: false,
		pagination: {},
		api_url: 'api/posts',
	}, 

	mutations:{

		/*index(state, payload){
			axios.get('api/sections')
				.then(result => {
					state.sections = result.data.data;
				});
			axios.get('api/tags') 
				.then(result => {
					state.tags = result.data.data;
					Object.assign(state.chips.options.autocompleteOptions.data, state.tagsName);
				});
			axios.get(state.api_url)
				.then(result => {
					state.posts = result.data.data;
					this.commit('posts/paginate', {meta: result.data.meta, links: result.data.links})
				});
		},*/

		paginate(state, payload){
			let pagination = {
					current_page: payload.meta.current_page,
					last_page: payload.meta.last_page,
					next_page_url: payload.links.next,
					prev_page_url: payload.links.prev,
					current_page_url: state.api_url+'?page='+payload.meta.current_page,
				}

				state.pagination = pagination;
		},

		/*navigate(state, payload){
			if(payload != null)
			{
				axios.get(payload)
					.then(result => {
						state.posts = result.data.data;
						this.commit('posts/paginate', {meta: result.data.meta, links: result.data.links});
					})
			}
		},*/

		/*store(state, payload){

			this.commit('posts/setTagsFromChips');
			
			let _post ={ // creating a temporary _post object
				images: {},
				tags: state.post.tags,
			};
			
			if(!state.isEdit) // If not in edit mode
			{
				state.post.post(state.api_url, true) // AJAX API post
				.then(response => {

					this.commit('posts/resetChips'); // reset the tags data

					Object.assign(_post, response.data); // assign stored data to _post
					
					if(state.imageFiles.length) // if files selected
					{
						let images = this.dispatch('posts/storeFiles', {post_id: response.data.id})
											.then(response => {
													Object.assign(_post.images, response.images);
													console.log(_post);
													state.posts.unshift(_post);
												})
											.catch(error => console.log(error)); // store and return the stored image files

					}
					else
					{
						state.posts.unshift(_post);
					}
				})
				.catch(error => console.log(error.errors) );
			}

		},*/

		/*update(state, payload){
			
			this.commit('posts/setTagsFromChips');
			// state.post.tags = state.chips.chipsData.map(function(obj){ // getting the tags into an array
				// return obj.tag;
			// });
			
			let _post ={ // creating a temporary _post object
				images: {},
				tags: state.post.tags,
			};

			let url = state.api_url+'/'+state.post.id;
			state.post.put(url, true)
			.then(response => {

				this.commit('posts/resetChips');

				Object.assign(_post, response.data);
				
				if(state.imageFiles.length) // if files selected
				{
					let images = this.dispatch('posts/storeFiles', {post_id: response.data.id})
										.then(response => {
											for(let image in response.images)
											{
												_post.images.push(response.images[image]);
											}
												// Object.assign(_post.images, response.images);
												state.posts.splice(state.post_index, 1, _post);
											})
										.catch(error => console.log(error)); // store and return the stored image files

				}
				else
				{
					state.posts.splice(state.post_index, 1, _post);
				}
				
				state.post_index = '';
				state.isEdit = false;
			})
			.catch(error => { console.log(error.errors) });

		},*/

		edit(state, payload){

			state.isEdit = true; // setting edit state to true
			
			Object.assign(state.post, payload.post); // assign post values to post object

			state.post.section = state.post.section.id; // adding section id to the post object

			for(var k in payload.post.tags)
			{
				state.chips.addChip({tag: payload.post.tags[k].name}); // adding tags to the chip instance
			}
			
			state.post_index = payload.index; // setting the post_index variable
		},

		/*destroy(state, payload){
			
			if(confirm('Are you sure?')){
				let url = state.api_url+'/'+payload.id;
				state.post.delete(url, true)
				.then(response => {
					alert('Destroyed');
					state.posts.splice(payload.index, 1);
				})
				.catch(error => { console.log(error.errors) });
			}
		},*/

		onFileSelected(state, payload)
		{
			state.formData = new FormData();

			state.selectedFiles = payload.target.files;
			for(let i=0; i<state.selectedFiles.length; i++){
				state.imageFiles.push(state.selectedFiles[i]);	
			}
		},

		/*updateFile(state, payload){
			let form = new FormData();

			let selectedFile = payload.event.target.files[0];
			console.log(selectedFile)
			form.append('image', selectedFile, selectedFile.name);
			
			let url = 'api/images/'+payload.image.id;
			state.post.files('post', url, form, true)
			.then(response => {
				console.log(response);
				alert('Updated');
				state.posts[payload.post_index].images.splice(payload.img_index, 1, response.image);
			})
			.catch(error => { console.log(error) });

		},*/

		/*deleteFile(state, payload){
			let url = 'api/images/'+payload.post_id;
			state.post.delete(url, true)
			.then(response => {
				alert('Destroyed');
				state.posts[payload.post_index].images.splice(payload.img_index, 1);
			})
			.catch(error => { console.log(error.errors) });
			
		},*/


		/*storeFiles(state, payload){
			state.formData.append('post', payload.post_id); // add the post_id to the formData
			
			for(let i=0; i<state.imageFiles.length; i++){
				state.formData.append('images[]', state.imageFiles[i]); // add image files to the formData
			}

			state.post.files('post', 'api/images', state.formData, true) // API AJAX post image files
				.then(response => {
					state.imageFiles = []; // reset imageFiles array
					return response.images; // return stored images
				})
				.catch(error => console.log(error));
				
		},*/

		/* */

		fill(state, payload){
			state.posts = payload;
		},

		add(state, payload){
			state.posts.unshift(payload);
		},


		update(state, payload){
			state.posts.splice(state.post_index, 1, payload);
		},

		delete(state, payload){
			state.posts.splice(payload, 1);
		},

		setSections(state, payload){
			state.sections = payload;
		},

		setTags(state, payload){
			state.post.tags = payload;
		},

		setChipsAutocomplete(state, payload){
			Object.assign(state.chips.options.autocompleteOptions.data, state.tagsName);
		},

		setTagsFromChips(state, payload){
			state.post.tags = state.chips.chipsData.map(function(obj){ // getting the tags into an array
				return obj.tag;
			});
		},


		initChips(state, payload)
		{	
			let _this = this;
			payload.$nextTick(function () {
				var elem = document.getElementById('post_tags');
		     
		        var options = {
		    		autocompleteOptions: {
			      	data: {
				        'Apple': null,
				        'Microsoft': null,
				        'Google': null
				      },
				      limit: Infinity,
				      minLength: 1
				    },
				     placeholder: 'Enter a tag',
		    		 secondaryPlaceholder: '+Tag',
				}
				let instance = M.Chips.init(elem, options);
				_this.commit('posts/setChips', instance);
		   	});
		},

		setChips(state, payload){
			state.chips = payload;
		},

		resetChips(state, payload){
			while(state.chips.chipsData.length)
			{
				state.chips.deleteChip(0);
			}
		},

		updateFile(state, payload){
			state.posts[payload.post_index].images.splice(payload.img_index, 1, payload.image);
		},

		deleteFile(state, payload){
			state.posts[payload.post_index].images.splice(payload.img_index, 1);
		},

	},

	actions:{

		index({commit, state}, payload){
			axios.get('api/sections')
				.then(result => {
					commit('setSections', result.data.data);
				});
			axios.get('api/tags') 
				.then(result => {
					commit('setTags', result.data.data);
					commit('setChipsAutocomplete');
				});
			axios.get(state.api_url)
				.then(result => {
					commit('fill', result.data.data);
					commit('paginate', {meta: result.data.meta, links: result.data.links})
				});
		},

		navigate({commit, state}, payload){
			if(payload != null)
			{
				axios.get(payload)
					.then(result => {
						commit('fill', result.data.data);
						commit('paginate', {meta: result.data.meta, links: result.data.links});
					})
			}
		},

		store({commit, dispatch, state}, payload){
			return new Promise((resolve, reject) => {
				commit('setTagsFromChips');
				
				let _post ={ // creating a temporary _post object
					images: {},
					tags: state.post.tags,
				};
				
				if(!state.isEdit) // If not in edit mode
				{
					state.post.post(state.api_url, true) // AJAX API post
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
												.catch(error => console.log(error)); // store and return the stored image files

						}
						else
						{
							commit('add', _post);
						}
					})
					.catch(error => console.log(error.errors) );
				}
			});
		},

		update({commit, dispatch, state}, payload){
			return new Promise((resolve, reject) => {
				commit('setTagsFromChips');
				/*state.post.tags = state.chips.chipsData.map(function(obj){ // getting the tags into an array
					return obj.tag;
				});*/
				
				let _post ={ // creating a temporary _post object
					images: {},
					tags: state.post.tags,
				};

				let url = state.api_url+'/'+state.post.id;
				state.post.put(url, true)
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
													// Object.assign(_post.images, response.images);
													commit('update', _post)
												})
											.catch(error => console.log(error)); // store and return the stored image files

					}
					else
					{
						commit('update', _post)
					}
					
					state.post_index = '';
					state.isEdit = false;
				})
				.catch(error => { console.log(error.errors) });
			});
		},

		destroy({commit, state}, payload){
			return new Promise((resolve, reject) => {

				if(confirm('Are you sure?')){
					let url = state.api_url+'/'+payload.id;
					state.post.delete(url, true)
					.then(response => {
						alert('Destroyed');
						commit('delete', payload.index);
					})
					.catch(error => { console.log(error.errors) });
				}

			});
		},

		storeFiles({state}, payload){

			return new Promise((resolve, reject) => {

				state.formData.append('post', payload.post_id); // add the post_id to the formData
			
				for(let i=0; i<state.imageFiles.length; i++){
					state.formData.append('images[]', state.imageFiles[i]); // add image files to the formData
				}

				state.post.files('post', 'api/images', state.formData, true) // API AJAX post image files
					.then(response => {
						state.imageFiles = []; // reset imageFiles array
						resolve(response);
					})
					.catch(error => reject(error));

			});
				
		},

		updateFile({commit, state}, payload){
			return new Promise((resolve, reject) => {

				let form = new FormData();

				let selectedFile = payload.event.target.files[0];
				console.log(selectedFile)
				form.append('image', selectedFile, selectedFile.name);
				
				let url = 'api/images/'+payload.image.id;
				state.post.files('post', url, form, true)
				.then(response => {
					console.log(response);
					alert('Updated');
					commit('updateFile', {post_index: payload.post_index, img_index: payload.img_index, image: response.image})
				})
				.catch(error => { console.log(error) });
			});
		},

		deleteFile({commit, state}, payload){
			return new Promise((resolve, reject) => {

				let url = 'api/images/'+payload.post_id;
				state.post.delete(url, true)
				.then(response => {
					alert('Destroyed');
					commit('deleteFile', {post_index: payload.post_index, img_index: payload.img_index});
				})
				.catch(error => { console.log(error.errors) });

			});
			
		},
	},

	getters:{
		posts(state){
			return state.posts;
		},

		post(state){
			return state.post;
		},
		
		post_index(state){
			return state.post_index;
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

