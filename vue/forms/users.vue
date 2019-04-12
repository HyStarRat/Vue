<template>
    <form role="form">
        <div class="form-body">
            <div class="form-group">
                <label>Photo</label>
                <div class="row">
                    <div class="col-md-4">
                        <user-photo-upload :photo.sync="form.photo"></user-photo-upload>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label>Name</label>
                <div class="input-group">
                    <span class="input-group-addon">
                        <i class="fa fa-user"></i>
                    </span>
                    <input v-model="form.name" type="text" class="form-control" placeholder="Full name">
                </div>
                <small v-if="errors.name" class="text-danger">{{ errors.name[0] }}</small>
            </div>

            <div class="form-group">
                <label>Email</label>
                <div class="input-group">
                    <span class="input-group-addon">
                        <i class="fa fa-envelope"></i>
                    </span>
                    <input v-model="form.email" type="email" class="form-control" placeholder="Email" required="">
                </div>
                <small v-if="errors.email" class="text-danger">{{ errors.email[0] }}</small>
            </div>

            <div class="form-group">
                <label>Password</label>
                <div class="input-group">
                    <span class="input-group-addon">
                        <i class="fa fa-lock"></i>
                    </span>
                    <input v-model="form.password" type="text" class="form-control" placeholder="Generate password" required="">
                </div>
                <small v-if="errors.password" class="text-danger">{{ errors.password[0] }}</small>
            </div>

            <!-- Countries -->
            <div class="countries" v-if="(form.type != 'admin') && !auth.is_country_manager">
                <div class="form-group" >
                    <label>Country</label>
                    <select v-model="form.country_id" name="country_id" class="form-control" required>
                        <option value :selected="!form.country_id" >Select Country</option>
                        <template  v-for="country in countries" >
                            <option :value="country.id" :selected="form.country_id == country.id" :key="country" >
                                {{ country.name }}
                            </option>                            
                        </template>
                    </select>
                    <small v-if="errors.country_id" class="text-danger">{{ errors.country_id[0] }}</small>
                </div>
            </div>


            <div class="form-group">
                <label>Account type</label>
                <select v-model="form.type" class="form-control">
                    <option value="moderator" selected>Moderator</option>
                    <option value="admin" v-if="!auth.is_country_manager">Admin</option>
                    <option value="country_manager" v-if="!auth.is_country_manager">Country Manager</option>
                    <option value="team_leader">Team Leader</option>
                </select>
            </div>

            <hr />

            <div class="form-group">
                <label>Contact information</label>
                <textarea v-model="form.contact_info" class="form-control"></textarea>
            </div>

            <div class="form-group">
                <label>Pay rate</label>
                <div class="input-group">
                    <span class="input-group-addon">
                        <i class="fa fa-usd"></i>
                    </span>
                    <input step="0.01" v-model="form.pay_rate" type="number" class="form-control" placeholder="Pay rate">
                </div>
            </div>

            <div class="form-group">
                <label>Managed websites</label>
                <div class="input-group">
                    <div v-for="website in websites" class="icheck-list">
                        <label>
                        <input v-model="form.websites" v-bind:value="website.id" type="checkbox" class="icheck" />
                        {{ website.name }}
                        </label>
                    </div>
                </div>
            </div>
        </div>
    </form>
</template>

<script>

import User from './../stores/user'
import Website from './../stores/website'
import UserPhotoUpload from './../components/PhotoUpload.vue'
import Country from '../stores/country.js'

import UserModel from './../models/user'

import Spinner from './../spin'

export default {

    components: {
        UserPhotoUpload
    },

    props: {
        method: {
            type: String,
            default: 'POST'
        },

        form: {
            type: Object,
            twoWay: true,
            default() {
                return {
                    websites: [],
                }
            },
            required: false
        },
        auth:{
            type: Object            
        }
    },


    data() {
        return {
            websites: [],

            errors: [],
            currencies: [],

            saving: false,
            countries: []
        }
    },


    ready() {
        Website.all().then(response => {
            this.websites = response.data
        })
        Country.all()
            .then((r) => {
                this.countries = r.data
            })

    },


    methods: {
        submit() {
            if (!this.saving) {
                this.saving = Spinner.spin()
                if(this.auth.is_country_manager)
                    this.form.country_id = this.auth.country_id
                    
                User.store(this.form).then(response => {
                    this.form = {};
                    this.errors = [];
                    toastr.success('User: ' + response.data.name + ' is created!');
                    this.$dispatch('user:created', response.data);
                    this.saving = Spinner.stop()
                    console.log(response.data)
                }).catch(response => {
                    console.log(response.data)
                    this.saving = Spinner.stop()
                    this.errors = response.data
                })
            }
        }
    },

    events: {
        'form:submit'(form) {
            if (form == 'user')
                this.submit();
        }
    },
}
</script>