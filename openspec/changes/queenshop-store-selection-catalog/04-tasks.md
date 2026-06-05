# Tareas: Menú de Selección de Tienda y Catálogo de Zapatos

## Task 1: Landing Page — Store Selection
- [x] Crear `app/controllers/StoreSelectionController.php` con método index()
- [x] Crear `app/views/store-selection/index.php` con dos tarjetas (QueenShop + WolfStor)
- [x] Actualizar `index.php`: ruta `/` → StoreSelectionController, auth guard excluye `/`
- [x] Agregar helper `current_store_preselected()` en auth.php

## Task 2: Login/Register Contextual
- [x] Modificar `AuthController@loginForm` para mostrar branding de store_preselected
- [x] Modificar `AuthController@login` para validar acceso a store_preselected
- [x] Modificar `AuthController@registerForm` para mostrar branding
- [x] Modificar `AuthController@register` para ligar nuevo usuario a store_preselected
- [x] Agregar botón "Volver" en `auth/layout.php`
- [x] Actualizar `auth/login.php` y `auth/register.php` para usar branding contextual

## Task 3: Store Switch = Logout
- [x] Modificar `SwitchController@switch` para destruir sesión y redirect a landing
- [x] Actualizar navbar en `main.php`: "Cambiar tienda" → logout + landing
- [x] Actualizar `AuthController@logout` para redirect a landing

## Task 4: Schema Migration — Shoe Attributes
- [x] Agregar migración `008_shoe_attributes` en `config/database.php`
- [x] Ejecutar migración para agregar columnas color, brand, gender, boot_type

## Task 5: WolfStor Product Form — Extra Fields
- [x] Modificar `ProductController@create` para pasar available_brands y shoe_mode
- [x] Modificar `ProductController@edit` igual
- [x] Modificar `ProductController@store` y `@update` para guardar nuevos campos
- [x] Actualizar `products/create.php` con campos condicionales para WolfStor
- [x] Actualizar `products/edit.php` con campos condicionales para WolfStor

## Task 6: Dynamic Product Filters
- [x] Modificar `ProductController@index` para detectar WolfStor y recopilar filtros
- [x] Agregar queries DISTINCT en Product model para colores, marcas
- [x] Actualizar `products/index.php` con filtros dinámicos (color, marca, género, tipo)
- [x] Actualizar query de búsqueda en Product model para soportar multi-filtros

## Task 7: Text Visibility Fix
- [x] Mejorar tabla de productos con table-dark, mejor contraste
- [x] Mejorar tabla de clientes con mismo tratamiento
- [x] Asegurar badges y precios visibles en tema oscuro

## Task 8: Verify & Commit
- [ ] Probar landing page (sin login)
- [ ] Probar login contextual (QueenShop y WolfStor)
- [ ] Probar "Volver" desde login
- [ ] Probar "Cambiar tienda" desde navbar
- [ ] Probar registro de nuevo usuario con tienda preseleccionada
- [ ] Probar creación de producto con atributos de zapato
- [ ] Probar filtros dinámicos
- [ ] Commit y push
