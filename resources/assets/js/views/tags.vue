<template>
	<div>
		<div class="container">
			<h2>Tags</h2>

			<div class="row">
			    <form class="col s12"  v-if="!isEdit" @submit.prevent @keydown="tag.errors.clear($event.target.name)">
			        <div class="row" v-if="!isEdit">
				        <div class="input-field col s6">
							 <div class="chips" id="tags">
							 	<!-- <input class="validate"> -->
							 </div>

				          <!-- <label for="section">Section</label> -->
				          <span class="brown-text" v-if="tag.errors.has('tags')" v-text="tag.errors.get('tags')"></span>
				        </div>
			        </div>
				     <div class="row">
				     	 <button class="btn waves-effect waves-light brown lighten-2" type="submit" name="action" @click="store" :disabled="tag.errors.any()">Submit<i class="material-icons right">send</i></button>
				     </div>
			    </form>


			    <form class="col s12" v-if="isEdit" @submit.prevent @keydown="tag.errors.clear($event.target.name)">
			        <div class="row">
				        <div class="input-field col s6">
							
					         	<input class="validate" v-model="tag.name">

				          <!-- <label for="section">Section</label> -->
				          <span class="brown-text" v-if="tag.errors.has('name')" v-text="tag.errors.get('name')"></span>
				        </div>
			        </div>
				     <div class="row">
				     	 <button class="btn waves-effect waves-light brown lighten-2" type="submit" name="action" @click="update" :disabled="tag.errors.any()">Edit<i class="material-icons right">send</i></button>
				     	 <button class="btn waves-effect waves-light brown lighten-2" @click="cancelEdit" :disabled="tag.errors.any()">Cancel<i class="material-icons right">close</i></button>

				     </div>
			    </form>
		  	</div>
			
   			<transition-group name="router-anim" tag="div">
				<div class="row" v-for="(tag, index) in tags" v-bind:key="tag.id">
				    <div class="col s12 m12">
				      <div class="card brown lighten-3 hoverable">
				        <div class="card-content white-text">
				          <span class="card-title">{{ tag.name }}</span>
				        </div>
				        <div class="card-action">
				          <a class="white-text" href="#" @click="destroy(tag.id, index)"><i class="material-icons">delete</i></a>
				          <a class="white-text" href="#" @click="edit(tag, index)"><i class="material-icons">edit</i></a>
				        </div>
				      </div>
				    </div>
				</div>
			</transition-group>

			<ul class="pagination">
			    <li class="waves-effect" v-bind:class="[{disabled: !pagination.prev_page_url}]"><a href="#!"><i class="material-icons" @click="navigate(pagination.prev_page_url)">chevron_left</i></a></li>
			    <!-- <li class="active"><a href="#!">1</a></li> -->
			    <li class="waves-effect disabled"><a href="#!">{{pagination.current_page}}/{{pagination.last_page}}</a></li>
			    <li class="waves-effect" v-bind:class="[{disabled: !pagination.next_page_url}]"><a href="#!" @click="navigate(pagination.next_page_url)"><i class="material-icons">chevron_right</i></a></li>
			 </ul>
		</div>
	</div>
</template>

<script>
	export default{
		data(){
			return {
				// tag: new Form({
				// 	id: '',
				// 	name: '',
				// 	tags: ''
				// }),
				// pagination: {},
				// api_url: 'api/tags',
				// edit: false,
				// tag_index: '',
				// chips: ''
			}
		},

		created(){
			this.index();
		},

		mounted()
		{
	  		this.initChips();	
		},
		computed: {
				...mapGetters({
						// tags: 'tags/tags',
						tags: 'tags/data',
						tag: 'tags/form',
						tag_index: 'tags/current_index',
						pagination: 'tags/pagination',
						isEdit: 'tags/isEdit',
						api_url: 'tags/api_url',
						chips: 'tags/chips',
				}),
		},

		methods: {
			index(){
				this.$store.dispatch('tags/_get', {token: true});
			},
			navigate(page_url){
				this.$store.dispatch('tags/navigate', page_url);
			},
			edit(section, index)
			{
				this.$store.commit('tags/edit', {element: section, index: index});
			},
			store(){
				if(!this.isEdit)
				{
					this.$store.dispatch('tags/_store', {token: true});
				}	
			},
			destroy(id, index){
				this.$store.dispatch('tags/_destroy', {id: id, index: index, token: true});
			},
			update(){
				if(this.isEdit)
				{
					this.$store.dispatch('tags/_update', {token: true})
						.then(response => {
							this.$nextTick(function () {
							 	this.initChips();
							});
						});
				}
			},
			// index(){
			// 	let vm = this;
			// 	axios.get(this.api_url)
			// 		.then(result => {
			// 			this.$store.state.tags.tags = result.data.data;
			// 			vm.paginate(result.data.meta, result.data.links);
			// 		})
			// },
			// paginate(meta, links){
			// 	let pagination = {
			// 		current_page: meta.current_page,
			// 		last_page: meta.last_page,
			// 		next_page_url: links.next,
			// 		prev_page_url: links.prev,
			// 		current_page_url: this.api_url+'?page='+meta.current_page,
			// 	}

			// 	this.pagination = pagination;
			// },
			// navigate(page_url){
			// 	if(page_url != null)
			// 	{
			// 		let vm = this;
			// 		axios.get(page_url)
			// 			.then(result => {
			// 				this.$store.state.tags.tags = result.data.data;
			// 				vm.paginate(result.data.meta, result.data.links);
			// 			})
			// 	}
			// },
			// store(){
				
			// 	this.tag.tags = this.chips.chipsData.map(function(obj){
			// 		return obj.tag;
			// 	});

			// 	if(this.edit === false)
			// 	{
			// 		this.tag.post(this.api_url, true)
			// 		.then(response => {
			// 			alert('Stored');
			// 			// this.$store.state.tags.tags.unshift(response.data);
			// 			// this.$store.commit('tags/store', response.data);
			// 			console.log(response);
			// 			this.$store.dispatch('tags/store', response.data);


			// 			while(this.chips.chipsData.length)
			// 			{
			// 				this.chips.deleteChip(0);
			// 			}
						
			// 		})
			// 		.catch(error => console.log(error.errors) );

			// 	}
			// 	else {
			// 		let url = this.api_url+'/'+this.tag.id;
			// 		this.tag.put(url, true)
			// 		.then(response => {
			// 			alert('Updated');
			// 			// this.$store.state.tags.tags.splice(this.tag_index, 1, response.data);
			// 			this.$store.commit({ type: 'tags/update', index: this.tag_index, data: response.data});
			// 			this.tag_index = '';
			// 			this.edit = false;
			// 			this.initChips();
			// 			/*let _this = this;
			// 			this.$nextTick(function () {
			// 			 _this.initChips();
			// 			});*/

			// 		})
			// 		.catch(error => { console.log(error.errors) });
			// 	}	
			// },
			// destroy(id, index){
			// 	if(confirm('Are you sure?')){
			// 		let url = this.api_url+'/'+id;
			// 		this.tag.delete(url, true)
			// 		.then(response => {
			// 			alert('Destroyed');
			// 			// this.$store.state.tags.tags.splice(index, 1);
			// 			this.$store.commit('tags/destroy', index);
			// 		})
			// 		.catch(error => { console.log(error.errors) });
			// 	}
			// },
			// update(tag, index){
			// 	this.edit = true;
			// 	Object.assign(this.tag, tag);

			// 	this.tag_index = index;
			// },
			// initChips()
			// {
			// 	let _this = this;
			// 	this.$nextTick(function () {
			// 		var elem = document.getElementById('tags');
		        
			//         var options = {
			//     		autocompleteOptions: {
			// 	      	data: {
			// 		        'Apple': null,
			// 		        'Microsoft': null,
			// 		        'Google': null
			// 		      },
			// 		      limit: Infinity,
			// 		      minLength: 1
			// 		    },
			// 		     placeholder: 'Enter a tag',
			//     		 secondaryPlaceholder: '+Tag',
			// 		}

			//    		this.chips = M.Chips.init(elem, options);
			//    	});
			// },
			initChips()
			{
				let _this = this;
				this.$nextTick(function () {
					var elem = document.getElementById('tags');
		        
			        var options = {
			    		autocompleteOptions: {
				      	data: {
					        /*'Apple': null,
					        'Microsoft': null,
					        'Google': null*/
					      },
					      limit: Infinity,
					      minLength: 1
					    },
					     placeholder: 'Enter a tag',
			    		 secondaryPlaceholder: '+Tag',
					}
					let instance = M.Chips.init(elem, options);
			   		this.$store.commit('tags/setChips', instance);
			   	});
			},
			cancelEdit()
			{
				// this.edit = false;
				// this.tag.name = '';
				// this.initChips();
				this.$store.commit('tags/resetEdit');
				this.$nextTick(function () {
				 	this.initChips();
				});
			},

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