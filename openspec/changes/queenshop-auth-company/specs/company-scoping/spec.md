# Especificación de Aislamiento por Empresa (Company Scoping)

## Propósito

Aislar datos de productos y ventas por empresa mediante `company_id`, asegurando que cada usuario vea solo los datos de su propia empresa.

## Requisitos

### Requisito: Esquema con company_id

Las tablas `products` y `sales` DEBEN incluir `company_id` como clave foránea a `companies.id`.

#### Escenario: Clave foránea en products

- DADO que la tabla `products` existe
- CUANDO se ejecuta el esquema
- ENTONCES `products` DEBE tener `company_id` como FK a `companies.id`

#### Escenario: Clave foránea en sales

- DADO que la tabla `sales` existe
- CUANDO se ejecuta el esquema
- ENTONCES `sales` DEBE tener `company_id` como FK a `companies.id`

#### Escenario: FK con valor por defecto

- DADO que existen filas existentes sin `company_id`
- CUANDO se ejecuta la migración
- ENTONCES las filas existentes DEBEN recibir `company_id = 1` como valor por defecto

### Requisito: Filtrado por empresa en consultas

TODAS las consultas a productos y ventas DEBEN filtrar por `company_id`.

#### Escenario: Listar productos por empresa

- DADO que existen productos para empresa A y empresa B
- CUANDO un usuario de empresa A lista productos
- ENTONCES solo ve productos con `company_id = A`

#### Escenario: Insertar producto con company_id

- DADO un usuario con `company_id` en sesión
- CUANDO crea un producto
- ENTONCES el producto se inserta con su `company_id`

### Requisito: Controladores pasan company_id

Los controladores DEBEN obtener `company_id` de `$_SESSION` y pasarlo a los modelos.

#### Escenario: company_id desde sesión al modelo

- DADO que el usuario tiene `company_id` en `$_SESSION`
- CUANDO un controlador ejecuta una operación de datos
- ENTONCES el controlador pasa `$_SESSION['company_id']` al modelo

### Requisito: Datos de semilla

El sistema DEBE incluir 2 empresas de demostración con datos de muestra aislados.

#### Escenario: Seed con datos de demostración

- DADO que se ejecuta la migración inicial
- CUANDO se siembran los datos
- ENTONCES existen 2 empresas
- Y cada empresa tiene productos y ventas de muestra
- Y los datos entre empresas NO están mezclados
