<template>
	<div>
		<div class="container">
		    <div class="col s12 m12">
		      <div class="card brown lighten-3 hoverable">
				  <div class="card">
				    <div class="card-image">
				    	<div v-for="(image, img_index) in post.images" v-bind:key="image.id" >
						      <img class="responsive-img" :src="imageUrl(image.name)">
				    	</div>
				    </div>
				</div>
		        <div class="card-content white-text">
		          <span class="card-title">{{ post.title }}</span>
		          <span>{{ post.section.name }}</span>
		          <p>{{ post.body }}</p>
		          <span v-for="tag in post.tags" v-bind:key="tag.id">{{tag.name}}</span>
		        </div>
		      </div>
		    </div>
		</div>
	</div>
</template>

<script>
	export default{

		props: ['slug'],		
		
		data(){
			return{
				post: {
					title: '',
					section:{
						name: '',
					},
					body: ''
				},
			}
		},

		mounted() {
			this.$store.dispatch('posts/show', {slug: this.slug})
				.then(response => {
					this.post = response.data.data;
				});
		},

		methods: {

			imageUrl(name)
			{
				return '/uploads/posts/'+name;
			},

		}

	}
</script>