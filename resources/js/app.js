import Vue from 'vue'
import Swal from 'sweetalert2'

window.Vue = Vue
window.swal = Swal

require('./misc/utils')

// Vue scoped helper functions
Vue.mixin({
    methods: {
        userHasPermissionTo(perm){
            return window.userHasPermissionTo(perm, window.__user)
        }
    }
})

const store = require('./store').stateManager()
const router = require('./routes').router()

import appLayout from './layouts/App'

// App bootstrap
window.app = new Vue({
    el: '#app',
    store,
    router,
    components: {
        appLayout
    },
    data(){
        return {
            isRouterAlive: true,
        }
    },
    created(){
        if( window.__user )
            this.$store.commit('setUserProfile', window.__user)
    },
    mounted(){

    },
    methods: {
        rebootRoot(){
            this.isRouterAlive = false
            this.$nextTick(() => (this.isRouterAlive = true))
        },
    },
});
