<template>
	<div class="navbar-fixed" id="navbar">
		<nav class="brown lighten-2">
		    <div class="nav-wrapper">
		      <a href="#" class="brand-logo">Logo</a>
		      <ul id="nav-mobile" class="right hide-on-med-and-down">
	            <router-link tag="li" to="/" exact><a>Home</a></router-link>
	            <router-link tag="li" to="/posts"><a>Posts</a></router-link>
	            <router-link tag="li" to="/tags"><a>Tags</a></router-link>
	            <router-link tag="li" to="/sections"><a>Sections</a></router-link>
	            <router-link tag="li" to="/schedule"><a>Schedule</a></router-link>
	            <router-link tag="li" to="/chat"><a>Chat</a></router-link>
	            <router-link tag="li" to="/conversations"><a>Conversations</a></router-link>
	            <router-link tag="li" to="/products"><a>Products</a></router-link>
	            <router-link tag="li" to="/cart"><a>Cart</a></router-link>
		        <router-link tag="li" to="/signin" v-if="!logged_in"><a>Signin</a></router-link>
		        <router-link tag="li" to="/signup" v-if="!logged_in"><a>Signup</a></router-link>
		        <li><a href="#" @click="signout" v-if="logged_in">Signout</a></li>
		      </ul>
		    </div>
	  	</nav>
	</div>
</template>

<script>
	import {mapGetters} from 'vuex';
	export default{
		computed: {
				...mapGetters({
						logged_in: 'user/logged_in',
				}),
		},

		mounted(){
			// this.$store.dispatch('user/checkUser');
			// this.form.get('api/user', true)
			// 	.then(response => {
			// 		this.$store.commit('user/signin', response.user);
			// 	})
			// 	.catch(error => console.log(error));
		},

		created(){
				this.scrollNav();
		},

		methods:{
			signout(){
				this.$store.dispatch('user/signout')
					.then(response => {
						this.$router.push({name:'home'});
					})
					.catch(error => {
						console.log(error);
					});
			},
			scrollNav(){
				var navHeight = 70;
				var prevScrollpos = window.pageYOffset+navHeight;
				window.onscroll = function() {
				var currentScrollPos = window.pageYOffset;
				  if (prevScrollpos < currentScrollPos) {
				    document.getElementById("navbar").style.top = -navHeight+"px";
				  } else {
				    document.getElementById("navbar").style.top = "0";
				  }
				  if(currentScrollPos > navHeight)
				  prevScrollpos = currentScrollPos;
				}
			},
		}
	}


</script>

<style>
	#navbar{
		transition: top .3s;
	}
</style>