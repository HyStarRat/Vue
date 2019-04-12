import Vue from 'vue'

export default {

	all(){
		return Vue.http.get('countries');
	},

	store(country) {
		return Vue.http.post('countries',country );
	},
    
}