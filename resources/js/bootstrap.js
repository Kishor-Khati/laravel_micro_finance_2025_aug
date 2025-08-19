import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// Import jQuery and make it available globally
import $ from 'jquery';
window.$ = window.jQuery = $;
