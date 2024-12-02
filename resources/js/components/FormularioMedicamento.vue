<template>
    <div class="container">
      <h2 class="title">Lista de Medicamentos</h2>
  
      <!-- Barra de búsqueda -->
      <div class="search-container">
        <input
          type="text"
          v-model="searchQuery"
          @input="searchMedicamentos"
          placeholder="Buscar medicamento..."
          class="search-input"
        />
        <button @click="searchMedicamentos" class="search-button">
          <i class="fa fa-search"></i> Buscar
        </button>
      </div>
  
      <!-- Tabla de Medicamentos -->
      <div class="table-container">
        <table>
          <thead>
            <tr>
              <th>N°</th>
              <th>Nombre</th>
              <th>Descripción</th>
              <th>Precio</th>
              <th>Stock</th>
              <th>Tipo</th>
              <th>Acciones</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(medicamento, index) in medicamentosFiltrados" :key="medicamento.ID">
              <td>{{ index + 1 }}</td>
              <td>{{ medicamento.Nombre }}</td>
              <td>{{ medicamento.Descripcion }}</td>
              <td>{{ medicamento.Precio }}</td>
              <td>{{ medicamento.Stock }}</td>
              <td>{{ medicamento.Tipo }}</td>
              <td>
                <button @click="editMedicamento(medicamento)" class="edit-button">
                  <i class="fa fa-pencil"></i>
                </button>
                <button @click="deleteMedicamento(medicamento.ID)" class="delete-button">
                  <i class="fa fa-trash"></i>
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
  
      <!-- Botón para abrir el modal de agregar medicamento -->
      <button @click="openAddModal" class="add-button">
        <i class="fa fa-plus"></i> Agregar Medicamento
      </button>
  
      <!-- Modal para Agregar o Editar Medicamento -->
      <FormularioMedicamento
        :modalOpen="modalOpen"
        :editing="editing"
        :currentMedicamento="currentMedicamento"
        @close-modal="closeModal"
        @add-medicamento="addMedicamento"
        @update-medicamento="updateMedicamento"
      />
    </div>
  </template>
  
  <script>
  import axios from 'axios';

  export default {

    data() {
      return {
        medicamentos: [],
        medicamentosFiltrados: [],
        searchQuery: '',
        currentMedicamento: {
          Nombre: '',
          Descripcion: '',
          Precio: '',
          Stock: '',
          Tipo: '',
        },
        editing: false,
        modalOpen: false,
      };
    },
    methods: {
      async fetchMedicamentos() {
        try {
          const response = await axios.get('/medicamentos');
          this.medicamentos = response.data;
          this.medicamentosFiltrados = response.data;
        } catch (error) {
          console.error('Error al obtener medicamentos:', error);
        }
      },
      searchMedicamentos() {
        this.medicamentosFiltrados = this.medicamentos.filter(medicamento =>
          medicamento.Nombre.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
          medicamento.Descripcion.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
          medicamento.Tipo.toLowerCase().includes(this.searchQuery.toLowerCase())
        );
      },
      openAddModal() {
        this.modalOpen = true;
        this.editing = false;
        this.currentMedicamento = { Nombre: '', Descripcion: '', Precio: '', Stock: '', Tipo: '' };
      },
      editMedicamento(medicamento) {
        this.modalOpen = true;
        this.editing = true;
        this.currentMedicamento = { ...medicamento };
      },
      async addMedicamento(medicamento) {
        try {
          const response = await axios.post('/medicamentos', medicamento);
          this.medicamentos.push(response.data);
          this.medicamentosFiltrados.push(response.data);
          this.closeModal();
        } catch (error) {
          console.error('Error al agregar medicamento:', error);
        }
      },
      async updateMedicamento(medicamento) {
        try {
          const response = await axios.put(`/medicamentos/${medicamento.ID}`, medicamento);
          const index = this.medicamentos.findIndex(m => m.ID === response.data.ID);
          if (index !== -1) {
            this.medicamentos.splice(index, 1, response.data);
            this.medicamentosFiltrados.splice(index, 1, response.data);
          }
          this.closeModal();
        } catch (error) {
          console.error('Error al actualizar medicamento:', error);
        }
      },
      closeModal() {
        this.modalOpen = false;
      },
      async deleteMedicamento(id) {
        try {
          await axios.delete(`/medicamentos/${id}`);
          this.medicamentos = this.medicamentos.filter(m => m.ID !== id);
          this.medicamentosFiltrados = this.medicamentosFiltrados.filter(m => m.ID !== id);
        } catch (error) {
          console.error('Error al eliminar medicamento:', error);
        }
      },
    },
    mounted() {
      this.fetchMedicamentos();
    },
  };
  </script>
  