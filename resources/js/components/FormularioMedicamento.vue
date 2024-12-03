<template>
    <div v-if="isOpen" class="modal-overlay">
      <div class="modal-content">
        <h3>Agregar Medicamento</h3>
        <form @submit.prevent="addMedicamento">
          <div class="form-group">
            <label for="nombre">Nombre</label>
            <input id="nombre" v-model="medicamento.Nombre" type="text" required />
          </div>
          <div class="form-group">
            <label for="descripcion">Descripción</label>
            <input id="descripcion" v-model="medicamento.Descripcion" type="text" required />
          </div>
          <div class="form-group">
            <label for="precio">Precio</label>
            <input id="precio" v-model="medicamento.Precio" type="number" step="0.01" required />
          </div>
          <div class="form-group">
            <label for="stock">Stock</label>
            <input id="stock" v-model="medicamento.Stock" type="number" required />
          </div>
          <div class="form-group">
            <label for="tipo">Tipo</label>
            <input id="tipo" v-model="medicamento.Tipo" type="text" required />
          </div>
          <div class="modal-footer">
            <button type="button" @click="closeModal" class="cancel-button">Cancelar</button>
            <button type="submit" class="submit-button">Agregar Medicamento</button>
          </div>
        </form>
      </div>
    </div>
  </template>
  
  <script>
  import axios from 'axios';
  export default {
    props: {
      isOpen: {
        type: Boolean,
        required: true,
      },
    },
    data() {
      return {
        medicamento: {
          Nombre: '',
          Descripcion: '',
          Precio: '',
          Stock: '',
          Tipo: '',
        },
      };
    },
    methods: {
        async addMedicamento() {
    try {
      // Realiza la solicitud POST al servidor
      const response = await axios.post('/medicamentos', this.medicamento);
      // Emitir el evento con los datos recibidos del servidor
      this.$emit('add-medicamento', response.data);
      // Resetear el formulario y cerrar el modal
      this.resetForm();
      this.closeModal();
      alert('Medicamento agregado correctamente');
    } catch (error) {
      console.error('Error al agregar el medicamento:', error);
      alert('Hubo un error al agregar el medicamento');
    }
  },
      closeModal() {
        this.$emit('close-modal');
      },
      resetForm() {
        this.medicamento = {
          Nombre: '',
          Descripcion: '',
          Precio: '',
          Stock: '',
          Tipo: '',
        };
      },
    },
  };
  </script>
  
  <style scoped>
  /* Similar styles to your main modal */
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
  
  .form-group {
    margin-bottom: 15px;
    text-align: left;
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
  </style>
  