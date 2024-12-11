<template>
  <div class="form-container">
    <h2>Realizar Venta</h2>

    <!-- Sección Cliente -->
    <div class="cliente-section">
      <div class="input-group">
        <label>DNI Cliente:</label>
        <div class="search-group">
          <input 
            v-model="cliente.DNI" 
            @keyup.enter="buscarCliente"
            type="text" 
            class="input-field"
            placeholder="Ingrese DNI"
          />
          <button @click="buscarCliente" class="search-btn">Buscar</button>
        </div>
      </div>

      <div v-if="mostrarFormCliente" class="cliente-form">
        <div class="input-group">
          <label>Nombre:</label>
          <input 
            v-model="cliente.Nombre" 
            type="text" 
            class="input-field"
            :disabled="clienteEncontrado"
          />
        </div>
        <div class="input-group">
          <label>Apellido Paterno:</label>
          <input 
            v-model="cliente.Apellido_Pat" 
            type="text" 
            class="input-field"
            :disabled="clienteEncontrado"
          />
        </div>
        <div class="input-group">
          <label>Apellido Materno:</label>
          <input 
            v-model="cliente.Apellido_Mat" 
            type="text" 
            class="input-field"
            :disabled="clienteEncontrado"
          />
        </div>
        <button @click="continuarAVenta" class="btn-primary">
          Continuar
        </button>
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
      totalVenta: 0
    }
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
      // Cliente no encontrado, mostrar formulario vacío
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
      const response = await axios.post('/clientes', {
        DNI: this.cliente.DNI,
        Nombre: this.cliente.Nombre,
        Apellido_Pat: this.cliente.Apellido_Pat,
        Apellido_Mat: this.cliente.Apellido_Mat
      })
      
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
      }
    } catch (error) {
      console.error('Error al registrar cliente:', error)
      alert(error.response?.data?.message || 'Error al registrar el cliente')
      return
    }
  }
  this.mostrarVenta = true
},
async finalizarVenta() {
    if (this.medicamentosSeleccionados.length === 0) {
        alert('Agregue al menos un medicamento a la venta')
        return
    }

    try {
        // 1. Crear la venta
        const ventaData = {
            Cliente_ID: this.cliente.ID,
            Monto_Total: this.totalVenta,
            Tipo_de_Pago: this.formaPago
        }
        
        console.log('Datos venta a enviar:', ventaData)
        const ventaResponse = await axios.post('/salida', ventaData)
        console.log('Respuesta de venta:', ventaResponse.data)

        if (ventaResponse.data.success) {
            const salidaId = ventaResponse.data.salida.ID
            console.log('ID de salida obtenido:', salidaId)

            // 3. Crear los detalles con el nombre de campo correcto
            const detallesData = {
                salida_id: salidaId, // Cambiado a minúsculas
                detalles: this.medicamentosSeleccionados.map(med => ({
                    medicamento_id: med.ID, // Cambiado a minúsculas
                    cantidad: med.cantidad
                }))
            }
            
            console.log('Datos detalles a enviar:', detallesData)
            const detallesResponse = await axios.post('/detalle-salida/masivo', detallesData)
            console.log('Respuesta de detalles:', detallesResponse.data)

            if (detallesResponse.data.success) {
                alert('Venta realizada con éxito')
                this.reiniciarFormulario()
            }
        } else {
            throw new Error('Error al crear la venta')
        }
    } catch (error) {
        console.error('Error al procesar la venta:', error)
        if (error.response?.data?.message) {
            alert(error.response.data.message)
        } else {
            alert('Error al procesar la venta: ' + error.message)
        }
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
  max-width: 1200px;
  margin: 0 auto;
  padding: 20px;
}

.cliente-section, .venta-section {
  background-color: white;
  padding: 20px;
  border-radius: 8px;
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
  margin-bottom: 20px;
}

.input-group {
  margin-bottom: 15px;
}

.input-group label {
  display: block;
  margin-bottom: 5px;
  font-weight: bold;
}

.search-group {
  display: flex;
  gap: 10px;
}

.input-field {
  width: 100%;
  padding: 8px 12px;
  border: 1px solid #ddd;
  border-radius: 4px;
  font-size: 14px;
}

.btn-primary, .btn-success, .search-btn {
  padding: 8px 16px;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  font-weight: bold;
  transition: background-color 0.2s;
}

.btn-primary, .search-btn {
  background-color: #4CAF50;
  color: white;
}

.btn-success {
  background-color: #2196F3;
  color: white;
  padding: 12px 24px;
  font-size: 16px;
}

.medicamentos-search {
  position: relative;
  margin-bottom: 20px;
}

.medicamentos-dropdown {
  position: absolute;
  width: 100%;
  max-height: 200px;
  overflow-y: auto;
  background: white;
  border: 1px solid #ddd;
  border-radius: 4px;
  z-index: 1000;
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.medicamento-item {
  padding: 8px 12px;
  cursor: pointer;
  border-bottom: 1px solid #eee;
}

.medicamento-item:hover {
  background-color: #f5f5f5;
}

.medicamentos-table {
  width: 100%;
  border-collapse: collapse;
  margin-bottom: 20px;
}

.medicamentos-table th,
.medicamentos-table td {
  padding: 12px;
  text-align: left;
  border-bottom: 1px solid #ddd;
}

.medicamentos-table th {
  background-color: #f5f5f5;
  font-weight: bold;
}

.cantidad-control {
  display: flex;
  align-items: center;
  gap: 5px;
}

.btn-cantidad {
  width: 30px;
  height: 30px;
  border: 1px solid #ddd;
  background: #f5f5f5;
  border-radius: 4px;
  cursor: pointer;
}

.input-cantidad {
  width: 60px;
  text-align: center;
  padding: 4px;
  border: 1px solid #ddd;
  border-radius: 4px;
}

.btn-delete {
  color: #dc3545;
  background: none;
  border: none;
  cursor: pointer;
  font-size: 18px;
}

.venta-footer {
  margin-top: 20px;
  padding: 20px;
  background-color: #f8f9fa;
  border-radius: 4px;
  border: 1px solid #ddd;
}

.text-right {
  text-align: right;
}

@media (max-width: 768px) {
  .search-group {
    flex-direction: column;
  }
  
  .btn-primary, .btn-success, .search-btn {
    width: 100%;
  }
}
</style>