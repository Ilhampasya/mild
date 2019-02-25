require('bootstrap');
window._ = require('lodash');
window.axios = require('axios');
window.Popper = require('popper.js').default;
window.$ = window.jQuery = require('jquery');
let token = document.head.querySelector('meta[name="csrf-token"]');
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
if (token) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
} else {
    console.error('CSRF Token not found.');
}