# Guía de Pruebas de Borde (Español)
[English Version](./index.md)

## Puntos de Falla Críticos y Condiciones de Límite

### 1. Fallas de API e Inteligencia
- **Escenario**: GEMINI_API_KEY falta o no es válida.
  - **Esperado**: El sistema debe fallar con elegancia. La interfaz debe mostrar "Servicio de IA no disponible" en lugar de romperse.
- **Escenario**: Tiempo de espera agotado del proveedor de clima.
  - **Esperado**: El diario debe permitir el envío. La sección de clima debe mostrar "Datos climáticos no disponibles".

### 2. Casos Límite Financieros
- **Escenario**: 100% de utilización del presupuesto.
  - **Esperado**: Las barras de progreso deben quedar en el 100%. Las alertas de varianza deben marcarse como "Críticas".
- **Escenario**: Códigos de costo con presupuesto cero.
  - **Esperado**: Los cálculos de varianza deben manejar la división por cero (el resultado debe ser 0%).

### 3. Concurrencia y Colisión
- **Escenario**: Cierre simultáneo de RFI.
  - **Esperado**: Simulación de dos usuarios cerrando la misma RFI. El sistema debe notificar al segundo usuario que el estado ya cambió.

### 4. Estrés de Entrada y Validación
- **Escenario**: Notas de diario excesivamente grandes (más de 10,000 caracteres).
  - **Esperado**: La generación de IA debe truncar la entrada para mantenerse dentro de los límites de tokens.
- **Escenario**: Códigos MFA inválidos (fallas secuenciales).
  - **Esperado**: Después de 3 intentos fallidos, el sistema debe requerir un tiempo de espera de 60 segundos (Simulación).

### 5. Casos Límite de UI/UX
- **Escenario**: Ventanas móviles extremas (280px de ancho).
  - **Esperado**: La navegación inferior debe colapsar con elegancia.
- **Escenario**: Consultas de medios de impresión.
  - **Esperado**: Verifique que la navegación y los botones estén ocultos al imprimir informes de RFI.
