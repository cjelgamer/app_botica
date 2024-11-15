<template>
    <div class="flex items-center justify-center min-h-screen bg-gray-100">
        <div class="container mx-auto p-4">
            <div class="text-center mb-6">
                <img src="/images/logo_c.png" alt="Logo" class="mx-auto w-28 h-28">
            </div>
            <form @submit.prevent="submitLogin" class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
                <div class="mb-4">
                    <label for="Nombre" class="block text-gray-700 text-sm font-bold mb-2">Nombre</label>
                    <input type="text" v-model="Nombre" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required />
                </div>
                <div class="mb-4 relative">
                    <label for="Contraseña" class="block text-gray-700 text-sm font-bold mb-2">Contraseña</label>
                    <input :type="showPassword ? 'text' : 'password'" v-model="Contraseña" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required />
                    <i @click="togglePassword" :class="showPassword ? 'fa fa-eye-slash' : 'fa fa-eye'" class="absolute right-3 top-10 cursor-pointer text-gray-500"></i>
                </div>
                <button type="submit" class="text-white font-bold py-2 px-4 rounded w-full focus:outline-none focus:shadow-outline" style="background-color: #00A859;">Iniciar sesión</button>
            </form>
            <p v-if="error" class="text-red-500 text-xs italic text-center">{{ error }}</p>
        </div>
    </div>
</template>

<script>
import axios from 'axios';

export default {
    data() {
        return {
            Nombre: '',
            Contraseña: '',
            showPassword: false, // Para controlar la visibilidad de la contraseña
            error: null
        };
    },
    methods: {
        togglePassword() {
            this.showPassword = !this.showPassword;
        },
        async submitLogin() {
            try {
                const response = await axios.post('/vendedor/login', {
                    Nombre: this.Nombre,
                    Contraseña: this.Contraseña,
                });

                if (response.data.success) {
                    this.error = null;

                    // Redirigir según el rol del usuario
                    if (response.data.role === 'Admin') {
                        this.$router.push({ name: 'AdminDashboard' });
                    } else if (response.data.role === 'Vendedor') {
                        alert('Inicio de vendedor exitoso.');
                    }
                }
            } catch (error) {
                if (error.response && error.response.status === 401) {
                    this.error = error.response.data.error;
                } else {
                    this.error = "Error en la comunicación con el servidor.";
                }
            }
        }
    }
};
</script>

<style scoped>
.container {
    max-width: 400px;
    padding: 20px;
    border: 1px solid #ddd;
    border-radius: 5px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    background-color: #fff;
}

.fa-eye, .fa-eye-slash {
    font-size: 1.2em;
}
</style>
