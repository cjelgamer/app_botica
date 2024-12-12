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
          <tr v-for="(medicamento, index) in medicamentosFiltrados" :key="medicamento.ID || index">
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

    <!-- Modal para Agregar o Editar Medicamento -->
    <div v-if="modalOpen" class="modal-overlay">
      <div class="modal-content">
        <h3>{{ editing ? 'Editar Medicamento' : 'Agregar Medicamento' }}</h3>
        <form @submit.prevent="editing ? updateMedicamento() : addMedicamento()">
          <div class="form-group">
            <label for="nombre">Nombre</label>
            <input id="nombre" v-model="currentMedicamento.Nombre" type="text" required />
          </div>
          <div class="form-group">
            <label for="descripcion">Descripción</label>
            <input id="descripcion" v-model="currentMedicamento.Descripcion" type="text" required />
          </div>
          <div class="form-group">
            <label for="precio">Precio</label>
            <input id="precio" v-model="currentMedicamento.Precio" type="number" step="0.01" required />
          </div>
          <div class="form-group">
            <label for="stock">Stock</label>
            <input id="stock" v-model="currentMedicamento.Stock" type="number" required />
          </div>
          <div class="form-group">
            <label for="tipo">Tipo</label>
            <input id="tipo" v-model="currentMedicamento.Tipo" type="text" required />
          </div>
          <div class="modal-footer">
            <button type="button" @click="closeModal" class="cancel-button">Cancelar</button>
            <button type="submit" class="submit-button">Guardar Cambios</button>
          </div>
        </form>
      </div>
    </div>

    <!-- Botón para abrir el modal de agregar medicamento -->
    <button @click="openAddModal" class="add-button">
      <i class="fa fa-plus"></i> Agregar Medicamento
    </button>
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
        this.medicamentosFiltrados = [...response.data];
      } catch (error) {
        console.error('Error al obtener medicamentos:', error);
      }
    },
    searchMedicamentos() {
      const searchTerm = this.searchQuery.toLowerCase().trim();
      if (!searchTerm) {
        this.medicamentosFiltrados = [...this.medicamentos];
      } else {
        this.medicamentosFiltrados = this.medicamentos.filter(medicamento =>
          medicamento.Nombre.toLowerCase().includes(searchTerm) ||
          medicamento.Descripcion.toLowerCase().includes(searchTerm) ||
          medicamento.Tipo.toLowerCase().includes(searchTerm)
        );
      }
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
    async addMedicamento() {
      try {
        const response = await axios.post('/medicamentos', this.currentMedicamento);
        this.medicamentos.push(response.data);
        this.searchMedicamentos();
        this.closeModal();
      } catch (error) {
        console.error('Error al agregar medicamento:', error);
      }
    },
    async updateMedicamento() {
      try {
        const response = await axios.put(`/medicamentos/${this.currentMedicamento.ID}`, this.currentMedicamento);
        const index = this.medicamentos.findIndex(m => m.ID === response.data.ID);
        if (index !== -1) {
          this.medicamentos.splice(index, 1, response.data);
        }
        this.searchMedicamentos();
        this.closeModal();
      } catch (error) {
        console.error('Error al actualizar medicamento:', error);
      }
    },
    async deleteMedicamento(id) {
      try {
        await axios.delete(`/medicamentos/${id}`);
        this.medicamentos = this.medicamentos.filter(m => m.ID !== id);
        this.searchMedicamentos();
      } catch (error) {
        console.error('Error al eliminar medicamento:', error);
      }
    },
    closeModal() {
      this.modalOpen = false;
      this.currentMedicamento = { Nombre: '', Descripcion: '', Precio: '', Stock: '', Tipo: '' };
    },
  },
  created() {
    this.fetchMedicamentos();
  },
};
</script>

<style scoped>
/* Estilos para la barra de búsqueda */
.search-container {
  display: flex;
  justify-content: space-between;
  margin-bottom: 20px;
  align-items: center;
}

.search-input {
  width: 70%;
  padding: 10px;
  font-size: 16px;
  border-radius: 5px;
  border: 1px solid #ddd;
}

.search-button {
  padding: 10px 20px;
  background-color: #4caf50;
  color: white;
  font-size: 16px;
  border: none;
  border-radius: 5px;
  cursor: pointer;
}

.search-button i {
  margin-right: 5px;
}

.search-button:hover {
  background-color: #45a049;
}

/* Estilos para la tabla y otros elementos */
.table-container {
  margin-top: 30px;
  width: 100%;
  max-height: 500px;
  overflow-y: auto;
  border: 1px solid #ddd;
  border-radius: 5px;
  background: white;
  position: relative;
}

.table-container table {
  width: 100%;
  border-collapse: collapse;
  text-align: left;
}

.table-container thead th {
  position: sticky;
  top: 0;
  background-color: #333;
  color: #fff;
  z-index: 1;
  padding: 8px 15px;
  height: 40px;
  line-height: 1.2;
}

.table-container tbody td {
  padding: 8px 15px;
  height: 40px;
  line-height: 1.2;
  border-bottom: 1px solid #ddd;
}

.table-container tbody tr:hover {
  background-color: #f5f5f5;
}

.table-container tbody tr:last-child td {
  border-bottom: none;
}

/* Estilos del scroll */
.table-container::-webkit-scrollbar {
  width: 8px;
}

.table-container::-webkit-scrollbar-track {
  background: #f1f1f1;
}

.table-container::-webkit-scrollbar-thumb {
  background: #888;
  border-radius: 4px;
}

.table-container::-webkit-scrollbar-thumb:hover {
  background: #555;
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
  z-index: 1000; /* Asegura que el modal esté por encima de todo */
}

.modal-content {
  background-color: #fff;
  padding: 20px;
  border-radius: 5px;
  max-width: 400px;
  width: 100%;
  text-align: center;
  position: relative;
  z-index: 1001; /* Mayor que el overlay */
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
  margin-top: 20px;
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
}

.add-button:hover, 
.submit-button:hover, 
.save-password-button:hover {
  background-color: #45a049;
}

.cancel-button:hover {
  background-color: #d32f2f;
}
</style>
  
  