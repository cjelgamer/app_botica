<template>
    <div class="container mx-auto p-4">
        <h2 class="text-2xl font-bold text-center mb-4">Iniciar sesión</h2>
        <form @submit.prevent="submitLogin" class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
            <div class="mb-4">
                <label for="Nombre" class="block text-gray-700 text-sm font-bold mb-2">Nombre</label>
                <input type="text" v-model="Nombre" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required />
            </div>
            <div class="mb-4">
                <label for="Contraseña" class="block text-gray-700 text-sm font-bold mb-2">Contraseña</label>
                <input type="password" v-model="Contraseña" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required />
            </div>
            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">Iniciar sesión</button>
        </form>
        <p v-if="error" class="text-red-500 text-xs italic">{{ error }}</p>
    </div>
</template>

<script>
import axios from 'axios';

export default {
    data() {
        return {
            Nombre: '',
            Contraseña: '',
            error: null
        };
    },
    methods: {
        async submitLogin() {
        try {
            const response = await axios.post('/vendedor/login', {
                Nombre: this.Nombre,
                Contraseña: this.Contraseña, // Asegúrate de que coincida con el nombre en el controlador
            });

        // Si la respuesta es exitosa
            if (response.data.success) {
                this.error = null; // Limpiar el mensaje de error
                alert('Inicio de sesión exitoso.');
            // Aquí puedes redirigir o realizar cualquier otra acción
            }
        } catch (error) {
        // Manejar errores de autenticación
            if (error.response && error.response.status === 401) {
                this.error = error.response.data.error; // Mostrar el mensaje de error específico
            } else {
                this.error = "Error en la comunicación con el servidor."; // Otro tipo de error
            }
        }
        }

    }
};
</script>

<style scoped>
.container {
    max-width: 400px;
    margin: auto;
    padding: 20px;
    border: 1px solid #ddd;
    border-radius: 5px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    background-color: #fff;
}
</style>
