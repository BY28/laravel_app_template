<template>
	<div>
		<div class="container">
			<h2>Products</h2>

			<div class="row">
			    <form class="col s12" @submit.prevent @keydown="product.errors.clear($event.target.name)">
			    	<div class="row">
				        <div class="input-field col s6">
				          <input name="name" placeholder="Title" id="name" type="text" class="validate" v-model="product.name">
				          <!-- <label for="name">Title</label> -->
				          <span class="brown-text" v-if="product.errors.has('name')" v-text="product.errors.get('name')"></span>
				        </div>
			        </div>

		        	<div class="row">
				         <div class="input-field col s6">
				          <input name="price" placeholder="Price" id="price" type="text" class="validate" v-model="product.price">
				          <!-- <label for="price">Title</label> -->
				          <span class="brown-text" v-if="product.errors.has('price')" v-text="product.errors.get('price')"></span>
				        </div>
				    </div>

		        	<div class="row">
				         <div class="input-field col s6">
				          <textarea name="description" placeholder="Description" id="textarea" class="materialize-textarea" v-model="product.description"></textarea>
				          <span class="brown-text" v-if="product.errors.has('description')" v-text="product.errors.get('description')"></span>
				        </div>
				    </div>
				    
				     <div class="row">
				     	<button v-if="!isEdit" class="btn waves-effect waves-light brown lighten-2" type="submit" name="action" @click="store" :disabled="product.errors.any()">Submit<i class="material-icons right">send</i></button>
			     	 	<button v-if="isEdit" class="btn brown lighten-2" type="submit" name="action" @click="update" :disabled="product.errors.any()">Edit<i class="material-icons right">send</i></button>
				     </div>
			    </form>
		  	</div>
			
    		<transition-group name="router-anim" tag="div">
				<div class="row" v-for="(product, index) in products" v-bind:key="product.id">
				    <div class="col s12 m12">
				      <div class="card brown lighten-3 hoverable">
				        <div class="card-content white-text">
				          <span class="card-title">{{ product.name }}</span>
				          <span>{{ product.price }}$</span>
				          <p>{{ product.description }}</p>
				        </div>
				        <div class="card-action">
				          <a class="white-text" href="#" @click="destroy(product.id, index)"><i class="material-icons">delete</i></a>
				          <a class="white-text" href="#" @click="edit(product, index)"><i class="material-icons">edit</i></a>
				          <a class="white-text" href="#" @click="add_to_cart(product)"><i class="material-icons">add_shopping_cart</i></a>
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
						products: 'products/data',
						product: 'products/form',
						product_index: 'products/current_index',
						pagination: 'products/pagination',
						isEdit: 'products/isEdit',
						api_url: 'products/api_url',
				}),
		},

		methods: {
			index(){
				this.$store.dispatch('products/_get', {token: true});
			},
			navigate(page_url){
				this.$store.dispatch('products/navigate', page_url);
			},
			edit(product, index)
			{
				this.$store.commit('products/edit', {element: product, index: index});
			},
			store(){
				if(!this.isEdit)
				{
					this.$store.dispatch('products/_store', {token: true});
				}	
			},
			destroy(id, index){
				this.$store.dispatch('products/_destroy', {id: id, index: index, token: true});
			},
			update(){
				if(this.isEdit)
				{
					this.$store.dispatch('products/_update', {token: true});
				}
			},
			add_to_cart(product){
				this.$store.dispatch('cart/store', product);
			}

		}
	}
</script>

<style type="text/css">
.pagination li.active {
    background-color: #a1887f;
}
</style>