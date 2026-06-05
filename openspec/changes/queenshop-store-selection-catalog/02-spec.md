# Especificación: Menú de Selección de Tienda y Catálogo de Zapatos

## Resumen

QueenShop tendrá un selector de tienda público, login contextual, cambio de tienda con re-login, y WolfStor tendrá schema de zapatos con filtros dinámicos.

## Requerimientos Funcionales

### RF1: Landing Page — Selector de Tiendas
- **Ruta**: `GET /` → `StoreSelectionController@index`
- Muestra dos tarjetas grandes con logo, nombre, color temático y descripción
- QueenShop: #ffc107 (amarillo), logo pet shop
- WolfStor: #2563eb (azul), logo zapato
- Al hacer clic: guarda `$_SESSION['store_preselected']` y redirige a `/login`

### RF2: Login Contextual
- **Ruta**: `GET /login` + `POST /login`
- Si hay `store_preselected` en sesión, muestra el branding de esa tienda
- Valida que el usuario tenga acceso a la tienda seleccionada
- "Volver" en login → destruye store_preselected y redirige a `/`

### RF3: Register Contextual
- **Ruta**: `GET /register` + `POST /register`
- Misma lógica de tienda preseleccionada
- Al crear cuenta, asigna la tienda seleccionada como company_id
- "Volver" → mismo comportamiento que login

### RF4: Cambio de Tienda = Logout
- **Ruta**: `GET /switch-store/{id}`
- Destruye la sesión completa
- Guarda flash message y redirige a `/`
- Navbar: "Cambiar tienda" → `/switch-store/0` o `/logout?redirect=store`

### RF5: Schema de Zapatos
- Migración `008_shoe_attributes`:
  - `color VARCHAR(100) DEFAULT ''`
  - `brand VARCHAR(100) DEFAULT ''`  
  - `gender VARCHAR(20) DEFAULT ''`
  - `boot_type VARCHAR(20) DEFAULT ''`

### RF6: Formulario de Producto con Campos Extra
- Cuando `current_theme_class() === 'wolfstor'`, mostrar campos adicionales:
  - Color: input text
  - Marca: select con opciones predefinidas
  - Género: radio buttons (Hombre / Mujer / Unisex)
  - Tipo: radio buttons (Bota / Normal)

### RF7: Filtros Dinámicos en Productos
- Cuando WolfStor, mostrar filtros extra:
  - Color: checkboxes generados de SELECT DISTINCT color FROM products
  - Marca: checkboxes generados de SELECT DISTINCT brand FROM products
  - Género: checkboxes (Hombre, Mujer, Unisex)
  - Tipo: checkboxes (Bota, Normal)
- Multi-select con arrays tipo `colors[]`
- Compatible con filtros existentes (nombre, categoría, stock)

### RF8: Visibilidad de Textos
- Tabla de productos: usar `table-dark` mejorado, badges con más contraste
- Tabla de clientes: mismo tratamiento
- Texto de celdas en blanco (#f8f9fa) sobre fondo oscuro

## Escenarios

### Escenario 1: Usuario elige QueenShop y accede
1. Usuario visita `/` → ve dos tarjetas
2. Click en QueenShop → redirige a `/login` con branding QueenShop
3. Login con usuario "norte" (tiene acceso a QueenShop) → dashboard de QueenShop
4. Navbar muestra "Cambiar tienda"
5. Click "Cambiar tienda" → logout → landing page

### Escenario 2: Usuario elige WolfStor
1. Landing → click WolfStor
2. Login con usuario "QueenShop" (único usuario de WolfStor) → dashboard WolfStor

### Escenario 3: Producto de zapatos con atributos
1. En WolfStor, crear producto → formulario muestra color, marca, género, tipo
2. Guardar → producto tiene atributos en BD
3. Lista de productos → filtros dinámicos muestran colores/marcas disponibles
4. Filtrar por "Adidas" + "Hombre" → solo muestra zapatos Adidas para hombre
