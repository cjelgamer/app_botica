<template>
    <div class="flex items-center justify-center min-h-screen bg-gray-100">
        <div class="container mx-auto p-4">
            <div class="text-center mb-6">
                <img src="/images/logo_c.png" alt="Logo" class="mx-auto w-28 h-28">
            </div>
            <form @submit.prevent="submitLogin" class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
                <div class="form__group mb-4">
                    <input type="text" v-model="Nombre" class="form__field" id="Nombre" placeholder=" " required />
                    <label for="Nombre" class="form__label">Nombre</label>
                </div>
                <div class="form__group mb-4 relative">
                    <input :type="showPassword ? 'text' : 'password'" v-model="Contraseña" class="form__field" id="Contraseña" placeholder=" " required />
                    <label for="Contraseña" class="form__label">Contraseña</label>
                    <i @click="togglePassword" :class="showPassword ? 'fa fa-eye-slash' : 'fa fa-eye'" class="absolute right-2 top-8 cursor-pointer text-gray-500"></i>
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

.form__group {
    position: relative;
    padding: 20px 0 0;
    width: 100%;
    /* Puedes ajustar o eliminar el max-width si es necesario */
    /* max-width: 180px; */
}

.form__field {
    font-family: inherit;
    width: 100%;
    border: none;
    border-bottom: 2px solid #9b9b9b;
    outline: 0;
    font-size: 17px;
    color: #000; /* Cambiado a negro para mejor legibilidad */
    padding: 7px 0;
    background: transparent;
    transition: border-color 0.2s;
}

.form__field::placeholder {
    color: transparent;
}

.form__field:placeholder-shown ~ .form__label {
    font-size: 17px;
    cursor: text;
    top: 20px;
}

.form__label {
    position: absolute;
    top: 0;
    display: block;
    transition: 0.2s;
    font-size: 17px;
    color: #9b9b9b;
    pointer-events: none;
}

.form__field:focus {
    padding-bottom: 6px;
    border-width: 3px;
    border-image: linear-gradient(to right, #00A859, #38caef); /* Cambiado a verde */
    border-image-slice: 1;
}

.form__field:focus ~ .form__label {
    position: absolute;
    top: 0;
    display: block;
    transition: 0.2s;
    font-size: 17px;
    color: #00A859; /* Cambiado a verde */
    font-weight: 700;
}

/* Reset input */
.form__field:required, .form__field:invalid {
    box-shadow: none;
}

.fa-eye, .fa-eye-slash {
    font-size: 1.2em;
}
</style>
