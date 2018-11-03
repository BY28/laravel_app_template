<template>
	<div>

		<div class="container section">
			<span class="new badge brown lighten-2">{{users.length}}</span>
			<div v-for="(message, index) in messages" v-bind:key="index">
				<div class="row">
				    <div class="col s12">
					  <div class="card-panel brown lighten-2">
					    <p class="white-text">{{ message.text }}
					    </p>
					  </div>
					</div>
				</div>
			</div>

			<div class="row">
				<div class="col s12">
				  	<div class="row">
					    <div class="input-field col s11">
						      <i class="material-icons prefix">edit</i>
						      <input id="icon_prefix" type="text" v-model="message.text" @keyup.enter="store"></input>
						      <label for="icon_prefix">Message</label>
					    </div>
					    <div class="input-field col s1">
					    		<button class="btn brown lighten-2" name="action" @click="store"><i class="material-icons">send</i></button>
					    </div>
					    <div class="row">
                          	<span class="brown-text" v-if="message.errors.has('text')" v-text="message.errors.get('text')"></span>
					    </div>
				  	</div>
				</div>
			</div>
		</div>

	</div>
</template>

<script>
	export default{

		data(){
			return{
				messages: '',
				message: new Form({
					text: ''
				}),
				users: [],
				api_url: '/api/messages',
			}
		},
		created(){
			this.index();
			this.echo();
		},
		methods:{
			index(){
				this.message.get(this.api_url, true)
					.then(response => {
						this.messages = response.data;
					})
					.catch(error => {
						console.log(error);
					});
			},

			store(){
				this.message.post(this.api_url, true)
					.then(response => {
						this.messages.push(response.data);
					})
					.catch(error => {
						console.log(error);
					});
			},

			echo(){
				/*
					config/app.php uncomment App\Providers\BroadcastServiceProvider::class,
					.env BROADCAST_DRIVER=pusher and add app_ id, key, secred and cluster
					composer require pusher/pusher-php-server "~3.0"
					npm install --save laravel-echo pusher-js
					ressources/assets/js/bootstrap.js uncomment Echo and Pusher comments and add key and cluster
					php artisan make:event EventClass add public properties and channel name
					EventController event(new EventClass($properties, ...)); or broadcast(new EventClass($properties, ...))->toOthers();

					JWT:

					ressources/assets/js/bootstrap.js :
	
					authEndpoint: '/broadcasting/auth?token=' + localStorage.getItem('token'),
					or better
					auth: {
			       		 headers: {
				            Authorization: 'Bearer ' + localStorage.getItem('token')
				        },
				    },
                    on the fly in the function of the vue file:
				    Echo.connector.pusher.config.auth.headers.Authorization = 'Bearer ' + localStorage.getItem('token');
					
					app\Providers\BroadcastServiceProvider.php Broadcast::routes( [ 'middleware' => [ 'api', 'auth.jwt' ] ] );

				*/
				Echo.connector.pusher.config.auth.headers.Authorization = 'Bearer ' + localStorage.getItem('token');
				Echo.join('chatroom')
					.here(users => {
						this.users = users;
					})
					.joining(user => {
						this.users.push(user);
					})
					.leaving(user => {
						this.users = this.users.filter(u => u != user);
					})
					.listen('MessageSent', (e) => {
						console.log(e);
						this.messages.push(e.message);
					});
			},
		}	

	}
</script>