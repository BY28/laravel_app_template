<template>
	<div>
		<div class="container">
			
			<table class="responsive-table highlight centered">
		        <thead>
		          <tr>
		              <th>Item Name</th>
		              <th>Item Descrîption</th>
		              <th>Item Quantity</th>
		              <th>Item Price</th>
		          </tr>
		        </thead>

		        <tbody>
		          <tr v-for="(item, index) in items" :key="item.id">
		            <td>{{ item.name }}</td>
		            <td>{{ item.description }}</td>
		            <td><input type="text" v-model="item.quantity" @input="save"></td>
		            <td>{{ item.price }} $</td>
		            <td>
				          <a class="brown-text" href="#" @click="destroy(index)"><i class="material-icons">clear</i></a>
				    </td>
		          </tr>
		          <tr>
		            <td><b>Total:</b></td>
		            <td></td>
		            <td></td>
		            <td></td>
		            <td><b>{{ total_price }}$</b></td>
		          </tr>
		        </tbody>
		     </table>
			

			<!-- STRIPE -->
		     <form id="payment-form">
			<!--   <div class="form-row">
			    <label for="card-element">
			      Credit or debit card
			    </label> -->
			    <div class="row">
				    <div id="card-element">
				      <!-- A Stripe Element will be inserted here. -->
				    </div>

				    <!-- Used to display form errors. -->
				    <div id="card-errors" role="alert"></div>
			    </div>
			    <div class="row">
                         <button class="btn brown lighten-2 right" type="submit" name="action">PAY<i class="material-icons right">send</i></button>
				</div>
			  <!-- </div>

			  <button>Submit Payment</button>-->
			</form> 
			
			<!-- PAYPAL -->
			<div class="row">
				<div class="right" id="paypal-button"></div>
			</div>

		</div>
	</div>
</template>

<script>
	import paypal from 'paypal-checkout';

	export default{
		mounted(){
			this.$store.commit('cart/init');
			this.initPaypal();
			this.initStripe();
		},

		computed:{
			...mapGetters({
				items: 'cart/data',
				item_number: 'cart/item_number',
				item_quantity: 'cart/item_quantity',
				total_price: 'cart/total_price',
			}),
		},

		methods:{
			save(){
				this.$store.commit('cart/save');
			},
			destroy(index){
				this.$store.dispatch('cart/destroy', index);
			},
			initPaypal(){
				let _this = this;
				this.$nextTick(function () {
					paypal.Button.render({
						env: 'sandbox',
						style: {
							size: 'small',
							color: 'blue',
							shape: 'rect',
							label: 'checkout',
							tagline: 'true'
						},
						commit: true,
						payment: function(data, actions) {
					      // 2. Make a request to your server
					      return actions.request.post('/api/paypal', {items: JSON.stringify(_this.items), total_price: _this.total_price})
					        .then(function(res) {
					          // 3. Return res.id from the response
					          return res.id;
					        });
					    },
					    // Execute the payment:
					    // 1. Add an onAuthorize callback
					    onAuthorize: function(data, actions) {
					      // 2. Make a request to your server
					      return actions.request.post('/api/paypal/execute', {
					        paymentID: data.paymentID,
					        payerID:   data.payerID
					      })
					        .then(function(res) {
					          // 3. Show the buyer a confirmation message.
					          // console.log(res);
					        });
					    }
					}, '#paypal-button');
			   	});
			},
			initStripe(){
				let _this = this;
				this.$nextTick(function () {
					// Create a Stripe client.
					var stripe = Stripe('pk_test_e8kWHmLoz6IhWBKu0MSiue4M');

					// Create an instance of Elements.
					var elements = stripe.elements();

					// Custom styling can be passed to options when creating an Element.
					// (Note that this demo uses a wider set of styles than the guide below.)
					var style = {
					  base: {
					    color: '#32325d',
					    lineHeight: '18px',
					    fontFamily: '"Helvetica Neue", Helvetica, sans-serif',
					    fontSmoothing: 'antialiased',
					    fontSize: '16px',
					    '::placeholder': {
					      color: '#aab7c4'
					    }
					  },
					  invalid: {
					    color: '#fa755a',
					    iconColor: '#fa755a'
					  }
					};

					// Create an instance of the card Element.
					var card = elements.create('card', {style: style});

					// Add an instance of the card Element into the `card-element` <div>.
					card.mount('#card-element');

					// Handle real-time validation errors from the card Element.
					card.addEventListener('change', function(event) {
					  var displayError = document.getElementById('card-errors');
					  if (event.error) {
					    displayError.textContent = event.error.message;
					  } else {
					    displayError.textContent = '';
					  }
					});

					// Handle form submission.
					var form = document.getElementById('payment-form');
					form.addEventListener('submit', function(event) {
					  event.preventDefault();

					  stripe.createToken(card).then(function(result) {
					    if (result.error) {
					      // Inform the user if there was an error.
					      var errorElement = document.getElementById('card-errors');
					      errorElement.textContent = result.error.message;
					    } else {
					      // Send the token to your server.
					      _this.stripeTokenHandler(result.token);
					    }
					  });
					});
			   	});
			},
			stripeTokenHandler(token) {
				  // Insert the token ID into the form so it gets submitted to the server
				  var form = document.getElementById('payment-form');
				  var hiddenInput = document.createElement('input');
				  hiddenInput.setAttribute('type', 'hidden');
				  hiddenInput.setAttribute('name', 'stripeToken');
				  hiddenInput.setAttribute('value', token.id);
				  form.appendChild(hiddenInput);
				  this.stripeCheckout(form);
				  // Submit the form
				  // form.submit();
			},
			stripeCheckout(form)
			{
				var formData = new FormData(form);
				formData.append('items', JSON.stringify(this.items));
				formData.append('total_price', JSON.stringify(this.total_price));

				axios.post('/api/stripe', formData)
					.then(response => {
						console.log(response);
					})
					.catch(error => {
						console.log(error);
					});
			},
		}
	}
</script>

<style>
.StripeElement {
  background-color: white;
  height: 40px;
  padding: 10px 12px;
  border-radius: 4px;
  border: 1px solid transparent;
  box-shadow: 0 1px 3px 0 #e6ebf1;
  -webkit-transition: box-shadow 150ms ease;
  transition: box-shadow 150ms ease;
}

.StripeElement--focus {
  box-shadow: 0 1px 3px 0 #cfd7df;
}

.StripeElement--invalid {
  border-color: #fa755a;
}

.StripeElement--webkit-autofill {
  background-color: #fefde5 !important;
}
</style>