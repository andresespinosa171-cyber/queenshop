# Especificación de Marca por Empresa (Company Branding)

## Propósito

Cada empresa tiene identidad visual propia (tema, colores, logo, nombre, descripción) cargada desde la base de datos y aplicada mediante CSS custom properties. QueenShop usa amarillo (`#ffc107`), WolfStor usa azul (`#2563eb`).

## Requisitos

### Requisito: Columnas de branding en companies

La tabla `companies` DEBE incluir `theme` (VARCHAR 50), `primary_color` (VARCHAR 7), `logo` (VARCHAR 255), `store_name` (VARCHAR 100), `description` (TEXT).

| # | Escenario | Dado | Cuando | Entonces |
|---|-----------|------|--------|----------|
| B1 | Seed WolfStor | Migración ejecutada | Se siembran datos | Existe empresa "WolfStor" con `theme='wolfstor'`, `primary_color='#2563eb'`, `store_name='WolfStor'` |
| B2 | QueenShop defaults | QueenShop sin tema explícito | Se carga branding | Fallback a `theme='queen'`, `primary_color='#ffc107'` |
| B3 | Login carga branding | Usuario inicia sesión | AuthController ejecuta login | `$_SESSION` recibe `theme`, `primary_color`, `logo`, `store_name`, `description` |

### Requisito: CSS custom properties

El sistema DEBE definir todos los colores temáticos como variables CSS en `:root` (QueenShop). DEBE soportar `body.theme-wolfstor` que sobrescribe las variables para WolfStor.

| # | Escenario | Dado | Cuando | Entonces |
|---|-----------|------|--------|----------|
| B4 | Tema QueenShop por defecto | Body sin clase theme | CSS se aplica | Todos los elementos usan amarillo (#ffc107) |
| B5 | Tema WolfStor activo | Body tiene `class="theme-wolfstor"` | CSS se aplica | Botones, bordes, links usan azul (#2563eb) |
| B6 | Sin colores hardcodeados | style.css auditado | Post-refactor | Cero referencias a `#ffc107` o `#2563eb` fuera de bloques `:root` / `.theme-wolfstor` |

### Requisito: Layout dinámico

Navbar, footer y título de página DEBEN usar `store_name` y `logo` de la sesión.

| # | Escenario | Dado | Cuando | Entonces |
|---|-----------|------|--------|----------|
| B7 | Navbar con marca dinámica | Sesión WolfStor activa | Navbar renderiza | Muestra logo WolfStor + "WolfStor", no "QueenShop" |
| B8 | Título de página | Sesión WolfStor | Página carga | `<title>` contiene "WolfStor" |
| B9 | Footer dinámico | Sesión WolfStor | Footer renderiza | Muestra "WolfStor" |
| B10 | Theme class en body | Sesión tiene `theme='wolfstor'` | Body renderiza | `<body class="theme-wolfstor">` |

### Requisito: Tipo de tienda en registro

El formulario de registro DEBE preguntar "Pet Shop" o "Shoe Store". La selección determina el tema de la empresa.

| # | Escenario | Dado | Cuando | Entonces |
|---|-----------|------|--------|----------|
| B11 | Registro Pet Shop | Usuario selecciona "Pet Shop" | Registro completo | Company tiene `theme='queen'`, `primary_color='#ffc107'` |
| B12 | Registro Shoe Store | Usuario selecciona "Shoe Store" | Registro completo | Company tiene `theme='wolfstor'`, `primary_color='#2563eb'` |

### Requisito: Logo WolfStor SVG

DEBE existir `assets/img/wolfstor-logo.svg` con un icono de zapatería en azul (#2563eb).

| # | Escenario | Dado | Cuando | Entonces |
|---|-----------|------|--------|----------|
| B13 | Logo servido | Archivo existe | Navegador solicita | Devuelve SVG válido con esquema azul |
| B14 | Fallback a QueenShop | WolfStor logo no existe | Auth page renderiza | Usa logo QueenShop por defecto |

### Requisito: Animaciones CSS

El sistema DEBE incluir animaciones: fade-in al cargar página, hover en cards/botones, slide-up en modales.

| # | Escenario | Dado | Cuando | Entonces |
|---|-----------|------|--------|----------|
| B15 | Fade-in en carga | Página carga completamente | CSS animation se aplica | Contenido principal aparece con opacidad 0→1 en 0.3s |
| B16 | Hover en card | Usuario pasa mouse sobre card | :hover se activa | Card se eleva 2px con sombra incrementada |
| B17 | Slide-up en modal | Modal se abre | Bootstrap muestra modal | Modal se desliza hacia arriba desde abajo |
