# Biblia - Project Rules / Reglas del Proyecto

## English

### 1. Bilingualism
Everything must be bilingual between English and Spanish. The default language is English.

### 2. Mandatory Regeneration and Testing
Every time a feature or aspect of the app is built or modified, the following actions must be taken:
- **Regenerate User Documentation**: Located in the `docs/` directory.
- **Regenerate Testing Guides**: Located in the `docs/testing-guide/` directory.
- **Regenerate Edge Testing Guides**: Located in the `docs/edge-testing/` directory.
- **Playwright Testing**: Build or update a Playwright script to test the changes thoroughly.
- **Bug Fixes**: Fix any bugs found and keep running tests until the entire flow can be completed without errors of any kind.

### 3. Procore Competitive Analysis & OpenBuilder Strategy

Understanding the user sentiment around Procore allows us to build OpenBuilder as a "Procore-killer"—retaining the power of a centralized hub while stripping away the legacy friction.

1. What People Like (The "Must-Haves")

The "Single Source of Truth": Procore is excellent at ensuring everyone (Owner, GC, Sub) is looking at the same set of drawings and RFIs.

Unlimited User Model: Unlike many SaaS products, Procore doesn't charge per seat. This encourages collaboration because GCs don't have to pay extra to invite their subcontractors.

Mobile-First Field Access: Field supers love being able to pull up a drawing on an iPad and pin an RFI directly to a coordinate.

Ecosystem: It integrates with almost everything (Sage, Proest, DocuSign).

2. What People Hate (The "Friction")

High Cost: Procore is notoriously expensive, often pricing out small-to-mid-sized contractors. It’s a "prestige" tool.

Complexity/Bloat: For many projects, 70% of the features go unused. The UI can feel overwhelming for a foreman who just wants to log man-hours.

Rigid Reporting: While it has lots of data, getting custom insights out of it often requires a "Procore Certified" expert or complex export processes.

Permission Hell: Setting up granular permissions for dozens of companies on one project is a massive administrative burden.

3. The Wishlist (What Users Want Changed)

Better AI Automation: Users are tired of manual data entry. They want to speak a daily log and have it "auto-populate" the system.

Faster Financial Syncing: The gap between the field and the accounting office is still too wide in many implementations.

Simplified UI: A desire for a "Light Mode" or a version of the app that only shows what is relevant to the specific user's role.

4. How OpenBuilder Wins (Implementation Strategy)

Based on our PHP & TailAdmin roadmap, here is how we implement these "best-in-class" features:

A. Lean UI with TailAdmin (Solves "Bloat")

Instead of Procore's dense, menu-heavy interface, use TailAdmin’s clean sidebar and card-based layout.

Action: In Phase 1, keep the dashboard high-level. Only show the user the metrics that matter to their role (e.g., a Subcontractor only sees their assigned RFIs).

B. Agentic AI with Gemini (Solves "Manual Entry")

Procore is just starting to add AI; we are building it into the core.

Action: In Phase 4, our Daily Log module shouldn't just be a form. Use the Gemini API to allow "voice-to-text" notes that the AI then categorizes into Manpower, Material, and Safety sections automatically. This removes the #1 complaint of field staff: paperwork.

C. Conversational Analytics (Solves "Rigid Reporting")

Instead of building 50 different static reports, we use the "Project Assistant."

Action: In Phase 5, enable users to ask, "Show me all RFIs that are holding up the concrete pour." The PHP backend should fetch the data, and Gemini should synthesize it. This makes the data accessible to non-technical users.

D. Simplified Permissions (Solves "Admin Burden")

Action: Implement a "Role-Based" system rather than "User-Based." When a user is added to the Directory (Phase 5), they are assigned a "Persona" (Superintendent, Project Manager, Owner) that automatically toggles the TailAdmin sidebar links they can see.

E. PHP/PDO Performance (Solves "Speed")

Procore can occasionally lag due to its massive scale.

Action: By using a clean PHP/PDO backend with optimized SQL queries, OpenBuilder can remain lightning-fast even on low-bandwidth job site connections.

---

## Español

### 1. Bilingüismo
Todo debe ser bilingüe entre inglés y español. El idioma predeterminado es el inglés.

### 2. Regeneración y Pruebas Obligatorias
Cada vez que se construya o modifique una característica o aspecto de la aplicación, se deben tomar las siguientes acciones:
- **Regenerar Documentación de Usuario**: Ubicada en el directorio `docs/`.
- **Regenerar Guías de Prueba**: Ubicada en el directorio `docs/testing-guide/`.
- **Regenerar Guías de Pruebas de Casos Límite (Edge Testing)**: Ubicada en el directorio `docs/edge-testing/`.
- **Pruebas de Playwright**: Crear o actualizar un script de Playwright para probar los cambios a fondo.
- **Corrección de Errores**: Corregir cualquier error encontrado y seguir ejecutando las pruebas hasta que se pueda completar todo el proceso sin errores de ningún tipo.

### 3. Análisis Competitivo de Procore y Estrategia de OpenBuilder

Comprender el sentimiento de los usuarios respecto a Procore nos permite construir OpenBuilder como un "asesino de Procore", conservando el poder de un centro centralizado y eliminando la fricción heredada.

1. Lo que a la Gente le Gusta (Los "Imprescindibles")

La "Única Fuente de Verdad": Procore es excelente para asegurar que todos (Propietario, Contratista General, Subcontratista) vean el mismo conjunto de planos y solicitudes de información (RFIs).

Modelo de Usuarios Ilimitados: A diferencia de muchos productos SaaS, Procore no cobra por asiento. Esto fomenta la colaboración porque los Contratistas Generales no tienen que pagar extra para invitar a sus subcontratistas.

Acceso de Campo Priorizado en Móviles: A los supervisores de campo les encanta poder abrir un plano en un iPad y anclar un RFI directamente a una coordenada.

Ecosistema: Se integra con casi todo (Sage, Proest, DocuSign).

2. Lo que la Gente Odia (La "Fricción")

Alto Costo: Procore es notoriamente costoso, a menudo excluyendo a los contratistas pequeños y medianos. Es una herramienta de "prestigio".

Complejidad/Exceso: Para muchos proyectos, el 70% de las características no se utilizan. La interfaz de usuario puede resultar abrumadora para un capataz que solo quiere registrar horas-hombre.

Informes Rígidos: Aunque tiene muchos datos, obtener información personalizada a menudo requiere un experto "Certificado por Procore" o procesos de exportación complejos.

Infierno de Permisos: Configurar permisos granulares para docenas de empresas en un proyecto es una carga administrativa masiva.

3. La Lista de Deseos (Lo que los Usuarios Quieren Cambiar)

Mejor Automatización con IA: Los usuarios están cansados de la entrada de datos manual. Quieren poder hablar un registro diario y que "autocompleta" el sistema.

Sincronización Financiera Más Rápida: La brecha entre el campo y la oficina de contabilidad sigue siendo demasiado grande en muchas implementaciones.

Interfaz de Usuario Simplificada: Un deseo de un "Modo Ligero" o una versión de la aplicación que solo muestre lo que es relevante para el rol específico del usuario.

4. Cómo Gana OpenBuilder (Estrategia de Implementación)

Basado en nuestra hoja de ruta con PHP y TailAdmin, así es como implementamos estas características de "lo mejor de su clase":

A. Interfaz de Usuario Ligera con TailAdmin (Resuelve el "Exceso")

En lugar de la interfaz densa y llena de menús de Procore, usa la barra lateral limpia y el diseño basado en tarjetas de TailAdmin.

Acción: En la Fase 1, mantén el panel de control a un nivel alto. Solo muestra al usuario las métricas que importan para su rol (por ejemplo, un Subcontratista solo ve sus RFIs asignados).

B. IA Agéntica con Gemini (Resuelve la "Entrada Manual")

Procore apenas está comenzando a agregar IA; nosotros la estamos construyendo en el núcleo.

Acción: En la Fase 4, nuestro módulo de Registro Diario (Daily Log) no debería ser solo un formulario. Usa la API de Gemini para permitir notas "voz a texto" que la IA luego categorice en secciones de Mano de Obra, Materiales y Seguridad automáticamente. Esto elimina la queja número 1 del personal de campo: el papeleo.

C. Analíticas Conversacionales (Resuelve los "Informes Rígidos")

En lugar de construir 50 informes estáticos diferentes, usamos el "Asistente de Proyecto".

Acción: En la Fase 5, permite a los usuarios preguntar, "Muéstrame todos los RFIs que están retrasando el vertido de concreto". El backend en PHP debería buscar los datos, y Gemini debería sintetizarlos. Esto hace que los datos sean accesibles para usuarios no técnicos.

D. Permisos Simplificados (Resuelve la "Carga Administrativa")

Acción: Implementar un sistema "Basado en Roles" en lugar de "Basado en Usuarios". Cuando se agrega un usuario al Directorio (Fase 5), se le asigna una "Persona" (Superintendente, Gerente de Proyecto, Propietario) que automáticamente alterna los enlaces de la barra lateral de TailAdmin que puede ver.

E. Rendimiento de PHP/PDO (Resuelve la "Velocidad")

Procore puede retrasarse ocasionalmente debido a su escala masiva.

Acción: Al usar un backend de PHP/PDO limpio con consultas SQL optimizadas, OpenBuilder puede seguir siendo ultrarrápido incluso en conexiones de sitio de trabajo con bajo ancho de banda.
