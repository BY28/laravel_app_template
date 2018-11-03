<template>
	 <section class="section">
        <div class="container">
            <div class="row">
                <div class="col l6 m8 s12 offset-l3 offset-m2 z-depth-1 white" style="padding: 2%; margin-top: 6%; margin-bottom: 6%;">

                    <div style="padding: 2%">
                        <div>
                            <h3>Email</h3>
                            <p>Fill up the form.</p>
                            <form @submit.prevent="passwordResetEmail" @keydown="user.errors.clear($event.target.name)">
                                <div class="input-field">
                                    <i class="material-icons prefix">email</i>
                                        <input name="email" id="email" type="email" class="validate" v-model="user.email" >
                                        <label for="email">Email</label>
                                            <span class="brown-text" v-if="user.errors.has('email')" >
                                                 <i class="material-icons left">error_outline</i><strong v-text="user.errors.get('email')"></strong>
                                            </span>
                                </div>
                                
                                <div class="input-field row">   
                                    <button class="btn waves-effect waves-light brown lighten-2 right" type="submit" :disabled="user.errors.any()"><i class="material-icons">send</i></button>
                                </div>

                                <div class="row">
                                    <p>Not registered yet ? <router-link to="/signup"><a>Signup</a></router-link>	</p>
                                </div>

                            </form>
                        </div>
                    </div>

                </div>
            </div> 
    	</div>
	</section>
</template>

<script>
	export default{
		data(){
			return{
				user: new Form({
						email: '',
					}),
				api_url: '/api/password/email',
			}
		},

		methods:{
			passwordResetEmail(){
                this.user.post(this.api_url)
					.then(response => { 
                        console.log("check your mails");
                     })
					.catch(error => console.log(error));
			}
		}
	}
</script>