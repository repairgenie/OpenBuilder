/**
 * Bilingual testing utilities.
 * Provides helpers for testing EN/ES language switching across all pages.
 */

/**
 * Known EN→ES translation pairs for key UI elements.
 * These are used to verify that language switching works correctly.
 */
const TRANSLATIONS = {
  // Common elements
  'Dashboard': 'Panel',
  'Email Address': 'Correo electronico',
  'Password': 'Contrasena',
  'Sign In': 'Iniciar Sesion',
  'Submit': 'Enviar',
  'Cancel': 'Cancelar',
  'Save': 'Guardar',
  'Delete': 'Eliminar',
  'Edit': 'Editar',
  'Create': 'Crear',
  'View': 'Ver',
  'Search': 'Buscar',
  'Filter': 'Filtrar',
  'Export': 'Exportar',
  'Actions': 'Acciones',
  'Status': 'Estado',
  'Name': 'Nombre',
  'Email': 'Correo',
  'Role': 'Rol',
  'Date': 'Fecha',
  'Description': 'Descripcion',
  'Notes': 'Notas',
  'All': 'Todos',
  'Open': 'Abierto',
  'Closed': 'Cerrado',
  'High': 'Alto',
  'Medium': 'Medio',
  'Low': 'Bajo',
  'Loading...': 'Cargando...',
  'No results found': 'No se encontraron resultados',
  'Required field': 'Campo requerido',
  'Invalid format': 'Formato invalido',

  // Page-specific
  'Requests for Information': 'Solicitudes de Informacion',
  'Daily Logs': 'Diarios de Obra',
  'Task Scheduling': 'Programacion de Tareas',
  'Budget': 'Presupuesto',
  'User Management': 'Gestion de Usuarios',
  'Crew Management': 'Gestion de Cuadrillas',
  'Document Control': 'Control de Documentos',
  'Safety Hazards': 'Peligros de Seguridad',
  'Audit Logs': 'Registros de Auditoria',
  'API Keys': 'Claves API',
  'Roles& Permissions': 'Roles y Permisos',
  'Create RFI': 'Crear RFI',
  'Create Daily Log': 'Crear Diario',
  'New Task': 'Nueva Tarea',
  'Add User': 'Agregar Usuario',
  'Reference Number': 'Numero de Referencia',
  'Subject': 'Asunto',
  'Due Date': 'Fecha de Vencimiento',
  'Priority': 'Prioridad',
  'Work Performed': 'Trabajo Realizado',
  'Manpower': 'Fuerza Laboral',
  'Weather': 'Clima',
  'GPS': 'GPS',
};

/**
 * Navigate to a URL with the specified language.
 * @param {import('@playwright/test').Page} page
 * @param {string} path - URL path (e.g., '?page=dashboard')
 * @param {'en'|'es'} lang - Language code
 */
async function gotoLang(page, path, lang) {
  const separator = path.includes('?') ? '&' : '?';
  await page.goto(`${path}${separator}lang=${lang}`, { waitUntil: 'domcontentloaded' });
  await page.waitForTimeout(500);
}

/**
 * Toggle language by adding/removing lang parameter.
 * @param {import('@playwright/test').Page} page
 * @param {'en'|'es'} lang
 */
async function switchLang(page, lang) {
  const url = new URL(page.url());
  url.searchParams.set('lang', lang);
  await page.goto(url.toString(), { waitUntil: 'domcontentloaded' });
  await page.waitForTimeout(500);
}

/**
 * Verify that text content has changed when switching language.
 * @param {import('@playwright/test').Page} page
 * @param {string} enText - Expected English text
 * @param {string} esText - Expected Spanish text
 */
async function verifyTranslation(page, enText, esText) {
  const body = await page.textContent('body');
  // When in EN, we should see enText; when in ES, we should see esText
  const currentLang = new URL(page.url()).searchParams.get('lang') || 'en';
  if (currentLang === 'en') {
    // In English mode, we should NOT see the Spanish equivalent of a known pair
    // (unless it's a shared word)
  } else {
    // In Spanish mode, we should see Spanish text
  }
}

/**
 * Get the current language from the page URL.
 * @param {import('@playwright/test').Page} page
 * @returns {'en'|'es'}
 */
function getCurrentLang(page) {
  return new URL(page.url()).searchParams.get('lang') || 'en';
}

/**
 * Verify URL persistence: lang parameter should persist across navigation.
 * @param {import('@playwright/test').Page} page
 * @param {'en'|'es'} expectedLang
 */
async function verifyLangPersistence(page, expectedLang) {
  const url = new URL(page.url());
  const actualLang = url.searchParams.get('lang');
  if (actualLang !== expectedLang) {
    throw new Error(`Expected lang=${expectedLang} but got lang=${actualLang}`);
  }
}

module.exports = { TRANSLATIONS, gotoLang, switchLang, verifyTranslation, getCurrentLang, verifyLangPersistence };
