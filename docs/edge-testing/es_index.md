# Guía de Pruebas de Borde (Español)
[English Version](./index.md)

## Puntos de Falla Críticos y Condiciones de Límite

### 1. Fallas de API e Inteligencia
- **Escenario**: GEMINI_API_KEY falta o no es válida.
  - **Esperado**: El sistema debe fallar con elegancia. La interfaz debe mostrar "Servicio de IA no disponible" en lugar de romperse.
- **Escenario**: Tiempo de espera agotado del proveedor de clima.
  - **Esperado**: El diario debe permitir el envío. La sección de clima debe mostrar "Datos climáticos no disponibles".
- **Escenario**: Límite de tasa de API excedido (>100 req/min).
  - **Esperado**: Devolver HTTP 429 con `{"error": "Rate limit exceeded"}`. Registrar intento en `api_logs`.

### 2. Casos Límite Financieros
- **Escenario**: 100% de utilización del presupuesto.
  - **Esperado**: Las barras de progreso deben quedar en el 100%. Las alertas de varianza deben marcarse como "Críticas".
- **Escenario**: Códigos de costo con presupuesto cero.
  - **Esperado**: Los cálculos de varianza deben manejar la división por cero (el resultado debe ser 0%).
- **Escenario**: Orden de Cambio con monto $0.
  - **Esperado**: La CO se guarda sin error. Sin división por cero en la matemática de varianza. La lógica de `budget_committed` aún funciona.
- **Escenario**: CO comprometida cuando ya está Issued.
  - **Esperado**: El handler rechaza con mensaje de error. Sin compromiso duplicado.

### 3. Concurrencia y Colisión
- **Escenario**: Cierre simultáneo de RFI.
  - **Esperado**: Simulación de dos usuarios cerrando la misma RFI. El sistema debe notificar al segundo usuario que el estado ya cambió.
- **Escenario**: Aprobación simultánea de parte de horas por dos capataces.
  - **Esperado**: La primera aprobación gana. La segunda ve el mensaje flash "Ya aprobado".

### 4. Estrés de Entrada y Validación
- **Escenario**: Notas de diario excesivamente grandes (más de 10,000 caracteres).
  - **Esperado**: La generación de IA debe truncar la entrada para mantenerse dentro de los límites de tokens.
- **Escenario**: Códigos MFA inválidos (fallas secuenciales).
  - **Esperado**: Después de 3 intentos fallidos, el sistema debe requerir un tiempo de espera de 60 segundos (Simulación).
- **Escenario**: Subida de foto > 5MB en formulario de Hazardos de Seguridad.
  - **Esperado**: El handler rechaza con mensaje de error: "Archivo demasiado grande (máx 5MB)". Sin carga parcial.
- **Escenario**: Token CSRF faltante en POST del handler.
  - **Esperado**: El handler devuelve HTTP 400 y redirige con mensaje de error.
- **Escenario**: Permiso GPS denegado en formulario de parte/horas/hazaro.
  - **Esperado**: El formulario se envía exitosamente. `latitude`/`longitude` se inicializan en NULL. Sin error de bloqueo.

### 5. Casos Límite de UI/UX
- **Escenario**: Ventanas móviles extremas (280px de ancho).
  - **Esperado**: La navegación inferior debe colapsar con elegancia. Los encabezados modales deben ajustarse sin romper los botones de cierre.
- **Escenario**: Consultas de medios de impresión.
  - **Esperado**: Verifique que la navegación y los botones estén ocultos al imprimir informes.
- **Escenario**: Interruptor bilingüe con `?lang=es`.
  - **Esperado**: Los 14 nuevos módulos (Timesheets, Equipment, Safety Hazards, Inspections, Observations, Punch List, Media, Change Orders, Tasks, Prime Contracts, Docs, API) renderizan todas las etiquetas, insignias y mensajes en español.

### 6. Casos Límite de Integración GPS
- **Escenario**: Coordenadas GPS fuera de rango (lat > 90, lon > 180).
  - **Esperado**: `GPSEngine::isValidCoords()` devuelve false. El formulario aún se guarda con GPS NULL. Sin bloqueo.
- **Escenario**: GPSEngine llamado con valores nulos.
  - **Esperado**: `formatStamp(null, null)` devuelve `"Unknown location"`. Sin excepción.
- **Escenario**: rand()/mt_rand() aún presentes en GPSEngine.
  - **Esperado**: La prueba falla. Las coordenadas GPS deben venir solo de la API real de Geolocalización del navegador — sin simulación.

### 7. Casos Límite de Partes de Horas (Timesheets)
- **Escenario**: Parte de horas con horas = 0.
  - **Esperado**: El formulario bloquea el envío o acepta con `hours=0` en BD (depende de reglas de negocio). Sin bloqueo.
- **Escenario**: Parte de horas con fecha futura.
  - **Esperado**: Aceptada y almacenada. Se permite pre-datar para propósitos de programación.
- **Escenario**: Parte de horas aprobada por rol que no es capataz.
  - **Esperado**: PermissionHelper bloquea la acción. Se muestra mensaje de error.

### 8. Casos Límite de Equipo y Seguridad
- **Escenario**: Retirar equipo ya retirado.
  - **Esperado**: Sin actualización duplicada en BD. Sin error. Idempotente.
- **Escenario**: Agregar registro de servicio a equipo retirado.
  - **Esperado**: Permitido. El equipo puede mantenerse después de retirarse.
- **Escenario**: Cerrar un hazaro de seguridad ya cerrado.
  - **Esperado**: Sin actualización de estado duplicada. Idempotente.
- **Escenario**: Subida de foto de hazaro con tipo MIME incorrecto.
  - **Esperado**: El handler valida el tipo MIME. Rechaza con error si no es image/*.

### 9. Casos Límite de Inspecciones y Observaciones
- **Escenario**: Ejecutar inspección sin ítems asignados.
  - **Esperado**: El formulario carga sin error. El guardado debe succeeder con cero ítems.
- **Escenario**: Todos los ítems de inspección marcados como N/A.
  - **Esperado**: La inspección se guarda exitosamente. Sin error de conjunto de resultados vacío.
- **Escenario**: Transición de estado de observación hacia atrás (Closed → Open).
  - **Esperado**: ¿Permitido? (depende de reglas de negocio — documente el comportamiento esperado). Sin bloqueo.

### 10. Casos Límite de Punch List
- **Escenario**: Asignación por lote a cuadrilla que ha sido eliminada.
  - **Esperado**: La clave foránea de BD ya sea cascada o rechaza. El handler debe manejar con elegancia.
- **Escenario**: Exportar CSV con 0 ítems seleccionados.
  - **Esperado**: Descargar CSV vacío o mostrar "No se seleccionaron ítems". Sin error PHP.

### 11. Casos Límite de API y Webhooks
- **Escenario**: Solicitud con clave API expirada o revocada.
  - **Esperado**: HTTP 401 con `{"error": "Invalid or revoked API key"}`. Registrar en `api_logs`.
- **Escenario**: JSON malformado en cuerpo POST.
  - **Esperado**: HTTP 400 con `{"error": "Invalid JSON"}`. No bloquear ni exponer stack trace.
- **Escenario**: Webhook recibido con discrepancia HMAC.
  - **Esperado**: HTTP 401. Registrar como entrega fallida. No procesar payload.
- **Escenario**: Tiempo de entrega de webhook agotado.
  - **Esperado**: Entrega marcada como fallida en tabla `webhooks` después de 3 reintentos.

### 12. Casos Límite de Control de Documentos
- **Escenario**: Check-out de documento ya bloqueado por otro usuario.
  - **Esperado**: Mostrar "El documento está actualmente bloqueado por [nombre]". No sobrescribir.
- **Escenario**: Check-in de documento que no está bloqueado.
  - **Esperado**: El handler ignora silenciosamente o muestra advertencia. Sin error.
- **Escenario**: Subir revisión con el mismo número de revisión.
  - **Esperado**: Permitirlo o advertir. El historial de versiones del documento debe reflejar duplicados claramente.

### 13. Casos Límite de Programación de Tareas
- **Escenario**: Tarea predecesora eliminada mientras la dependiente existe.
  - **Esperado**: `predecessor_task_id` se convierte en NULL o muestra "La tarea ya no existe". Sin referencia huérfana.
- **Escenario**: Fecha de fin de tarea antes de fecha de inicio.
  - **Esperado**: La validación del formulario debe prevenir el guardado. Mostrar "La fecha de fin debe ser posterior a la fecha de inicio".
- **Escenario**: Vista Gantt con 500+ tareas.
  - **Esperado**: La renderización no debe congelar el navegador. Considerar paginación o scroll virtual.

### 14. Casos Límite de Contratos Principales
- **Escenario**: Eliminar un contrato principal con COs vinculadas existentes.
  - **Esperado**: Ya sea eliminación en cascada (en cascada a `prime_contract_versions`) o rechazar eliminación con "El contrato tiene órdenes de cambio vinculadas".
- **Escenario**: `contract_value + change_order_value` excede el entero máximo de PHP.
  - **Esperado**: Usar BCmath o float. Sin desbordamiento de entero. Mostrar "N/A" si el valor es demasiado grande.