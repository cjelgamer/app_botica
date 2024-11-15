// app.js
import { createApp } from 'vue';
import { createRouter, createWebHistory } from 'vue-router';
import LoginForm from './components/LoginForm.vue';
import AdminDashboard from './components/AdminDashboard.vue';
import Vendedores from './components/Vendedores.vue';
//import Proveedor from './components/Proveedor';
import '@fortawesome/fontawesome-free/css/all.min.css';

// Define las rutas
const routes = [
    { path: '/', name: 'LoginForm', component: LoginForm },
    {
        path: '/admin-dashboard',
        name: 'AdminDashboard',
        component: AdminDashboard,
        children: [
            { path: 'vendedores', name: 'Vendedores', component: Vendedores },
        ]
    }
];

// Configura el router
const router = createRouter({
    history: createWebHistory(),
    routes,
});

// Crea un componente raíz que contenga `router-view`
const App = {
    template: '<router-view></router-view>'
};

// Crea la aplicación, usa el router y monta el componente raíz
const app = createApp(App);
app.use(router);
app.mount('#app');
