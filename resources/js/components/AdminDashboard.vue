<template>
    <div class="dashboard">
        <header class="dashboard-header">
            <img src="/images/logo_c.png" alt="Logo Botica" class="logo">
            <nav class="dashboard-nav">
                <button @click="$router.push({ path: '/admin-dashboard/vendedores' })">Ver Vendedores</button>
                <button @click="$router.push({ path: '/admin-dashboard/laboratorios' })">Ver Laboratorios</button>
                <button @click="showMessage('descargarReportes')">Descargar Reportes</button>
                <button @click="showMessage('ajustarStock')">Ajustar Stock</button>
            </nav>
            <!--<input type="text" placeholder="Buscar..." class="search-bar" />-->
            <div class="user-options">
                <i class="fas fa-bell notification-icon"></i>
                <i class="fas fa-user-circle user-icon"></i>
            </div>
        </header>

        <!-- Segunda cabecera para los botones adicionales -->
        <header class="sub-header">
            <button @click="$router.push({ path: '/admin-dashboard/registrar-venta' })">Realizar Venta</button>
            <button @click="$router.push({ path: '/admin-dashboard/registrar-compra' })">Registrar Compra</button>
            <!--<button @click="showMessage('listaMedicamentos')">Lista de Medicamentos</button>-->
            <button @click="$router.push({ path: '/admin-dashboard/medicamentos' })">Lista de Medicamentos</button>
        </header>

        <main class="dashboard-content">
            <!-- Mostrar esta sección solo si no estamos en la ruta "Vendedores" -->
            <section class="main-section" v-if="$route.name !== 'Vendedores' && $route.name !== 'Laboratorios'&& $route.name !== 'Medicamento' && $route.name !== 'RegistrarCompra'  && $route.name !== 'RegistrarVenta'" >
                <div class="action-container">
                    <div class="action-icon" @click="openModal('publicarMedicamento')">
                        <img src="/images/publish_icon.png" alt="Publicar Medicamento" class="icon-image" />
                        <p>Publicar un Medicamento</p>
                    </div>
                    <div class="action-icon" @click="showMessage('entrevistasContratos')">
                        <img src="/images/contracts_icon.png" alt="Entrevistas y Contratos" class="icon-image" />
                        <p>Entrevistas y Contratos</p>
                    </div>
                </div>
                <button class="publish-button" @click="openModal('publicarMedicamento')">Publicar un Medicamento</button>
            </section>

            <!-- Área principal para cargar el componente de "Vendedores" -->
            <section class="main-section" v-if="$route.name === 'Vendedores'">
                <router-view></router-view> <!-- Aquí se mostrarán los componentes hijos como Vendedores -->
            </section>
            <!-- Área principal para cargar el componente de "Laboratorios" -->
            <section class="main-section" v-if="$route.name === 'Laboratorios'">
                <router-view></router-view> <!-- Aquí se mostrarán los componentes hijos como Vendedores -->
            </section>

                        <!-- Área principal para cargar el componente de "Laboratorios" -->
            <section class="main-section" v-if="$route.name === 'Medicamento'">
                <router-view></router-view> <!-- Aquí se mostrarán los componentes hijos como Vendedores -->
            </section>

    <!-- Mostrar Registrar Compra -->
            <section class="main-section" v-else-if="$route.name === 'RegistrarCompra'">
                <router-view></router-view>
            </section>
    <!-- Mostrar Registrar Venta  -->
           <section class="main-section" v-else-if="$route.name === 'RegistrarVenta'">
                <router-view></router-view>
            </section>

            <aside class="faq-section">
                <h3>Preguntas</h3>
                <ul>
                    <li>¿Cómo sé que estoy protegido?</li>
                    <li>¿Cómo paga?</li>
                    <li>¿Cómo empiezo?</li>
                    <li><a href="#">Más &gt;</a></li>
                </ul>
            </aside>
        </main>

        <FormularioMedicamento 
            :isOpen="modalOpen"
            @close-modal="closeModal" 
            @add-medicamento="handleAddMedicamento"
        />

    </div>
</template>


<script>
import FormularioMedicamento from '@/components/FormularioMedicamento.vue';  // Usando alias '@' para acceder a 'src/components'
export default {
    
    components: {
        FormularioMedicamento
    },

    data() {
        return {
            modalOpen: false,  // Estado para controlar la visibilidad del modal
            medicamento: null, // Aquí puedes almacenar el medicamento que se está publicando
        };
    },
    methods: {
    // Método para abrir el modal
    openModal(action) {
        if (action === 'publicarMedicamento') {
            this.modalOpen = true;  // Abrir el modal cuando se haga clic en 'Publicar Medicamento'
        }
    },

    // Método para cerrar el modal
    closeModal() {
        this.modalOpen = false;  // Cerrar el modal
    },

    handleAddMedicamento(medicamento) {
      // Maneja la lógica para agregar el medicamento
      console.log('Medicamento agregado:', medicamento);
      this.closeModal();  // Cierra el modal después de agregar el medicamento
    },

    // Otros métodos para la navegación
    showMessage(action) {
        if (action === 'verVendedores') {
            this.$router.push({ name: 'Vendedores' });
        } else if(action === 'verLaboratorios'){
            this.$router.push({ name: 'Laboratorios' });
        } else if (action === 'listaMedicamentos') {
            this.$router.push({ name: 'Medicamento' });
        } else if (action === 'registrarCompra') {
            this.$router.push({ path: '/admin-dashboard/registrar-compra' });
        } else if (action === 'registrarVenta') {
            this.$router.push({ path: '/admin-dashboard/registrar-venta' });
        } else {
            const messages = {
                verProveedores: "Moviendo a pestaña de proveedores",
                descargarReportes: "Descargando reportes",
                ajustarStock: "Ajustando stock",
                realizarVenta: "Moviendo a realizar venta",
                publicarMedicamento: "Moviendo a publicar medicamento",
                entrevistasContratos: "Moviendo a entrevistas y contratos",
            };
            console.log(messages[action]);
            alert(messages[action]);
        }
    },
}

};
</script>

<style scoped>
.dashboard {
    display: flex;
    flex-direction: column;
    font-family: Arial, sans-serif;
}

.dashboard-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    background-color: #fff; /* Fondo blanco para el header principal */
    padding: 10px 20px;
    color: #333; /* Color de texto general para el header */
}

.dashboard-header .dashboard-nav button {
    background: none;
    border: none;
    color: #333; /* Color de texto en negro para los botones */
    cursor: pointer;
    font-size: 14px;
    font-weight: bold;
    padding: 5px 15px;
    transition: color 0.3s ease;
}

.dashboard-header .dashboard-nav button:hover {
    color: #4CAF50; /* Color de hover en verde */
}

.search-bar {
    padding: 5px;
    width: 200px;
    margin-left: 20px;
    border-radius: 5px;
    border: 1px solid #ccc;
}


.sub-header {
    display: flex;
    justify-content: space-around;
    background-color: #333; /* Fondo negro para el sub-header */
    padding: 10px 0;
}

.sub-header button {
    background: none;
    border: none;
    color: #fff;
    cursor: pointer;
    font-size: 14px;
    font-weight: bold;
    padding: 5px 15px;
    transition: color 0.3s ease;
}

.sub-header button:hover {
    color: #4CAF50; /* Color de hover en verde */
}


.logo {
    width: 50px;
}




.user-options i {
    font-size: 20px;
    color: #333; /* Cambia el color de los íconos a negro */
    cursor: pointer;
    margin-left: 15px;
    transition: color 0.3s ease;
}

.user-options i:hover {
    color: #4CAF50; /* Color de hover en verde para los íconos */
}

.notification-icon, .user-icon {
    font-size: 20px;
    cursor: pointer;
    color: white;
}

.dashboard-content {
    display: flex;
    padding: 20px;
    background-color: #f4f4f4;
    height: calc(100vh - 60px); /* Ajuste dinámico para la altura */
}

.side-menu {
    width: 200px;
    display: flex;
    flex-direction: column;
    gap: 10px;
    margin-right: 20px;
}

.side-menu button {
    padding: 12px;
    background-color: #4CAF50;
    color: white;
    border: none;
    cursor: pointer;
    text-align: left;
    border-radius: 5px;
    font-weight: bold;
}

.main-section {
    flex: 1;
    display: flex;
    flex-direction: column;
    align-items: center;
    background-color: white;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0px 4px 12px rgba(0, 0, 0, 0.1);
    margin-right: 20px;
}

.action-container {
    display: flex;
    justify-content: space-around;
    width: 100%;
    margin-bottom: 20px;
}

.action-icon {
    display: flex;
    flex-direction: column;
    align-items: center;
    cursor: pointer;
    padding: 15px;
    text-align: center;
}

.action-icon img {
    width: 60px;
    height: 60px;
    margin-bottom: 10px;
}

.publish-button {
    background-color: #333;
    color: white;
    padding: 10px 20px;
    border: none;
    cursor: pointer;
    font-size: 16px;
    border-radius: 5px;
    font-weight: bold;
    margin-top: 10px;
}

.faq-section {
    width: 200px;
    background-color: white;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0px 4px 12px rgba(0, 0, 0, 0.1);
}

.faq-section h3 {
    margin-bottom: 10px;
    font-size: 16px;
    font-weight: bold;
}

.faq-section ul {
    list-style: none;
    padding: 0;
    font-size: 14px;
}

.faq-section ul li {
    margin-bottom: 8px;
}

.faq-section ul li a {
    color: #333;
    text-decoration: none;
    font-weight: bold;
}

.action-icon {
    display: flex;
    flex-direction: column;
    align-items: center;
    cursor: pointer;
    padding: 15px;
    text-align: center;
}

/* Control de tamaño de las imágenes */
.icon-image {
    width: 150px !important; /* Cambia este valor según el tamaño deseado */
    height: 150px !important; /* Cambia este valor según el tamaño deseado */
    margin-bottom: 10px !important;
}

</style>


