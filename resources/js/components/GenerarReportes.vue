<template>
  <div class="generar-reportes p-4">
      <h2>Generar Reportes</h2>
      <a href="/generar-reporte-compras" 
           class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded inline-block">
            Generar Reporte de Compras
      </a>
      
      <div v-if="error" class="text-red-600 mt-2">{{ error }}</div>
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
      async generarReporteCompras() {
          this.error = null;
          this.generando = true;

          try {
              const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
              
              const response = await axios({
                  url: '/generar-reporte-compras',
                  method: 'POST',
                  responseType: 'blob',
                  headers: {
                      'X-CSRF-TOKEN': token,
                      'Content-Type': 'application/json',
                      'Accept': 'application/pdf'
                  }
              });

              const blob = new Blob([response.data], { type: 'application/pdf' });
              const url = window.URL.createObjectURL(blob);
              const link = document.createElement('a');
              link.href = url;
              link.download = 'reporte_compras.pdf';
              link.click();
              
              window.URL.revokeObjectURL(url);
          } catch (error) {
              console.error('Error:', error);
              this.error = 'Error al generar el reporte';
          } finally {
              this.generando = false;
          }
      }
  }
}
</script>

<style scoped>
.generar-reportes {
  padding: 20px;
}

.botones-reportes {
  margin-top: 20px;
}

.btn-reporte {
  padding: 10px 20px;
  background-color: #4CAF50;
  color: white;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  font-size: 16px;
}

.btn-reporte:hover {
  background-color: #45a049;
}

.error-mensaje {
  margin-top: 20px;
  padding: 10px;
  background-color: #ffebee;
  color: #c62828;
  border: 1px solid #ef9a9a;
  border-radius: 4px;
}
</style>