require('./bootstrap');
window.Vue = require('vue');
Vue.component('example', require('./components/Example').default);
const app = new Vue({
    el: '#app'
});