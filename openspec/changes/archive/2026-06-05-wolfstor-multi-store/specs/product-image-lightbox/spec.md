# Especificación de Lightbox para Imágenes (Product Image Lightbox)

## Propósito

Al hacer clic en una imagen de producto en la tabla o vista detalle, se abre un overlay a pantalla completa con la imagen en tamaño completo y zoom.

## Requisitos

### Requisito: Click en imagen abre lightbox

El sistema DEBE abrir un overlay modal al hacer clic en cualquier imagen de producto (`<img>` con clase `product-img-clickable`).

| # | Escenario | Dado | Cuando | Entonces |
|---|-----------|------|--------|----------|
| L1 | Lightbox se abre | Usuario ve lista de productos | Hace clic en imagen de producto | Overlay full-screen aparece con la imagen en tamaño completo |
| L2 | Misma imagen | Producto con imagen JPG | Lightbox se abre | Muestra la misma imagen sin distorsión |
| L3 | Sin imagen | Producto sin imagen (no-image.svg) | Usuario hace clic | Lightbox no se abre (imagen no es clickleable) |

### Requisito: Cerrar lightbox

El lightbox DEBE cerrarse al hacer clic en el botón X, al hacer clic fuera de la imagen, o al presionar Escape.

| # | Escenario | Dado | Cuando | Entonces |
|---|-----------|------|--------|----------|
| L4 | Cerrar con X | Lightbox abierto | Usuario hace clic en X | Lightbox se cierra, overlay desaparece |
| L5 | Cerrar con Escape | Lightbox abierto | Usuario presiona Escape | Lightbox se cierra |
| L6 | Cerrar click fuera | Lightbox abierto | Usuario hace clic en fondo oscuro | Lightbox se cierra |

### Requisito: Zoom y navegación

El lightbox DEBE permitir zoom al hacer clic en la imagen ampliada (toggle entre fit y zoom 2x).

| # | Escenario | Dado | Cuando | Entonces |
|---|-----------|------|--------|----------|
| L7 | Zoom toggle | Lightbox abierto mostrando imagen fit | Usuario hace clic en imagen | Imagen escala a 2x con scroll |
| L8 | Volver a fit | Imagen en zoom 2x | Usuario hace clic de nuevo | Imagen vuelve a fit |

### Requisito: Animación de transición

El lightbox DEBE tener una transición suave de opacidad (fade-in al abrir, fade-out al cerrar).

| # | Escenario | Dado | Cuando | Entonces |
|---|-----------|------|--------|----------|
| L9 | Fade-in al abrir | Usuario hace clic en imagen | Lightbox se muestra | Overlay aparece con transición de opacidad 0→1 en 0.2s |
| L10 | Fade-out al cerrar | Lightbox abierto | Usuario cierra | Overlay desaparece con transición opacidad 1→0 en 0.15s |
