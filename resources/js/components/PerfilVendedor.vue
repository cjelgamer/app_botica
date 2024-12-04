<!-- components/PerfilVendedor.vue -->
<template>
    <div class="perfil-container bg-white rounded-lg shadow-lg p-6 max-w-2xl mx-auto mt-8">
      <div class="flex flex-col items-center">
        <!-- Icono de usuario más grande -->
        <div class="mb-6">
          <i class="fas fa-user-circle text-6xl text-gray-700"></i>
        </div>
  
        <!-- Información del vendedor -->
        <div class="w-full space-y-4">
          <div class="info-row border-b pb-3">
            <h3 class="text-gray-600 font-medium">Nombre:</h3>
            <p class="text-gray-800 text-lg">{{ vendedorData.nombre }}</p>
          </div>
  
          <div class="info-row border-b pb-3">
            <h3 class="text-gray-600 font-medium">Teléfono:</h3>
            <p class="text-gray-800 text-lg">{{ vendedorData.telefono }}</p>
          </div>
  
          <div class="info-row border-b pb-3">
            <h3 class="text-gray-600 font-medium">Rol:</h3>
            <p class="text-gray-800 text-lg">{{ vendedorData.rol }}</p>
          </div>
  
          <div class="info-row border-b pb-3">
            <h3 class="text-gray-600 font-medium">Estado:</h3>
            <p class="text-gray-800 text-lg" :class="{ 'text-green-600': vendedorData.estado === 'Activo', 'text-red-600': vendedorData.estado !== 'Activo' }">
              {{ vendedorData.estado }}
            </p>
          </div>
        </div>
  
        <!-- Botón de cerrar sesión -->
        <button 
          @click="cerrarSesion" 
          class="mt-8 bg-red-600 text-white px-6 py-2 rounded-lg hover:bg-red-700 transition-colors duration-300"
        >
          Cerrar Sesión
        </button>
      </div>
    </div>
  </template>
  
  <script>
  import axios from 'axios';  // Añade esta línea al inicio del script

  export default {
    data() {
      return {
        vendedorData: {
          nombre: '',
          telefono: '',
          rol: '',
          estado: ''
        }
      }
    },
    
    mounted() {
      this.obtenerDatosVendedor()
    },
  
    methods: {
      async obtenerDatosVendedor() {
        try {
          const response = await axios.get('/vendedor/perfil')
          this.vendedorData = response.data
        } catch (error) {
          console.error('Error al obtener datos del vendedor:', error)
        }
      },
  
      async cerrarSesion() {
        try {
          await axios.post('/vendedor/logout')
          this.$router.push('/')
        } catch (error) {
          console.error('Error al cerrar sesión:', error)
        }
      }
    }
  }
  </script>
  
  <style scoped>
  .info-row {
    display: grid;
    grid-template-columns: 120px 1fr;
    gap: 1rem;
    align-items: center;
  }
  </style>