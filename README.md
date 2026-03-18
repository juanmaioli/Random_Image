# 🖼️ rnd_img - Servidor de Imágenes Aleatorias

<div align="center">
  <img src="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><text y=%22.9em%22 font-size=%2290%22>🖼️</text></svg>" width="100" height="100" alt="Logo">
  <p align="center">Sistema dinámico para servir y gestionar colecciones de imágenes categorizadas.</p>
</div>

---

## 🚀 Descripción General
**rnd_img** es una solución ligera basada en **PHP** y **MySQL/MariaDB** diseñada para servir imágenes de forma aleatoria a través de una API sencilla. El sistema garantiza una distribución equitativa de las imágenes mediante el seguimiento de "exposiciones" y permite una gestión administrativa eficiente para la carga y categorización de nuevos activos.

### ✨ Características Principales:
- **🔀 Selección Inteligente**: Algoritmo que prioriza imágenes menos vistas para maximizar la variedad.
- **🛡️ Seguridad en Archivos**: Los nombres originales se ocultan mediante hashes **SHA-256** para evitar predecibilidad y colisiones.
- **📊 Registro de Sesiones**: Tracking detallado de cada imagen servida (IP, fecha y acción).
- **⚙️ Ingesta Automatizada**: Procesamiento masivo de imágenes con detección automática de dimensiones.

---

## 🏗️ Estructura del Proyecto

1. **`index.php`**: Punto de entrada para servir imágenes. Soporta el parámetro `?id=categoria`.
2. **`load_images.php`**: Utilidad administrativa para procesar archivos desde `new_images/` hacia `img/`.
3. **`config.php`**: Configuración centralizada de la base de datos.
4. **`admin_img.sql`**: Esquema estructural de la base de datos.
5. **`gallery.php`**: Visualizador de la colección (Galería).

---

## 🛠️ Instalación y Configuración

### 1. ⚙️ Requisitos
- Servidor Web (Apache/Nginx) con soporte PHP 8.x.
- MariaDB o MySQL 5.7+.
- Librería PHP `gd` para detección de dimensiones (opcional pero recomendada).

### 2. 🗄️ Base de Datos
Importa el esquema inicial en tu servidor:
```bash
mysql -u tu_usuario -p admin_img < admin_img.sql
```

### 3. 📝 Configuración
Edita `config.php` con tus credenciales:
```php
$db_server = "tu_host";
$db_user = "tu_usuario";
$db_pass = "tu_password";
$db_name = "admin_img";
```

---

## 📖 Modo de Uso

### Servir una Imagen
Solicita una imagen aleatoria de una categoría específica (ej. `landscape`):
```text
GET /index.php?id=landscape
```
*Si no se especifica ID, se servirá una imagen aleatoria general.*

### Cargar Nuevas Imágenes
1. Sube tus archivos `.jpg` o `.jpeg` a la carpeta `new_images/`.
2. Ejecuta el procesador especificando la categoría de destino:
```text
GET /load_images.php?id=anime
```

---

## 👤 Autor
Desarrollado con ❤️ por **Juan Gabriel Maioli**.

---
*Este proyecto es parte de un ecosistema de microservicios para gestión de contenido multimedia.*
