<template>
    <div class="dashboard">
        <header class="dashboard-header">
            <img src="/images/logo_c.png" alt="Logo Botica" class="logo" @click="$router.push('/vendedor-dashboard')" style="cursor: pointer;">
            <nav class="dashboard-nav">
                <button @click="$router.push({ path: '/vendedor-dashboard/medicamentos' })">Lista de Medicamentos</button>
                <button @click="$router.push({ path: '/vendedor-dashboard/registrar-venta' })"><span class="key-button">F4</span>Realizar Venta</button>
            </nav>

            <div class="spotify-player">
                <button @click="togglePlayer" class="player-toggle">
                    <span class="svgContainer">
                        <svg
                            viewBox="0 0 496 512"
                            height="1.6em"
                            xmlns="http://www.w3.org/2000/svg"
                            fill="#1db954"
                        >
                            <path
                                d="M248 8C111.1 8 0 119.1 0 256s111.1 248 248 248 248-111.1 248-248S384.9 8 248 8zm100.7 364.9c-4.2 0-6.8-1.3-10.7-3.6-62.4-37.6-135-39.2-206.7-24.5-3.9 1-9 2.6-11.9 2.6-9.7 0-15.8-7.7-15.8-15.8 0-10.3 6.1-15.2 13.6-16.8 81.9-18.1 165.6-16.5 237 26.2 6.1 3.9 9.7 7.4 9.7 16.5s-7.1 15.4-15.2 15.4zm26.9-65.6c-5.2 0-8.7-2.3-12.3-4.2-62.5-37-155.7-51.9-238.6-29.4-4.8 1.3-7.4 2.6-11.9 2.6-10.7 0-19.4-8.7-19.4-19.4s5.2-17.8 15.5-20.7c27.8-7.8 56.2-13.6 97.8-13.6 64.9 0 127.6 16.1 177 45.5 8.1 4.8 11.3 11 11.3 19.7-.1 10.8-8.5 19.5-19.4 19.5zm31-76.2c-5.2 0-8.4-1.3-12.9-3.9-71.2-42.5-198.5-52.7-280.9-29.7-3.6 1-8.1 2.6-12.9 2.6-13.2 0-23.3-10.3-23.3-23.6 0-13.6 8.4-21.3 17.4-23.9 35.2-10.3 74.6-15.2 117.5-15.2 73 0 149.5 15.2 205.4 47.8 7.8 4.5 12.9 10.7 12.9 22.6 0 13.6-11 23.3-23.2 23.3z"
                            ></path>
                        </svg>
                    </span>
                <span class="BG"></span>
                </button>

                <iframe 
                    v-if="showPlayer"
                    src="https://open.spotify.com/embed/playlist/0fFHdocDh14tvtPXlJ8mXs?utm_source=generator&theme=0" 
                    width="300" 
                    height="80" 
                    frameBorder="0" 
                    style="min-width: 300px; min-height: 80px;"
                    allowtransparency="true"
                    allow="autoplay; clipboard-write; encrypted-media; fullscreen; picture-in-picture"
                    loading="lazy"
                    class="spotify-iframe"
                ></iframe>
            </div>

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

    data() {
        return {
            showPlayer: false,
        };
    },

    name: 'VendedorDashboard',
    
    mounted() {
        // Agregar el evento de teclado cuando se monta el componente
        document.addEventListener('keydown', this.handleKeyPress);
    },
    beforeUnmount() {
        // Remover el evento de teclado cuando se destruye el componente
        document.removeEventListener('keydown', this.handleKeyPress);
    },
    
    methods: {


        togglePlayer() {
            this.showPlayer = !this.showPlayer;
        },

        showMessage(action) {
            const messages = {
                realizarVenta: "Moviendo a realizar venta",
                verMedicamentos: "Viendo lista de medicamentos",
                verPerfil: "Viendo perfil"
            };
            console.log(messages[action]);
            alert(messages[action]);
        },

        handleKeyPress(event) {
    if (this.$route.path.startsWith('/vendedor-dashboard')) {
        if (event.key === "F4") {
            event.preventDefault();
            this.$router.push({ path: '/vendedor-dashboard/registrar-venta' });
        }
    }
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

.key-button {
    background: linear-gradient(-225deg, #d5dbe4, #f8f8f8); /* Gradiente similar a teclas */
    border: 1px solid #cdcde6;
    border-radius: 4px;
    box-shadow: inset 0 -2px 0 0 #cdcde6, inset 0 0 1px 1px #fff,
        0 1px 2px 1px rgba(30, 35, 90, 0.4);
    color: #555;
    font-size: 0.9em;
    font-weight: bold;
    padding: 3px 6px;
    margin-right: 8px; /* Espacio entre la tecla y el texto */
    text-transform: uppercase;
    display: inline-block;
    user-select: none; /* Evita que se seleccione */
}

.spotify-player {
    display: flex;
    align-items: center;
    gap: 8px;
    margin: 0 15px;
}

.player-toggle {
    background: none;
    border: 1px solid #e0e0e0;
    color: #333;
    cursor: pointer;
    padding: 8px;
    font-size: 16px;
    transition: all 0.3s ease;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    width: 36px;
    height: 36px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}

.player-toggle:hover {
    color: #4CAF50;
    background-color: rgba(76, 175, 80, 0.1);
    transform: scale(1.05);
}

.spotify-iframe {
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    max-height: 80px;
    background-color: #282828;
}
/* Ajusta el header para acomodar el reproductor */
.dashboard-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 10px 20px;
    gap: 10px;
}
</style>