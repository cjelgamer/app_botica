<template>
  <div class="form-container">
    <!-- Búsqueda de Medicamento -->
    <div class="search-container">
      <input
        v-model="searchQuery"
        type="text"
        placeholder="Buscar Medicamento"
        @input="searchMedicamentos"
        class="search-input"
      />
    </div>

    <!-- Medicamentos -->
    <div class="medicamentos-container">
      <table class="medicamentos-table">
        <thead>
          <tr>
            <th>N°</th>
            <th>Producto</th>
            <th>Cantidad Disponible</th>
            <th>Cantidad</th>
            <th>Precio C/U</th>
            <th>Precio Total</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="(medicamento, index) in medicamentos" :key="medicamento.id">
            <td>{{ index + 1 }}</td>
            <td>{{ medicamento.Nombre }}</td>
            <td>{{ medicamento.Stock }}</td>
            <td>
              <button @click="updateCantidad(medicamento, -1)" class="quantity-btn">-</button>
              <input
                v-model="medicamento.cantidad"
                type="number"
                :max="medicamento.Stock"
                min="1"
                @blur="updateTotal"
                class="quantity-input"
              />
              <button @click="updateCantidad(medicamento, 1)" class="quantity-btn">+</button>
            </td>
            <td>{{ medicamento.Precio }}</td>
            <td>{{ medicamento.Precio * medicamento.cantidad }}</td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Información del Cliente -->
    <div class="cliente-container">
      <div class="input-group">
        <label for="nombreCliente">Nombres:</label>
        <input v-model="nombreCliente" type="text" id="nombreCliente" class="input-field" />
      </div>
      <div class="input-group">
        <label for="apellidoCliente">Apellidos:</label>
        <input v-model="apellidoCliente" type="text" id="apellidoCliente" class="input-field" />
      </div>
      <div class="input-group">
        <label for="dniCliente">DNI:</label>
        <input v-model="dniCliente" type="text" id="dniCliente" class="input-field" />
      </div>
    </div>

    <!-- Botones de Acción -->
    <div class="buttons-container">
      <button @click="aceptarVenta" class="action-btn accept-btn">Aceptar Venta</button>
      <button @click="cancelarVenta" class="action-btn cancel-btn">Cancelar Venta</button>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  data() {
    return {
      medicamentos: [], // Lista de medicamentos
      searchQuery: "", // Término de búsqueda
      nombreCliente: "", // Nombre del cliente
      apellidoCliente: "", // Apellido del cliente
      dniCliente: "", // DNI del cliente
    };
  },
  methods: {
    // Función para buscar medicamentos
    async searchMedicamentos() {
      try {
        if (this.searchQuery.length > 2) {
          const response = await axios.get('/medicamentos', {
            params: {
              search: this.searchQuery
            }
          });
          this.medicamentos = response.data.map(medicamento => ({
            ...medicamento,
            cantidad: 1, // Inicializa la cantidad en 1
          }));
        }
      } catch (error) {
        console.error('Error al buscar medicamentos:', error);
      }
    },

    // Función para actualizar la cantidad de un medicamento
    updateCantidad(medicamento, delta) {
      if (medicamento.cantidad + delta <= medicamento.Stock && medicamento.cantidad + delta >= 1) {
        medicamento.cantidad += delta;
      }
    },

    // Función para calcular el total
    updateTotal() {
      // Se puede agregar aquí lógica para recalcular el total de la venta si es necesario.
    },

    // Función para aceptar la venta
    async aceptarVenta() {
      try {
        const response = await axios.post('/venta', {
          medicamentos: this.medicamentos,
          cliente: {
            nombre: this.nombreCliente,
            apellido: this.apellidoCliente,
            dni: this.dniCliente
          }
        });
        alert(response.data.message);
        this.cancelarVenta(); // Limpiar después de aceptar la venta
      } catch (error) {
        console.error('Error al aceptar la venta:', error);
      }
    },

    // Función para cancelar la venta
    async cancelarVenta() {
      try {
        const response = await axios.post('/cancelar-venta');
        this.medicamentos = [];
        this.nombreCliente = '';
        this.apellidoCliente = '';
        this.dniCliente = '';
        alert(response.data.message);
      } catch (error) {
        console.error('Error al cancelar la venta:', error);
      }
    }
  }
};
</script>

<style scoped>
/* Contenedor principal */
.form-container {
  width: 100%;
  margin: 20px auto;
  padding: 20px;
  border-radius: 8px;
  background-color: #f9f9f9;
  box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
}

/* Estilo para el campo de búsqueda */
.search-container {
  margin-bottom: 20px;
  border: #e53935;
}

.search-input {
  width: 100%;
  padding: 10px;
  font-size: 16px;
  border: 1px solid #ccc;
  border-radius: 8px;
  margin-top: 5px;
}

/* Tabla de medicamentos 
.medicamentos-container {
  margin-top: 20px;
}
*/
.medicamentos-container {
  width: 100%;
  margin-top: 20px;
  display: block; /* Asegúrate de que sea visible */
  border: #4CAF50;
}
.medicamentos-table {
  width: 100%;
  border-collapse: collapse;
  margin-bottom: 20px;
  border-radius: 8px;
  overflow: hidden;
}

.medicamentos-table th, .medicamentos-table td {
  padding: 10px;
  text-align: center;
  border-bottom: 1px solid #ddd;
}

.medicamentos-table th {
  background-color: #333;
  color: white;
}

.medicamentos-table tr:nth-child(even) {
  background-color: #f2f2f2;
}

.quantity-btn {
  background-color: #f1f1f1;
  border: 1px solid #ddd;
  padding: 5px 10px;
  cursor: pointer;
  border-radius: 5px;
}

.quantity-btn:hover {
  background-color: #ddd;
}

.quantity-input {
  width: 50px;
  padding: 5px;
  text-align: center;
  border: 1px solid #ccc;
  border-radius: 5px;
}

/* Estilos para la información del cliente */
.cliente-container {
  margin-top: 20px;
  display: flex;
  flex-direction: column;
}

.input-group {
  margin-bottom: 15px;
}

.input-group label {
  font-weight: bold;
  margin-bottom: 5px;
}

.input-field {
  width: 100%;
  padding: 10px;
  font-size: 16px;
  border: 1px solid #ccc;
  border-radius: 8px;
}

/* Botones */
.buttons-container {
  margin-top: 20px;
  display: flex;
  justify-content: space-between;
}

.action-btn {
  padding: 10px 20px;
  font-size: 16px;
  border: none;
  border-radius: 5px;
  cursor: pointer;
  width: 48%;
}

.accept-btn {
  background-color: #4CAF50;
  color: white;
}

.accept-btn:hover {
  background-color: #45a049;
}

.cancel-btn {
  background-color: #f44336;
  color: white;
}

.cancel-btn:hover {
  background-color: #e53935;
}

</style>
