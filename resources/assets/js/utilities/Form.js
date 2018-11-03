
import Errors from './Errors'

class Form{

	constructor(data)
	{

		this.original_data = data;

		for(let field in data)
		{
			this[field] = data[field];
		}

		this.errors = new Errors();

	}

	data()
	{
		
		let data = {};

		for(let property in this.original_data)
		{
			data[property] = this[property];
		}

		return data;
	}

	reset()
	{

		for(let field in this.original_data)
		{
			this[field] = '';
		}

		this.errors.clear();

	}

	post(url, token=false)
	{
		return this.submit('post', url, token);
	}

	put(url, token=false)
	{
		return this.submit('put', url, token);
	}

	delete(url, token=false)
	{
		return this.submit('delete', url, token);
	}

	get(url, token=false)
	{
		return this.submit('get', url, token);
	}

	submit(request_type, url, token)
	{
		const _token = localStorage.getItem('token');

		if(token)
		{
			url = url + "?token=" + _token;
		}
		// VueProgressBarEventBus.$Progress.start();
		return new Promise((resolve, reject) => {
			axios[request_type](url, this.data()/*, {
				onUploadProgress: progressEvent => {
				    let percentCompleted = Math.floor((progressEvent.loaded * 100) / progressEvent.total);
				    VueProgressBarEventBus.$Progress.increase(percentCompleted);
				  }
			}*/)
			.then(response => {
				this.onSuccess(response.data);
				// VueProgressBarEventBus.$Progress.finish();
				resolve(response.data);
			})
			.catch(error => {
				this.onFail(error.response.data);
				// VueProgressBarEventBus.$Progress.finish();
				// app.__vue__.$store.dispatch('user/checkUser')
				// 					.then(response => {
										
				// 					})
				// 					.catch(error => {
				// 						// if(token)
				// // {
				// // 	app.__vue__.$store.dispatch('user/signout')
				// // 				.then(response => {
				// // 					this.$router.push({name:'signin'});
				// // 				})
				// // 				.catch(error => {
				// // 					console.log(error);
				// // 					this.$router.push({name:'signin'});
				// // 				});
				// // }
				// 					});
				

				reject(error.response.data);
			});
		});
	
	}

	onSuccess(response)
	{
		this.reset();
	}

	onFail(error)
	{
		this.errors.record(error.errors);
	}

	files(request_type, url, data, token)
	{
		const _token = localStorage.getItem('token');

		if(token)
		{
			url = url + "?token=" + _token;
		}

		return new Promise((resolve, reject) => {
			axios[request_type](url, data, {
			    headers: {
			      'Content-Type': 'multipart/form-data'
			    }
			})
			.then(response => {
				this.onSuccess(response.data);

				resolve(response.data);
			})
			.catch(error => {
				this.onFail(error.response.data);

				reject(error.response.data);
			});
		});
	}
}


export default Form;