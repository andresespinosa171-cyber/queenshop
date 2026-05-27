# Especificación de Localización (Localization)

## Propósito

Configurar zona horaria Colombia, formato monetario COP y periodos quincenales para el dashboard.

## Requisitos

### Requisito: Zona horaria

El sistema DEBE usar `America/Bogota` como zona horaria por defecto mediante `date_default_timezone_set()`.

#### Escenario: Timezone configurado al iniciar

- DADO que la aplicación inicia
- CUANDO se ejecuta la configuración
- ENTONCES la zona horaria es `America/Bogota`

### Requisito: Formato monetario COP

El sistema DEBE mostrar montos en formato COP: símbolo `$`, separador de miles `.`, sin decimales. Ej: `$1.234.567`.

#### Escenario: Formateo de número entero

- DADO el monto 1234567
- CUANDO se formatea como moneda
- ENTONCES el resultado es `$1.234.567`

#### Escenario: Monto cero

- DADO el monto 0
- CUANDO se formatea como moneda
- ENTONCES el resultado es `$0`

### Requisito: Periodos quincenales

El sistema DEBE permitir alternar entre "Quincena actual" y "Todo el tiempo" en el dashboard. Quincena 1 = días 1-15, Quincena 2 = día 16 a fin de mes.

#### Escenario: Primera quincena

- DADO que es día 10 del mes
- CUANDO se selecciona "Quincena actual"
- ENTONCES el dashboard filtra por días 1-15 del mes actual

#### Escenario: Segunda quincena

- DADO que es día 20 del mes
- CUANDO se selecciona "Quincena actual"
- ENTONCES el dashboard filtra desde día 16 hasta fin de mes

#### Escenario: Todo el tiempo

- DADO cualquier fecha
- CUANDO se selecciona "Todo el tiempo"
- ENTONCES el dashboard muestra datos sin filtro de fecha
