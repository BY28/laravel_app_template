<template>
	<div>
		<h2>Posts</h2>

		<div class="row">
		    <form class="col s12" @submit.prevent @keydown="post.errors.clear($event.target.name)">
		    	<div class="row">
			        <div class="input-field col s6">
			          <input name="title" placeholder="Title" id="title" type="text" class="validate" v-model="post.title">
			          <!-- <label for="title">Title</label> -->
			          <span class="brown-text" v-if="post.errors.has('title')" v-text="post.errors.get('title')"></span>
			        </div>
		        </div>
		    	<div class="row">
			        <div class="input-field col s6">
						  <select class="browser-default" name="section" id="section" v-model="post.section">
						    <option value="" disabled selected>Choose a section</option>
						    <option v-for="section in sections" v-bind:key="section.id" v-bind:value="section.id">{{ section.name }}</option>
						  </select>
			          <!-- <label for="section">Section</label> -->
			          <span class="brown-text" v-if="post.errors.has('section')" v-text="post.errors.get('section')"></span>
			        </div>
		        </div>
		        <div class="row">
			        <div class="input-field col s6">
						 <div class="chips" id="post_tags">
						 	<!-- <input class="validate"> -->
						 </div>
			          <!-- <label for="section">Section</label> -->
			          <span class="brown-text" v-if="post.errors.has('tags')" v-text="post.errors.get('tags')"></span>
			        </div>
		        </div>
		        <div class="row">
		        	 <div class="file-field input-field col s6">
					      <div style="display: none;" class="btn brown lighten-2">
					        <span>File</span>
					        <input type="file" multiple @change="onFileSelected" ref="fileInput">
					      </div>
					      
					      <div style="display: none;" class="file-path-wrapper">
					        <input class="file-path validate" type="text" placeholder="Upload one or more files">
					      </div>
					      <button class="btn btn-lg brown lighten-2" @click="$refs.fileInput.click()">Add image</button>
					  
					    </div>
						<!-- -->
						<div class="row">
						    <div v-for="(file, file_index) in imageFiles" v-bind:key="file_index" >
							      <img class="responsive-img" :src="fileUrl(file)" width="20%">
							      <a href="#" @click="imageFiles.splice(file_index, 1)"><i class="material-icons">delete</i></a>
					    	</div>
						</div>
		        </div>
			     <div class="row">
			        <div class="input-field col s6">
			          <textarea name="body" placeholder="Body" id="textarea" class="materialize-textarea" v-model="post.body"></textarea>
			          <!-- <label for="textarea1">Textarea</label> -->
			          <span class="brown-text" v-if="post.errors.has('body')" v-text="post.errors.get('body')"></span>
			        </div>
			     </div>
			     <div class="row">
			     	 <button v-if="!isEdit" class="btn brown lighten-2" type="submit" name="action" @click="store" :disabled="post.errors.any()">Submit<i class="material-icons right">send</i></button>
			     	 <button v-if="isEdit" class="btn brown lighten-2" type="submit" name="action" @click="update" :disabled="post.errors.any()">Edit<i class="material-icons right">send</i></button>
			     </div>
		    </form>
	  	</div>
	
    <transition-group name="router-anim" tag="div">
		<div class="row" v-for="(post, index) in posts" v-bind:key="post.id">
		    <div class="col s12 m12">
		      <div class="card brown lighten-3 hoverable">
				  <div class="card">
				    <div class="card-image">

				    	
				    	<div v-for="(image, img_index) in post.images" v-bind:key="image.id" >
						      <img class="responsive-img" :src="imageUrl(image.name)">
						      <input type="file"  @change="onEditFileSelected($event, image, index, img_index)" ref="imgFileInput">
						      <a href="#" @click="delete_image(image.id, index, img_index)"><i class="material-icons">delete</i></a>
				    	</div>
				    </div>
				</div>
		        <div class="card-content white-text">
		          <span class="card-title">{{ post.title }}</span>
		          <span>{{ post.section.name }}</span>
		          <p>{{ post.body }}</p>
		          <span v-for="tag in post.tags" v-bind:key="tag.id">{{tag.name}}</span>
		        </div>
		        <div class="card-action">
		          <a class="white-text" href="#" @click="destroy(post.id, index)"><i class="material-icons">delete</i></a>
		          <a class="white-text" href="#" @click="edit(post, index)"><i class="material-icons">edit</i></a>
		        </div>
		      </div>
		    </div>
		</div>
	</transition-group>
		<ul class="pagination">
		    <li v-bind:class="[{disabled: !pagination.prev_page_url}]"><a href="#!"><i class="material-icons" @click="navigate(pagination.prev_page_url)">chevron_left</i></a></li>
		    <!-- <li class="active"><a href="#!">1</a></li> -->
		    <li class="disabled"><a href="#!">{{pagination.current_page}}/{{pagination.last_page}}</a></li>
		    <li v-bind:class="[{disabled: !pagination.next_page_url}]"><a href="#!" @click="navigate(pagination.next_page_url)"><i class="material-icons">chevron_right</i></a></li>
		 </ul>
	</div>
</template>

<script>
	export default{
		data(){
			return {
				// post: new Form({
				// 	id: '',
				// 	title: '',
				// 	body: '',
				// 	section: '',
				// 	tags: ''
				// }),
				// pagination: {},
				// api_url: 'api/posts',
				// isEdit: false,
				// post_index: '',
				// chips: '',
				// selectedFiles: null,
				// formData: '',
				// imageFiles: [],
			}

		},
		created()
		{
			// this.index();
			this.$store.dispatch('posts/index');
		},

		mounted(){
	        this.initChips();
			// this.$store.commit('posts/initChips', this);
		},

		computed: {
				...mapGetters({
						sections: 'posts/sections',
						tagsName: 'posts/tagsName',
						posts: 'posts/posts',
						post: 'posts/post',
						chips: 'posts/chips',
						isEdit: 'posts/isEdit',
						formData: 'posts/formData',
						selectedFiles: 'posts/selectedFiles',
						imageFiles: 'posts/imageFiles',
						post_index: 'posts/post_index',
						api_url: 'posts/api_url',
						pagination: 'posts/pagination',
				}),
		},

		methods: {
			index(){
				this.$store.commit('posts/index');
				// axios.get('api/sections') // make a mutation to get sections
				// 	.then(result => {
				// 		this.$store.state.posts.sections = result.data.data;
				// 	});
				// axios.get('api/tags') // make a mutation to get sections
				// 	.then(result => {
				// 		this.$store.state.posts.tags = result.data.data;
				// 		Object.assign(this.chips.options.autocompleteOptions.data, this.tagsName);
				// 	});
				// axios.get(this.api_url)
				// 	.then(result => {
				// 		console.log(result.data.data)
				// 		this.$store.state.posts.posts = result.data.data;
				// 		this.paginate(result.data.meta, result.data.links);
				// 	});
			},
			paginate(meta, links){
				this.$store.commit('posts/paginate', {meta: meta, links: links});
				// let pagination = {
				// 	current_page: meta.current_page,
				// 	last_page: meta.last_page,
				// 	next_page_url: links.next,
				// 	prev_page_url: links.prev,
				// 	current_page_url: this.api_url+'?page='+meta.current_page,
				// }

				// this.pagination = pagination;
			},
			navigate(page_url){
				this.$store.dispatch('posts/navigate', page_url);
				// if(page_url != null)
				// {
				// 	axios.get(page_url)
				// 		.then(result => {
				// 			this.$store.state.posts.posts = result.data.data;
				// 			this.paginate(result.data.meta, result.data.links);
				// 		})
				// }
			},
			store(){
				this.$store.dispatch('posts/store');
				// this.post.tags = this.chips.chipsData.map(function(obj){
				// 	return obj.tag;
				// });

				// 		let _post ={
				// 			images: {},
				// 			tags: this.post.tags,
				// 		};
				// if(this.isEdit === false)
				// {
				// 	this.post.post(this.api_url, true)
				// 	.then(response => {

				// 		while(this.chips.chipsData.length)
				// 		{
				// 			this.chips.deleteChip(0);
				// 		}

				// 		console.log(response)
				// 		alert('Stored');
				// 		Object.assign(_post, response.data);
				// 		// this.$store.state.posts.posts.unshift(response.data);
				// 		// this.$store.commit('posts/store', response.data);
				// 		console.log(response.data.id)
				// 		this.formData.append('post', response.data.id);
				// 		for(let i=0; i<this.imageFiles.length; i++){
				// 			this.formData.append('images[]', this.imageFiles[i]);
				// 		}
				// 		console.log(this.formData);
				// 		this.post.files('post', 'api/images', this.formData, true)
				// 			.then(response => {

				// 				Object.assign(_post.images, response.images);
				// 				console.log(_post)
				// 				this.imageFiles = [];
				// 				this.$store.dispatch('posts/store', _post);
				// 				//this.navigate(this.pagination.current_page_url);
				// 			})
				// 			.catch(error => console.log(error));
				// 		// 	console.log(response.data);
				// 		// this.$store.dispatch('posts/store', response.data);
				// 	})
				// 	.catch(error => console.log(error.errors) );

				// }
				// else {
				// 	//this.$store.commit('posts/update');
				// 	let url = this.api_url+'/'+this.post.id;
				// 	this.post.put(url, true)
				// 	.then(response => {

				// 		while(this.chips.chipsData.length)
				// 		{
				// 			this.chips.deleteChip(0);
				// 		}

				// 		Object.assign(_post, response.data);

				// 		this.formData.append('post', response.data.id);
				// 		for(let i=0; i<this.imageFiles.length; i++){
				// 			this.formData.append('images[]', this.imageFiles[i]);
				// 		}
				// 		this.post.files('post', 'api/images', this.formData, true)
				// 			.then(response => {

								
				// 				Object.assign(_post.images, response.images);
				// 				for(let image in this.post.images)
				// 				{
				// 					_post.images.unshift(this.post.images[image]);
				// 				}
				// 				console.log(_post.images)
				// 				this.imageFiles = [];

				// 				this.$store.commit({ type: 'posts/update', index: this.post_index, data: _post});
				// 				//this.navigate(this.pagination.current_page_url);
				// 			})
				// 			.catch(error => console.log(error));
				// 		alert('Updated');
				// 		// this.$store.state.posts.posts.splice(this.post_index, 1, response.data);
				// 		// this.$store.commit({ type: 'posts/update', index: this.post_index, data: response.data});
				// 		this.post_index = '';
				// 		this.isEdit = false;
				// 	})
				// 	.catch(error => { console.log(error.errors) });
				// }	
			},
			update(){
				this.$store.dispatch('posts/update');
			},
			edit(post, index){
				this.$store.commit('posts/edit', {post: post, index: index});
				/*this.$store.state.posts.isEdit = true;
				Object.assign(this.post, post);
				this.post.section = this.post.section.id;
				for(var k in post.tags)
				{
					console.log(post.tags[k])
					this.chips.addChip({tag: post.tags[k].name});
				}
				

				this.post_index = index;*/
			},
			destroy(id, index){
				this.$store.dispatch('posts/destroy', {id: id, index: index});
				/*if(confirm('Are you sure?')){
					let url = this.api_url+'/'+id;
					this.post.delete(url, true)
					.then(response => {
						alert('Destroyed');
						// this.$store.state.posts.posts.splice(index, 1);
						this.$store.commit('posts/destroy', {index: index});
					})
					.catch(error => { console.log(error.errors) });
				}*/
			},
			onFileSelected(event)
			{
				this.$store.commit('posts/onFileSelected', event);
				// this.selectedFile = event.target.files[0];
				/*this.formData = new FormData();*/
				// this.formData.append('images', this.selectedFile);

				/*this.selectedFiles = event.target.files;*/
				// let attachments = [];
				/*for(let i=0; i<this.selectedFiles.length; i++){
					// attachments.push(this.selectedFiles[i]);	
					this.imageFiles.push(this.selectedFiles[i]);	
				}*/
				// for(let i=0; i<attachments.length; i++){
				// 	this.formData.append('images[]', attachments[i]);
				// }
				// attachments.push(selectedFiles)
			},
			imageUrl(name)
			{
				return 'uploads/posts/'+name;
			},
			fileUrl(file)
			{
				return URL.createObjectURL(file);
			},
			onEditFileSelected(event, image, post_index, img_index)
			{
				/*// this.selectedFile = event.target.files[0];
				let form = new FormData();
				// this.formData.append('images', this.selectedFile);

				let selectedFile = event.target.files[0];
				console.log(selectedFile)
				form.append('image', selectedFile, selectedFile.name);
				// attachments.push(selectedFiles)

				this.edit_image(image, post_index, img_index, form)*/
				this.$store.dispatch('posts/updateFile', {event: event, post_index: post_index, img_index: img_index, image: image});
			},
			edit_image(image, post_index, img_index, form)
			{
				// this.$store.commit('posts/updateFile', {post_index: post_index, img_index: img_index, image: response.image});
				/*console.log('EDIT ' + image.id + ' ' + post_index + ' ' + img_index);

				let url = 'api/images/'+image.id;
					this.post.files('post', url, form, true)
					.then(response => {
						console.log(response);
						this.$store.commit('posts/updateFile', {post_index: post_index, img_index: img_index, image: response.image});
						alert('Updated');
						// this.$store.state.posts.posts.splice(this.post_index, 1, response.data);
						// this.$store.commit({ type: 'posts/update', index: this.post_index, data: response.data});
						// this.post_index = '';
						// this.isEdit = false;
					})
					.catch(error => { console.log(error) });*/

			},
			delete_image(id, post_index, img_index)
			{
				this.$store.dispatch('posts/deleteFile', {post_id: id, post_index: post_index, img_index: img_index});
				/*if(confirm('Are you sure?')){
					let url = 'api/images/'+id;
					this.post.delete(url, true)
					.then(response => {
						alert('Destroyed');
						// this.$store.state.posts.posts.splice(index, 1);
						// this.$store.commit('posts/destroy', index);
						this.$store.commit('posts/deleteFile', {post_index: post_index, img_index: img_index});
					})
					.catch(error => { console.log(error.errors) });
				}*/
			},
			initChips()
			{
				let _this = this;
				this.$nextTick(function () {
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
			   		this.$store.commit('posts/setChips', instance);
			   	});
			}
		}
	}
</script>

<style type="text/css">
.pagination li.active {
    background-color: #a1887f;
}
.chip:active, .chip:focus{
	background-color: #a1887f !important;
}
</style>