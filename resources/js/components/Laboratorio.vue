<template>
    <div class="container">
      <h2 class="title">Lista de laboratorios</h2>
  
      <!-- Tabla de Vendedores -->
      <div class="table-container">
        <table>
          <thead>
            <tr>
              <th>N°</th>
              <th>Nombre</th>
              <th>Teléfono</th>
              <th>Direccion</th>
              <th>RUC</th>
              <th>Acciones</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(laboratorio, index) in laboratorios" :key="laboratorio.ID">
              <td>{{ index + 1 }}</td>
              <td>{{ laboratorio.Nombre }}</td>
              <td>{{ laboratorio.Telefono }}</td>
              <td>{{ laboratorio.Direccion }}</td>
              <td>{{ laboratorio.RUC}}</td>
              <td>
                <button @click="editLaboratorios(laboratorio)" class="edit-button">
                  <i class="fa fa-pencil"></i>
                </button>
                <button @click="deleteLaboratorio(laboratorio.ID)" class="delete-button">
                  <i class="fa fa-trash"></i>
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
  
      <!-- Modal para Agregar o Editar Vendedor -->
      <div v-if="modalOpen" class="modal-overlay">
        <div class="modal-content">
          <h3>{{ editing ? 'Editar Laboratorio' : 'Agregar Laboratorio' }}</h3>
          <div class="icon-container">
            <i class="fa fa-user-circle person-icon"></i>
          </div>
          <form @submit.prevent="editing ? updateLaboratorio() : addLaboratorio()">
            <div class="form-group">
              <label for="nombre">Nombres</label>
              <input id="nombre" v-model="currentLaboratorio.Nombre" type="text" required />
            </div>
            <label for="telefono">Teléfono</label>
            <div class="form-group">
              <input id="telefono" v-model="currentLaboratorio.Telefono" type="text" required />
            </div>
            <label for="telefono">Direccion</label>
            <div class="form-group">
              <input id="direccion" v-model="currentLaboratorio.Direccion" type="text" required />
            </div>
            <label for="telefono">RUC</label>
            <div class="form-group">
              <input id="ruc" v-model="currentLaboratorio.RUC" type="text" required />
            </div>
            <!-- Campo de Contraseña solo para agregar nuevo vendedor 
            <div v-if="!editing" class="form-group password-group">
              <label for="contraseña">Contraseña</label>
              <input :type="showPassword ? 'text' : 'password'" id="contraseña" v-model="currentVendedor.Contraseña" required />
              <i @click="togglePassword" :class="showPassword ? 'fa fa-eye-slash' : 'fa fa-eye'" class="eye-icon"></i>
            </div>-->
  
            <div class="modal-footer">
              <button type="button" @click="closeModal" class="cancel-button">Cancelar</button>
              <button type="submit" class="submit-button">Guardar Cambios</button>
            </div>
          </form>
        </div>
      </div>
  
      <!-- Modal para Cambiar Contraseña 
      <div v-if="passwordModalOpen" class="modal-overlay">
        <div class="modal-content">
          <h3>Cambiar Contraseña</h3>
          <form @submit.prevent="updatePassword">
            <div class="form-group password-group">
              <label for="newPassword">Nueva Contraseña</label>
              <input :type="showPassword ? 'text' : 'password'" id="newPassword" v-model="newPassword" required />
              <i @click="togglePassword" :class="showPassword ? 'fa fa-eye-slash' : 'fa fa-eye'" class="eye-icon"></i>
            </div>
            <div class="modal-footer">
              <button type="button" @click="closePasswordModal" class="cancel-button">Cancelar</button>
              <button type="submit" class="save-password-button">Guardar Contraseña</button>
            </div>
          </form>
        </div>
      </div>
    </div>-->
  
  </div>
      <!-- Botón para abrir el modal de agregar vendedor -->
      <button @click="openAddModal" class="add-button">
        <i class="fa fa-plus"></i> Agregar Laboratorio
      </button>
  
  </template>
  
  <script>
  import axios from 'axios';
  
  export default {
    data() {
      return {
        laboratorios: [],
        currentLaboratorio: {
          Nombre: '',
          Telefono: '',
          Direccion: '',
          RUC: '',
          //Contraseña: '' 
        },
        editing: false,
        modalOpen: false,
        /*passwordModalOpen: false,
        selectedVendedorId: null,
        newPassword: '',
        showPassword: false*/
      };
    },
    methods: {
      async fetchLaboratorios() {
        try {
          const response = await axios.get('/laboratorios');
          this.laboratorios = response.data;
        } catch (error) {
          console.error('Error al obtener laboratorios:', error);
        }
      },
      openAddModal() {
        this.modalOpen = true;
        this.editing = false;
        this.currentLaboratorio = { Nombre: '', Telefono: '',Direccion:'', RUC: '' };
      },
      editLaboratorios(laboratorio) {
        this.modalOpen = true;
        this.editing = true;
        this.currentLaboratorio = { ...laboratorio };
      },
      async addLaboratorio() {
        if (!this.currentLaboratorio.Nombre || !this.currentLaboratorio.Telefono || !this.currentLaboratorio.Direccion || !this.currentLaboratorio.RUC) {
        alert("Por favor, completa todos los campos.");
        return;
        }

      try {
        const response = await axios.post("/laboratorios", this.currentLaboratorio);
        this.laboratorios.push(response.data);
        this.closeModal();
      } catch (error) {
        console.error("Error al agregar laboratorio:", error);
      }
      },
      async updateLaboratorio() {
        try {
          const response = await axios.put(`/laboratorios/${this.currentLaboratorio.ID}`, this.currentLaboratorio);
          const index = this.laboratorios.findIndex(v => v.ID === response.data.ID);
          if (index !== -1) {
            this.laboratorios.splice(index, 1, response.data);
          }
          this.closeModal();
        } catch (error) {
          console.error('Error al actualizar laboratorio:', error);
        }
      },
      /*async updatePassword() {
        if (this.newPassword) {
          try {
            await axios.put(`/vendedores/${this.selectedVendedorId}/cambiar-contrasena`, { Contraseña: this.newPassword });
            this.closePasswordModal();
            alert('Contraseña actualizada correctamente');
          } catch (error) {
            console.error('Error al actualizar la contraseña:', error);
          }
        } else {
          alert('Por favor, ingresa una nueva contraseña');
        }
      },*/
      /*async toggleEstado(laboratorio) {
        try {
          const nuevoEstado = laboratorio.Estado === 'Activo' ? 'Inactivo' : 'Activo';
          await axios.put(`/laboratorios/${laboratorio.ID}/actualizar-estado`, { Estado: nuevoEstado });
          laboratorio.Estado = nuevoEstado; // Actualizar en la vista
        } catch (error) {
          console.error('Error al cambiar el estado:', error);
        }
      },*/
      async deleteLaboratorio(id) {
        try {
          await axios.delete(`/laboratorios/${id}`);
          this.laboratorios = this.laboratorios.filter(v => v.ID !== id);
        } catch (error) {
          console.error('Error al eliminar laboratorio:', error);
        }
      },
      closeModal() {
        this.modalOpen = false;
        this.currentLaboratorio = { Nombre: '', Telefono: '',Direccion:'', RUC: '' };
      },
    },
    created() {
      this.fetchLaboratorios();
    }
  };
  </script>
  
  <style scoped>
  /* Estilos para el título */
  .title {
    font-size: 2em;
    font-weight: bold;
  }
  
  /* Estilos del toggle switch */
  .switch {
    position: relative;
    display: inline-block;
    width: 34px;
    height: 20px;
  }
  .switch input {
    opacity: 0;
    width: 0;
    height: 0;
  }
  .slider {
    position: absolute;
    cursor: pointer;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: #ccc;
    transition: .4s;
  }
  .slider:before {
    position: absolute;
    content: "";
    height: 16px;
    width: 16px;
    left: 2px;
    bottom: 2px;
    background-color: white;
    transition: .4s;
  }
  input:checked + .slider {
    background-color: #4caf50;
  }
  input:checked + .slider:before {
    transform: translateX(14px);
  }
  .slider.round {
    border-radius: 34px;
  }
  .slider.round:before {
    border-radius: 50%;
  }
  
  /* Estilos para el contenedor principal */
  .container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px;
  }
  
  /* Estilos para la tabla */
  .table-container {
    margin-top: 30px;
    border-collapse: collapse;
    width: 100%;
  }
  
  .table-container table {
    width: 100%;
    border: 1px solid #ddd;
    text-align: left;
  }
  
  .table-container th, .table-container td {
    padding: 12px 15px;
    border-bottom: 1px solid #ddd;
  }
  
  .table-container th {
    background-color: #333;
    color: #fff;
  }
  
  /* Estilos de los botones de acciones */
  .edit-button, .delete-button, .password-button {
    border: none;
    background-color: transparent;
    cursor: pointer;
    font-size: 18px;
    margin-right: 10px;
  }
  
  .edit-button {
    color: #4caf50;
  }
  
  .delete-button {
    color: #f44336;
  }
  
  .password-button {
    color: #ff9800;
  }
  
  /* Modal Styles */
  .modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.5);
    display: flex;
    justify-content: center;
    align-items: center;
  }
  
  .modal-content {
    background-color: #fff;
    padding: 20px;
    border-radius: 5px;
    max-width: 400px;
    width: 100%;
    text-align: center;
  }
  
  /* Icon container */
  .icon-container {
    display: flex;
    justify-content: center;
    margin-bottom: 20px;
  }
  
  .person-icon {
    font-size: 60px;
    color: #333;
  }
  
  .form-group {
    margin-bottom: 15px;
    text-align: left;
  }
  
  .password-group {
    position: relative;
  }
  
  label {
    font-weight: bold;
    margin-bottom: 5px;
    display: block;
  }
  
  input, select {
    width: 100%;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 5px;
  }
  
  .eye-icon {
    position: absolute;
    right: 10px;
    top: 35px;
    cursor: pointer;
  }
  
  .modal-footer {
    display: flex;
    justify-content: space-between;
  }
  
  .cancel-button {
    background-color: #f44336;
    color: white;
    padding: 10px 20px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
  }
  
  .submit-button, .save-password-button {
    background-color: #4caf50;
    color: white;
    padding: 10px 20px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
  }
  


  .add-button {
  background-color: #4caf50;
  color: white;
  padding: 10px 20px;
  border: none;
  border-radius: 5px;
  cursor: pointer;
  font-size: 16px;
  margin-top: 20px;
  display: block;
  margin-left: auto;
  margin-right: auto;
}
  
  .status-button {
    width: 24px;
    height: 24px;
    border-radius: 50%;
    border: none;
    display: flex;
    align-items: center;
    justify-content: center;
  }
  
  .active-status {
    background-color: green;
    color: white;
  }
  
  .inactive-status {
    background-color: red;
    color: white;
  }
  
  
  </style>
  