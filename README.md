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

## 🔧 Instalación

1. **Instalar dependencias**:
```bash
composer install
```

2. **Configurar base de datos**:
El archivo `.env` ya está configurado para SQLite:
```
DB_CONNECTION=sqlite
```

3. **Ejecutar migraciones y seeders**:
```bash
php artisan migrate:fresh --seed
```

Esto creará:
- ✅ Todas las tablas necesarias
- ✅ 304 accesorios migrados desde el JSON
- ✅ 6 categorías
- ✅ Múltiples subcategorías
- ✅ Usuario admin (usuario: **admin**, contraseña: **admin**)

4. **Iniciar servidor**:
```bash
php artisan serve
```

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
