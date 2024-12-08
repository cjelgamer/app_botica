import { createApp } from 'vue';
import { createRouter, createWebHistory } from 'vue-router';
import axios from 'axios';
import LoginForm from './components/LoginForm.vue';
import AdminDashboard from './components/AdminDashboard.vue';
import Vendedores from './components/Vendedores.vue';
import Laboratorio from './components/Laboratorio.vue';
import Medicamento from './components/Medicamento.vue';
import RegistrarCompra from './components/RegistrarCompra.vue';
import PerfilVendedor from './components/PerfilVendedor.vue';
import '@fortawesome/fontawesome-free/css/all.min.css';
import RegistrarVenta from './components/Registrarventa.vue';
import GenerarReportes from './components/GenerarReportes.vue';

// Configuración global de axios
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
let token = document.head.querySelector('meta[name="csrf-token"]');
if (token) {
    axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
} else {
    console.error('CSRF token not found: https://laravel.com/docs/csrf#csrf-x-csrf-token');
}

// Hacer axios disponible globalmente
window.axios = axios;

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
            { path: 'perfil', name: 'PerfilVendedor', component: PerfilVendedor },
            { path: 'generar-reportes', name: 'GenerarReportes', component: GenerarReportes }
        ]
    }
];

// Configura el router
const router = createRouter({
    history: createWebHistory(),
    routes,
});

// Crea el componente raíz
const App = {
    template: '<router-view></router-view>'
};

// Crea la aplicación
const app = createApp(App);

// Configura axios globalmente para Vue
app.config.globalProperties.$axios = axios;

app.use(router);
app.mount('#app');