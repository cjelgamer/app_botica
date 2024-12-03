<template>
  <div class="container">
    <h2 class="title">Realizar Compra</h2>

    <form @submit.prevent="guardarEntradaYDetalles">
      <div class="form-group">
        <label for="laboratorio">Laboratorio:</label>
        <div class="select-container">
          <input
            v-if="!laboratorioSeleccionado"
            type="text"
            v-model="searchLaboratorio"
            class="input-field"
            placeholder="Buscar laboratorio..."
            @keydown.enter="enviarEntrada"
          />
          <button
            v-if="!laboratorioSeleccionado"
            type="button"
            class="add-button"
            @click="openModalLaboratorio"
          >
            <span class="add-icon">+</span> Agregar
          </button>

          <button
            v-if="laboratorioSeleccionado"
            type="button"
            class="remove-button"
            @click="eliminarLaboratorio"
          >
            <span class="fa fa-trash-alt" style="color: red;"></span> Eliminar
          </button>
        </div>

        <div class="laboratorios-list" v-if="!laboratorioSeleccionado && filtradosLaboratorios.length > 0">
          <div
            v-for="laboratorio in filtradosLaboratorios"
            :key="laboratorio.ID"
            class="laboratorio-item"
            @click="seleccionarLaboratorio(laboratorio)"
          >
            {{ laboratorio.Nombre }} (ID: {{ laboratorio.ID }})
          </div>
        </div>

        <div v-if="laboratorioSeleccionado" class="form-group">
          <p><strong>Laboratorio Seleccionado:</strong> {{ laboratorioSeleccionado.Nombre }}</p>
        </div>
      </div>

      <div class="form-group">
        <label for="fecha_entrega">Fecha de Entrega:</label>
        <input
          type="text"
          id="datepicker"
          v-model="fechaEntrega"
          class="input-field"
          placeholder="Seleccione una fecha"
          required
          @keydown.enter="enviarEntrada"
        />
      </div>

      <div class="form-group">
        <label for="total">Total:</label>
        <input
          type="number"
          v-model="total"
          class="input-field"
          min="0"
          placeholder="Ingrese el total"
          required
          @keydown.enter="enviarEntrada"
        />
      </div>

      <div v-if="entradaGuardada">
        <h3>Agregar Detalles</h3>

        <div class="form-group">
          <label for="medicamento">Medicamento:</label>
          <div class="select-container">
            <input
              v-if="!medicamentoSeleccionado"
              type="text"
              v-model="searchMedicamento"
              class="input-field"
              placeholder="Buscar medicamento..."
            />
          </div>

          <div class="medicamentos-list" v-if="!medicamentoSeleccionado && filtradosMedicamentos.length > 0">
            <div
              v-for="medicamento in filtradosMedicamentos"
              :key="medicamento.ID"
              class="medicamento-item"
              @click="seleccionarMedicamento(medicamento)"
            >
              {{ medicamento.Nombre }} (Stock: {{ medicamento.Stock }})
            </div>
          </div>

          <div v-if="medicamentoSeleccionado" class="form-group">
            <p><strong>Medicamento Seleccionado:</strong> {{ medicamentoSeleccionado.Nombre }}</p>
          </div>
        </div>

        <div class="form-group" v-if="medicamentoSeleccionado">
          <label for="cantidad">Cantidad:</label>
          <input
            type="number"
            v-model="cantidad"
            class="input-field"
            min="1"
            :max="medicamentoSeleccionado.Stock"
            placeholder="Cantidad"
            required
          />
        </div>

        <button
          type="button"
          @click="manejarCompra"
          class="submit-button"
          :disabled="!medicamentoSeleccionado"
        >
          {{ detalles.length === 0 ? 'Agregar Detalle' : 'Finalizar Compra' }}
        </button>
      </div>
    </form>

    <FormularioLaboratorio
      v-if="modalLaboratorioAbierto"
      :laboratorio="currentLaboratorio"
      :editing="editingLaboratorio"
      @add-laboratorio="addLaboratorio"
      @update-laboratorio="updateLaboratorio"
      @close="closeModalLaboratorio"
    />
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
      modalLaboratorioAbierto: false,
      currentLaboratorio: {},
      editingLaboratorio: false,
      searchLaboratorio: "",
      searchMedicamento: "",
    };
  },
  computed: {
    filtradosLaboratorios() {
      return this.laboratorios.filter(laboratorio =>
        laboratorio.Nombre.toLowerCase().includes(this.searchLaboratorio.toLowerCase())
      );
    },
    filtradosMedicamentos() {
      return this.medicamentos.filter(medicamento =>
        medicamento.Nombre.toLowerCase().includes(this.searchMedicamento.toLowerCase())
      );
    }
  },
  methods: {
    async cargarDatos() {
      try {
        const resLaboratorios = await axios.get("/laboratorios");
        this.laboratorios = resLaboratorios.data;

        const resMedicamentos = await axios.get("/medicamentos");
        this.medicamentos = resMedicamentos.data;
      } catch (error) {
        console.error("Error al cargar datos:", error.response?.data || error.message);
      }
    },

    async enviarEntrada() {
      if (!this.entradaGuardada) {
        const entrada = {
          laboratorio_id: this.laboratorioSeleccionado.ID,
          fecha_entrega: this.fechaEntrega,
          total: this.total,
        };

        try {
          const response = await axios.post("/entrada", entrada);
          if (response.data.id) {
            this.entradaID = response.data.id;
            this.entradaGuardada = true;
          }
        } catch (error) {
          console.error("Error al registrar la entrada:", error);
        }
      }
    },

    async manejarCompra() {
      if (this.detalles.length === 0) {
        await this.agregarDetalle();
      }

      await this.finalizarCompra();
    },

    async agregarDetalle() {
      if (this.medicamentoSeleccionado && this.cantidad && this.entradaID) {
        const detalle = {
          entrada_id: this.entradaID,
          medicamento_id: this.medicamentoSeleccionado.ID,
          cantidad: this.cantidad,
        };

        try {
          await axios.post("/detalle-entrada", detalle);
          
          this.detalles.push({
            nombre: this.medicamentoSeleccionado.Nombre,
            cantidad: this.cantidad,
          });

          this.medicamentoSeleccionado = null;
          this.cantidad = 1;
          this.searchMedicamento = '';

          if (this.detalles.length > 0) {
            await this.finalizarCompra();
          }
        } catch (error) {
          console.error("Error al agregar el detalle:", error);
        }
      }
    },

    async finalizarCompra() {
      try {
        this.laboratorioSeleccionado = null;
        this.fechaEntrega = "";
        this.total = 0;
        this.entradaID = null;
        this.entradaGuardada = false;
        this.medicamentoSeleccionado = null;
        this.cantidad = 1;
        this.detalles = [];
        this.searchLaboratorio = '';
        this.searchMedicamento = '';
      } catch (error) {
        console.error("Error al finalizar la compra:", error);
      }
    },

    seleccionarLaboratorio(laboratorio) {
      this.laboratorioSeleccionado = laboratorio;
      this.searchLaboratorio = '';
    },

    seleccionarMedicamento(medicamento) {
      this.medicamentoSeleccionado = medicamento;
      this.searchMedicamento = '';
    },

    openModalLaboratorio() {
      this.modalLaboratorioAbierto = true;
      this.editingLaboratorio = false;
      this.currentLaboratorio = {};
    },

    closeModalLaboratorio() {
      this.modalLaboratorioAbierto = false;
    },

    async addLaboratorio(laboratorio) {
      try {
        const response = await axios.post("/laboratorios", laboratorio);
        this.laboratorios.push(response.data);
        this.closeModalLaboratorio();
      } catch (error) {
        console.error("Error al agregar laboratorio:", error);
      }
    },

    async updateLaboratorio(laboratorio) {
      try {
        const response = await axios.put(`/laboratorios/${laboratorio.ID}`, laboratorio);
        const index = this.laboratorios.findIndex(l => l.ID === laboratorio.ID);
        if (index !== -1) {
          this.laboratorios.splice(index, 1, response.data);
        }
        this.closeModalLaboratorio();
      } catch (error) {
        console.error("Error al actualizar laboratorio:", error);
      }
    },

    eliminarLaboratorio() {
      if (this.laboratorioSeleccionado) {
        this.laboratorioSeleccionado = null;
        this.searchLaboratorio = "";
      }
    },
  },
  mounted() {
    this.cargarDatos();
    flatpickr("#datepicker", { dateFormat: "Y-m-d" });
  },
};
</script>

<style scoped>
/* Estilos de la interfaz */
.container {
  overflow-x: hidden; /* Prevenir desbordamiento horizontal */
  max-width: 100%;/* Changed from max-width to full width */
  margin: 0 auto;
  padding: 10px; /* Further reduced padding */
  background-color: #f4f4f4;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
  font-family: 'Arial', sans-serif;
}

.title {
  text-align: center;
  color: #333;
  margin-bottom: 20px; /* Reducido */
  font-size: 20px; /* Reducido de 24px */
  font-weight: bold;
}

.form-group {
  margin-bottom: 15px; /* Reducido */
  background-color: white;
  padding: 10px; /* Reducido de 15px */
  border-radius: 6px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
}


.input-field,
.select-field {
  width: 100%;
  padding: 5px; /* Reduced padding */
  margin-top: 3px; /* Further reduced margin */
  border-radius: 5px;
  border: 1px solid #ddd;
  transition: border-color 0.3s ease;
  font-size: 12px; /* Smaller font size */
  height: 30px; /* Added fixed height to make inputs shorter */
}

.input-field:focus,
.select-field:focus {
  outline: none;
  border-color: #4CAF50;
  box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.1);
}

.select-container {
  display: flex;
  align-items: center;
  gap: 10px;
}

.input-field {
  flex: 1;
}

.add-button {
  padding: 6px 10px; /* Reducido */
  background-color: #4CAF50;
  color: white;
  border: none;
  border-radius: 5px;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: background-color 0.3s ease;
  font-size: 14px; /* Tamaño de letra más pequeño */
}

.add-button:hover {
  background-color: #45a049;
}

.add-icon {
  font-size: 18px;
  margin-right: 8px;
}

.laboratorios-list,
.medicamentos-list {
  max-height: 150px; /* Reducir la altura máxima */
  overflow-y: auto;
  margin-top: 10px; /* Reducir margen superior */
  border: 1px solid #e0e0e0;
  border-radius: 5px;
  padding: 5px; /* Reducir padding */
  background-color: #fafafa;
  font-size: 13px; /* Reducir tamaño de fuente global para la lista */
}

.laboratorio-item,
.medicamento-item {
  padding: 6px; /* Reducir padding */
  background-color: white;
  margin-bottom: 5px; /* Reducir espacio entre elementos */
  border-radius: 4px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
  display: flex;
  justify-content: space-between;
  align-items: center;
  transition: background-color 0.3s ease, transform 0.2s ease;
}

.laboratorio-item:hover,
.medicamento-item:hover {
  background-color: #f0f0f0;
  transform: translateX(5px);
}

.submit-button {
  width: 100%;
  padding: 10px; /* Reducido de 15px */
  background-color: #4CAF50;
  color: white;
  border: none;
  border-radius: 6px;
  font-size: 14px; /* Reducido de 16px */
  cursor: pointer;
  transition: background-color 0.3s ease, transform 0.2s ease;
}

.submit-button:hover {
  background-color: #4CAF50;
  transform: translateY(-2px);
}

.finalizar-button {
  margin-top: 20px;
}

select {
  width: 100%;
  padding: 5px; /* Reduced padding */
  border-radius: 5px;
  border: 1px solid #ddd;
  appearance: none;
  font-size: 12px; /* Smaller font size */
  height: 30px; /* Added fixed height to make select inputs shorter */
  background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%23999' d='M10.3 3.3L6 7.6 1.7 3.3c-.4-.4-1-.4-1.4 0s-.4 1 0 1.4l5 5c.4.4 1 .4 1.4 0l5-5c.4-.4.4-1 0-1.4s-1-.4-1.4 0z'/%3E%3C/svg%3E");
  background-repeat: no-repeat;
  background-position: right 10px center;
}

select:disabled {
  background-color: #e9ecef;
  cursor: not-allowed;
  color: #6c757d;
}

input[type="number"] {
  width: 100%;
  padding: 5px; /* Reduced padding */
  margin-top: 3px; /* Further reduced margin */
  border-radius: 5px;
  border: 1px solid #ddd;
  font-size: 12px; /* Smaller font size */
  height: 30px; /* Added fixed height to make number inputs shorter */
}

/* Scroll bar styling for list containers */
.laboratorios-list::-webkit-scrollbar,
.medicamentos-list::-webkit-scrollbar {
  width: 8px;
}

.laboratorios-list::-webkit-scrollbar-track,
.medicamentos-list::-webkit-scrollbar-track {
  background: #f1f1f1;
}

.laboratorios-list::-webkit-scrollbar-thumb,
.medicamentos-list::-webkit-scrollbar-thumb {
  background: #888;
  border-radius: 4px;
}

.laboratorios-list::-webkit-scrollbar-thumb:hover,
.medicamentos-list::-webkit-scrollbar-thumb:hover {
  background: #555;
}
</style>
