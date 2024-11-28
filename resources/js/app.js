// app.js
import { createApp } from 'vue';
import { createRouter, createWebHistory } from 'vue-router';
import LoginForm from './components/LoginForm.vue';
import AdminDashboard from './components/AdminDashboard.vue';
import Vendedores from './components/Vendedores.vue';
import Laboratorio from './components/Laboratorio.vue';
import Medicamento from './components/Medicamento.vue';
import RegistrarCompra from './components/RegistrarCompra.vue';
import '@fortawesome/fontawesome-free/css/all.min.css';
import RegistrarVenta from './components/Registrarventa.vue';

// Define las rutas
const routes = [
    { path: '/', name: 'LoginForm', component: LoginForm },
    {
        path: '/admin-dashboard',
        name: 'AdminDashboard',
        component: AdminDashboard,
        children: [
            { path: 'vendedores', name: 'Vendedores', component: Vendedores },
            { path: 'laboratorios', name: 'Laboratorios',component: Laboratorio },
            { path: 'medicamentos', name: 'Medicamento', component: Medicamento },
            { path: 'registrar-compra', name: 'RegistrarCompra', component: RegistrarCompra },
            { path: 'registrar-venta', name: 'RegistrarVenta', component: RegistrarVenta },
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
