<template>
    <div class="dashboard">
        <header class="dashboard-header">
            <img src="/images/logo_c.png" alt="Logo Botica" class="logo" @click="$router.push('/vendedor-dashboard')" style="cursor: pointer;">
            <nav class="dashboard-nav">
                <button @click="$router.push({ path: '/vendedor-dashboard/medicamentos' })">Lista de Medicamentos</button>
                <button @click="$router.push({ path: '/vendedor-dashboard/registrar-venta' })">Realizar Venta</button>
            </nav>
            <div class="user-options">
                <i class="fas fa-user-circle user-icon" @click="$router.push({ path: '/vendedor-dashboard/perfil' })"></i>
            </div>
        </header>

        <main class="dashboard-content">
            <!-- Área principal para la lista de medicamentos -->
            <section class="main-section" v-if="$route.name === 'VendedorMedicamento'">
                <router-view></router-view>
            </section>

            <!-- Área para registrar venta -->
            <section class="main-section" v-else-if="$route.name === 'VendedorRegistrarVenta'">
                <router-view></router-view>
            </section>

            <!-- Área para el perfil -->
            <section class="main-section" v-else-if="$route.name === 'VendedorPerfil'">
                <router-view></router-view>
            </section>

            <!-- Sección de ayuda rápida -->
            <aside class="faq-section">
                <h3>Ayuda Rápida</h3>
                <ul>
                    <li>¿Cómo registrar una venta?</li>
                    <li>¿Cómo buscar medicamentos?</li>
                    <li>¿Cómo actualizar mi perfil?</li>
                    <li><a href="#">Más ayuda ></a></li>
                </ul>
            </aside>
        </main>
    </div>
</template>

<script>
export default {
    name: 'VendedorDashboard',
    
    methods: {
        showMessage(action) {
            const messages = {
                realizarVenta: "Moviendo a realizar venta",
                verMedicamentos: "Viendo lista de medicamentos",
                verPerfil: "Viendo perfil"
            };
            console.log(messages[action]);
            alert(messages[action]);
        }
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
    background-color: #fff;
    padding: 10px 20px;
    color: #333;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.dashboard-header .dashboard-nav {
    display: flex;
    gap: 8px;
}

.dashboard-header .dashboard-nav button {
    background: transparent;
    border: 2px solid transparent;
    color: #333;
    cursor: pointer;
    font-size: 14px;
    font-weight: bold;
    padding: 8px 15px;
    transition: all 0.2s ease;
    border-radius: 5px;
}

.dashboard-header .dashboard-nav button:hover {
    background-color: rgba(76, 175, 80, 0.1);
    color: #4CAF50;
}

.logo {
    width: 50px;
    cursor: pointer;
    transition: transform 0.2s ease;
}

.logo:hover {
    transform: scale(1.05);
}

.user-options i {
    font-size: 20px;
    color: #333;
    cursor: pointer;
    transition: all 0.2s ease;
}

.user-options i:hover {
    color: #4CAF50;
    transform: scale(1.1);
}

.dashboard-content {
    display: flex;
    padding: 20px;
    background-color: #f4f4f4;
    height: calc(100vh - 80px);
    overflow: hidden;
}

.main-section {
    flex: 1;
    background-color: white;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    margin-right: 20px;
    overflow-y: auto;
}

.faq-section {
    width: 200px;
    background-color: white;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    height: fit-content;
}

.faq-section h3 {
    margin-bottom: 15px;
    font-size: 16px;
    font-weight: bold;
    color: #333;
    border-bottom: 2px solid #4CAF50;
    padding-bottom: 8px;
}

.faq-section ul {
    list-style: none;
    padding: 0;
    font-size: 14px;
}

.faq-section ul li {
    margin-bottom: 12px;
    padding: 8px;
    border-radius: 4px;
    transition: background-color 0.2s ease;
}

.faq-section ul li:hover {
    background-color: rgba(76, 175, 80, 0.1);
}

.faq-section ul li a {
    color: #333;
    text-decoration: none;
    font-weight: bold;
    transition: color 0.2s ease;
}

.faq-section ul li a:hover {
    color: #4CAF50;
}

@media (max-width: 768px) {
    .dashboard-header {
        flex-direction: column;
        gap: 10px;
    }

    .dashboard-nav {
        width: 100%;
        flex-wrap: wrap;
        justify-content: center;
    }

    .dashboard-content {
        flex-direction: column;
    }

    .main-section {
        margin-right: 0;
        margin-bottom: 20px;
    }

    .faq-section {
        width: 100%;
    }
}
</style>