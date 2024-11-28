<template>
    <div class="modal-overlay">
      <div class="modal-content">
        <h3>{{ editing ? 'Editar Laboratorio' : 'Agregar Laboratorio' }}</h3>
        
        <form @submit.prevent="submitForm">
          <div class="form-group">
            <label for="nombre">Nombre</label>
            <input id="nombre" v-model="currentLaboratorio.Nombre" type="text" required />
          </div>
          <div class="form-group">
            <label for="telefono">Teléfono</label>
            <input id="telefono" v-model="currentLaboratorio.Telefono" type="text" required />
          </div>
          <div class="form-group">
            <label for="direccion">Dirección</label>
            <input id="direccion" v-model="currentLaboratorio.Direccion" type="text" required />
          </div>
          <div class="form-group">
            <label for="ruc">RUC</label>
            <input id="ruc" v-model="currentLaboratorio.RUC" type="text" required />
          </div>
          
          <div class="modal-footer">
            <button type="button" @click="closeModal" class="cancel-button">Cancelar</button>
            <button type="submit" class="submit-button">Guardar</button>
          </div>
        </form>
      </div>
    </div>
  </template>
  
  <script>
  export default {
    props: {
      laboratorio: {
        type: Object,
        default: () => ({})
      },
      editing: {
        type: Boolean,
        default: false
      }
    },
    data() {
      return {
        currentLaboratorio: { ...this.laboratorio }
      };
    },
    methods: {
      submitForm() {
        if (this.editing) {
          this.$emit('update-laboratorio', this.currentLaboratorio);
        } else {
          this.$emit('add-laboratorio', this.currentLaboratorio);
        }
      },
      closeModal() {
        this.$emit('close');
      }
    }
  };
  </script>
  
  <style scoped>
  /* Estilos para el formulario modal */
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
  