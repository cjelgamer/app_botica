<template>
    <div class="container">
      <h2 class="title">Lista de Vendedores</h2>
  
      <!-- Botón para abrir el modal de agregar vendedor -->
      <button @click="openAddModal" class="add-button">
        <i class="fa fa-plus"></i> Agregar Vendedor
      </button>
  
      <!-- Tabla de Vendedores -->
      <div class="table-container">
        <table>
          <thead>
            <tr>
              <th>N°</th>
              <th>Nombre</th>
              <th>Teléfono</th>
              <th>Estado</th>
              <th>Rol</th>
              <th>Acciones</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(vendedor, index) in vendedores" :key="vendedor.ID">
              <td>{{ index + 1 }}</td>
              <td>{{ vendedor.Nombre }}</td>
              <td>{{ vendedor.Telefono }}</td>
              <td><span :class="{'active-status': vendedor.Estado === 'Activo', 'inactive-status': vendedor.Estado !== 'Activo'}">{{ vendedor.Estado }}</span></td>
              <td>{{ vendedor.Rol }}</td>
              <td>
                <button @click="editVendedor(vendedor)" class="edit-button">
                  <i class="fa fa-pencil"></i>
                </button>
                <button @click="deleteVendedor(vendedor.ID)" class="delete-button">
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
          <h3>{{ editing ? 'Editar Vendedor' : 'Agregar Vendedor' }}</h3>
          <form @submit.prevent="editing ? updateVendedor() : addVendedor()">
            <div class="form-group">
              <label for="nombre">Nombre</label>
              <input id="nombre" v-model="currentVendedor.Nombre" type="text" required />
            </div>
            <div class="form-group">
              <label for="telefono">Teléfono</label>
              <input id="telefono" v-model="currentVendedor.Telefono" type="text" required />
            </div>
            <div class="form-group">
              <label for="estado">Estado</label>
              <input id="estado" v-model="currentVendedor.Estado" type="text" required />
            </div>
            <div class="form-group">
              <label for="rol">Rol</label>
              <input id="rol" v-model="currentVendedor.Rol" type="text" required />
            </div>
            <div class="form-group">
              <label for="contraseña">Contraseña</label>
              <input id="contraseña" v-model="currentVendedor.Contraseña" type="password" required />
            </div>
            <div class="modal-footer">
              <button type="button" @click="closeModal" class="cancel-button">Cancelar</button>
              <button type="submit" class="submit-button">Guardar</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </template>
  
  <script>
  import axios from 'axios';
  
  export default {
    data() {
      return {
        vendedores: [],
        currentVendedor: {
          Nombre: '',
          Telefono: '',
          Estado: '',
          Rol: '',
          Contraseña: ''
        },
        editing: false,
        modalOpen: false,
      };
    },
    methods: {
      async fetchVendedores() {
        const response = await axios.get('/vendedores');
        this.vendedores = response.data;
      },
      openAddModal() {
        this.modalOpen = true;
        this.editing = false;
        this.currentVendedor = { Nombre: '', Telefono: '', Estado: '', Rol: '', Contraseña: '' };
      },
      editVendedor(vendedor) {
        this.modalOpen = true;
        this.editing = true;
        this.currentVendedor = { ...vendedor };
      },
      async addVendedor() {
        try {
          const response = await axios.post('/vendedores', this.currentVendedor);
          this.vendedores.push(response.data);
          this.closeModal();
        } catch (error) {
          console.error('Error al agregar vendedor:', error);
        }
      },
      async updateVendedor() {
        try {
          const response = await axios.put(`/vendedores/${this.currentVendedor.ID}`, this.currentVendedor);
          const index = this.vendedores.findIndex(v => v.ID === response.data.ID);
          this.vendedores[index] = response.data;
          this.closeModal();
        } catch (error) {
          console.error('Error al actualizar vendedor:', error);
        }
      },
      async deleteVendedor(id) {
        try {
          await axios.delete(`/vendedores/${id}`);
          this.vendedores = this.vendedores.filter(v => v.ID !== id);
        } catch (error) {
          console.error('Error al eliminar vendedor:', error);
        }
      },
      closeModal() {
        this.modalOpen = false;
        this.currentVendedor = { Nombre: '', Telefono: '', Estado: '', Rol: '', Contraseña: '' };
      }
    },
    created() {
      this.fetchVendedores();
    }
  };
  </script>
  
  <style scoped>
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
  .edit-button, .delete-button {
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
    max-width: 500px;
    width: 100%;
  }
  
  .form-group {
    margin-bottom: 15px;
  }
  
  label {
    font-weight: bold;
    margin-bottom: 5px;
    display: block;
  }
  
  input {
    width: 100%;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 5px;
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
  
  .submit-button {
    background-color: #4caf50;
    color: white;
    padding: 10px 20px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
  }
  
  /* Estilos para el botón de agregar */
  .add-button {
    background-color: #4caf50;
    color: white;
    padding: 10px 20px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-size: 16px;
  }
  
  .active-status {
    color: green;
  }
  
  .inactive-status {
    color: red;
  }
  </style>
  