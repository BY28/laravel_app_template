<template>
	<div>
		<div class="container">
			<h2>Sections</h2>

			<div class="row">
			    <form class="col s12" @submit.prevent @keydown="section.errors.clear($event.target.name)">
			    	<div class="row">
				        <div class="input-field col s6">
				          <input name="name" placeholder="Title" id="name" type="text" class="validate" v-model="section.name">
				          <!-- <label for="name">Title</label> -->
				          <span class="brown-text" v-if="section.errors.has('name')" v-text="section.errors.get('name')"></span>
				        </div>
			        </div>
				     <div class="row">
				     	<button v-if="!isEdit" class="btn waves-effect waves-light brown lighten-2" type="submit" name="action" @click="store" :disabled="section.errors.any()">Submit<i class="material-icons right">send</i></button>
			     	 	<button v-if="isEdit" class="btn brown lighten-2" type="submit" name="action" @click="update" :disabled="section.errors.any()">Edit<i class="material-icons right">send</i></button>
				     </div>
			    </form>
		  	</div>
			
    		<transition-group name="router-anim" tag="div">
				<div class="row" v-for="(section, index) in sections" v-bind:key="section.id">
				    <div class="col s12 m12">
				      <div class="card brown lighten-3 hoverable">
				        <div class="card-content white-text">
				          <span class="card-title">{{ section.name }}</span>
				        </div>
				        <div class="card-action">
				          <a class="white-text" href="#" @click="destroy(section.id, index)"><i class="material-icons">delete</i></a>
				          <a class="white-text" href="#" @click="edit(section, index)"><i class="material-icons">edit</i></a>
				        </div>
				      </div>
				    </div>
				</div>
			</transition-group>

			<ul class="pagination">
			    <li class="waves-effect" v-bind:class="[{disabled: !pagination.prev_page_url}]"><a href="#!"><i class="material-icons" @click="navigate(pagination.prev_page_url)">chevron_left</i></a></li>
			    <li class="waves-effect disabled"><a href="#!">{{pagination.current_page}}/{{pagination.last_page}}</a></li>
			    <li class="waves-effect" v-bind:class="[{disabled: !pagination.next_page_url}]"><a href="#!" @click="navigate(pagination.next_page_url)"><i class="material-icons">chevron_right</i></a></li>
			 </ul>
		</div>
	</div>
</template>

<script>
	export default{
		created(){
			this.index();
		},

		computed: {
				...mapGetters({
						sections: 'sections/data',
						section: 'sections/form',
						section_index: 'sections/current_index',
						pagination: 'sections/pagination',
						isEdit: 'sections/isEdit',
						api_url: 'sections/api_url',
				}),
		},

		methods: {
			index(){
				this.$store.dispatch('sections/_get', {token: true});
			},
			navigate(page_url){
				this.$store.dispatch('sections/navigate', page_url);
			},
			edit(section, index)
			{
				this.$store.commit('sections/edit', {element: section, index: index});
			},
			store(){
				if(!this.isEdit)
				{
					this.$store.dispatch('sections/_store', {token: true});
				}	
			},
			destroy(id, index){
				this.$store.dispatch('sections/_destroy', {id: id, index: index, token: true});
			},
			update(){
				if(this.isEdit)
				{
					this.$store.dispatch('sections/_update', {token: true});
				}
			}

		}
	}
</script>

<style type="text/css">
.pagination li.active {
    background-color: #a1887f;
}
</style>