<template>
  <div class="form-container">
    <!-- Header con título y saldo -->
    <div class="header-section">
      <h2>Realizar Venta</h2>
      <div class="saldo-display">
        <span class="saldo-label">Saldo Actual:</span>
        <span class="saldo-monto">S/. {{ saldoCaja.toFixed(2) }}</span>
      </div>
    </div>

    <!-- Nueva sección de cliente mejorada -->
    <div class="cliente-section">
      <div class="cliente-grid">
        <div class="search-container">
          <label>DNI Cliente:</label>
          <div class="search-group">
            <input 
              v-model="cliente.DNI" 
              @keyup.enter="buscarCliente"
              type="text" 
              class="search-input"
              placeholder="Ingrese DNI"
            />
            <button @click="buscarCliente" class="search-btn">
              <i class="fas fa-search"></i> Buscar
            </button>
          </div>
        </div>

        <div v-if="mostrarFormCliente" class="cliente-datos">
          <div class="input-row">
            <div class="input-group">
              <label>Nombre:</label>
              <input 
                v-model="cliente.Nombre" 
                type="text" 
                :disabled="clienteEncontrado"
                placeholder="Nombre"
              />
            </div>
            <div class="input-group">
              <label>Apellido Paterno:</label>
              <input 
                v-model="cliente.Apellido_Pat" 
                type="text" 
                :disabled="clienteEncontrado"
                placeholder="Apellido Paterno"
              />
            </div>
            <div class="input-group">
              <label>Apellido Materno:</label>
              <input 
                v-model="cliente.Apellido_Mat" 
                type="text" 
                :disabled="clienteEncontrado"
                placeholder="Apellido Materno"
              />
            </div>
          </div>
          <div class="actions-row">
            <button @click="continuarAVenta" class="btn-continuar">
              <i class="fas fa-arrow-right"></i> Continuar a Venta
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Sección Venta -->
    <div v-if="mostrarVenta" class="venta-section">
      <div class="medicamentos-search">
        <input 
          v-model="busquedaMedicamento" 
          type="text" 
          class="input-field"
          placeholder="Buscar medicamento..." 
          @input="filtrarMedicamentos"
          @keyup.enter="seleccionarPrimerMedicamento"
        />
        <div v-if="medicamentosFiltrados.length && busquedaMedicamento" class="medicamentos-dropdown">
          <div 
            v-for="medicamento in medicamentosFiltrados" 
            :key="medicamento.ID"
            class="medicamento-item"
            @click="seleccionarMedicamento(medicamento)"
          >
            {{ medicamento.Nombre }} - Stock: {{ medicamento.Stock }}
          </div>
        </div>
      </div>

      <!-- Tabla de medicamentos seleccionados -->
      <table v-if="medicamentosSeleccionados.length" class="medicamentos-table">
        <thead>
          <tr>
            <th>N°</th>
            <th>Medicamento</th>
            <th>Stock</th>
            <th>Cantidad</th>
            <th>Precio</th>
            <th>Total</th>
            <th>Acciones</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="(med, index) in medicamentosSeleccionados" :key="index">
            <td>{{ index + 1 }}</td>
            <td>{{ med.Nombre }}</td>
            <td>{{ med.Stock }}</td>
            <td>
              <div class="cantidad-control">
                <button @click="ajustarCantidad(index, -1)" class="btn-cantidad">-</button>
                <input 
                  v-model.number="med.cantidad" 
                  type="number"
                  min="1"
                  :max="med.Stock"
                  @input="calcularTotal"
                  class="input-cantidad"
                />
                <button @click="ajustarCantidad(index, 1)" class="btn-cantidad">+</button>
              </div>
            </td>
            <td>{{ med.Precio }}</td>
            <td>{{ (med.Precio * med.cantidad).toFixed(2) }}</td>
            <td>
              <button @click="eliminarMedicamento(index)" class="btn-delete">
                <i class="fas fa-trash"></i>
              </button>
            </td>
          </tr>
        </tbody>
        <tfoot>
          <tr>
            <td colspan="5" class="text-right"><strong>Total:</strong></td>
            <td>S/. {{ totalVenta.toFixed(2) }}</td>
            <td></td>
          </tr>
        </tfoot>
      </table>

      <div v-if="medicamentosSeleccionados.length" class="venta-footer">
        <div class="forma-pago">
          <label>Forma de Pago:</label>
          <select v-model="formaPago" class="input-field">
            <option value="efectivo">Efectivo</option>
            <option value="tarjeta">Tarjeta</option>
            <option value="yape">Yape</option>
          </select>
        </div>
        <button @click="finalizarVenta" class="btn-success">
          Finalizar Venta
        </button>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios'

export default {
  data() {
    return {
      cliente: {
        DNI: '',
        Nombre: '',
        Apellido_Pat: '',
        Apellido_Mat: '',
        id: null
      },
      clienteEncontrado: false,
      mostrarFormCliente: false,
      mostrarVenta: false,
      busquedaMedicamento: '',
      medicamentosFiltrados: [],
      medicamentosSeleccionados: [],
      formaPago: 'efectivo',
      totalVenta: 0,
      saldoCaja: 0
    }
  },

  mounted() {
    this.obtenerSaldoCaja();
    document.addEventListener('visibilitychange', this.handleVisibilityChange);
  },

  beforeDestroy() {
    document.removeEventListener('visibilitychange', this.handleVisibilityChange);
  },

  methods: {
    async buscarCliente() {
      if (!this.cliente.DNI) {
        alert('Por favor ingrese un DNI')
        return
      }
      
      try {
        const response = await axios.get(`/clientes/buscar/${this.cliente.DNI}`)
        
        if (response.data.success) {
          const clienteData = response.data.cliente
          this.cliente = {
            DNI: clienteData.DNI,
            Nombre: clienteData.Nombre,
            Apellido_Pat: clienteData.Apellido_Pat,
            Apellido_Mat: clienteData.Apellido_Mat,
            ID: clienteData.ID
          }
          this.clienteEncontrado = true
          this.mostrarVenta = true
        } else {
          this.clienteEncontrado = false
          this.cliente = {
            DNI: this.cliente.DNI,
            Nombre: '',
            Apellido_Pat: '',
            Apellido_Mat: '',
            ID: null
          }
        }
        this.mostrarFormCliente = true
      } catch (error) {
        console.error('Error al buscar cliente:', error)
        this.clienteEncontrado = false
        this.mostrarFormCliente = true
        if (error.response?.status === 404) {
          this.cliente = {
            DNI: this.cliente.DNI,
            Nombre: '',
            Apellido_Pat: '',
            Apellido_Mat: '',
            ID: null
          }
        } else {
          alert('Error al buscar cliente')
        }
      }
    },

    async filtrarMedicamentos() {
      if (this.busquedaMedicamento.length < 2) {
        this.medicamentosFiltrados = []
        return
      }

      try {
        const response = await axios.get('/medicamentos', {
          params: { search: this.busquedaMedicamento }
        })
        this.medicamentosFiltrados = response.data
      } catch (error) {
        console.error('Error al buscar medicamentos:', error)
      }
    },

    seleccionarPrimerMedicamento() {
      if (this.medicamentosFiltrados.length > 0) {
        this.seleccionarMedicamento(this.medicamentosFiltrados[0])
      }
    },

    seleccionarMedicamento(medicamento) {
      const existe = this.medicamentosSeleccionados.find(m => m.ID === medicamento.ID)
      if (!existe && medicamento.Stock > 0) {
        this.medicamentosSeleccionados.push({
          ...medicamento,
          cantidad: 1
        })
        this.calcularTotal()
      }
      this.busquedaMedicamento = ''
      this.medicamentosFiltrados = []
    },

    ajustarCantidad(index, delta) {
      const med = this.medicamentosSeleccionados[index]
      const nuevaCantidad = med.cantidad + delta
      if (nuevaCantidad > 0 && nuevaCantidad <= med.Stock) {
        med.cantidad = nuevaCantidad
        this.calcularTotal()
      }
    },

    eliminarMedicamento(index) {
      this.medicamentosSeleccionados.splice(index, 1)
      this.calcularTotal()
    },

    calcularTotal() {
      this.totalVenta = this.medicamentosSeleccionados.reduce(
        (total, med) => total + (med.Precio * med.cantidad),
        0
      )
    },

    async continuarAVenta() {
      if (!this.clienteEncontrado) {
        if (!this.cliente.DNI || !this.cliente.Nombre || 
            !this.cliente.Apellido_Pat || !this.cliente.Apellido_Mat) {
          alert('Por favor complete todos los campos del cliente')
          return
        }

        try {
          // Intentamos registrar el nuevo cliente
          console.log('Intentando registrar cliente:', this.cliente)
          
          const response = await axios.post('/clientes', {
            DNI: this.cliente.DNI,
            Nombre: this.cliente.Nombre,
            Apellido_Pat: this.cliente.Apellido_Pat,
            Apellido_Mat: this.cliente.Apellido_Mat
          })
          
          console.log('Respuesta del servidor:', response.data)
          
          if (response.data.success && response.data.cliente) {
            // Actualizamos el estado del cliente con la respuesta
            this.cliente = {
              ...response.data.cliente,
              ID: response.data.cliente.ID // Aseguramos que el ID esté presente
            }
            console.log('Cliente actualizado:', this.cliente)
            this.clienteEncontrado = true
          } else {
            throw new Error('La respuesta del servidor no contiene los datos esperados')
          }
          
        } catch (error) {
          console.error('Error completo:', error)
          console.error('Response data:', error.response?.data)
          alert('Error al procesar el cliente: ' + (error.response?.data?.message || error.message))
          return
        }
      }

      if (!this.cliente.ID) {
        console.error('No hay ID de cliente después del proceso:', this.cliente)
        alert('Error: No se pudo obtener el ID del cliente')
        return
      }
      
      this.mostrarVenta = true
    },

    async obtenerSaldoCaja() {
      try {
        const response = await axios.get('/caja/saldo-actual')
        if (response.data.success) {
          this.saldoCaja = parseFloat(response.data.saldo_final) || 0
        }
      } catch (error) {
        console.error('Error al obtener saldo:', error)
      }
    },

    handleVisibilityChange() {
      if (document.visibilityState === 'visible') {
        this.obtenerSaldoCaja()
      }
    },

    async finalizarVenta() {
      if (this.medicamentosSeleccionados.length === 0) {
        alert('Agregue al menos un medicamento a la venta')
        return
      }

      try {
        const ventaData = {
          Cliente_ID: this.cliente.ID,
          Monto_Total: this.totalVenta,
          Tipo_de_Pago: this.formaPago
        }
        
        const ventaResponse = await axios.post('/salida', ventaData)

        if (ventaResponse.data.success) {
          const salidaId = ventaResponse.data.salida.ID
          
          const detallesData = {
            salida_id: salidaId,
            detalles: this.medicamentosSeleccionados.map(med => ({
              medicamento_id: med.ID,
              cantidad: med.cantidad
            }))
          }
          
          const detallesResponse = await axios.post('/detalle-salida/masivo', detallesData)

          if (detallesResponse.data.success) {
            setTimeout(() => this.obtenerSaldoCaja(), 1000)
            alert('Venta realizada con éxito')
            this.reiniciarFormulario()
          }
        } else {
          throw new Error('Error al crear la venta')
        }
      } catch (error) {
        console.error('Error al procesar la venta:', error)
        alert(error.response?.data?.message || 'Error al procesar la venta: ' + error.message)
      }
    },

    reiniciarFormulario() {
      this.cliente = {
        DNI: '',
        Nombre: '',
        Apellido_Pat: '',
        Apellido_Mat: '',
        ID: null
      }
      this.clienteEncontrado = false
      this.mostrarFormCliente = false
      this.mostrarVenta = false
      this.medicamentosSeleccionados = []
      this.formaPago = 'efectivo'
      this.totalVenta = 0
    }
  }
}
</script>

<style scoped>
.form-container {
  max-width: 1400px;  /* Aumentado de 1000px */
  margin: 0 auto;
  padding: 15px;
}

.header-section {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 0.75rem;
  padding: 0.5rem;
  background-color: #f8f9fa;
  border-radius: 8px;
  box-shadow: 0 2px 4px rgba(0,0,0,0.05);
}

.saldo-display {
  display: flex;
  align-items: center;
  gap: 0.35rem;
  padding: 0.25rem 0.75rem;
  background-color: white;
  border-radius: 6px;
  box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}

.saldo-label {
  font-weight: 600;
  color: #666;
}

.saldo-monto {
  font-size: 0.9rem;
  font-weight: bold;
  color: #2ecc71;
}

.cliente-section {
  background-color: white;
  border-radius: 8px;
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
  padding: 0.75rem;
  margin-bottom: 1rem;
}

.cliente-grid {
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
}

/* Ajustar el contenedor de búsqueda */
.search-container {
  width: 100%;
  max-width: 100%;  /* Eliminado el límite de 350px */
  margin-bottom: 0.5rem;
}


.search-group {
  display: flex;
  gap: 0.35rem;
  margin-top: 0.25rem;
}

.search-input {
  flex: 1;
  padding: 0.35rem 0.5rem;  /* Reducido el padding vertical */
  border: 1px solid #ddd;
  border-radius: 4px;
  font-size: 0.9rem;
  height: 32px;  /* Altura fija más compacta */
}

.search-btn {
  padding: 0.35rem 1rem;
  background-color: #4CAF50;
  color: white;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  display: flex;
  align-items: center;
  gap: 0.35rem;
  font-weight: 600;
  transition: background-color 0.2s;
  height: 32px;  /* Altura fija más compacta */
}

.search-btn:hover {
  background-color: #45a049;
}

.cliente-datos {
  width: 100%;
  background-color: #f8f9fa;
  padding: 0.75rem;
  border-radius: 6px;
  border: 1px solid #e9ecef;
  display: flex;  /* Agregado */
  align-items: center;  /* Agregado */
  gap: 1rem;  /* Agregado */
}

.input-row {
  display: flex;
  flex: 1;  /* Agregado */
  gap: 1rem;
  align-items: center;  /* Agregado */
  margin-bottom: 0;  /* Modificado */
}

.input-group {
  display: flex;
  flex-direction: row;  /* Cambiado a row */
  align-items: center;  /* Agregado */
  gap: 0.5rem;
  flex: 1;
}

.input-group label {
  font-weight: 600;
  color: #495057;
  font-size: 0.85rem;
  white-space: nowrap;  /* Agregado */
  margin-right: 0.5rem;  /* Agregado */
}

.input-group input {
  flex: 1;  /* Agregado */
  height: 32px;
  padding: 0.35rem 0.5rem;
  border: 1px solid #ddd;
  border-radius: 4px;
  font-size: 0.9rem;
  background-color: white;
}

.input-group input:disabled {
  background-color: #e9ecef;
  cursor: not-allowed;
}

.actions-row {
  display: flex;
  margin: 0;  /* Modificado */
  align-items: center;  /* Agregado */
}

.btn-continuar {
  padding: 0.35rem 1.25rem;
  background-color: #2196F3;
  color: white;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  display: flex;
  align-items: center;
  gap: 0.35rem;
  font-weight: 600;
  transition: background-color 0.2s;
  height: 32px;
  white-space: nowrap;  /* Agregado */
  height: 32px;
  padding: 0 1.25rem;
  display: flex;
  align-items: center;  /* Altura fija más compacta */
}

.btn-continuar:hover {
  background-color: #1976D2;
}

/* Estilos para la sección de venta */
.venta-section {
  background-color: white;
  padding: 12px;
  border-radius: 8px;
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
  margin-bottom: 12px;
}

.medicamentos-search {
  position: relative;
  margin-bottom: 12px;
}

.medicamentos-dropdown {
  position: absolute;
  width: 100%;
  max-height: 160px;
  overflow-y: auto;
  background: white;
  border: 1px solid #ddd;
  border-radius: 4px;
  z-index: 1000;
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.medicamento-item {
  padding: 4px 8px;
  cursor: pointer;
  border-bottom: 1px solid #eee;
  font-size: 0.9rem;
  height: 28px;  /* Altura fija más compacta */
  line-height: 20px;
}

.medicamento-item:hover {
  background-color: #f5f5f5;
}

.medicamentos-table {
  min-width: 100%;
  width: 100%;
  border-collapse: collapse;
  margin-bottom: 12px;
}

.medicamentos-table th,
.medicamentos-table td {
  padding: 6px 8px;  /* Reducido el padding vertical */
  text-align: left;
  border-bottom: 1px solid #ddd;
  font-size: 0.9rem;
  height: 32px;  /* Altura fija más compacta */
}

.medicamentos-table th {
  background-color: #f5f5f5;
  font-weight: bold;
}

.cantidad-control {
  display: flex;
  align-items: center;
  gap: 4px;
}

.btn-cantidad {
  width: 24px;
  height: 24px;
  border: 1px solid #ddd;
  background: #f5f5f5;
  border-radius: 4px;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
}

.input-cantidad {
  width: 45px;
  text-align: center;
  padding: 2px;
  border: 1px solid #ddd;
  border-radius: 4px;
  height: 24px;  /* Altura fija más compacta */
}

.btn-delete {
  color: #dc3545;
  background: none;
  border: none;
  cursor: pointer;
  font-size: 14px;
  height: 24px;  /* Altura fija más compacta */
}

.venta-footer {
  margin-top: 12px;
  padding: 12px;
  background-color: #f8f9fa;
  border-radius: 4px;
  border: 1px solid #ddd;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.text-right {
  text-align: right;
}

.btn-success {
  background-color: #2196F3;
  color: white;
  padding: 0.35rem 1rem;
  font-size: 14px;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  font-weight: bold;
  transition: background-color 0.2s;
  height: 32px;  /* Altura fija más compacta */
}

.input-field {
  width: 100%;
  padding: 0.35rem 0.5rem;
  border: 1px solid #ddd;
  border-radius: 4px;
  font-size: 13px;
  height: 32px;  /* Altura fija más compacta */
}

@media (max-width: 768px) {
  .header-section {
    flex-direction: column;
    gap: 0.75rem;
  }
  
  .input-row {
    grid-template-columns: 1fr;
  }
  
  .search-group {
    flex-direction: column;
  }
  
  .search-btn {
    width: 100%;
  }
  
  .venta-footer {
    flex-direction: column;
    gap: 0.75rem;
  }
}
</style>