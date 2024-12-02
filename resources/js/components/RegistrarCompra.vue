<template>
  <div class="container">
    <h2 class="title">Realizar Compra</h2>

    <!-- Formulario único para entrada y detalles -->
    <form @submit.prevent="guardarEntradaYDetalles">

      <!-- Sección de datos de la entrada -->
      <div class="form-group">
        <label for="laboratorio">Laboratorio:</label>
        <div class="select-container">
          <!-- Campo de búsqueda de laboratorio -->
          <input
            v-if="!laboratorioSeleccionado"
            type="text"
            v-model="searchLaboratorio"
            class="input-field"
            placeholder="Buscar laboratorio..."
            @keydown.enter="enviarEntrada"
          />
          <button
            type="button"
            class="add-button"
            @click="openModalLaboratorio"
          >
            <span class="add-icon">+</span> Agregar
          </button>

          <!-- Botón de eliminar, solo visible cuando un laboratorio está seleccionado -->
          <button
            v-if="laboratorioSeleccionado"
            type="button"
            class="remove-button"
            @click="eliminarLaboratorio"
          >
            <span class="remove-icon">×</span> Eliminar
          </button>
        </div>

        <!-- Contenedor para la lista de laboratorios filtrados -->
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

        <!-- Mostrar laboratorio seleccionado -->
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

      <!-- Sección de detalles de la compra (solo visible después de guardar la entrada) -->
      <div v-if="entradaGuardada">
        <h3>Agregar Detalles</h3>

        <div class="form-group">
          <label for="medicamento">Medicamento:</label>
          <select
            v-model="medicamentoSeleccionado"
            class="select-field"
            required
            @keydown.enter="agregarDetalle"
          >
            <option value="" disabled>Seleccione un medicamento</option>
            <option
              v-for="medicamento in medicamentos"
              :value="medicamento.ID"
              :key="medicamento.ID"
            >
              {{ medicamento.Nombre }} (Stock: {{ medicamento.Stock }})
            </option>
          </select>
        </div>

        <div class="form-group">
          <label for="cantidad">Cantidad:</label>
          <input
            type="number"
            v-model="cantidad"
            class="input-field"
            min="1"
            placeholder="Cantidad"
            required
            @keydown.enter="agregarDetalle"
          />
        </div>

        <button type="button" class="add-button" @click="agregarDetalle">
          Agregar Detalle
        </button>

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
        <button
          type="button"
          @click="finalizarCompra"
          class="submit-button finalizar-button"
        >
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
      searchLaboratorio: "", // Filtro para búsqueda
    };
  },
  computed: {
    // Filtro de laboratorios para búsqueda
    filtradosLaboratorios() {
      return this.laboratorios.filter(laboratorio =>
        laboratorio.Nombre.toLowerCase().includes(this.searchLaboratorio.toLowerCase())
      );
    }
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
          laboratorio_id: this.laboratorioSeleccionado.ID,
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

    // Seleccionar laboratorio de la lista
    seleccionarLaboratorio(laboratorio) {
      this.laboratorioSeleccionado = laboratorio;
      this.searchLaboratorio = ''; // Limpiar campo de búsqueda
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
        alert("Laboratorio agregado con éxito.");
      } catch (error) {
        alert("Error al agregar laboratorio.");
      }
    },

    // Editar laboratorio
    async updateLaboratorio(laboratorio) {
      try {
        const response = await axios.put(`/laboratorios/${laboratorio.ID}`, laboratorio);
        const index = this.laboratorios.findIndex(l => l.ID === laboratorio.ID);
        if (index !== -1) {
          this.laboratorios.splice(index, 1, response.data);
        }
        this.closeModalLaboratorio();
        alert("Laboratorio actualizado con éxito.");
      } catch (error) {
        alert("Error al actualizar laboratorio.");
      }
    },

    // Eliminar laboratorio seleccionado
    eliminarLaboratorio() {
      if (this.laboratorioSeleccionado) {
        this.laboratorioSeleccionado = null; // Eliminar la selección del laboratorio
        this.searchLaboratorio = ""; // Restablecer el campo de búsqueda
        alert("Laboratorio eliminado.");
      } else {
        alert("No hay laboratorio seleccionado para eliminar.");
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
  max-width: 800px;
  margin: 0 auto;
  padding: 20px;
}

.title {
  text-align: center;
}

.form-group {
  margin-bottom: 15px;
}

.input-field,
.select-field {
  width: 100%;
  padding: 10px;
  margin-top: 5px;
  border-radius: 4px;
  border: 1px solid #ccc;
}

.add-button {
  margin-left: 10px;
  padding: 10px;
  background-color: #28a745;
  color: white;
  border: none;
  border-radius: 4px;
  cursor: pointer;
}

.add-icon {
  font-size: 18px;
  margin-right: 5px;
}

.laboratorios-list {
  max-height: 150px;
  overflow-y: auto;
  margin-top: 10px;
}

.laboratorio-item {
  padding: 5px;
  background-color: #f8f8f8;
  margin-bottom: 5px;
  cursor: pointer;
}

.laboratorio-item:hover {
  background-color: #eaeaea;
}

.submit-button {
  width: 100%;
  padding: 15px;
  background-color: #007bff;
  color: white;
  border: none;
  border-radius: 4px;
  font-size: 16px;
  cursor: pointer;
}

.finalizar-button {
  margin-top: 20px;
}

select {
  width: 100%;
  padding: 10px;
}

input[type="number"] {
  width: 100%;
  padding: 10px;
  margin-top: 5px;
  border-radius: 4px;
  border: 1px solid #ccc;
}

select:disabled {
  background-color: #e0e0e0;
  cursor: not-allowed;
}


.select-container {
  display: flex; /* Utiliza flexbox para alinearlos en línea */
  align-items: center; /* Alinea los elementos verticalmente en el centro */
}

.input-field {
  flex: 1; /* Hace que el input ocupe el espacio disponible */
  padding: 8px;
  margin-right: 8px; /* Agrega un margen entre el input y el botón */
}

.add-button {
  padding: 8px 12px;
  background-color: #4CAF50; /* Color de fondo para el botón */
  color: white;
  border: none;
  cursor: pointer;
  border-radius: 4px;
}

.add-button:hover {
  background-color: #45a049; /* Color de fondo en hover */
}
</style>
