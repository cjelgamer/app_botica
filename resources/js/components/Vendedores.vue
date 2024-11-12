<template>
    <div>
      <h2>Lista de Vendedores</h2>
  
      <!-- Formulario para crear un nuevo vendedor -->
      <form @submit.prevent="addVendedor">
        <input v-model="newVendedor.Nombre" placeholder="Nombre" />
        <input v-model="newVendedor.Telefono" placeholder="Teléfono" />
        <input v-model="newVendedor.Estado" placeholder="Estado" />
        <input v-model="newVendedor.Rol" placeholder="Rol" />
        <button type="submit">Agregar Vendedor</button>
      </form>
  
      <!-- Mostrar la lista de vendedores -->
      <table>
        <thead>
          <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Teléfono</th>
            <th>Estado</th>
            <th>Rol</th>
            <th>Acciones</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="vendedor in vendedores" :key="vendedor.id">
            <td>{{ vendedor.ID }}</td>
            <td>{{ vendedor.Nombre }}</td>
            <td>{{ vendedor.Telefono }}</td>
            <td>{{ vendedor.Estado }}</td>
            <td>{{ vendedor.Rol }}</td>
            <td>
              <button @click="editVendedor(vendedor)">Editar</button>
              <button @click="deleteVendedor(vendedor.ID)">Eliminar</button>
            </td>
          </tr>
        </tbody>
      </table>
  
      <!-- Modal para editar vendedor -->
      <div v-if="editing" class="modal">
        <h3>Editar Vendedor</h3>
        <input v-model="currentVendedor.Nombre" placeholder="Nombre" />
        <input v-model="currentVendedor.Telefono" placeholder="Teléfono" />
        <input v-model="currentVendedor.Estado" placeholder="Estado" />
        <input v-model="currentVendedor.Rol" placeholder="Rol" />
        <button @click="updateVendedor">Actualizar</button>
      </div>
    </div>
  </template>
  
  <script>
  import axios from 'axios';
  
  export default {
    data() {
      return {
        vendedores: [],
        newVendedor: {
          Nombre: '',
          Telefono: '',
          Estado: '',
          Rol: '',
        },
        currentVendedor: null,
        editing: false,
      };
    },
    methods: {
      async fetchVendedores() {
        const response = await axios.get('/vendedores');
        this.vendedores = response.data;
      },
      async addVendedor() {
        try {
          const response = await axios.post('/vendedores', this.newVendedor);
          this.vendedores.push(response.data);
          this.newVendedor = { Nombre: '', Telefono: '', Estado: '', Rol: '' };
        } catch (error) {
          console.error('Error al agregar vendedor:', error);
        }
      },
      editVendedor(vendedor) {
        this.currentVendedor = { ...vendedor };
        this.editing = true;
      },
      async updateVendedor() {
        try {
          const response = await axios.put(`/vendedores/${this.currentVendedor.ID}`, this.currentVendedor);
          const index = this.vendedores.findIndex(v => v.ID === response.data.ID);
          this.vendedores[index] = response.data;
          this.editing = false;
          this.currentVendedor = null;
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
    },
    created() {
      this.fetchVendedores();
    },
  };
  </script>
  
  <style scoped>
  /* Estilos del modal y tabla */
  .modal {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    display: flex;
    justify-content: center;
    align-items: center;
  }
  </style>
  