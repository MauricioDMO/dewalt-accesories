# DeWALT Accesorios - Sistema CRUD con Laravel

Sistema completo de gestión de accesorios DeWALT con panel de administración y catálogo público.

## 🚀 Características

- ✅ Migración completa de datos desde JSON a SQLite
- ✅ Panel de administración con autenticación
- ✅ CRUD completo de accesorios
- ✅ Catálogo público con filtros y búsqueda
- ✅ Gestión de categorías y subcategorías
- ✅ Soporte para precios con ofertas
- ✅ Diseño responsivo con Tailwind CSS
- ✅ Imágenes desde https://construfijaciones.com

## 📊 Base de Datos

Se han creado las siguientes tablas:

- **categories**: Categorías de productos
- **subcategories**: Subcategorías asociadas a categorías
- **accessories**: Productos con todos sus atributos (incluyendo campo opcional `offer`)
- **admins**: Usuarios administradores

### Estructura del modelo Accessory

```php
- code (único)
- name
- description
- price
- offer (opcional) ← Precio de oferta
- image
- alt
- type
- units (boolean)
- category_id
- subcategory_id
```

## 🔧 Instalación (Proyecto Clonado)

Si clonaste el proyecto y no tiene datos, sigue estos pasos:

### Windows (PowerShell):
```powershell
# 1. Instalar dependencias de PHP
composer install

# 2. Crear el archivo de configuración
copy .env.example .env

# 3. Generar la clave de la aplicación
php artisan key:generate

# 4. Crear el archivo de base de datos SQLite
New-Item -ItemType File -Path database/database.sqlite -Force

# 5. Ejecutar las migraciones (crea las tablas)
php artisan migrate

# 6. Migrar el JSON a la base de datos
php artisan db:seed

# 7. Iniciar el servidor
php artisan serve
```

### Linux/Mac (Bash):
```bash
# 1. Instalar dependencias de PHP
composer install

# 2. Crear el archivo de configuración
cp .env.example .env

# 3. Generar la clave de la aplicación
php artisan key:generate

# 4. Crear el archivo de base de datos SQLite
touch database/database.sqlite

# 5. Ejecutar las migraciones (crea las tablas)
php artisan migrate

# 6. Migrar el JSON a la base de datos
php artisan db:seed

# 7. Iniciar el servidor
php artisan serve
```

### ¿Qué hace cada comando?

- **`composer install`**: Instala todas las dependencias de Laravel
- **`copy .env.example .env`**: Crea el archivo de configuración
- **`php artisan key:generate`**: Genera una clave única para la aplicación
- **`New-Item database/database.sqlite`**: Crea el archivo de base de datos vacío
- **`php artisan migrate`**: Crea todas las tablas (categories, subcategories, accessories, admins)
- **`php artisan db:seed`**: **Migra los 304 productos del archivo `resources/accesories.json` a la base de datos**
- **`php artisan serve`**: Inicia el servidor en http://127.0.0.1:8000

## 📁 Archivo de Datos

El archivo JSON original se encuentra en:
```
resources/accesories.json
```

Contiene **304 accesorios** organizados en **6 categorías** con sus respectivas subcategorías.

## 🔧 Configuración de Base de Datos

El archivo `.env` está configurado para SQLite:
```
DB_CONNECTION=sqlite
```

El archivo de base de datos se crea automáticamente en:
```
database/database.sqlite
```

## 👤 Credenciales de Administrador

Después de ejecutar `php artisan db:seed`, se crea un usuario administrador:

- **Usuario**: `admin`
- **Contraseña**: `admin`

## 🌐 Rutas del Sistema

### Rutas Públicas (Cliente)
- `GET /` - Catálogo de productos
- `GET /accesorio/{id}` - Detalle del producto

### Rutas de Administración
- `GET /login` - Login de administrador
- `POST /login` - Autenticación
- `POST /logout` - Cerrar sesión
- `GET /admin/dashboard` - Panel principal
- `GET /admin/accesorios` - Lista de accesorios
- `GET /admin/accesorios/crear` - Crear accesorio
- `POST /admin/accesorios` - Guardar accesorio
- `GET /admin/accesorios/{id}/editar` - Editar accesorio
- `PUT /admin/accesorios/{id}` - Actualizar accesorio
- `DELETE /admin/accesorios/{id}` - Eliminar accesorio

## 🔐 Credenciales de Administrador

```
Usuario: admin
Contraseña: admin
```

**Nota**: Las credenciales están hardcoded en el seeder para facilitar el proyecto de ejemplo.

## 🎨 Funcionalidades del Cliente

### Catálogo
- Búsqueda por nombre o código
- Filtro por categoría
- Ordenamiento por:
  - Nombre A-Z
  - Precio menor a mayor
  - Precio mayor a menor
- Paginación
- Vista de ofertas destacadas

### Detalle del Producto
- Imagen del producto
- Información completa
- Precio normal y precio de oferta (si aplica)
- Porcentaje de descuento
- Productos relacionados
- Breadcrumb de navegación

## 🛠️ Funcionalidades del Administrador

### Dashboard
- Total de accesorios
- Total de categorías
- Total de subcategorías
- Lista de accesorios recientes

### Gestión de Accesorios
- Crear nuevo accesorio
- Editar accesorio existente
- Eliminar accesorio
- Carga dinámica de subcategorías según categoría
- Validación de formularios
- Soporte para precios con oferta

## 🖼️ Manejo de Imágenes

Las imágenes se almacenan como rutas relativas en la base de datos (ej: `/products/accessory/DW8424.webp`) y se convierten automáticamente a URLs completas usando el dominio `https://construfijaciones.com`.

El modelo `Accessory` incluye un accessor `image_url` que:
- Retorna la URL completa: `https://construfijaciones.com/products/accessory/DW8424.webp`
- Maneja imágenes vacías
- Permite URLs completas si ya incluyen http/https

## 📱 Diseño Responsivo

El sistema está completamente optimizado para:
- 📱 Móviles
- 📱 Tablets
- 💻 Escritorio

Utilizando Tailwind CSS con sistema de grillas adaptativas.

## 🎯 Características Especiales

### Campo `offer` (Oferta)
- Campo **opcional** en la tabla accessories
- Si existe, se muestra el precio tachado y el precio de oferta en rojo
- Se calcula automáticamente el porcentaje de descuento
- Badge "OFERTA" en el catálogo
- Método helper `hasOffer()` en el modelo

### Relaciones
- Category hasMany Accessories
- Category hasMany Subcategories
- Subcategory belongsTo Category
- Subcategory hasMany Accessories
- Accessory belongsTo Category
- Accessory belongsTo Subcategory

## 📦 Tecnologías Utilizadas

- Laravel 11.x
- SQLite
- Tailwind CSS (via CDN)
- PHP 8.2+
- Blade Templates

## 🎓 Datos Migrados

✅ **304 accesorios** migrados exitosamente desde `resources/accesories.json`  
✅ **6 categorías** únicas identificadas y creadas  
✅ **Múltiples subcategorías** asociadas correctamente  
✅ Todos los campos migrados incluyendo el campo opcional `offer`

## 📝 Notas Importantes

1. Las credenciales de admin están en el código para facilitar el uso en ambiente de desarrollo
2. Las imágenes apuntan a https://construfijaciones.com
3. La base de datos es SQLite (archivo: `database/database.sqlite`)
4. El seeder es idempotente (se puede ejecutar múltiples veces)
5. El campo `offer` es completamente opcional y puede ser null

## 🚦 Cómo Usar

1. **Visitar el catálogo**: http://127.0.0.1:8000
2. **Acceder al admin**: http://127.0.0.1:8000/login
3. **Ingresar credenciales**: admin / admin
4. **Gestionar productos** desde el panel de administración

---

Desarrollado como proyecto de ejemplo para PROPOSICION DE LENGUAJES DE PROGRAMACION PARA NEGOCIOS
