# Guía de Pruebas de OpenBuilder (Español)

## Principios de Prueba
1. **Integridad Bilingüe**: Cada elemento de la interfaz debe soportar Inglés y Español. Use el parámetro `lang` en las URLs para la validación.
2. **Fiabilidad de la IA**: El contenido generado por IA (Informes Diarios, Borradores de RFI) debe ser verificado por su relevancia técnica y formato.
3. **Consistencia de Datos**: Asegúrese de que las métricas del presupuesto (Gastado vs. Restante) sean matemáticamente consistentes en las vistas de Panel y Presupuesto.

## Nuevos Módulos de Características (v2 — Fase 100+)

Los siguientes módulos fueron agregados en la ola de implementación Fase 100. Todos deben pasar validación bilingüe, CSRF y SweetAlert2.

---

### 1. Parte de Horas (Timesheets)
**Plantilla**: `templates/timesheets.php`  
**Handler**: `templates/timesheet_handler.php`

#### Escenarios de Prueba Manual
- [ ] **Crear parte de horas**: Seleccione nombre del trabajador, oficio (EN/ES), fecha, ingrese horas, seleccione código de costo del menú desplegable con búsqueda. Envíe. Verifique que la entrada aparezca en la lista con los datos correctos.
- [ ] **Captura GPS**: Después de enviar un parte, verifique que las columnas `latitude`/`longitude`/`gps_stamp` estén pobladas en la base de datos. El GPS se captura automáticamente vía API de Geolocalización del navegador.
- [ ] **Aprobación del capataz**: Envíe un parte como Trabajador. Inicie sesión como Capataz. Verifique que la acción de aprobación sea visible. Haga clic en Aprobar y confirme que el estado cambia a "Approved".
- [ ] **Editar parte de horas**: Haga clic en Editar en una entrada existente. Modifique horas y código de costo. Guarde. Verifique los cambios reflejados en la lista y la base de datos.
- [ ] **Eliminar parte de horas**: Haga clic en Eliminar. Confirme mediante diálogo SweetAlert2. Verifique que la entrada sea removida de la lista y la BD.
- [ ] **Interruptor bilingüe**: Agregue `?lang=es` a la URL. Verifique que todas las etiquetas (Worker Name, Trade, Hours, Date, Cost Code, Status) se muestren en español. Lo mismo para los mensajes de éxito/error.

#### Casos Límite
- [ ] **Sin permiso GPS**: Deniegue la geolocalización en el navegador. Verifique que el formulario aún se envíe con `latitude=NULL`, `longitude=NULL` — sin error de bloqueo.
- [ ] **Horas cero**: Ingrese 0 horas. Verifique que la validación del formulario prevenga el envío o lo permita con el estado de BD apropiado.
- [ ] **Fecha futura**: Seleccione una fecha en el futuro. Verifique que sea aceptada (los partes pueden ser pre-datados para programación).

---

### 2. Seguimiento de Equipo
**Plantilla**: `templates/equipment.php`  
**Handler**: `templates/equipment_handler.php`

#### Escenarios de Prueba Manual
- [ ] **Agregar equipo**: Llene nombre, categoría, marca, modelo, número de serie, estado (Active/Retired), cuadrilla asignada. Envíe. Verifique que la tarjeta aparezca en la cuadrícula de equipos.
- [ ] **Registro de servicio**: Haga clic en una tarjeta de equipo. Abra la pestaña Service Log. Agregue una entrada: fecha, tipo (Routine/Repair/Maintenance), descripción, costo. Verifique que aparezca en la lista de registros.
- [ ] **Retirar equipo**: Haga clic en Retire en una tarjeta de equipo Active. Confirme mediante SweetAlert2. Verifique que el estado cambie a Retired y la tarjeta se distinga visualmente.
- [ ] **Reasignar cuadrilla**: Edite el equipo, cambie la cuadrilla asignada. Guarde. Verifique que el nuevo nombre de cuadrilla aparezca en la tarjeta.
- [ ] **Interruptor bilingüe**: Verifique que las categorías de equipo, etiquetas de estado y campos del registro de servicio se muestren en español (`?lang=es`).

#### Casos Límite
- [ ] **Retirar ya retirado**: Intente retirar equipo que ya está en estado Retired. Verifique que no haya actualización duplicada ni error.
- [ ] **Registro de servicio en retirado**: Agregue una entrada de registro de servicio a un equipo retirado. Verifique que se permita (el equipo puede mantenerse después de retirarse).

---

### 3. Registro de Hazardos de Seguridad
**Plantilla**: `templates/safety_hazards.php`  
**Handler**: `templates/safety_handler.php`

#### Escenarios de Prueba Manual
- [ ] **Reportar hazaro**: Haga clic en "Report Hazard". Llene descripción, ubicación, severidad (Low/Medium/High/Critical), fecha de reporte, cuadrilla asignada. Adjunte una foto. Envíe.
- [ ] **Subir foto**: Suba una foto con el hazaro. Verifique que la miniatura aparezca en la tarjeta del hazaro. Verifique que la foto se guarde en `uploads/safety/`.
- [ ] **Captura GPS**: Verifique que lat/lon se capturen automáticamente vía API de Geolocalización del navegador al reportar un hazaro.
- [ ] **Editar hazaro**: Haga clic en Editar en un hazaro existente. Cambie severidad y agregue una acción correctiva. Guarde. Verifique que la tarjeta se actualice.
- [ ] **Cerrar hazaro**: Haga clic en Close en un hazaro Open. Confirme mediante SweetAlert2. Verifique que la insignia de estado cambie a "Closed".
- [ ] **Interruptor bilingüe**: Verifique que las etiquetas de severidad, insignias de estado y campos del formulario se muestren en español (`?lang=es`).

#### Casos Límite
- [ ] **Sin permiso GPS**: Deniegue la geolocalización. Verifique que el formulario aún se envíe — los campos GPS se inicializan en NULL.
- [ ] **Subida de foto grande**: Suba una foto > 5MB. Verifique que el handler la rechace con un mensaje de error (validación de tipo y tamaño de archivo).
- [ ] **Cerrar ya cerrado**: Intente cerrar un hazaro ya Closed. Verifique que no haya actualización duplicada.

---

### 4. Inspecciones
**Plantillas**: `templates/inspection_schedule.php`, `templates/inspection_execution.php`, `templates/inspection_templates.php`  
**Handler**: `templates/inspection_handler.php`

#### Escenarios de Prueba Manual
- [ ] **Programar inspección**: Cree una nueva inspección — título, nombre del inspector, fecha programada, ubicación, proyecto, plantilla (si existen plantillas). Envíe. Verifique que aparezca en la lista de programación con insignia de estado.
- [ ] **Ejecutar inspección**: Abra una inspección programada. Para cada ítem, seleccione Pass/Fail/N/A. Agregue comentarios. Guarde los resultados.
- [ ] **Plantillas de inspección**: Cree una plantilla con secciones e ítems. Use la plantilla al programar una nueva inspección. Verifique que los ítems se pre-poblen.
- [ ] **Interruptor bilingüe**: Verifique que todas las etiquetas (Scheduled Date, Inspector, Status, Pass/Fail/N/A) se muestren en español.

#### Casos Límite
- [ ] **Ejecutar sin ítems**: Programe una inspección sin asignar una plantilla o ítems. Ejecútela. Verifique que no se arroje ningún error.
- [ ] **Todos los ítems N/A**: Marque todos los ítems de inspección como N/A. Guarde. Verifique que la inspección se guarde exitosamente.

---

### 5. Observaciones
**Plantilla**: `templates/observations.php`  
**Handler**: `templates/observations_handler.php`

#### Escenarios de Prueba Manual
- [ ] **Registrar observación**: Cree una observación — proyecto, categoría (Safety/Quality/Progress/Issue), texto de observación, asignado a, prioridad (Low/Medium/High/Critical), estado (Open/In Progress/Verified/Closed).
- [ ] **Adjunto de foto**: Adjunte una foto a una observación. Verifique que se muestre en la tarjeta de observación.
- [ ] **Captura GPS**: Verifique que lat/lon se capturen automáticamente.
- [ ] **Flujo de estado**: Cree una observación Open. Progrésela: Open → In Progress → Verified → Closed. Verifique que cada transición actualice el color de la insignia.
- [ ] **Interruptor bilingüe**: Verifique que las categorías, prioridades y etiquetas de estado se muestren en español.

---

### 6. Punch List
**Plantilla**: `templates/punch_list_v2.php`  
**Handler**: `templates/punch_handler.php`

#### Escenarios de Prueba Manual
- [ ] **Crear ítem punch**: Ingrese descripción, ubicación, prioridad, asignado, código de costo. Envíe. Verifique que la tarjeta aparezca en la lista.
- [ ] **Asignación por lote**: Seleccione múltiples ítems punch mediante casillas de verificación. Asigne por lote a una cuadrilla. Verifique que todos los ítems seleccionados actualicen su asignado.
- [ ] **Cierre por lote**: Seleccione múltiples ítems. Cierrelos por lote. Verifique que todos muestren estado Closed.
- [ ] **Exportar CSV**: Haga clic en Export CSV. Verifique que se descargue un archivo CSV con las columnas correctas (id, descripción, estado, asignado, ubicación).
- [ ] **Verificar ítem**: Marque un ítem punch como Verified (separado de Closed). Verifique que el color de la insignia cambie.
- [ ] **Interruptor bilingüe**: Verifique que las insignias de estado y las etiquetas del formulario se muestren en español.

---

### 7. Galería de Medios
**Plantilla**: `templates/media.php`  
**Handler**: `templates/media_handler.php`

#### Escenarios de Prueba Manual
- [ ] **Subir medio**: Suba una foto — llene título, proyecto, código de costo, fecha de toma, etiquetas. Verifique que aparezca en la cuadrícula de la galería.
- [ ] **Vincular a RFI**: Abra un ítem de medio. Vinculelo a una RFI existente. Verifique que el enlace se guarde y sea visible en la vista de detalle del medio.
- [ ] **Vincular a Punch List**: Vincule un ítem de medio a un ítem de punch list. Verifique que el enlace esté registrado en la tabla `media_links`.
- [ ] **Eliminar medio**: Elimine un ítem de medio. Confirme mediante SweetAlert2. Verifique que el archivo sea removido del disco y el registro de BD se elimine.
- [ ] **Interruptor bilingüe**: Verifique que las etiquetas del formulario y los metadatos de la galería se muestren en español.

---

### 8. Órdenes de Cambio
**Plantilla**: `templates/change_orders.php`  
**Handler**: `templates/change_order_handler.php`

#### Escenarios de Prueba Manual
- [ ] **Crear orden de cambio**: Seleccione evento de cambio, código de costo, tipo de CO (NCC/CCD/CO/CR), monto, estado (Draft). Envíe.
- [ ] **Flujo de estado**: Progrese una CO a través de todas las etapas — Draft → Submitted → Reviewed → Approved → Issued. Verifique que los colores de las insignias cambien en cada etapa.
- [ ] **Comprometer al presupuesto**: Cuando una CO está Approved, haga clic en "Commit to Budget". Confirme mediante SweetAlert2. Verifique que `budget_committed=1` y el estado cambie a Issued.
- [ ] **Vincular al código de costo**: Verifique que el monto de la CO se refleje en la columna `change_orders` del código de costo e impacte `committed_costs`.
- [ ] **Interruptor bilingüe**: Verifique que las etiquetas de tipo y estado se muestren en español.

#### Casos Límite
- [ ] **Comprometer no aprobada**: Intente comprometer una CO que aún está en Draft o Submitted. Verifique que la acción sea bloqueada o que la confirmación requiera más pasos del flujo de trabajo.
- [ ] **CO con monto cero**: Cree una CO con $0 de monto. Verifique que se guarde sin error.

---

### 9. Programación de Tareas
**Plantilla**: `templates/tasks.php`  
**Handler**: `templates/tasks_handler.php`

#### Escenarios de Prueba Manual
- [ ] **Crear tarea**: Ingrese nombre de tarea, fecha de inicio, fecha de fin, cuadrilla asignada, código de costo, tarea predecesora (dependencia). Envíe.
- [ ] **Indicador de ruta crítica**: Cree una tarea marcada como crítica. Verifique que el punto rojo o insignia crítica aparezca en la tarjeta.
- [ ] **Vista de Gantt**: Cambie a vista de gráfico de Gantt. Verifique que las tareas se muestren como barras que abarcan sus fechas de inicio/fin. Verifique que las flechas de dependencia del predecesor se representen correctamente.
- [ ] **Vista de calendario**: Cambie a vista de calendario. Verifique que las tareas aparezcan en sus fechas programadas.
- [ ] **Dependencia de predecesor**: Asigne un predecesor a una tarea. Verifique que en la vista Gantt la flecha de dependencia sea visible.
- [ ] **Editar/eliminar tarea**: Edite las fechas y cuadrilla de una tarea. Verifique que la barra Gantt se actualice. Elimine una tarea y verifique que sea removida de la lista y del Gantt.
- [ ] **Interruptor bilingüe**: Verifique que las etiquetas de fecha, nombres de cuadrilla y etiquetas de estado se muestren en español.

---

### 10. Contratos Principales
**Plantilla**: `templates/prime_contracts.php`  
**Handler**: `templates/prime_contract_handler.php`

#### Escenarios de Prueba Manual
- [ ] **Crear contrato**: Llene número de contrato, nombre del contratista, valor, fecha de inicio, fecha de fin, estado (Active/Completed/Terminated), % de retención, frecuencia de facturación. Envíe.
- [ ] **Seguimiento de valor de CO**: Cree órdenes de cambio vinculadas a este contrato. Verifique que `change_order_value` y `revised_contract_value` se calculen automáticamente (`contract_value + change_order_value`).
- [ ] **Historial de versiones**: Actualice el valor de un contrato. Verifique que se cree un nuevo registro de versión en `prime_contract_versions`. Vea el historial de versiones y verifique que se listen todas las versiones.
- [ ] **Interruptor bilingüe**: Verifique que todas las etiquetas se muestren en español.

---

### 11. Control de Documentos
**Plantilla**: `templates/docs.php`  
**Handler**: `templates/docs_handler.php`

#### Escenarios de Prueba Manual
- [ ] **Subir documento**: Suba un archivo — título, número de revisión. Verifique que aparezca en la lista de documentos.
- [ ] **Check-out de documento**: Haga clic en Check Out en un documento. Verifique que `checked_out_by` y `checked_out_at` estén establecidos. Verifique que otros usuarios lo vean como bloqueado.
- [ ] **Check-in de documento**: Regrese al documento bloqueado. Haga check-in. Verifique que `checked_out_by` se libere.
- [ ] **Nueva revisión**: Suba una nueva revisión de un documento existente. Verifique que el número de revisión aumente y el historial de versiones se preserve.
- [ ] **Vincular a submittals/RFIs/COs**: Vincule un documento a un submittal o RFI existente. Verifique que el enlace se guarde.
- [ ] **Interruptor bilingüe**: Verifique que las etiquetas del formulario y las insignias de estado se muestren en español.

---

### 12. Integración GPS de Campo
**Componentes**: `src/GPSEngine.php`, `templates/timesheets.php`, `templates/safety_hazards.php`, `templates/create_daily_log.php`

#### Escenarios de Prueba Manual
- [ ] **GPS real (sin simulación)**: En un formulario de parte de horas o hazaro de seguridad, verifique que las coordenadas se capturen desde la API real de Geolocalización del navegador — no valores aleatorios.
- [ ] **Validación de GPSEngine**: Inspeccione `GPSEngine.php`. Confirme que `isValidCoords()` valida rango lat [-90, 90] y lon [-180, 180]. Confirme que no queden llamadas a `rand()` o `mt_rand()`.
- [ ] **Formato de GPS stamp**: Envíe un parte con GPS. Verifique que la columna `gps_stamp` contenga una cadena correctamente formateada (ej., `"37.774900,-122.419400"`).
- [ ] **Respaldo de ubicación desconocida**: Envíe un formulario con GPS denegado. Verifique que la etiqueta de respaldo muestre "Unknown location" en EN o "Ubicación desconocida" en ES.
- [ ] **Etiquetas GPS bilingües**: Con `?lang=es`, verifique que el texto de estado GPS muestre "Solicitando ubicación..." y "Ubicación capturada".

---

### 13. API REST y Webhooks
**Archivos**: `src/api/project_api.php`, `src/api/middleware.php`, `src/api/webhooks.php`

#### Escenarios de Prueba Manual
- [ ] **Generación de clave API**: Navegue a la gestión de claves API. Cree una nueva clave. Verifique que aparezca en la lista con previsualización enmascarada y marca de tiempo de creación.
- [ ] **Solicitud autenticada**: Use la clave como token de portador. Llame a `GET /api/projects`. Verifique respuesta JSON con HTTP 200.
- [ ] **Solicitud no autenticada**: Llame a `/api/projects` sin token de portador. Verifique HTTP 401 y `{"error": "Unauthorized"}`.
- [ ] **Límite de tasa**: Envíe solicitudes rápidas (>100/min). Verifique HTTP 429 después de exceder el umbral.
- [ ] **POST timesheet**: Envíe `POST /api/timesheets` con cuerpo JSON. Verifique que el registro se cree en BD y se devuelva HTTP 201.
- [ ] **Firma de webhook**: Active un evento de webhook. Verifique que el encabezado de firma (`X-Webhook-Signature`) use HMAC-SHA256 y coincida con el payload.
- [ ] **Firma de webhook inválida**: Envíe una solicitud de webhook con una firma incorrecta. Verifique que sea rechazada con HTTP 401.

---

## Cobertura de Pruebas Automatizadas (Playwright)

Todas las nuevas características anteriores deben tener casos de prueba correspondientes en `tests/full_suite.spec.js`. Áreas clave a cubrir:

- **Navegación**: Verifica la visibilidad y los enlaces de la barra superior, la barra lateral y la navegación inferior móvil.
- **Ciclo de Vida de RFI**: Prueba la creación, el filtrado, la interacción con los pines del mapa y la exportación masiva de PDF.
- **Finanzas**: Valida las tablas de presupuesto, el ciclo de vida de las órdenes de cambio y el simulador interactivo "What-If".
- **Administración**: Prueba las etiquetas de roles de gestión de usuarios y la persistencia de los ajustes del proyecto.
- **Timesheets**: Crear, editar, aprobar, eliminar entradas de parte de horas con verificación de captura GPS.
- **Equipo**: Agregar equipo, agregar registro de servicio, retirar equipo.
- **Hazaros de Seguridad**: Reportar hazaro con foto, cerrar hazaro, editar acción correctiva.
- **Inspecciones**: Programar, ejecutar con Pass/Fail/N/A por ítem, guardar resultados.
- **Observaciones**: Registrar observación con foto, transiciones de estado.
- **Punch List**: Crear ítem, asignar por lote, cerrar por lote, exportar CSV.
- **Galería de Medios**: Subir, anotar, vincular a RFI/Punch List.
- **Órdenes de Cambio**: Ciclo completo Draft→Submitted→Reviewed→Approved→Issued, comprometer al presupuesto.
- **Programación de Tareas**: Crear tarea, vista Gantt, vista Calendario, dependencia de predecesor.
- **Autenticación API**: Validación de token Bearer, límite de tasa, respuestas 401/429.
- **Webhooks**: Verificación de firma HMAC-SHA256.

Ejecute la suite completa:
```bash
npx playwright test
```

## Escenarios de Prueba Manual

### 1. Widgets Interactivos
- [ ] **Simulador de Presupuesto**: Deslice la varianza al 25%. Verifique que la etiqueta "Impacto Proyectado" se actualice correctamente.
- [ ] **Mapa de RFIs**: Pase el cursor sobre los pines en el plano. Verifique que las etiquetas muestren los títulos correctos de RFI. Haga clic en un pin para navegar a los detalles.
- [ ] **Toggles de Notificación**: Alterne las alertas de presupuesto. Verifique que la notificación toast confirme el cambio.
- [ ] **Gantt de Tareas**: Cree dos tareas con una dependencia de predecesor. Verifique que la flecha se represente en la vista Gantt.

### 2. Validación de Funciones de IA
- [ ] **Redacción de RFI**: Genere un borrador de IA para un problema de "Grieta en Losa". Verifique que la respuesta incluya Asunto y Pregunta.
- [ ] **Informe Diario**: Convierta notas de campo en un informe. Verifique si los "Riesgos de Seguridad" están listados en una sección de Markdown.

### 3. Seguridad y Acceso
- [ ] **Verificación MFA**: Ingrese un código simulado de 6 dígitos. Verifique el toast de éxito y la redirección al panel.
- [ ] **RBAC**: Inicie sesión como Subcontratista (Simulación). Verifique que "Ajustes del Proyecto" y "Registros de Auditoría" estén ocultos o restringidos.
- [ ] **CSRF**: Intente enviar un formulario sin un token CSRF válido. Verifique que la solicitud sea rechazada.

## Verificación de Backend
Ejecute la suite de pruebas de lógica central:
```bash
php tests/run_tests.php
```

## Lista de Verificación de Cumplimiento de Biblia

Para cada nueva característica agregada, verifique:

- [ ] **Bilingüe**: Todas las etiquetas, botones, insignias y mensajes visibles para el usuario tienen variantes EN/ES
- [ ] **SweetAlert2**: Todas las acciones destructivas (eliminar, retirar, cerrar) usan confirmaciones SweetAlert2 — sin `alert()`/`confirm()` nativos
- [ ] **CSRF**: Todos los formularios POST incluyen un token CSRF válido; el handler valida vía `csrf_validate()`
- [ ] **PermissionHelper**: Todos los handlers instancian `PermissionHelper` y verifican permisos
- [ ] **htmlspecialchars**: Toda la salida dinámica (títulos de tarjetas, nombres, descripciones) está envuelta con `htmlspecialchars(..., ENT_QUOTES, 'UTF-8')`
- [ ] **GPS**: Los formularios capturan GPS automáticamente vía API de Geolocalización del navegador con etiquetas de respaldo EN/ES
- [ ] **searchableSelect**: Todos los menús desplegables de múltiples opciones (códigos de costo, cuadrillas, categorías) usan la clase CSS `.searchable-select`