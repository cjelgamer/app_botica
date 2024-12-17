<template>
  <div class="generar-reportes p-4">
    <h2 class="text-lg font-bold mb-4">Panel de Reportes</h2>
    
    <!-- Grid superior -->
    <div class="grid grid-cols-12 gap-4 mb-4">
      <!-- Primera columna: Reportes de Compras (más estrecha) -->
      <div class="col-span-2 bg-white p-4 rounded-lg shadow-sm">
        <h3 class="text-sm font-semibold mb-3 text-gray-800">Reportes de Compras</h3>
        <div class="flex flex-col gap-2">
          <button 
            @click="generarReporteCompras('todo')"
            :disabled="generando"
            class="bg-green-500 hover:bg-green-600 text-white text-xs py-1.5 px-3 rounded transition">
            Reporte Completo
          </button>

          <button 
            @click="generarReporteCompras('semana')"
            :disabled="generando"
            class="bg-blue-500 hover:bg-blue-600 text-white text-xs py-1.5 px-3 rounded transition">
            Últimos 7 Días
          </button>

          <button 
            @click="generarReporteCompras('mes')"
            :disabled="generando"
            class="bg-purple-500 hover:bg-purple-600 text-white text-xs py-1.5 px-3 rounded transition">
            Último Mes
          </button>
        </div>
      </div>

      <!-- Segunda columna: Reporte de Cajas -->
      <div class="col-span-7 bg-white p-4 rounded-lg shadow-sm">
        <h3 class="text-sm font-semibold mb-3 text-gray-800">Reporte de Cajas</h3>
        
        <!-- Selector de fecha y botón -->
        <div class="flex gap-3 mb-4">
          <div class="flex-1">
            <label class="block text-xs text-gray-600 mb-1">
              Seleccionar Fecha
            </label>
            <input 
              type="date" 
              v-model="fechaCaja"
              class="w-full text-sm rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 p-1.5"
            >
          </div>
          <button 
            @click="generarReporteCajas"
            :disabled="generando"
            class="bg-indigo-500 hover:bg-indigo-600 text-white text-xs py-1.5 px-3 rounded transition self-end">
            Imprimir Reporte
          </button>
        </div>

        <!-- Vista previa de datos -->
        <div class="overflow-x-auto mt-2 border rounded-lg">
          <div v-if="datosCaja.length === 0" class="text-center text-gray-500 py-3 text-sm">
            No hay datos para la fecha seleccionada
          </div>
          <table v-else class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Vendedor</th>
                <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Saldo Final</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="caja in datosCaja" :key="caja.Vendedor_ID" class="hover:bg-gray-50">
                <td class="px-4 py-2 text-xs text-gray-900 whitespace-nowrap">{{ caja.Vendedor }}</td>
                <td class="px-4 py-2 text-xs text-gray-900 text-right whitespace-nowrap">
                  S/. {{ formatearMonto(caja.Saldo_Final) }}
                </td>
              </tr>
            </tbody>
            <tfoot class="bg-gray-50">
              <tr>
                <td class="px-4 py-2 text-xs font-medium text-gray-900">Total:</td>
                <td class="px-4 py-2 text-xs font-medium text-gray-900 text-right">
                  S/. {{ formatearMonto(totalSaldoFinal) }}
                </td>
              </tr>
            </tfoot>
          </table>
        </div>
      </div>

      <!-- Tercera columna -->
      <div class="col-span-3 bg-white p-4 rounded-lg shadow-sm">
        <h3 class="text-sm font-semibold mb-3 text-gray-800">Total del Día</h3>
        <div class="flex items-center justify-center h-32 bg-gray-50 rounded-lg border border-gray-200">
          <div class="flex items-center justify-center space-x-6">
            <i class="fas fa-cash-register saldo-icon text-5xl text-gray-600"></i>
            <!--<p class="text-gray-500 text-xs mb-2">Caja General</p>-->
            <p class="text-2xl font-bold text-gray-800">
              S/. {{ formatearMonto(totalSaldoFinal) }}
            </p>
          </div>
        </div>
      </div>
    </div>

    <!-- Sección de Reporte de Ventas por Vendedor -->
    <div class="bg-white p-4 rounded-lg shadow-sm mt-4">
      <h3 class="text-sm font-semibold mb-3 text-gray-800">Reporte de Ventas por Vendedor</h3>
      
      <!-- Filtros -->
      <div class="flex gap-4 mb-4">
        <div class="flex-1">
          <label class="block text-xs text-gray-600 mb-1">
            Seleccionar Vendedor
          </label>
          <select 
            v-model="vendedorSeleccionado"
            class="w-full text-sm rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 p-1.5"
          >
            <option value="">Seleccione un vendedor</option>
            <option v-for="vendedor in datosCaja" :key="vendedor.Vendedor_ID" :value="vendedor.Vendedor_ID">
              {{ vendedor.Vendedor }}
            </option>
          </select>
        </div>
        <div class="flex-1">
          <label class="block text-xs text-gray-600 mb-1">
            Seleccionar Fecha
          </label>
          <input 
            type="date" 
            v-model="fechaVentas"
            class="w-full text-sm rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 p-1.5"
          >
        </div>
        <button 
          @click="cargarVentasVendedor"
          :disabled="!vendedorSeleccionado || generando"
          class="bg-indigo-500 hover:bg-indigo-600 text-white text-xs py-1.5 px-3 rounded transition self-end">
          Buscar Ventas
        </button>
      </div>

      <!-- Tabla de ventas -->
      <div class="overflow-x-auto border rounded-lg">
        <div v-if="!ventasVendedor.length" class="text-center text-gray-500 py-3 text-sm">
          {{ vendedorSeleccionado ? 'No hay ventas para mostrar' : 'Seleccione un vendedor para ver sus ventas' }}
        </div>
        <table v-else class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cliente</th>
              <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">DNI</th>
              <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Detalle</th>
              <th class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Tipo Pago</th>
              <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr v-for="venta in ventasVendedor" :key="venta.Numero_Venta" class="hover:bg-gray-50">
              <td class="px-4 py-2 text-xs text-gray-900">{{ venta.Cliente }}</td>
              <td class="px-4 py-2 text-xs text-gray-900">{{ venta.DNI_Cliente }}</td>
              <td class="px-4 py-2 text-xs text-gray-900">
                <div class="max-w-md truncate" :title="venta.Detalle_Productos">
                  {{ venta.Detalle_Productos }}
                </div>
              </td>
              <td class="px-4 py-2 text-xs text-gray-900 text-center">{{ venta.Tipo_de_Pago }}</td>
              <td class="px-4 py-2 text-xs text-gray-900 text-right">S/. {{ formatearMonto(venta.Monto_Total) }}</td>
            </tr>
          </tbody>
          <tfoot class="bg-gray-50">
            <tr>
              <td colspan="4" class="px-4 py-2 text-xs font-medium text-gray-900">Total Vendido:</td>
              <td class="px-4 py-2 text-xs font-medium text-gray-900 text-right">
                S/. {{ formatearMonto(totalVentasVendedor) }}
              </td>
            </tr>
          </tfoot>
        </table>
      </div>
    </div>
    
    <!-- Mensaje de error -->
    <div v-if="error" class="mt-3 p-3 bg-red-100 border border-red-400 text-red-700 rounded text-sm">
      {{ error }}
    </div>

    <!-- Indicador de carga -->
    <div v-if="generando" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center">
      <div class="bg-white p-4 rounded-lg shadow-lg">
        <p class="text-gray-800 text-sm">Cargando datos...</p>
      </div>
    </div>
  </div>
</template>
<script>
import axios from 'axios';

export default {
  name: 'GenerarReportes',
  data() {
    return {
      error: null,
      generando: false,
      fechaCaja: this.getFechaActual(),
      datosCaja: [],
      vendedorSeleccionado: '',
      fechaVentas: this.getFechaActual(),
      ventasVendedor: []
    }
  },
  computed: {
    totalSaldoFinal() {
      return this.datosCaja.reduce((total, caja) => total + parseFloat(caja.Saldo_Final), 0);
    },
    totalVentasVendedor() {
      return this.ventasVendedor.reduce((total, venta) => total + parseFloat(venta.Monto_Total), 0);
    }
  },
  watch: {
    fechaCaja: {
      handler(newVal) {
        this.cargarDatosCaja();
      },
      immediate: true
    }
  },
  methods: {
    getFechaActual() {
      const fecha = new Date();
      fecha.setHours(fecha.getHours() - 5); // Ajuste para hora Perú
      return fecha.toISOString().split('T')[0];
    },

    formatearMonto(monto) {
      return Number(monto).toFixed(2);
    },

    async cargarDatosCaja() {
      try {
        const response = await axios.get(`/datos-caja/${this.fechaCaja}`);
        this.datosCaja = response.data;
        this.error = null;
      } catch (error) {
        console.error('Error al cargar datos:', error);
        this.datosCaja = [];
        this.error = 'Error al cargar los datos de caja';
      }
    },

    async cargarVentasVendedor() {
      if (!this.vendedorSeleccionado) return;
      
      this.generando = true;
      try {
        const response = await axios.get(`/ventas-vendedor/${this.vendedorSeleccionado}/${this.fechaVentas}`);
        if (response.data.success) {
          this.ventasVendedor = response.data.ventas;
          this.error = null;
        } else {
          this.ventasVendedor = [];
          throw new Error(response.data.message);
        }
      } catch (error) {
        console.error('Error:', error);
        this.ventasVendedor = [];
        this.error = error.response?.data?.message || 'Error al cargar las ventas del vendedor';
      } finally {
        this.generando = false;
      }
    },

    async generarReporteCompras(tipo) {
      this.error = null;
      this.generando = true;

      try {
        const response = await axios({
          url: `/generar-reporte-compras/${tipo}`,
          method: 'GET',
          responseType: 'blob',
          headers: {
            'Accept': 'application/pdf'
          }
        });

        const blob = new Blob([response.data], { type: 'application/pdf' });
        const url = window.URL.createObjectURL(blob);
        const link = document.createElement('a');
        link.href = url;
        link.download = `reporte_compras_${tipo}.pdf`;
        link.click();
        window.URL.revokeObjectURL(url);
      } catch (error) {
        console.error('Error:', error);
        this.error = 'Error al generar el reporte de compras. Por favor, intente nuevamente.';
      } finally {
        this.generando = false;
      }
    },

    async generarReporteCajas() {
      this.error = null;
      this.generando = true;

      try {
        const fechaSeleccionada = new Date(this.fechaCaja);
        const fechaActual = new Date();
        
        if (fechaSeleccionada > fechaActual) {
          throw new Error('No se puede generar reportes para fechas futuras');
        }

        const response = await axios({
          url: `/generar-reporte-cajas/${this.fechaCaja}`,
          method: 'GET',
          responseType: 'blob',
          headers: {
            'Accept': 'application/pdf'
          }
        });

        if (response.data.size === 0) {
          throw new Error('No se generó el reporte correctamente');
        }

        const blob = new Blob([response.data], { type: 'application/pdf' });
        const url = window.URL.createObjectURL(blob);
        const link = document.createElement('a');
        link.href = url;
        link.download = `reporte_cajas_${this.fechaCaja}.pdf`;
        link.click();
        window.URL.revokeObjectURL(url);
      } catch (error) {
        console.error('Error:', error);
        this.error = error.message || 'Error al generar el reporte de cajas. Por favor, intente nuevamente.';
      } finally {
        this.generando = false;
      }
    }
  }
}
</script>

<style scoped>
.transition {
  transition: all 0.3s ease;
}

button:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

table {
  border-collapse: collapse;
  width: 100%;
}

.max-w-md {
  max-width: 28rem;
}

.truncate {
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}
</style>