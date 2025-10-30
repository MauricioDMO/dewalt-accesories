# Documentación Técnica - DeWALT Accesorios

## 📁 Estructura del Proyecto

```
dewalt-accesories/
├── app/
│   ├── Http/
│   │   └── Controllers/
│   │       ├── AuthController.php      # Autenticación de admin
│   │       ├── AdminController.php     # CRUD de accesorios
│   │       └── ClientController.php    # Catálogo público
│   └── Models/
│       ├── Accessory.php              # Modelo principal con campo offer
│       ├── Admin.php                  # Modelo de administrador
│       ├── Category.php               # Categorías
│       └── Subcategory.php            # Subcategorías
│
├── database/
│   ├── migrations/
│   │   ├── 2024_01_01_000003_create_categories_table.php
│   │   ├── 2024_01_01_000004_create_subcategories_table.php
│   │   ├── 2024_01_01_000005_create_accessories_table.php  # Incluye campo offer
│   │   └── 2024_01_01_000006_create_admins_table.php
│   └── seeders/
│       ├── AccessoriesSeeder.php      # Migra datos del JSON
│       └── DatabaseSeeder.php
│
├── resources/
│   ├── accesories.json                # Datos originales
│   └── views/
│       ├── layouts/
│       │   ├── app.blade.php          # Layout cliente
│       │   └── admin.blade.php        # Layout admin
│       ├── auth/
│       │   └── login.blade.php        # Login admin
│       ├── admin/
│       │   ├── dashboard.blade.php
│       │   └── accessories/
│       │       ├── index.blade.php    # Lista CRUD
│       │       ├── create.blade.php   # Formulario crear
│       │       └── edit.blade.php     # Formulario editar
│       └── client/
│           ├── index.blade.php        # Catálogo
│           └── show.blade.php         # Detalle producto
│
└── routes/
    └── web.php                        # Todas las rutas
```

## 🗃️ Esquema de Base de Datos

### Tabla: accessories
```sql
CREATE TABLE accessories (
    id INTEGER PRIMARY KEY,
    code VARCHAR UNIQUE NOT NULL,
    name VARCHAR NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    offer DECIMAL(10,2) NULL,           -- Campo opcional para ofertas
    image VARCHAR,
    alt VARCHAR,
    type VARCHAR,
    units BOOLEAN DEFAULT 0,
    category_id INTEGER,
    subcategory_id INTEGER,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id),
    FOREIGN KEY (subcategory_id) REFERENCES subcategories(id)
);
```

### Tabla: categories
```sql
CREATE TABLE categories (
    id INTEGER PRIMARY KEY,
    name VARCHAR UNIQUE NOT NULL,
    slug VARCHAR UNIQUE NOT NULL,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

### Tabla: subcategories
```sql
CREATE TABLE subcategories (
    id INTEGER PRIMARY KEY,
    name VARCHAR NOT NULL,
    slug VARCHAR NOT NULL,
    category_id INTEGER NOT NULL,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id),
    UNIQUE(category_id, slug)
);
```

### Tabla: admins
```sql
CREATE TABLE admins (
    id INTEGER PRIMARY KEY,
    username VARCHAR UNIQUE NOT NULL,
    password VARCHAR NOT NULL,
    name VARCHAR NOT NULL,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

## 🔄 Flujo de Datos

### Migración de JSON a SQLite

```
resources/accesories.json
        ↓
AccessoriesSeeder::run()
        ↓
1. Lee el JSON
2. Extrae categorías únicas
3. Extrae subcategorías por categoría
4. Crea registros en categories
5. Crea registros en subcategories
6. Crea registros en accessories (con offer opcional)
7. Crea usuario admin
        ↓
Base de datos SQLite poblada
```

## 🎯 Modelo Accessory - Métodos Importantes

### Accessors

```php
// Retorna URL completa de la imagen
public function getImageUrlAttribute()
{
    if (empty($this->image)) {
        return null;
    }
    
    if (str_starts_with($this->image, 'http')) {
        return $this->image;
    }
    
    return 'https://construfijaciones.com' . $this->image;
}

// Retorna precio final (oferta o precio normal)
public function getFinalPriceAttribute()
{
    return $this->offer ?? $this->price;
}
```

### Helpers

```php
// Verifica si el producto tiene oferta
public function hasOffer()
{
    return $this->offer !== null && $this->offer > 0;
}
```

### Uso en Blade

```blade
{{-- URL de imagen --}}
<img src="{{ $accessory->image_url }}" alt="{{ $accessory->alt }}">

{{-- Mostrar precio con oferta --}}
@if($accessory->hasOffer())
    <span class="line-through">${{ number_format($accessory->price, 2) }}</span>
    <span class="text-red-600">${{ number_format($accessory->offer, 2) }}</span>
@else
    ${{ number_format($accessory->price, 2) }}
@endif

{{-- Precio final --}}
${{ number_format($accessory->final_price, 2) }}
```

## 🔐 Sistema de Autenticación

El sistema usa **sesiones nativas de Laravel** sin Laravel Breeze/Fortify:

### AuthController

```php
// Login
public function login(Request $request)
{
    $admin = Admin::where('username', $request->username)->first();
    
    if ($admin && Hash::check($request->password, $admin->password)) {
        session(['admin_id' => $admin->id, 'admin_name' => $admin->name]);
        return redirect()->route('admin.dashboard');
    }
    
    return back()->withErrors(['username' => 'Credenciales incorrectas']);
}

// Verificación en controladores
if (!session('admin_id')) {
    return redirect()->route('login');
}
```

## 📊 Datos Estadísticos

Después de ejecutar el seeder:

- **304 accesorios** migrados
- **6 categorías** creadas:
  - Brocas
  - Discos
  - Lijas, Cepillos y Más
  - Accesorios para Construcción
  - Organizadores y Almacenamiento
  - Otros

- **Subcategorías** variadas según categoría (ej: "Desbaste", "Corte", etc.)
- **1 usuario admin** con credenciales admin/admin

## 🎨 Diseño UI/UX

### Colores del Brand
- Amarillo principal: `#FACC15` (yellow-400)
- Negro: `#000000`
- Gris claro: `#F3F4F6` (gray-100)
- Rojo para ofertas: `#DC2626` (red-600)

### Componentes Principales

1. **Cards de Producto**
   - Imagen responsive
   - Badge de oferta
   - Precio normal y oferta
   - Hover con sombra

2. **Formularios**
   - Validación client-side y server-side
   - Carga dinámica de subcategorías (AJAX)
   - Mensajes de error inline

3. **Tablas Admin**
   - Paginación
   - Acciones (Editar/Eliminar)
   - Vista previa de imagen

## 🔧 Personalización

### Agregar nuevos campos al Accessory

1. Crear migración:
```bash
php artisan make:migration add_new_field_to_accessories_table
```

2. Agregar campo en migración:
```php
$table->string('nuevo_campo')->nullable();
```

3. Agregar a `$fillable` en modelo:
```php
protected $fillable = [
    // ... campos existentes
    'nuevo_campo',
];
```

4. Actualizar formularios y vistas

### Cambiar dominio de imágenes

Editar `app/Models/Accessory.php`:

```php
return 'https://tudominio.com' . $this->image;
```

## 📝 Comandos Útiles

```bash
# Recrear base de datos
php artisan migrate:fresh --seed

# Solo ejecutar seeder
php artisan db:seed

# Limpiar cache
php artisan cache:clear
php artisan config:clear
php artisan view:clear

# Ver rutas
php artisan route:list

# Iniciar servidor
php artisan serve
```

## ⚠️ Consideraciones de Producción

Para llevar a producción, considerar:

1. **Seguridad**
   - Mover credenciales a variables de entorno
   - Implementar middleware de autenticación robusto
   - Usar políticas de autorización

2. **Base de datos**
   - Cambiar a MySQL/PostgreSQL para mayor rendimiento
   - Implementar índices en campos frecuentes

3. **Imágenes**
   - Implementar upload de imágenes
   - Usar storage de Laravel
   - Optimizar y comprimir imágenes

4. **Performance**
   - Implementar caché de consultas
   - Lazy loading de relaciones
   - Paginación optimizada

## 📚 Referencias

- [Documentación Laravel](https://laravel.com/docs)
- [Tailwind CSS](https://tailwindcss.com)
- [Laravel Blade](https://laravel.com/docs/blade)
- [Eloquent ORM](https://laravel.com/docs/eloquent)
