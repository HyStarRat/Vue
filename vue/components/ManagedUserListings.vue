<template>
	<new-managed-account-modal title="Add new user to manage" target="newManagedAccount">
		<managed-user-form slot="content" :website="website" :countries="countries"></managed-user-form>
		<div slot="modal-footer"></div>
	</new-managed-account-modal>

	<div class="portlet light bordered">
		<div class="portlet-title">
			<div class="caption caption-md font-red-sunglo">
				<i class="icon-bar-chart theme-font hide"></i>
				<span class="caption-subject theme-font bold uppercase">Moderated profiles</span>
			</div>
			<div class="actions">
				<div class="inputs">
					<div class="portlet-input input-inline input-small">
						<div class="input-icon right">
							<i class="icon-magnifier"></i>
							<input v-model="search" type="text" class="form-control input-circle" placeholder="search...">
						</div>
					</div>
					<div class="btn-group btn-group-devided">
						<button data-toggle="modal" data-target="#newManagedAccount" class="btn btn-transparent grey-salsa btn-circle btn-sm active">Add moderated profile</button>
						<button v-if="checkedUsers.length" @click="unmanageUsers()" class="btn btn-transparent grey-salsa btn-circle btn-sm active">Unmanage</button>
					</div>
				</div>
			</div>
		</div>
		<div class="portlet-body">
			<div class="table-scrollable table-scrollable-borderless">
				<table class="table table-hover table-light">
					<thead>
						<tr class="uppercase">
							<th>
								
							</th>
							<th colspan="2">
								 Name
							</th>
							<th>
								 Email
							</th>
							<th>
								 Country
							</th>
							<th>
								Fake message
							</th>
						</tr>
					</thead>
					<tr v-for="user in users.slice().reverse() | filterBy search">
						<td>
							<input :value="user.id" v-model="checkedUsers" type="checkbox" class="liChild">
						</td>
						<td>
							<img class="user-pic" :src="user.user.avatar.url || '/img/default-photo.png'">
						</td>
						<td>
							{{ user.user.username }}
						</td>
						<td>
							{{ user.user.email }}
						</td>
						<td>
							<span v-if="auth.is_country_manager || auth.is_team_leader">
								{{ getCountryName(user) }}							
							</span>
							<div v-else>
								 <select v-model="user.country_id" name="country_id" class="form-control" required>
									<option value :selected="!user.country_id" >Select Country</option>
									<option :value="country.id" v-for="country in countries" >
										{{ country.name }}
									</option>
								</select>
							</div>
						</td>
						<td>
							<div class="input-group">
								<input v-model="user.fake_message" class="form-control" type="text" placeholder="Add fake message">
								<span class="input-group-btn">
									<button @click="updateManagedUser(user)" class="btn btn-success" type="button"><i class="fa fa-edit fa-fw"></i></button>
								</span>
							</div>
						</td>
					</tr>
				</table>
				<div v-if="!users.length" class="note note-info note-bordered">
					<p>No users listed.</p>
				</div>
			</div>
		</div>
	</div>
</template>

<style lang="sass">
	.editable {
		.table-edit {
			display: none;
			cursor: pointer;
		}
	}

	.editable:hover {
		.table-edit {
			display: inline-block;
		}
	}
</style>

<script>
	import _ from 'underscore'
	import swal from 'sweetalert'
	import WebsiteUser from './../stores/website_user'
	import paginator from './../services/paginator'
	import Spinner from './../spin'
	import Country from '../stores/country.js'

	import NewManagedAccountModal from './Modal.vue'
	import ManagedUserForm from './../forms/managed-user.vue'

	export default {

		components: {
			NewManagedAccountModal, ManagedUserForm
		},

		data() {
			return {
				checkedUsers: [],
				paginator: {},

				isFetching: false,
				countries: []
			}
		},

		props: {

			website: {
				type: Object,
				default() {
					return {}
				},
				required: false
			},

			users: {
				type: Array,
				default() {
					return []
				},
				required: false
			},
			auth:{
				type: Object,
				default() {
					return {}
				},
				required: false				
			}

		},

		ready() {
			Country.all().then(r => {
				this.countries = r.data
			})
			if (this.users) {
				let self = this;

				self.$http.get('websites/' + self.website.id + '/users').then(response => {
					self.paginator = response.data;
					self.users = self.paginator.data;
				})

				window.onscroll = function(ev) {
					let url = self.paginator.next_page_url

				    if ((window.innerHeight + window.scrollY) >= document.body.offsetHeight) {
				    	if (url) {
				    		self.isFetching = true
				    		paginator.next(url).then(response => {
				    			self.paginator = response.data
				    			
				    			self.paginator.data.forEach(data => {
				    				self.users.push(data);
				    			})

				    			self.isFetching = false;
				    		})
						}
				    }
				};
			}
		},

		methods: {
			getCountryName(user){
				let country = this.countries.find(c =>{
					return user.country_id == c.id
				});
				return (country == undefined) ? 'Unknown' : country.name
			},
			unmanageUsers() {
				var self = this
				if (this.checkedUsers.length) {
					swal({
						title: "Are you sure?",
						text: "You will not be able to recover this after deletion.",
						type: "warning",
						showCancelButton: true,
						showLoaderOnConfirm: true
					}, () => {
						WebsiteUser.delete({ website: this.website, users: this.checkedUsers }).then(response => {
							self.users = _.reject(self.users, user => {
								return _.contains(self.checkedUsers, user.id);
							})
							this.checkedUsers = [];
							toastr.success(response.data);
						})
					});
				}
			},

			updateManagedUser(user) {
				console.log(user)
				this.$http.put('websites/' + this.website.id + '/managed-users/' + user.id, user).then(response => {
					toastr.success(response.data);
				}).catch(error => {
					toastr.error('Something went wrong.');
				})
			},

			editIntroMessage(user) {
				console.log(user.id);
			}
		},

		events: {
			'user:created'(user) {
				this.users.push(user);
			}
		},

		watch: {
			isFetching(val) {
				this.$nextTick(() => {
					if (val)
						Spinner.spin()
					else
						Spinner.stop()
				})
				
			}
		}
		
	}
</script>