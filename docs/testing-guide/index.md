# Testing Guide / Guía de Pruebas

## English
This guide outlines the standard testing procedures for OpenBuilder.

### Comprehensive Guides
- [General Testing Guide — EN](../en/testing.md) ← **Expanded with 13 new feature modules**
- [Edge-Testing Guide — EN](../edge-testing/index.md) ← **Expanded with 14 edge case categories**

### Playwright Tests
- All features must have a corresponding Playwright script in `tests/full_suite.spec.js`.
- Execute tests using: `npx playwright test`.

### Quick Links — New Features (Phase 100+)
| Module | Template | Handler | Edge Cases |
|--------|----------|---------|------------|
| Timesheets | `templates/timesheets.php` | `timesheet_handler.php` | GPS fallback, zero hours, future date |
| Equipment | `templates/equipment.php` | `equipment_handler.php` | Retire retired, service on retired |
| Safety Hazards | `templates/safety_hazards.php` | `safety_handler.php` | Photo size limit, close already-closed |
| Inspections | `inspection_schedule.php`, `inspection_execution.php` | `inspection_handler.php` | No items, all N/A |
| Observations | `templates/observations.php` | `observations_handler.php` | Status reverse transition |
| Punch List | `templates/punch_list_v2.php` | `punch_handler.php` | Batch assign deleted crew, CSV export empty |
| Media Gallery | `templates/media.php` | `media_handler.php` | Link to RFI/Punch, delete file |
| Change Orders | `templates/change_orders.php` | `change_order_handler.php` | $0 CO, commit unapproved |
| Task Scheduling | `templates/tasks.php` | `tasks_handler.php` | Pred deleted, end before start, Gantt perf |
| Prime Contracts | `templates/prime_contracts.php` | `prime_contract_handler.php` | Cascade delete, BCmath overflow |
| Document Control | `templates/docs.php` | `docs_handler.php` | Check-out collision, check-in not-checked-out |
| REST API | `src/api/project_api.php` | — | 401, 429, malformed JSON |
| Webhooks | `src/api/webhooks.php` | — | HMAC mismatch, delivery timeout |

---

## Español
Esta guía describe los procedimientos de prueba estándar para OpenBuilder.

### Guías Completas
- [Guía General de Pruebas — ES](../es/testing.md) ← **Ampliada con 13 nuevos módulos de características**
- [Guía de Pruebas de Borde — ES](../edge-testing/es_index.md) ← **Ampliada con 14 categorías de casos límite**

### Pruebas de Playwright
- Todas las características deben tener un script de Playwright correspondiente en `tests/full_suite.spec.js`.
- Ejecute las pruebas usando: `npx playwright test`.

### Enlaces Rápidos — Nuevas Características (Fase 100+)
| Módulo | Plantilla | Handler | Casos Límite |
|--------|-----------|---------|-------------|
| Partes de Horas | `templates/timesheets.php` | `timesheet_handler.php` | GPS fallback, horas cero, fecha futura |
| Equipo | `templates/equipment.php` | `equipment_handler.php` | Retirar ya retirado, servicio en retirado |
| Hazardos de Seguridad | `templates/safety_hazards.php` | `safety_handler.php` | Tamaño de foto, cerrar ya cerrado |
| Inspecciones | `inspection_schedule.php`, `inspection_execution.php` | `inspection_handler.php` | Sin ítems, todos N/A |
| Observaciones | `templates/observations.php` | `observations_handler.php` | Transición de estado inversa |
| Punch List | `templates/punch_list_v2.php` | `punch_handler.php` | Asignar cuadrilla eliminada, CSV vacío |
| Galería de Medios | `templates/media.php` | `media_handler.php` | Vincular a RFI/Punch, eliminar archivo |
| Órdenes de Cambio | `templates/change_orders.php` | `change_order_handler.php` | CO $0, comprometer no aprobada |
| Programación de Tareas | `templates/tasks.php` | `tasks_handler.php` | Pred eliminada, fin antes inicio, perf Gantt |
| Contratos Principales | `templates/prime_contracts.php` | `prime_contract_handler.php` | Eliminación en cascada, overflow BCmath |
| Control de Documentos | `templates/docs.php` | `docs_handler.php` | Colisión check-out, check-in sin check-out |
| API REST | `src/api/project_api.php` | — | 401, 429, JSON malformado |
| Webhooks | `src/api/webhooks.php` | — | HMAC incorrecto, timeout de entrega |