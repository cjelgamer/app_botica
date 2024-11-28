<template>
    <div class="container">
      <h2 class="title">Realizar Compra</h2>
  
      <!-- Formulario único para entrada y detalles -->
      <form @submit.prevent="guardarEntradaYDetalles">
  
        <!-- Sección de datos de la entrada -->
        <div class="form-group">
          <label for="laboratorio">Laboratorio:</label>
          <select v-model="laboratorioSeleccionado" class="select-field" required @keydown.enter="enviarEntrada">
            <option value="" disabled>Seleccione un laboratorio</option>
            <option v-for="laboratorio in laboratorios" :value="laboratorio.ID" :key="laboratorio.ID">
              {{ laboratorio.Nombre }} (ID: {{ laboratorio.ID }})
            </option>
          </select>
        </div>
  
        <div v-if="laboratorioSeleccionado" class="form-group">
          <p><strong>Laboratorio Seleccionado:</strong> {{ laboratorioSeleccionado }}</p>
        </div>
  
        <div class="form-group">
          <label for="fecha_entrega">Fecha de Entrega:</label>
          <input type="text" id="datepicker" v-model="fechaEntrega" class="input-field" placeholder="Seleccione una fecha" required @keydown.enter="enviarEntrada" />
        </div>
  
        <div class="form-group">
          <label for="total">Total:</label>
          <input type="number" v-model="total" class="input-field" min="0" placeholder="Ingrese el total" required @keydown.enter="enviarEntrada" />
        </div>
  
        <!-- Sección de detalles de la compra (solo visible después de guardar la entrada) -->
        <div v-if="entradaGuardada">
          <h3>Agregar Detalles</h3>
  
          <div class="form-group">
            <label for="medicamento">Medicamento:</label>
            <select v-model="medicamentoSeleccionado" class="select-field" required @keydown.enter="agregarDetalle">
              <option value="" disabled>Seleccione un medicamento</option>
              <option v-for="medicamento in medicamentos" :value="medicamento.ID" :key="medicamento.ID">
                {{ medicamento.Nombre }} (Stock: {{ medicamento.Stock }})
              </option>
            </select>
          </div>
  
          <div class="form-group">
            <label for="cantidad">Cantidad:</label>
            <input type="number" v-model="cantidad" class="input-field" min="1" placeholder="Cantidad" required @keydown.enter="agregarDetalle" />
          </div>
  
          <button type="button" class="add-button" @click="agregarDetalle">Agregar Detalle</button>
  
          <!-- Lista de detalles agregados -->
          <div v-if="detalles.length">
            <h4>Detalles Agregados</h4>
            <ul>
              <li v-for="(detalle, index) in detalles" :key="index">
                {{ detalle.nombre }} - Cantidad: {{ detalle.cantidad }}
              </li>
            </ul>
          </div>
  
          <!-- Botón para finalizar compra -->
          <button type="button" @click="finalizarCompra" class="submit-button finalizar-button">
            Finalizar Compra
          </button>
        </div>
      </form>
  
      <!-- Modal para agregar laboratorio (FormularioLaboratorio) -->
      <FormularioLaboratorio
        v-if="modalLaboratorioAbierto"
        :laboratorio="currentLaboratorio"
        :editing="editingLaboratorio"
        @add-laboratorio="addLaboratorio"
        @update-laboratorio="updateLaboratorio"
        @close="closeModalLaboratorio"
      />
  
      <!-- Botón para agregar un nuevo laboratorio -->
      <button @click="openModalLaboratorio" class="add-button">Agregar Nuevo Laboratorio</button>
    </div>
  </template>
  
  <script>
  import axios from "axios";
  import flatpickr from "flatpickr";
  import "flatpickr/dist/flatpickr.min.css";
  import FormularioLaboratorio from './FormularioLaboratorio.vue';
  
  export default {
    components: {
      FormularioLaboratorio,
    },
    data() {
      return {
        laboratorios: [],
        medicamentos: [],
        laboratorioSeleccionado: null,
        fechaEntrega: "",
        total: 0,
        entradaID: null,
        entradaGuardada: false,
        medicamentoSeleccionado: null,
        cantidad: 1,
        detalles: [],
        modalLaboratorioAbierto: false, // Controlar la apertura del modal de laboratorio
        currentLaboratorio: {},
        editingLaboratorio: false,
      };
    },
    methods: {
      // Cargar datos iniciales
      async cargarDatos() {
        try {
          const resLaboratorios = await axios.get("/laboratorios");
          this.laboratorios = resLaboratorios.data;
  
          const resMedicamentos = await axios.get("/medicamentos");
          this.medicamentos = resMedicamentos.data;
        } catch (error) {
          console.error("Error al cargar datos:", error.response?.data || error.message);
          alert("Hubo un problema al cargar los datos.");
        }
      },
  
      // Guardar entrada y avanzar a la sección de detalles
      async enviarEntrada() {
        if (!this.entradaGuardada) {
          const entrada = {
            laboratorio_id: this.laboratorioSeleccionado,
            fecha_entrega: this.fechaEntrega,
            total: this.total,
          };
  
          try {
            const response = await axios.post("/entrada", entrada);
            if (response.data.id) {
              this.entradaID = response.data.id;
              this.entradaGuardada = true;
              alert("Entrada registrada con éxito.");
            } else {
              alert("Error al registrar la entrada.");
            }
          } catch (error) {
            alert("Error al registrar la entrada.");
          }
        }
      },
  
      // Agregar detalle de medicamento
      async agregarDetalle() {
        if (this.medicamentoSeleccionado && this.cantidad && this.entradaID) {
          const detalle = {
            entrada_id: this.entradaID,
            medicamento_id: this.medicamentoSeleccionado,
            cantidad: this.cantidad,
          };
  
          try {
            const medicamento = this.medicamentos.find(
              (m) => m.ID === this.medicamentoSeleccionado
            );
  
            await axios.post("/detalle-entrada", detalle);
  
            this.detalles.push({
              nombre: medicamento.Nombre,
              cantidad: this.cantidad,
            });
  
            this.medicamentoSeleccionado = null;
            this.cantidad = 1;
  
            alert("Detalle agregado con éxito.");
          } catch (error) {
            alert("Error al agregar el detalle.");
          }
        }
      },
  
      // Finalizar compra
      finalizarCompra() {
        this.laboratorioSeleccionado = null;
        this.fechaEntrega = "";
        this.total = 0;
        this.entradaID = null;
        this.entradaGuardada = false;
        this.medicamentoSeleccionado = null;
        this.cantidad = 1;
        this.detalles = [];
  
        alert("Compra completada con éxito. Puede registrar otra compra.");
      },
  
      // Abrir modal de laboratorio
      openModalLaboratorio() {
        this.modalLaboratorioAbierto = true;
        this.editingLaboratorio = false;
        this.currentLaboratorio = {}; // Resetear laboratorio actual
      },
  
      // Cerrar modal de laboratorio
      closeModalLaboratorio() {
        this.modalLaboratorioAbierto = false;
      },
  
      // Agregar laboratorio
      async addLaboratorio(laboratorio) {
        try {
          const response = await axios.post("/laboratorios", laboratorio);
          this.laboratorios.push(response.data);
          this.closeModalLaboratorio();
        } catch (error) {
          alert("Error al agregar laboratorio.");
        }
      },
  
      // Actualizar laboratorio
      async updateLaboratorio(laboratorio) {
        try {
          const response = await axios.put(`/laboratorios/${laboratorio.ID}`, laboratorio);
          const index = this.laboratorios.findIndex(l => l.ID === response.data.ID);
          if (index !== -1) {
            this.laboratorios.splice(index, 1, response.data);
          }
          this.closeModalLaboratorio();
        } catch (error) {
          alert("Error al actualizar laboratorio.");
        }
      },
    },
    mounted() {
      this.cargarDatos();
  
      // Configurar Flatpickr para el selector de fecha
      flatpickr("#datepicker", {
        dateFormat: "Y-m-d",
        minDate: "today",
        onChange: (selectedDates, dateStr) => {
          this.fechaEntrega = dateStr;
        },
      });
    },
  };
  </script>
  
  <style scoped>
  /* Estilos para el formulario y otros componentes */
  .container {
    max-width: 1000px;
    margin: 20px auto;
    padding: 20px;
    background-color: #f9f9f9;
    border-radius: 10px;
    box-shadow: 0px 4px 12px rgba(0, 0, 0, 0.1);
    font-family: Arial, sans-serif;
  }
  
  .title {
    font-size: 28px;
    font-weight: bold;
    text-align: center;
    margin-bottom: 20px;
    color: #2c3e50;
  }
  
  .form-group {
    margin-bottom: 15px;
  }
  
  .select-field,
  .input-field {
    width: 80%;
    padding: 8px;
    border: 1px solid #ddd;
    border-radius: 5px;
    font-size: 14px;
  }
  
  .submit-button,
  .add-button,
  .finalizar-button {
    background-color: #4caf50;
    color: white;
    padding: 10px 15px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-size: 16px;
    margin-top: 10px;
  }
  
  .submit-button:hover,
  .add-button:hover,
  .finalizar-button:hover {
    background-color: #45a049;
  }
  
  .finalizar-button {
    margin-top: 20px;
    width: 100%;
  }
  
  h4 {
    margin-top: 20px;
    color: #34495e;
  }
  </style>
  