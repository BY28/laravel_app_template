<template>
	 <section class="section">
        <div class="container">
            <div class="row">
                <div class="col l6 m8 s12 offset-l3 offset-m2 z-depth-1 white" style="padding: 2%; margin-top: 6%; margin-bottom: 6%;">

                    <div style="padding: 2%">
                        <div>
                            <h3>Sign up</h3>
                            <p>Fill up the form.</p>
                            <form @submit.prevent="store" @keydown="user.errors.clear($event.target.name)">
                                <div class="input-field">
                                    <i class="material-icons prefix">email</i>
                                        <input name="email" id="email" type="email" class="validate" v-model="user.email" >
                                        <label for="email">Email</label>
                                            <span class="brown-text" v-if="user.errors.has('email')" >
                                                 <i class="material-icons left">error_outline</i><strong v-text="user.errors.get('email')"></strong>
                                            </span>
                                </div>
                                <div class="input-field">
                                    <i class="material-icons prefix">person</i>
                                        <input name="name" id="name" type="text" class="validate" v-model="user.name" >
                                        <label for="name">Username</label>
                                            <span class="brown-text" v-if="user.errors.has('name')" >
                                                 <i class="material-icons left">error_outline</i><strong v-text="user.errors.get('name')"></strong>
                                            </span>
                                </div>
                                <div class="input-field">
                                    <i class="material-icons prefix">lock</i>
                                        <input name="password" id="password" type="password" class="validate"  v-model="user.password" >
                                        <label for="password">Mot de passe</label>
                                </div>
                                <div class="input-field">
                                    <i class="material-icons prefix">lock_outline</i>
                                        <input name="password_confirmation" id="password_confirmation" type="password" class="validate"  v-model="user.password_confirmation" @keydown="user.errors.clear('password')">
                                        <label for="password_confirmation">Confirmer le mot de passe</label>
                                            <span class="brown-text" v-if="user.errors.has('password')" >
                                                 <i class="material-icons left">error_outline</i><strong v-text="user.errors.get('password')"></strong>
                                            </span>
                                </div>

                                <div class="input-field row">   
                                    <button class="btn waves-effect waves-light brown lighten-2 right" type="submit" :disabled="user.errors.any()"><i class="material-icons">send</i></button>
                                </div>

                                <div class="row">
                                    </a>
                                    <p>Already registered ? <router-link to="/signin"><a>Signin</a></router-link>	</p>
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
						name: '',
						password: '',
						password_confirmation: '',
					}),
				api_url: 'api/user',
			}
		},

		methods:{
			store(){
				this.user.post(this.api_url)
					.then(response => {
                        alert('Account Created!') 
                        this.$router.push({name:'signin'});
                    })
					.catch(error => console.log(error.errors));
			}
		}
	}
</script>