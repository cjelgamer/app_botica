<template>
    <div class="dashboard-perfil-container">
      <div class="perfil-content bg-white rounded-lg shadow-lg p-6">
        <!-- Header del perfil -->
        <div class="profile-header text-center mb-6">
          <div class="avatar-container mb-4">
            <div class="relative inline-block">
              <i class="fas fa-user-circle text-7xl text-gray-700 hover:text-gray-800 transition-colors"></i>
              <span class="status-indicator" :class="{ 'bg-green-500': vendedorData.estado === 'Activo', 'bg-red-500': vendedorData.estado !== 'Activo' }"></span>
            </div>
          </div>
          <h2 class="text-2xl font-bold text-gray-800 mb-1">{{ vendedorData.nombre }}</h2>
          <!--<p class="text-gray-600">{{ vendedorData.rol }}</p>-->
        </div>
  
        <!-- Información principal -->
        <div class="bg-gray-50 rounded-lg p-4 mb-6">
          <h3 class="text-lg font-semibold text-gray-700 mb-4 border-b pb-2">Información Personal</h3>
          <div class="space-y-4">
            <div class="info-row">
              <div class="info-label">
                <i class="fas fa-phone mr-2 text-gray-500"></i>
                <span>Teléfono:</span>
              </div>
              <p>{{ vendedorData.telefono || 'No disponible' }}</p>
            </div>
  
            <div class="info-row">
              <div class="info-label">
                <i class="fas fa-user-tag mr-2 text-gray-500"></i>
                <span>Rol:</span>
              </div>
              <p class="font-medium">{{ vendedorData.rol }}</p>
            </div>
  
            <div class="info-row">
              <div class="info-label">
                <i class="fas fa-circle mr-2 text-gray-500"></i>
                <span>Estado:</span>
              </div>
              <span 
                class="px-3 py-1 rounded-full text-sm font-medium"
                :class="estadoClasses"
              >
                {{ vendedorData.estado }}
              </span>
            </div>
          </div>
        </div>
  
        <!-- Acciones -->
        <div class="flex flex-col gap-5">
<!-- El botón con su ícono y texto -->
          <button 
            @click="volverDashboard" 
            class="flex items-center justify-center gap-2 bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300 transition-all duration-300"
          >
            <i class="fas fa-arrow-left"></i>
            <span>Volver al Dashboard</span>
          </button>
        </div>

        <button class="Btn" @click="cerrarSesion">
          <svg class="sign" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24">
            <path d="M16 17v-3H9v-4h7V7l5 5-5 5M14 2a2 2 0 012 2v2h-2V4H5v16h9v-2h2v2a2 2 0 01-2 2H5a2 2 0 01-2-2V4a2 2 0 012-2h9z" fill="white"/>
          </svg>
          <div class="text">Cerrar sesión</div>
        </button>

      </div>
    </div>


</template>

  <script>
  import axios from 'axios';
  
  export default {
    data() {
      return {
        vendedorData: {
          vendedor_id: '',
          nombre: '',
          telefono: '',
          rol: '',
          estado: ''
        },
        loading: false,
        error: null
      }
    },
  
    computed: {
      estadoClasses() {
        return {
          'bg-green-100 text-green-700': this.vendedorData.estado === 'Activo',
          'bg-red-100 text-red-700': this.vendedorData.estado !== 'Activo'
        }
      }
    },
    
    mounted() {
      this.obtenerDatosVendedor()
    },
  
    methods: {
      async obtenerDatosVendedor() {
        this.loading = true
        try {
          const response = await axios.get('/vendedor/perfil')
          this.vendedorData = response.data
        } catch (error) {
          console.error('Error al obtener datos del vendedor:', error)
          this.error = 'No se pudo cargar la información del perfil'
        } finally {
          this.loading = false
        }
      },
  
      async cerrarSesion() {
        try {
          await axios.post('/vendedor/logout')
          this.$router.push('/')
        } catch (error) {
          console.error('Error al cerrar sesión:', error)
        }
      },
  
      volverDashboard() {
        this.$router.push('/admin-dashboard')
      }
    }
  }
  </script>
  

  <style scoped>
  .dashboard-perfil-container {
    /*height: 100%;
    /*overflow-y: auto;*/
    padding: 1rem;
  }
  
  .perfil-content {
    max-width: 600px;
    margin: 0 auto;
  }
  
  .info-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.75rem;
    border-bottom: 1px solid #e5e7eb;
  }
  
  .info-row:last-child {
    border-bottom: none;
  }
  
  .info-label {
    display: flex;
    align-items: center;
    color: #4b5563;
    font-weight: 500;
  }
  
  .status-indicator {
    position: absolute;
    bottom: 5px;
    right: 5px;
    width: 12px;
    height: 12px;
    border-radius: 50%;
    border: 2px solid white;
  }
  
  @media (max-width: 640px) {
    .info-row {
      flex-direction: column;
      align-items: flex-start;
      gap: 0.5rem;
    }
    
    .perfil-content {
      padding: 1rem;
    }
  }

  .Btn {
  display: flex;
  align-items: center;
  justify-content: flex-start;
  width: 45px;
  height: 45px;
  border: none;
  border-radius: 50%;
  cursor: pointer;
  position: relative;
  overflow: hidden;
  transition-duration: .3s;
  box-shadow: 2px 2px 10px rgba(0, 0, 0, 0.199);
  background-color: rgb(255, 65, 65);
  margin-top: 20px;
}

/* Estilo para el ícono */
.sign {
  width: 100%;
  transition-duration: .3s;
  display: flex;
  align-items: center;
  justify-content: center;
}

/* Estilo para el texto */
.text {
  position: absolute;
  right: 0%;
  width: 0%;
  opacity: 0;
  color: white;
  font-size: 1em;
  font-weight: 600;
  transition-duration: .3s;
}

/* Efecto hover para expandir el botón */
.Btn:hover {
  width: 225px;
  border-radius: 40px;
  transition-duration: .3s;
}

/* Efecto hover para el ícono */
.Btn:hover .sign {
  width: 30%;
  transition-duration: .3s;
  padding-left: 20px;
}

/* Efecto hover para mostrar el texto */
.Btn:hover .text {
  opacity: 1;
  width: 70%;
  transition-duration: .3s;
  padding-right: 10px;
}

/* Efecto al hacer clic */
.Btn:active {
  transform: translate(2px, 2px);
}
  </style>