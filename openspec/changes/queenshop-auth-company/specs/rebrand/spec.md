# Especificación de Rebranding (Rebrand)

## Propósito

Renombrar PetShop a QueenShop en toda la interfaz visible manteniendo la identidad visual amarillo/negro/blanco y mejorando la legibilidad.

## Requisitos

### Requisito: Texto visible actualizado

Todo texto visible al usuario DEBE usar "QueenShop" en lugar de "PetShop".

#### Escenario: Título de página

- DADO que el usuario navega a cualquier página
- CUANDO se renderiza el `<title>`
- ENTONCES el título contiene "QueenShop"

#### Escenario: Marca en navbar

- DADO que el usuario ve la barra de navegación
- CUANDO se renderiza la marca
- ENTONCES muestra "QueenShop"

#### Escenario: Footer

- DADO que el usuario ve el pie de página
- CUANDO se renderiza
- ENTONCES muestra "QueenShop" en lugar de "PetShop"

### Requisito: Tema visual y legibilidad

El sistema DEBE mantener el tema amarillo/negro/blanco existente. El sistema DEBE mejorar el contraste y la tipografía para mayor legibilidad.

#### Escenario: Contraste mejorado

- DADO el tema amarillo/negro/blanco
- CUANDO se aplican los estilos
- ENTONCES el contraste de texto sobre fondo DEBE cumplir WCAG AA

#### Escenario: Tipografía limpia

- DADO los estilos actuales
- CUANDO se actualiza el CSS
- ENTONCES la tipografía DEBE ser más legible (tamaño, espaciado, peso)
