<template>
  <div class="container">
    <h2 class="title">Realizar Compra</h2>

    <form @submit.prevent="guardarEntradaYDetalles">
      <!-- Laboratorio section -->
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

      <!-- Date and Total fields -->
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

      <!-- Details section -->
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
            <button
              v-if="!medicamentoSeleccionado"
              type="button"
              class="add-button"
              @click="openModalMedicamento"
            >
              <span class="add-icon">+</span> Agregar
            </button>
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
          <div class="cantidad-container">
            <input
              type="number"
              v-model="cantidad"
              class="input-field"
              min="1"
              :max="medicamentoSeleccionado.Stock"
              placeholder="Cantidad"
              required
              @keydown.enter.prevent="agregarALista"
            />
            <button 
              type="button" 
              class="add-to-list-button"
              @click.prevent="agregarALista"
              @keydown.enter.prevent
            >
              Agregar a lista
            </button>
          </div>
        </div>

        <!-- Lista de medicamentos temporal -->
        <div v-if="medicamentosTemporal.length > 0" class="lista-medicamentos">
          <h4>Medicamentos a agregar:</h4>
          <div class="medicamentos-table">
            <table>
              <thead>
                <tr>
                  <th>N°</th>
                  <th>Medicamento</th>
                  <th>Cantidad</th>
                  <th>Acciones</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="(med, index) in medicamentosTemporal" :key="index">
                  <td>{{ index + 1 }}</td>
                  <td>{{ med.nombre }}</td>
                  <td>{{ med.cantidad }}</td>
                  <td>
                    <button @click="eliminarDeLista(index)" class="btn-eliminar">
                      <i class="fas fa-trash"></i>
                    </button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <button
          type="button"
          @click="manejarCompra"
          class="submit-button"
          :disabled="!medicamentoSeleccionado && medicamentosTemporal.length === 0"
        >
          {{ detalles.length === 0 ? 'Agregar Detalle' : 'Finalizar Compra' }}
        </button>
      </div>
    </form>

    <!-- Laboratory Modal -->
    <FormularioLaboratorio
      v-if="modalLaboratorioAbierto"
      :laboratorio="currentLaboratorio"
      :editing="editingLaboratorio"
      @add-laboratorio="addLaboratorio"
      @update-laboratorio="updateLaboratorio"
      @close="closeModalLaboratorio"
    />

    <!-- Medication Modal -->
    <FormularioMedicamento
      v-if="modalMedicamentoAbierto"
      :isOpen="modalMedicamentoAbierto"
      @add-medicamento="addMedicamento"
      @close-modal="closeModalMedicamento"
    />
  </div>
</template>

<script>
import axios from "axios";
import flatpickr from "flatpickr";
import "flatpickr/dist/flatpickr.min.css";
import FormularioLaboratorio from './FormularioLaboratorio.vue';
import FormularioMedicamento from './FormularioMedicamento.vue';

export default {
  components: {
    FormularioLaboratorio,
    FormularioMedicamento,
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
      modalMedicamentoAbierto: false,
      currentLaboratorio: {},
      editingLaboratorio: false,
      searchLaboratorio: "",
      searchMedicamento: "",
      medicamentosTemporal: [], // Nueva propiedad para la lista temporal
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

    // Nuevos métodos para manejar la lista temporal
    agregarALista(event) {
  if (event) {
    event.preventDefault();
    event.stopPropagation(); // Detener la propagación del evento
  }
  
  if (!this.medicamentoSeleccionado || !this.cantidad) {
    alert('Por favor seleccione un medicamento y especifique la cantidad');
    return;
  }

  const medExistente = this.medicamentosTemporal.find(
    m => m.id === this.medicamentoSeleccionado.ID
  );

  if (medExistente) {
    medExistente.cantidad += parseInt(this.cantidad);
  } else {
    this.medicamentosTemporal.push({
      id: this.medicamentoSeleccionado.ID,
      nombre: this.medicamentoSeleccionado.Nombre,
      cantidad: parseInt(this.cantidad)
    });
  }

  // Limpiar la selección
  this.medicamentoSeleccionado = null;
  this.cantidad = 1;
  this.searchMedicamento = '';
},
    eliminarDeLista(index) {
      this.medicamentosTemporal.splice(index, 1);
    },

    // Método modificado para manejar múltiples medicamentos
    async agregarDetalle() {
      if (this.medicamentosTemporal.length === 0) {
        alert('Agregue al menos un medicamento a la lista');
        return;
      }

      try {
        // Agregar todos los medicamentos de la lista temporal
        for (const med of this.medicamentosTemporal) {
          const detalle = {
            entrada_id: this.entradaID,
            medicamento_id: med.id,
            cantidad: med.cantidad,
          };

          await axios.post("/detalle-entrada", detalle);
          this.detalles.push({
            nombre: med.nombre,
            cantidad: med.cantidad,
          });
        }

        // Limpiar la lista temporal
        this.medicamentosTemporal = [];
        await this.finalizarCompra();
      } catch (error) {
        console.error("Error al agregar los detalles:", error);
      }
    },

    // Métodos existentes sin cambios
    openModalMedicamento() {
      this.modalMedicamentoAbierto = true;
    },

    closeModalMedicamento() {
      this.modalMedicamentoAbierto = false;
    },

    async addMedicamento(medicamento) {
      try {
        const response = await axios.post("/medicamentos", medicamento);
        this.medicamentos.push(response.data);
        this.closeModalMedicamento();
        this.seleccionarMedicamento(response.data);
      } catch (error) {
        console.error("Error al agregar medicamento:", error);
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
        this.medicamentosTemporal = []; // Limpiar también la lista temporal
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
        this.seleccionarLaboratorio(response.data);
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
    }
  },

  beforeRouteLeave(to, from, next) {
    if (this.entradaGuardada && this.detalles.length === 0) {
      alert('Debe agregar los detalles antes de salir.');
      next(false);
    } else {
      next();
    }
  },

  mounted() {
    this.cargarDatos();
    flatpickr("#datepicker", { dateFormat: "Y-m-d" });
    window.addEventListener('beforeunload', this.prevenirSalida);
  },

  beforeDestroy() {
    window.removeEventListener('beforeunload', this.prevenirSalida);
  },

  prevenirSalida(event) {
    if (this.entradaGuardada && this.detalles.length === 0) {
      event.preventDefault();
      event.returnValue = '';
      return event.returnValue;
    }
  }
};
</script>

<style scoped>
.container {
  overflow-x: hidden;
  max-width: 100%;
  margin: 0 auto;
  padding: 10px;
  background-color: #f4f4f4;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
  font-family: 'Arial', sans-serif;
}

.title {
  text-align: center;
  color: #333;
  margin-bottom: 20px;
  font-size: 20px;
  font-weight: bold;
}

.form-group {
  margin-bottom: 15px;
  background-color: white;
  padding: 10px;
  border-radius: 6px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
}

.input-field,
.select-field {
  width: 100%;
  padding: 5px;
  margin-top: 3px;
  border-radius: 5px;
  border: 1px solid #ddd;
  transition: border-color 0.3s ease;
  font-size: 12px;
  height: 30px;
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
  display: inline-block;
  transition: all 0.2s ease-in;
  position: relative;
  overflow: hidden;
  z-index: 0;
  color: #090909;
  padding: 0.2em 0.5em;
  cursor: pointer;
  font-size: 13px;
  border-radius: 0.5em;
  background: #c8ead5;
  border: 1px solid #e8e8e8;
  box-shadow: 6px 6px 12px #ffffff, -6px -6px 12px #ffffff;
}

.add-button:active {
  color: #ffffff;
  box-shadow: inset 4px 4px 12px #ffffff, inset -4px -4px 12px #ffffff;
}

.add-button:hover {
  color: #ffffff;
  border: 1px solid #45a049;
  background-color: #45a049;
}

.add-icon {
  font-size: 18px;
  margin-right: 8px;
}

/* Nueva clase para el contenedor de cantidad */
.cantidad-container {
  display: flex;
  align-items: center;
  gap: 10px;
  margin-top: 5px;
}

.cantidad-container .input-field {
  width: 120px;
  flex: none;
}

/* Botón para agregar a la lista */
.add-to-list-button {
  padding: 6px 12px;
  background-color: #2196F3;
  color: white;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  transition: background-color 0.2s;
  font-size: 12px;
  height: 30px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.add-to-list-button:hover {
  background-color: #1976D2;
}

/* Estilos para la lista de medicamentos */
.lista-medicamentos {
  margin-top: 15px;
  padding: 10px;
  background-color: white;
  border-radius: 6px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
}

.lista-medicamentos h4 {
  margin: 0 0 10px 0;
  color: #333;
  font-size: 14px;
}

.medicamentos-table {
  width: 100%;
  overflow-x: auto;
}

.medicamentos-table table {
  width: 100%;
  border-collapse: collapse;
  font-size: 13px;
}

.medicamentos-table th,
.medicamentos-table td {
  padding: 8px;
  text-align: left;
  border-bottom: 1px solid #eee;
}

.medicamentos-table th {
  background-color: #f8f9fa;
  font-weight: 600;
  color: #333;
}

.medicamentos-table tbody tr:hover {
  background-color: #f8f9fa;
}

/* Botón eliminar en la tabla */
.btn-eliminar {
  padding: 4px 8px;
  background: none;
  border: none;
  color: #dc3545;
  cursor: pointer;
  transition: color 0.2s;
}

.btn-eliminar:hover {
  color: #c82333;
}

.laboratorios-list,
.medicamentos-list {
  max-height: 150px;
  overflow-y: auto;
  margin-top: 10px;
  border: 1px solid #e0e0e0;
  border-radius: 5px;
  padding: 5px;
  background-color: #fafafa;
  z-index: 10;
  font-size: 13px;
}

.laboratorio-item,
.medicamento-item {
  padding: 6px;
  background-color: white;
  margin-bottom: 5px;
  border-radius: 4px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
  display: flex;
  justify-content: space-between;
  align-items: center;
  transition: background-color 0.3s ease, transform 0.2s ease;
  cursor: pointer;
}

.laboratorio-item:hover,
.medicamento-item:hover {
  background-color: #f0f0f0;
  transform: translateX(5px);
}

.submit-button {
  width: 100%;
  padding: 10px;
  background-color: #4CAF50;
  color: white;
  border: none;
  border-radius: 6px;
  font-size: 14px;
  cursor: pointer;
  transition: background-color 0.3s ease, transform 0.2s ease;
  margin-top: 15px;
}

.submit-button:hover {
  background-color: #45a049;
  transform: translateY(-2px);
}

.submit-button:disabled {
  background-color: #cccccc;
  cursor: not-allowed;
  transform: none;
}

select {
  width: 100%;
  padding: 5px;
  border-radius: 5px;
  border: 1px solid #ddd;
  appearance: none;
  font-size: 12px;
  height: 30px;
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
  padding: 5px;
  margin-top: 3px;
  border-radius: 5px;
  border: 1px solid #ddd;
  font-size: 12px;
  height: 30px;
}

/* Barra de desplazamiento */
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

/* Estilos responsivos */
@media (max-width: 768px) {
  .container {
    padding: 5px;
  }
  
  .select-container {
    flex-direction: column;
  }
  
  .cantidad-container {
    flex-direction: column;
    align-items: stretch;
  }
  
  .cantidad-container .input-field {
    width: 100%;
  }
  
  .add-to-list-button {
    margin-top: 5px;
  }
}
</style>