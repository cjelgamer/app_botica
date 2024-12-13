<template>
  <div class="form-container">
    <!-- Header con título y saldo -->
    <div class="header-section">
      <h2>Realizar Venta</h2>
      <div class="saldo-display">
        <span class="saldo-label">Dinero en Caja:</span>
        <span class="saldo-monto">S/. {{ saldoCaja.toFixed(2) }}</span>
      </div>
    </div>

    <!-- Nueva sección de cliente mejorada -->
    <div v-if="!ventaCompletada" class="cliente-section">
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
            <button @click="usarClienteGeneral" class="btn-general">
              <i class="fas fa-users"></i> Cliente General
            </button>
          </div>
        </div>

        <div v-if="mostrarFormCliente && !clienteGeneral" class="cliente-datos">
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
      <!-- Solo mostrar búsqueda si la venta no está completada -->
      <div v-if="!ventaCompletada" class="medicamentos-search">
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

      <!-- Solo mostrar tabla si hay medicamentos y la venta no está completada -->
      <table v-if="medicamentosSeleccionados.length && !ventaCompletada" class="medicamentos-table">
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

      <!-- Footer modificado -->
      <div v-if="medicamentosSeleccionados.length || ventaCompletada" class="venta-footer">
        <div v-if="!ventaCompletada" class="venta-footer-left">
          <div class="forma-pago">
            <label>Forma de Pago:</label>
            <select v-model="formaPago" class="input-field">
              <option value="efectivo">Efectivo</option>
              <option value="tarjeta">Tarjeta</option>
              <option value="yape">Yape</option>
            </select>
          </div>
        </div>
        <div class="venta-footer-right">
          <button v-if="ventaCompletada" @click="imprimirTicket" class="btn-ticket">
            <i class="fas fa-print"></i> Imprimir Ticket
          </button>
          <button v-if="ventaCompletada" @click="nuevaVenta" class="btn-success">
            <i class="fas fa-plus"></i> Nueva Venta
          </button>
          <button v-if="!ventaCompletada" @click="finalizarVenta" class="btn-success">
            Finalizar Venta
          </button>
        </div>
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
      saldoCaja: 0,
      ventaCompletada: false,
      ultimaVentaId: null,
      clienteGeneral: false // Nueva propiedad para controlar el cliente general
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
    // Nuevo método para cliente general
    async usarClienteGeneral() {
      try {
        // Buscar el cliente con DNI null
        const response = await axios.get('/clientes/general')
        
        if (response.data.success && response.data.cliente) {
          const clienteData = response.data.cliente
          this.cliente = {
            DNI: null,
            Nombre: null,
            Apellido_Pat: null,
            Apellido_Mat: null,
            ID: clienteData.ID
          }
          this.clienteEncontrado = true
          this.clienteGeneral = true
          this.mostrarFormCliente = false // Ocultamos el formulario
          this.mostrarVenta = true // Mostramos directamente la sección de venta
        } else {
          // Si no existe el cliente general, lo creamos
          const createResponse = await axios.post('/clientes', {
            DNI: null,
            Nombre: null,
            Apellido_Pat: null,
            Apellido_Mat: null
          })

          if (createResponse.data.success && createResponse.data.cliente) {
            this.cliente = {
              DNI: null,
              Nombre: null,
              Apellido_Pat: null,
              Apellido_Mat: null,
              ID: createResponse.data.cliente.ID
            }
            this.clienteEncontrado = true
            this.clienteGeneral = true
            this.mostrarFormCliente = false
            this.mostrarVenta = true
          }
        }
      } catch (error) {
        console.error('Error al obtener cliente general:', error)
        alert('Error al procesar cliente general')
      }
    },

    async buscarCliente() {
      if (!this.cliente.DNI) {
        alert('Por favor ingrese un DNI')
        return
      }

      // Si el DNI es '0', usar cliente general
      if (this.cliente.DNI === '0') {
        await this.usarClienteGeneral()
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
          this.clienteGeneral = false
        } else {
          this.clienteEncontrado = false
          this.clienteGeneral = false
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
        this.clienteGeneral = false
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

    async continuarAVenta() {
      if (!this.clienteEncontrado && !this.clienteGeneral) {
        if (!this.cliente.DNI || !this.cliente.Nombre || 
            !this.cliente.Apellido_Pat || !this.cliente.Apellido_Mat) {
          alert('Por favor complete todos los campos del cliente')
          return
        }

        try {
          const response = await axios.post('/clientes', {
            DNI: this.cliente.DNI,
            Nombre: this.cliente.Nombre,
            Apellido_Pat: this.cliente.Apellido_Pat,
            Apellido_Mat: this.cliente.Apellido_Mat
          })
          
          if (response.data.success && response.data.cliente) {
            this.cliente = {
              ...response.data.cliente,
              ID: response.data.cliente.ID
            }
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
      this.ventaCompletada = false
      this.ultimaVentaId = null
      this.clienteGeneral = false
    },

    // ... resto de métodos existentes sin cambios ...
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

    async obtenerSaldoCaja() {
      try {
        const response = await axios.get('/caja/saldo-actual')
        
        if (response.data.success) {
          const nuevoSaldo = parseFloat(response.data.saldo_final) || 0;
          this.saldoCaja = nuevoSaldo;
        } else {
          console.log('Respuesta no exitosa:', response.data);
        }
      } catch (error) {
        console.error('Error detallado:', {
          mensaje: error.message,
          respuesta: error.response?.data,
          status: error.response?.status
        });
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
          this.ultimaVentaId = salidaId
          
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
            this.ventaCompletada = true
            this.medicamentosSeleccionados = []
            this.totalVenta = 0
            alert('Venta realizada con éxito')
          }
        } else {
          throw new Error('Error al crear la venta')
        }
      } catch (error) {
        console.error('Error al procesar la venta:', error)
        alert(error.response?.data?.message || 'Error al procesar la venta: ' + error.message)
      }
    },

    async imprimirTicket() {
      if (this.ultimaVentaId) {
        try {
          const response = await axios.get(`/ticket/${this.ultimaVentaId}`, {
            responseType: 'blob'
          });
          
          const blob = new Blob([response.data], { type: 'application/pdf' });
          const url = window.URL.createObjectURL(blob);
          
          const link = document.createElement('a');
          link.href = url;
          link.target = '_blank';
          link.click();
          
          window.URL.revokeObjectURL(url);
        } catch (error) {
          console.error('Error al generar ticket:', error);
          alert('Error al generar el ticket');
        }
      }
    },

    nuevaVenta() {
      this.reiniciarFormulario()
    }
  }
}
</script>

<style scoped>
.form-container {
  max-width: 1400px;
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

.search-container {
  width: 100%;
  max-width: 100%;
  margin-bottom: 0.5rem;
}

.search-group {
  display: flex;
  gap: 0.35rem;
  margin-top: 0.25rem;
  flex-wrap: wrap; /* Agregado para mejor respuesta en móviles */
}

.search-input {
  flex: 1;
  padding: 0.35rem 0.5rem;
  border: 1px solid #ddd;
  border-radius: 4px;
  font-size: 0.9rem;
  height: 32px;
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
  height: 32px;
}

.search-btn:hover {
  background-color: #45a049;
}

/* Nuevo estilo para el botón de cliente general */
.btn-general {
  padding: 0.35rem 1rem;
  background-color: #6c757d;
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
}

.btn-general:hover {
  background-color: #5a6268;
}

.cliente-datos {
  width: 100%;
  background-color: #f8f9fa;
  padding: 0.75rem;
  border-radius: 6px;
  border: 1px solid #e9ecef;
  display: flex;
  align-items: center;
  gap: 1rem;
}

.input-row {
  display: flex;
  flex: 1;
  gap: 1rem;
  align-items: center;
  margin-bottom: 0;
}

.input-group {
  display: flex;
  flex-direction: row;
  align-items: center;
  gap: 0.5rem;
  flex: 1;
}

.input-group label {
  font-weight: 600;
  color: #495057;
  font-size: 0.85rem;
  white-space: nowrap;
  margin-right: 0.5rem;
}

.input-group input {
  flex: 1;
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
  margin: 0;
  align-items: center;
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
  white-space: nowrap;
  padding: 0 1.25rem;
}

.btn-continuar:hover {
  background-color: #1976D2;
}

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
  height: 28px;
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
  padding: 6px 8px;
  text-align: left;
  border-bottom: 1px solid #ddd;
  font-size: 0.9rem;
  height: 32px;
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
  height: 24px;
}

.btn-delete {
  color: #dc3545;
  background: none;
  border: none;
  cursor: pointer;
  font-size: 14px;
  height: 24px;
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

.venta-footer-left {
  display: flex;
  gap: 1rem;
  align-items: center;
}

.venta-footer-right {
  display: flex;
  gap: 0.75rem;
  align-items: center;
}

.btn-ticket {
  background-color: #6c757d;
  color: white;
  padding: 0.35rem 1rem;
  font-size: 14px;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  font-weight: bold;
  transition: background-color 0.2s;
  height: 32px;
  display: flex;
  align-items: center;
  gap: 0.35rem;
}

.btn-ticket:hover {
  background-color: #5a6268;
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
  height: 32px;
}

.input-field {
  width: 100%;
  padding: 0.35rem 0.5rem;
  border: 1px solid #ddd;
  border-radius: 4px;
  font-size: 13px;
  height: 32px;
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
  
  .search-btn,
  .btn-general {  /* Agregado para el botón de cliente general */
    width: 100%;
  }
  
  .venta-footer {
    flex-direction: column;
    gap: 0.75rem;
  }
  
  .venta-footer-right {
    display: flex;
    flex-direction: column;
    width: 100%;
    gap: 0.5rem;
  }

  .btn-ticket,
  .btn-success {
    width: 100%;
  }
}
</style>