
import '../css/app.css';
import { createApp } from 'vue';
import VueSweetalert2 from 'vue-sweetalert2';
import 'sweetalert2/dist/sweetalert2.min.css';
import { Bootstrap5Pagination } from 'laravel-vue-pagination';

import router from './routes.js';


// Import components
// import ExampleComponent from './components/ExampleComponent.vue';
import App from './components/App.vue';
// window.Vue = require('vue');
// let Fire = new Vue()
// window.Fire = Fire;

const app = createApp(App);


app.config.productionTip = false;
// app.component('example-component', ExampleComponent);
app.component('pagination', Bootstrap5Pagination);
app.use(VueSweetalert2);
app.use(router);

app.mount('#app');
