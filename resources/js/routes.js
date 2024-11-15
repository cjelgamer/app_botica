//const Home = ()=> import('./components/AdminDashboard.vue')
//const Home = ()=> import('./components/AdminDashboard.vue')

const Mostrar = ()=> import('./components/Proveedor/Mostrar.vue');
const Crear = ()=> import('./components/Proveedor/Crear.vue');
const Editar = ()=> import('./components/Proveedor/Editar.vue');

export const routes =[
    {
        name:'mostrarProveedor',
        paht:'/Proveedor',
        component:Mostrar
    },
    {
        name:'crearProveedor',
        paht:'/Proveedor',
        component:Crear
    },
    {
        name:'editarProveedor',
        paht:'/Proveedor',
        component:Editar
    }
]