<template>
    <div class="generar-reportes">
      <h2>Generar Reportes</h2>
      <div class="botones-reportes">
        <button @click="generarReporteCompras">Generar Reporte de Compras</button>
        <button @click="generarReporteVentas">Generar Reporte de Ventas</button>
      </div>
    </div>
  </template>
  
  <script>
  import axios from 'axios';
  
  export default {
    name: 'GenerarReportes',
    methods: {
      generarReporteCompras() {
        console.log('Generando reporte de compras...');
        axios.get('/reporte-compras', { responseType: 'blob' })
          .then(response => {
            const url = window.URL.createObjectURL(new Blob([response.data]));
            const link = document.createElement('a');
            link.href = url;
            link.setAttribute('download', 'reporte_compras.pdf');
            document.body.appendChild(link);
            link.click();
          })
          .catch(error => {
            console.error('Error al generar el reporte de compras:', error);
          });
      },
      generarReporteVentas() {
        console.log('Generando reporte de ventas...');
        axios.get('/reporte-ventas', { responseType: 'blob' })
          .then(response => {
            const url = window.URL.createObjectURL(new Blob([response.data]));
            const link = document.createElement('a');
            link.href = url;
            link.setAttribute('download', 'reporte_ventas.pdf');
            document.body.appendChild(link);
            link.click();
          })
          .catch(error => {
            console.error('Error al generar el reporte de ventas:', error);
          });
      }
    }
  };
  </script>
  
  
  <style scoped>
  .generar-reportes {
    text-align: center;
    margin-top: 20px;
  }
  
  .botones-reportes {
    display: flex;
    justify-content: center;
    gap: 20px;
    margin-top: 20px;
  }
  
  button {
    padding: 10px 20px;
    font-size: 16px;
    background-color: #4CAF50;
    color: white;
    border: none;
    border-radius: 5px;
    cursor: pointer;
  }
  </style>