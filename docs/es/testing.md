# Guía de Pruebas de OpenBuilder (Español)

## Principios de Prueba
1. **Integridad Bilingüe**: Cada elemento de la interfaz debe soportar Inglés y Español. Use el parámetro `lang` en las URLs para la validación.
2. **Fiabilidad de la IA**: El contenido generado por IA (Informes Diarios, Borradores de RFI) debe ser verificado por su relevancia técnica y formato.
3. **Consistencia de Datos**: Asegúrese de que las métricas del presupuesto (Gastado vs. Restante) sean matemáticamente consistentes en las vistas de Panel y Presupuesto.

## Cobertura de Pruebas Automatizadas (Playwright)
- **Navegación**: Verifica la visibilidad y los enlaces de la barra superior, la barra lateral y la navegación inferior móvil.
- **Ciclo de Vida de RFI**: Prueba la creación, el filtrado, la interacción con los pines del mapa y la exportación masiva de PDF.
- **Finanzas**: Valida las tablas de presupuesto y el simulador interactivo "What-If".
- **Administración**: Prueba las etiquetas de roles de gestión de usuarios y la persistencia de los ajustes del proyecto (Simulación).

## Escenarios de Prueba Manual

### 1. Widgets Interactivos
- [ ] **Simulador de Presupuesto**: Deslice la varianza al 25%. Verifique que la etiqueta "Impacto Proyectado" se actualice correctamente.
- [ ] **Mapa de RFIs**: Pase el cursor sobre los pines en el plano. Verifique que las etiquetas muestren los títulos correctos de RFI. Haga clic en un pin para navegar a los detalles.
- [ ] **Toggles de Notificación**: Alterne las alertas de presupuesto. Verifique que la notificación toast confirme el cambio.

### 2. Validación de Funciones de IA
- [ ] **Redacción de RFI**: Genere un borrador de IA para un problema de "Grieta en Losa". Verifique que la respuesta incluya Asunto y Pregunta.
- [ ] **Informe Diario**: Convierta notas de campo en un informe. Verifique si los "Riesgos de Seguridad" están listados en una sección de Markdown.

### 3. Seguridad y Acceso
- [ ] **Verificación MFA**: Ingrese un código simulado de 6 dígitos. Verifique el toast de éxito y la redirección al panel.
- [ ] **RBAC**: Inicie sesión como Subcontratista (Simulación). Verifique que "Ajustes del Proyecto" y "Registros de Auditoría" estén ocultos o restringidos.

## Verificación de Backend
Ejecute la suite de pruebas de lógica central:
```bash
php tests/run_tests.php
```
