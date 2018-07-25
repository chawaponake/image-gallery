
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.Vue = require('vue');

import VueRouter from 'vue-router';
import UploadComponent from './components/UploadComponent.vue';
import DiskUsageComponent from './components/DiskUsageComponent.vue';

Vue.use(VueRouter);

const routes = [
    { path : '/home', component : DiskUsageComponent },
    { path : '/gallery', component : UploadComponent }
];

const router = new VueRouter({
   routes
});

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

Vue.component('example-component', require('./components/ExampleComponent.vue'));
Vue.component('upload-component', require('./components/UploadComponent'));
Vue.component('diskusage-component', require('./components/DiskUsageComponent.vue'));

const app = new Vue({
    el: '#app',
    router : router
});
