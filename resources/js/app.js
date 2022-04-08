require('./bootstrap');
import Vue from 'vue/dist/vue.common';
window.Vue = require('vue');
 
import VueRouter from 'vue-router';
import VueAxios from 'vue-axios';

import axios from 'axios';
import { routes } from './routes';
import App from './components/App'
 
Vue.use(VueRouter);
Vue.use(VueAxios, axios);
 
const router = new VueRouter({
    mode: 'history',
    routes: routes
});
 
const app = new Vue({
    el: '#app',
    router: router,
    render: h => h(App),
});

