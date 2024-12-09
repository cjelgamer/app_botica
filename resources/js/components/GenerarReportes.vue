<template>
  <div class="generar-reportes p-4">
    <h2 class="text-2xl font-bold mb-6">Generar Reportes de Compras</h2>
    
    <div class="flex gap-4 flex-wrap">
      <button 
        @click="generarReporteCompras('todo')"
        :disabled="generando"
        class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded">
        Generar Reporte Completo
      </button>

      <button 
        @click="generarReporteCompras('semana')"
        :disabled="generando"
        class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded">
        Reporte Últimos 7 Días
      </button>

      <button 
        @click="generarReporteCompras('mes')"
        :disabled="generando"
        class="bg-purple-500 hover:bg-purple-600 text-white font-bold py-2 px-4 rounded">
        Reporte Último Mes
      </button>
    </div>
    
    <div v-if="error" class="mt-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
      {{ error }}
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
    }
  },
  methods: {
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
        this.error = 'Error al generar el reporte. Por favor, intente nuevamente.';
      } finally {
        this.generando = false;
      }
    }
  }
}
</script>