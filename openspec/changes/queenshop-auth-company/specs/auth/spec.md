# Especificación de Autenticación (Auth)

## Propósito

Registro, inicio y cierre de sesión con aislamiento multi-empresa. Cada usuario pertenece a una única empresa creada automáticamente al registrarse.

## Requisitos

### Requisito: Registro de usuario

El usuario DEBE registrarse con nombre de usuario y contraseña. El nombre de usuario DEBE ser único. El sistema DEBE crear una empresa con nombre `"{usuario}'s Shop"` al registrar. La contraseña DEBE almacenarse con `password_hash()`.

#### Escenario: Registro exitoso

- DADO credenciales válidas
- CUANDO el sistema procesa el registro
- ENTONCES crea el usuario con `password_hash()`
- Y crea la empresa `"{usuario}'s Shop"`
- E inicia sesión con `user_id`, `company_id` y `company_name` en `$_SESSION`

#### Escenario: Nombre de usuario duplicado

- DADO que "juan" ya existe como usuario
- CUANDO otro usuario intenta registrarse como "juan"
- ENTONCES el sistema RECHAZA el registro
- Y el sistema muestra un mensaje de error

### Requisito: Inicio de sesión

El sistema DEBE verificar credenciales usando `password_verify()`.

#### Escenario: Login exitoso

- DADO credenciales válidas
- CUANDO el usuario ingresa usuario y contraseña correctos
- ENTONCES el sistema inicia sesión con `user_id`, `company_id` y `company_name`

#### Escenario: Contraseña incorrecta

- DADO que el usuario "juan" existe
- CUANDO ingresa una contraseña incorrecta
- ENTONCES el sistema RECHAZA el acceso y muestra error

### Requisito: Cierre de sesión

El sistema DEBE destruir la sesión al cerrar sesión.

#### Escenario: Logout exitoso

- DADO una sesión activa
- CUANDO el usuario cierra sesión
- ENTONCES el sistema destruye `$_SESSION` y redirige a `/login`

### Requisito: Guardia de sesión

El sistema DEBE redirigir a `/login` si el usuario no está autenticado.

#### Escenario: Acceso sin autenticación

- DADO que el usuario no ha iniciado sesión
- CUANDO intenta acceder a una ruta protegida
- ENTONCES el sistema redirige a `/login`

#### Escenario: Sesión activa

- DADO que el usuario tiene `user_id` y `company_id` en `$_SESSION`
- CUANDO accede a una ruta protegida
- ENTONCES el sistema permite el acceso
