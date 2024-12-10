<template>
  <div class="form-container">
    <div class="search-container">
    <button class="search-button" @click="openModal">
      Buscar Medicamento
    </button>

    <div v-if="isModalVisible" class="modal-overlay" @click.self="closeModal">
    <div class="modal-content">
    <h2>Buscar Medicamento</h2>
    <p>Este es el contenido del modal.</p>
    <button class="close-button" @click="closeModal">Cerrar</button>
    </div>
    </div>

    </div>

    <!-- Modal -->
    <div v-if="isModalVisible" class="modal-overlay" @click.self="closeModal">
      <div class="modal-content">
        <input
          type="text"
          v-model="searchQuery"
          @input="filterMedicamentos"
          placeholder="Buscar medicamento..."
          class="search-input"
        />
        <ul class="medicamentos-list">
          <li
            v-for="medicamento in filteredMedicamentos"
            :key="medicamento.id"
            @click="addMedicamento(medicamento)"
          >
            {{ medicamento.Nombre }}
          </li>
        </ul>
        <button class="close-button" @click="closeModal">Cerrar</button>
      </div>
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
            <th>Acciones</th>
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
                class="quantity-input"
                @input="calcularPrecioTotal(medicamento)"
              />
              <button @click="updateCantidad(medicamento, 1)" class="quantity-btn">+</button>
            </td>
            <td>{{ medicamento.Precio }}</td>
            <td>{{ medicamento.Precio * medicamento.cantidad }}</td>
            <td>
              <button @click="removeMedicamento(medicamento)" class="delete-button">
                 <i class="fa fa-trash"></i>
              </button>
            </td>
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
      <div class="input-group">
      <label for="tipoPago">Tipo de Pago:</label>
      <select v-model="tipoPago" id="tipoPago" class="input-field">
        <option value="efectivo">Efectivo</option>
        <option value="tarjeta">Tarjeta de Crédito</option>
        <option value="aplicativo">Aplicativo (Yape)</option>
      </select>
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
      medicamentos: [], // Lista de medicamentos seleccionados
      nuevoMedicamento: {
        id: null,
        Nombre: '',
        Stock: 0,
        cantidad: 1,
        Precio: 0
      },
      nombreCliente: "", // Nombre del cliente
      apellidoCliente: "", // Campo para capturar ambos apellidos juntos
      dniCliente: "", // DNI del cliente
      allMedicamentos: [], // Todos los medicamentos disponibles para buscar
      filteredMedicamentos: [], // Medicamentos filtrados en la búsqueda
      searchQuery: "", // Texto de búsqueda
      isModalVisible: false, // Estado del modal 
      tipoPago: "", // Valor inicial del tipo de pago
    };
  },
  methods: {
    openModal() {
      this.isModalVisible = true; // Muestra el modal
      this.fetchAllMedicamentos(); // Cargar medicamentos cuando se abre el modal
    },
    closeModal() {
      this.isModalVisible = false; // Oculta el modal
    },
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
    /******/
    
    async fetchAllMedicamentos() {
      try {
        const response = await axios.get("/medicamentos");
        this.allMedicamentos = response.data;
        this.filteredMedicamentos = this.allMedicamentos;
      } catch (error) {
        console.error("Error al obtener medicamentos:", error);
      }
    },
    filterMedicamentos() {
      this.filteredMedicamentos = this.allMedicamentos.filter((medicamento) =>
        medicamento.Nombre.toLowerCase().includes(this.searchQuery.toLowerCase())
      );
    },
    // Función para agregar un medicamento a la venta
    addMedicamento(nuevoMedicamento) {
      // Verificar si el medicamento ya existe en la lista
    const medicamentoExistente = this.medicamentos.find(
      (medicamento) => medicamento.Nombre === nuevoMedicamento.Nombre
    );

    if (medicamentoExistente) {
      // Si existe, solo actualiza la cantidad
      medicamentoExistente.cantidad += nuevoMedicamento.cantidad;
    } else {
      // Si no existe, agrega un nuevo medicamento
      this.medicamentos.push({ ...nuevoMedicamento,
      cantidad: nuevoMedicamento.cantidad || 1, // Por defecto, 1
      Precio: nuevoMedicamento.Precio || 0 // Por defecto, 0
      });
    }

    // Limpiar el formulario o modal (opcional)
    this.nuevoMedicamento = {
      id: null,
      Nombre: '',
      Stock: 0,
      cantidad: 1,
      Precio: 0
    };
      this.closeModal(); // Cerrar el modal después de agregarlo
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
    // Calcular el precio total de un medicamento
    calcularPrecioTotal(medicamento) {
      if (medicamento.cantidad > medicamento.Stock) {
        medicamento.cantidad = medicamento.Stock;
      } else if (medicamento.cantidad < 1) {
        medicamento.cantidad = 1;
      }
    },

    separarApellidoPaterno() {
    if (this.apellidoCliente.trim() !== "") {
    const apellidos = this.apellidoCliente.trim().split(" ");
        return apellidos[0] || "";  // Primer apellido
     }
        return "Apellido Paterno Requerido"; 
     },

    separarApellidoMaterno() {
     if (this.apellidoCliente.trim() !== "") {
    const apellidos = this.apellidoCliente.trim().split(" ");
    return apellidos.length > 1 ? apellidos[apellidos.length - 1] : "";  // Último apellido
    }
    return "Apellido Materno Requerido"
    },
    // Función para aceptar la venta
    
    async aceptarVenta() {
    try { 
    let montoTotal = 0; // Inicializar el monto total
    let cantidadTotal =0;

    for (const medicamento of this.medicamentos) {
      if (medicamento.cantidad === undefined || medicamento.cantidad === null) {
        alert(`El medicamento ${medicamento.Nombre} no tiene una cantidad válida.`);
        return;
      }
      medicamento.cantidad = Number(medicamento.cantidad) || 0; // Convertir a número
      
      if (medicamento.cantidad <= 0) {
        alert(`La cantidad del medicamento ${medicamento.Nombre} debe ser mayor a 0.`);
        return;
      }

      // Calcular el precio total del medicamento y sumar al monto total
      montoTotal += medicamento.Precio * medicamento.cantidad;
      cantidadTotal += medicamento.cantidad;
    }
    //try {
    const apellidoPaterno = this.separarApellidoPaterno();
    const apellidoMaterno = this.separarApellidoMaterno();

    const clienteResponse = await axios.post('/cliente', {
    dni: this.dniCliente,
    nombre: this.nombreCliente,
    apellido_pat: apellidoPaterno,  // Enviar como apellido_pat
    apellido_mat: apellidoMaterno   // Enviar como apellido_mat
    });
    const clienteID = clienteResponse.data.cliente.ID;
    const response = await axios.get('/vendedor/perfil');
    const vendedorID = response.data.id; // Ajusta al formato de respuesta del backend
    // Paso 1: Crear la venta (Salida)
    const salidaResponse = await axios.post('/salida', {
      cliente_id: clienteID,          // ID del cliente
      vendedor_id: vendedorID,        // ID del vendedor
      monto_total: montoTotal, // Total de la venta
      Tipo_de_Pago: this.tipoPago,            // Tipo de pago
      fecha_venta: new Date().toISOString().split('T')[0], // Fecha de la venta
    });
    console.log("Respuesta del backend al crear salida:", salidaResponse.data);
    // Obtener el ID de la salida creada
    const salidaID = salidaResponse.data.salida.ID;
    console.log("Salida ID:", salidaID);  // Verifica si el ID está presente

    // Paso 2: Crear los detalles de la venta (Medicamentos)
      //const medicamentoID=medicamentos.ID;
     // Paso 2: Crear los detalles de la venta (Medicamentos)
     alert("Venta aceptada exitosamente");
     const detallesSalida = this.medicamentos.map(medicamento => {
     console.log(`ID Medicamento: ${medicamento.ID}, Cantidad: ${medicamento.cantidad}`);
     return {
     salida_id: salidaID,
     medicamento_id: medicamento.ID,
     cantidad: medicamento.cantidad || 0,
     };
    });
    console.log(detallesSalida);
    // Enviar los detalles de la venta como un array de objetos
    try {
   const response = await axios.post('/detalle-salida', detallesSalida);
   console.log(response.data);
   alert("Detalle venta creados exitosamente");
   this.cancelarVenta();  // Limpiar datos de la venta
    } catch (error) {
    if (error.response && error.response.data.errors) {
        // Mostrar los errores de validación
        console.error("Errores de validación:", error.response.data.errors);
        alert("Error al crear los detalles de la venta: " + JSON.stringify(error.response.data.errors));
    } else {
        console.error("Error al crear los detalles de la venta:", error);
        alert("Error desconocido al crear los detalles de la venta.");
    }
}

    this.cancelarVenta(); // Limpiar datos de la venta
    } catch (error) {
    // Intenta obtener el mensaje de error
    let errorMessage = "Hubo un error al procesar la venta";
  
    // Si el error es de Axios y tiene una respuesta del servidor
    if (error.response && error.response.data) {
    errorMessage = error.response.data.message || "Error en la respuesta del servidor";
    } else if (error.message) {
    // Si el error es genérico
    errorMessage = error.message;
    }

     // Muestra el mensaje del error en la consola y en el alert
    console.error("Error al procesar la venta:", error);
    alert(errorMessage);
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
    },
    
    removeMedicamento(medicamentoAEliminar) {
    const medicamentoExistente = this.medicamentos.find(
    (medicamento) => medicamento.Nombre === medicamentoAEliminar.Nombre
    );

    if (medicamentoExistente) {
      if (medicamentoExistente.cantidad > 1) {
      // Si la cantidad es mayor a 1, reducimos la cantidad
      medicamentoExistente.cantidad -= 1;
      } else {
      // Si es 1, eliminamos el medicamento de la lista
      this.medicamentos = this.medicamentos.filter(
        (medicamento) => medicamento.Nombre !== medicamentoAEliminar.Nombre
      );
      }
    }
   }

  }
};
</script>

<style scoped>
/* Contenedor principal del formulario */
.form-container {
  width: 100%;
  margin: 20px auto;
  padding: 20px;
  border-radius: 8px;
  background-color: #f9f9f9;
  box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
}

/* Estilo para el campo de búsqueda */
/* Contenedor del botón */
.search-container {
  display: flex;
  justify-content: center;
  align-items: center;
  width: 100%;
  margin: 20px 0;
}

/* Botón */
.search-button {
  padding: 10px 20px;
  font-size: 16px;
  font-weight: bold;
  color: white;
  background-color: #007bff;
  border: none;
  border-radius: 5px;
  cursor: pointer;
  transition: background-color 0.3s ease;
}

.search-button:hover {
  background-color: #0056b3;
}
.search-input {
  width: 100%;
  padding: 10px;
  margin-bottom: 10px;
  font-size: 16px;
  border: 1px solid #ccc;
  border-radius: 8px;
}
/* Modal Overlay */
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(0, 0, 0, 0.5);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 1000;
}

/* Modal Content */
.modal-content {
  background-color: white;
  padding: 20px;
  border-radius: 10px;
  text-align: center;
  width: 300px;
  box-shadow: 0 4px 10px rgba(0, 0, 0, 0.25);
}

/* Botón para cerrar */
.close-button {
  margin-top: 20px;
  padding: 10px 20px;
  font-size: 14px;
  color: white;
  background-color: #dc3545;
  border: none;
  border-radius: 5px;
  cursor: pointer;
  transition: background-color 0.3s ease;
}

.close-button:hover {
  background-color: #c82333;
}


.medicamentos-list {
  list-style: none;
  padding: 0;
  margin: 0;
  max-height: 200px;
  overflow-y: auto;
}

.medicamentos-list li {
  padding: 10px;
  border-bottom: 1px solid #ddd;
  cursor: pointer;
  transition: background-color 0.3s;
}
.medicamentos-list li:hover {
  background-color: #f1f1f1;
}

/* Tabla de medicamentos */
.medicamentos-container {
  width: 100%;
  margin-top: 20px;
  display: block;
}

.medicamentos-table {
  width: 100%;
  border-collapse: collapse;
  margin-bottom: 20px;
  border-radius: 8px;
  overflow: hidden;
}

.medicamentos-table th,
.medicamentos-table td {
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

/* Botones para cantidad */
.quantity-btn {
  background-color: #f1f1f1;
  border: 1px solid #ddd;
  padding: 5px 10px;
  cursor: pointer;
  border-radius: 5px;
  transition: background-color 0.3s;
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
  display: flex;
  flex-wrap: wrap; /* Permite que los elementos salten de línea si no hay espacio */
  gap: 16px; /* Espaciado entre los elementos */
  justify-content: space-between;
}
.input-group {
  display: flex;
  flex-direction: column;
  width: calc(50% - 8px); /* Mitad del ancho menos el gap */
  position: relative; /* Para el placeholder dentro del input */
}
.input-group label {
  font-weight: bold;
  margin-bottom: 5px;
}
.input-field::placeholder {
  font-size: 14px;
  color: #6a6a6a;
}
/*.input-field {
  height: 80%;
  width: 100%;
  padding: 10px;
  font-size: 16px;
  border: 1px solid #ccc;
  border-radius: 8px;
}*/
.input-field:focus {
  outline: none;
  border-color: #007bff;
  box-shadow: 0 0 4px rgba(0, 123, 255, 0.5);
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
