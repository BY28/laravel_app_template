<template>
	<div>

		<div class="container section">
			<div class="row">
				
				<div class="col s3">
					<!-- <span class="new badge brown lighten-2">{{users.length}}</span> -->
					<ul class="collection">
				      	<li v-for="(contact, index) in contacts" v-bind:key="index" class="collection-item" @click="currentUser(contact)">
				      		{{ contact.name }}
				      		<span class="new badge brown lighten-2" v-if="contact.unread">{{ contact.unread }}</span>
				  		</li>
				    </ul>
				</div>

				<div class="col s9">
					<h3 v-if="contact" >{{contact.name}}</h3>

					<div class="message_body">
						<div v-for="(message, message_index) in messages" v-bind:key="message_index">
							<div class="row">
							    <div class="col s12">
								  <div class="card-panel brown lighten-2">
								  	<span class="white-text">{{ message.user.name }}</span>
								    <p class="white-text" v-html="message.text">
								    	
								    </p>
								  </div>
								</div>
							</div>
						</div>	
					</div>
					
					<div class="row">
						<div class="col s12">
						  	<div class="row">
							    <div class="input-field col s11">
								      <i class="material-icons prefix">edit</i>
								      <textarea id="icon_prefix" class="materialize-textarea" type="text" v-model="message.text" @keyup.enter="store"></textarea>
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
		
		</div>

	</div>
</template>

<script>
	export default{

		data(){
			return{
				messages: '',
				message: new Form({
					text: '',
					receiver_id: '',
				}),
				users: [],
				contacts: [],
				contact: {},
				unread: [],
				api_url: '/api/messages',
				$message_body: '',
				pagination: {},
				count: ''
			}
		},
		created(){
			this.getContacts();
			this.echo();
			// this.index();
		},
		mounted(){
			this.$message_body = this.$el.querySelector('.message_body');
			// this.$message_body.addEventListener('scroll', this.onScroll);
		},

        computed: {
                ...mapGetters({
                        user: 'user/user',
                }),
        },

		methods:{
			index(){
				var _this = this;
				// var url = this.api_url + '/' + this.message.receiver_id;
				var url = this.api_url + '/' + this.message.receiver_id + '?token=' + localStorage.getItem('token');
				// this.message.get(url, true)
				axios.get(url)
					.then(response => {
						console.log(response);
						// this.messages = response.data.data.reverse();
						// this.paginate(response.data);
						this.messages = response.data.data.messages.reverse();
						this.count = response.data.data.count;
						var index = this.contacts.findIndex(item => item.id==this.message.receiver_id);
						if(/*this.unread[this.message.receiver_id]*/this.contacts[index].unread)
						{		
							// this.unread[this.message.receiver_id] = 0;
							
							this.contacts[index].unread = 0;
						}
						if(this.messages.length < this.count)
						{
							this.$message_body.addEventListener('scroll', this.onScroll);
						}
						this.scrollBot();
					})
					.catch(error => {
						console.log(error);
						if(error == 'token_expired'){
							this.$store.dispatch('user/checkUser')
								.then(response => {
									_this.index();
								})
								.catch(error => {
									console.log(error)
									_this.$store.dispatch('user/signout')
										.then(response => {
											_this.$router.push({name:'signin'});
										})
										.catch(error => {
											console.log(error);
											_this.$router.push({name:'signin'});
										});
								});}
					});
			},

			messagesPrepend(){
				console.log(this.messages)

				var previousHeight = this.$message_body.scrollHeight;

				var url = this.api_url + '/' + this.message.receiver_id + '?token=' + localStorage.getItem('token');

					url += '&before=' + this.messages[0].created_at;
					console.log(url)
				
				// this.message.get(url, true)
				axios.get(url)
					.then(response => {
							console.log(response);
							// this.messages = response.data.data.reverse();
							// this.paginate(response.data);
							this.messages = [...response.data.data.messages.reverse(), ...this.messages];
							// this.count = this.response.data.data.count;
							
							if(this.messages.length < this.count)
							{
								this.$message_body.addEventListener('scroll', this.onScroll);
							}

							this.$nextTick(function(){
								this.$message_body.scrollTop = this.$message_body.scrollHeight - previousHeight;
							});
						})
						.catch(error => {
							console.log(error);
							if(error == 'token_expired'){
								this.$store.dispatch('user/checkUser')
									.then(response => {
										_this.index();
									})
									.catch(error => {
										console.log(error)
										_this.$store.dispatch('user/signout')
											.then(response => {
												_this.$router.push({name:'signin'});
											})
											.catch(error => {
												console.log(error);
												_this.$router.push({name:'signin'});
											});
									});}
						});
			},

			getContacts(){
				this.message.get('/api/messages/conversations', true)
					.then(response => {
						this.contacts = response.data.users;
						// for(var i=0; i<this.contacts.length; i++)
						// {
						// 	if(this.contacts[i].id in response.data.unread){
						// 		this.contacts[i].unread = response.data.unread[this.contacts[i].id];
						// 	}
						// 	else{
						// 		this.contacts[i].unread = 0;
						// 		console.log(this.contacts[i].unread );
						// 	}
						// }
						// this.unread = response.data.unread;

					})
					.catch(error => {
						console.log(error);
					});

			},

			store(event){
				if(!event.shiftKey)
				{
					this.message.receiver_id = this.contact.id;
					this.message.post(this.api_url, true)
						.then(response => {
							this.messages.push(response.data);
							this.count++;
							this.scrollBot();
						})
						.catch(error => {
							console.log(error);
						});
				}
			},

			echo(){
				Echo.connector.pusher.config.auth.headers.Authorization = 'Bearer ' + localStorage.getItem('token');
				console.log(this.user.id);
				// Echo.join('chatroom.' + this.user.id)
				Echo.private('chatroom.' + this.user.id)
				// 	.here(users => {
				// 		this.users = users;
				// 	})
				// 	.joining(user => {
				// 		this.users.push(user);
				// 	})
				// 	.leaving(user => {
				// 		this.users = this.users.filter(u => u != user);
				// 	})
					.listen('MessageSent', (e) => {
						console.log(e);
						if(e.message.user_id == this.contact.id && this.contact)
						{
							this.messages.push(e.message);
							this.count++;
							this.scrollBot();
						}
						else
						{
							// this.contacts[e.message.user_id].unread++;
							var index = this.contacts.findIndex(item => item.id==e.message.user_id)
							this.contacts[index].unread++;
						}
					});
			},

			currentUser(user){
				this.contact = user;
				this.message.receiver_id = user.id;
				this.index();
				// this.$message_body.addEventListener('scroll', this.onScroll);
			},

			scrollBot(){
				this.$nextTick(function() {
					this.$message_body.scrollTop = this.$message_body.scrollHeight;
				});
			},

			onScroll(){
				if(this.$message_body.scrollTop == 0)
				{

					// console.log(this.pagination.next_page_url);
					this.$message_body.removeEventListener('scroll', this.onScroll);
					// this.navigate(this.pagination.next_page_url);					
					this.messagesPrepend();
				}
			},

			navigate(payload){
				var _token = localStorage.getItem('token');
				var url = payload + '&token=' + _token;
				axios.get(url)
					.then(response => {
						console.log(response);
						this.messages = [...response.data.data.data.reverse(), ...this.messages];
						// for(var i = 0; i<response.data.data.data.length; i++)
						// {
						// 	this.messages.unshift(response.data.data.data[i]);
						// }
						// this.messages = response.data.data.data.reverse().concat(this.messages);
						console.log(this.messages);
						this.paginate(response.data.data);
						// resolve(response);
					})
					.catch(error => {
						// reject(error);
						console.log(error);
					});
			},

			paginate(payload){
				let pagination = { // pagination object
						current_page: payload.current_page,
						last_page: payload.last_page,
						next_page_url: payload.next_page_url,
						prev_page_url: payload.prev_page_url,
						current_page_url: this.api_url+'?page='+payload.current_page,
					}

					this.pagination = pagination; // assign pagination informations to the pagination state object
			},

		}	

	}
</script>

<style>
	.message_body{
		max-height: calc(100vh - 250px);
		overflow-x: hidden;
		overflow-y: scroll; 
	}
</style>