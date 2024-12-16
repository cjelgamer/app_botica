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
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </template>
  
  <script>
  import axios from 'axios';
  
  export default {
    name: 'MedicamentoVendedor',
    data() {
      return {
        medicamentos: [],
        medicamentosFiltrados: [],
        searchQuery: '',
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
    },
    created() {
      this.fetchMedicamentos();
    },
  };
  </script>
  
  <style scoped>
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
  </style>