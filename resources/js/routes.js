
import { createRouter, createWebHistory } from 'vue-router';

// Import components
import Home from './components/Home.vue';
import ProductList from './components/product/ProductList.vue';
import ProductForm from './components/product/ProductForm.vue';
import Product from './components/product/Product.vue';

const router = createRouter({
    history: createWebHistory(),
    routes: [
        { path: '/', component: Home }, //Default Component
        { path: '/products', component: ProductList },
        { path: '/products/create', component: ProductForm },
        { path: '/products/:id', component: Product },
        { path: '/products/:id/edit', component: ProductForm },
    ]
});
export default router