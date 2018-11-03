<template>
	<div>
		<div class="container">
			<h2>Events</h2>

            <div class="row">
                <form class="col s6" @submit.prevent @keydown="event.errors.clear($event.target.name)">
                    <div class="row">
                        <div class="input-field col s12">
                          <input name="title" placeholder="Title" id="title" type="text" class="validate" v-model="event.title">
                          <!-- <label for="title">Title</label> -->
                          <span class="brown-text" v-if="event.errors.has('title')" v-text="event.errors.get('title')"></span>
                        </div>
                    </div>
                    <div class="row">
                    	<div class="input-field col s12">
                    		Start:
                    		<input type="date" v-model="event.start">
                          <span class="brown-text" v-if="event.errors.has('start')" v-text="event.errors.get('start')"></span>

                    	</div>
                    	<div class="input-field col s12">
                    		End:
                    		<input type="date" v-model="event.end">
                          <span class="brown-text" v-if="event.errors.has('end ')" v-text="event.errors.get('end ')"></span>

                    	</div>
                    </div>
                     <div class="row">
                        <div class="input-field col s12">
                          <textarea name="description" placeholder="Body" id="textarea" class="materialize-textarea" v-model="event.description"></textarea>
                          <span class="brown-text" v-if="event.errors.has('description')" v-text="event.errors.get('description')"></span>
                        </div>
                     </div>
                     <div class="row">
                         <button v-if="!isEdit" class="btn brown lighten-2" type="submit" name="action" @click="store" :disabled="event.errors.any()">Submit<i class="material-icons right">send</i></button>
                         <button v-if="isEdit" class="btn brown lighten-2" type="submit" name="action" @click="update" :disabled="event.errors.any()">Edit<i class="material-icons right">send</i></button>
                     </div>
                </form>
            </div>	
		</div>

		<div class="container">	
			<div class="section">	
				<div id="calendar"></div>
			</div>
		</div>

	</div>
</template>

<style src='fullcalendar/dist/fullcalendar.min.css'></style>

<script>
	import 'fullcalendar';
	import moment from 'moment';

	export default{

		data(){
			return {
				events: [],
				event: new Form({
					id: '',
					title: '',
					description: '',
					start: '',
					end: '',
				}),
				current_index: '',
				isEdit: false,
				api_url: '/api/events',
				calendar: '',
			}
		},

		mounted(){
			this.index();
		},

		methods: {
			initCalendar(){
				var _this = this;
				this.$nextTick(function(){
					_this.calendar = $('#calendar').fullCalendar({
					    
					    /* OPTIONS */
					    
					    header: {
					    	left: 'prev, next, today',
					    	center: 'title',
					    	right: 'month, agendaWeek, agendaDay, listWeek',
					    },
					    locale: 'fr',
					    eventLimit: true,
					    navLinks: true,
					   

					    events: _this.events,

					    /* EVENTS */

					    selectable: true,
					    selectHeper: true,
					    select: function(start, end, allDay){
					    	_this.event.start = moment(start).format('YYYY-MM-DD');
					    	_this.event.end = moment(end).format('YYYY-MM-DD');
					    	// alert(moment(start).format('YYYY-MM-DD HH:mm') + ' ' + moment(end).format('YYYY-MM-DD HH:mm'));
					    	// modal for ajax request (axios) to add event
					    },

					    editable: true,
					    eventResize: function(event){
					    	_this.event.start = moment(event.start).format('YYYY-MM-DD');
					    	_this.event.end = moment(event.end).format('YYYY-MM-DD');
					    	_this.update();
					    },

					    eventDrop: function(event){
					    	_this.event.id = event.id;
					    	_this.event.title = event.title;
					    	_this.event.start = moment(event.start).format('YYYY-MM-DD');
					    	_this.event.end = moment(event.end).format('YYYY-MM-DD');
					    	_this.event.description = event.description;
					    	_this.update();
					    },
					    
					    eventClick: function(event){
					    	_this.edit(event);
					    	// _this.destroy(event);
					    	// modal for ajax request (axios) to edit event
					    },

					 });
				});
			},
			refreshCalendar()
			{
                this.calendar.fullCalendar( 'removeEvents');
                this.calendar.fullCalendar( 'addEventSource', this.events); 
                this.calendar.fullCalendar( 'rerenderEvents' );				
			},

			index(){
				this.get()
					.then(response => {
						this.events = response.data;
						this.initCalendar();
					})
					.catch(error => {
						console.log(error);
					});
			},
			get()
			{
				return new Promise((resolve, reject) => {
					this.event.get(this.api_url)
						.then(response => {
							resolve(response);
						})
						.catch(error => {
							reject(error);
						});
				});
			},
            store(){
                this.event.post(this.api_url)
                	.then(response => {
                		console.log(response.data);
                		this.events.push(response.data);
                		this.refreshCalendar();
                	})
                	.catch(error => {
                		console.log(error);
                	});
            },
            edit(event){
            	this.event.id = event.id;
				this.event.title = event.title;
				this.event.start = moment(event.start).format('YYYY-MM-DD');
				this.event.end = moment(event.end).format('YYYY-MM-DD');
				this.event.description = event.description;
            	this.isEdit = true;
            	console.log(this.current_index);
                // Object.assign(this.event, event);
            },
            update(){
				let url = this.api_url+'/'+this.event.id;
            	var _this = this;
            	this.current_index = this.events.findIndex(item => item.id==_this.event.id);
                this.event.put(url)
                	.then(response => {
                		this.events.splice(this.current_index, 1, response.data);
                		this.isEdit = false;
                		this.refreshCalendar();
                	})
                	.catch(error => {
                		console.log(error);
                	});
            },
            destroy(event){
            	if(confirm('Are you sure?')){
					let url = this.api_url+'/'+event.id;
					var _this = this;
            		this.current_index = this.events.findIndex(item => item.id==event.id);
					this.event.delete(url)
					.then(response => {
						console.log(response)
						this.events.splice(this.current_index, 1);
                		this.refreshCalendar();
					})
					.catch(error => {
						console.log(error.errors)
					});
				}
            },
		}

	}
</script>