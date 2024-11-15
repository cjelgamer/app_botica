# App Botica

Bienvenido a **App Botica**, una aplicación web desarrollada para la gestión de ventas y control de inventario en una botica.

Este README te guiará paso a paso para instalar y configurar el proyecto en tu entorno local. ¡Comencemos!

## 🚀 Requisitos Previos

Asegúrate de tener instalados los siguientes componentes:

- PHP >= 7.3
- Composer
- Node.js y npm
- MySQL (gestor de bases de datos compatible)

## 📥 Paso 1: Clonar el Repositorio

Clona el repositorio de GitHub usando el siguiente comando:

```
git clone https://github.com/cjelgamer/app_botica.git
```

## 📦 Paso 2: Instalar Dependencias
Instalar Dependencias de PHP
Ejecuta el siguiente comando para instalar las dependencias de PHP con Composer:


Copiar código
```
composer install
```
Instalar Dependencias de Node
Ejecuta el siguiente comando para instalar las dependencias de Node.js:

bash
Copiar código
```
npm install
```

## 🗄️ Paso 3: Configurar la Base de Datos
Crear la base de datos: Utiliza el archivo create_boticadb.sql para crear la base de datos requerida en MySQL.

Configura el archivo .env con tus credenciales de base de datos. Asegúrate de tener algo similar a esto en tu archivo .env:

```
Copiar código
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=boticadb
DB_USERNAME=tu_usuario
DB_PASSWORD=tu_contraseña
```

## 🔄 Paso 4: Ejecutar las Migraciones
Ejecuta las migraciones para crear las tablas en la base de datos:


Copiar código
```
php artisan migrate
```
🔐 Paso 5: Insertar Datos de Prueba
Para poner a prueba la aplicación, inserta algunos datos iniciales, incluyendo usuarios con roles específicos, ejecutando el siguiente comando:


Copiar código
```
php artisan serve encrypt:insert-passwords
```
Usuario de Prueba
Un usuario administrador predefinido está disponible para iniciar sesión en la aplicación:
```
Nombre de usuario: Cristian
Contraseña: admin
```
## 🚦 Paso 6: Configurar Vue Router
Para el enrutamiento de la aplicación, asegúrate de tener Vue Router instalado. Ejecuta este comando en la raíz de tu proyecto:

Copiar código
```
npm install vue-router
```

## 🏗️ Paso 7: Compilar Activos y Ejecutar el Servidor de Desarrollo
Compila los activos con Vite o Laravel Mix:

Copiar código
```
npm run dev
```
Luego, ejecuta el servidor de desarrollo de Laravel:

Copiar código
```
php artisan serve
```


Ahora, la aplicación estará disponible en:
```
http://localhost:8000.
```

## 🎉 ¡Todo Listo!
Tu aplicación debería estar corriendo correctamente. Accede a la URL proporcionada y usa las credenciales del administrador para iniciar sesión.

## 💡 Notas Adicionales
Asegúrate de revisar los permisos de escritura en las carpetas de almacenamiento (storage) y en la carpeta bootstrap/cache.
Considera utilizar Laravel Sail para ejecutar el proyecto en Docker si prefieres un entorno más aislado.
Para el despliegue en producción, recuerda optimizar con php artisan optimize y configurar el entorno correctamente.
¡Gracias por usar App Botica!

