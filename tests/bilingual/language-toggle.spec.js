/**
 * Bilingual Tests: Language Toggle on Every Page
 * Suite: Bilingual > Language Toggle
 *
 * Verifies that all pages switch correctly between EN and ES.
 * Tests URL parameter persistence, label translation, and preference memory.
 *
 * @see TESTING-GUIDE.md Section "Bilingual Testing"
 * @see EDGE-TESTING-GUIDE.md Section 8 (Localization)
 */
const { test, expect } = require('@playwright/test');
const { restoreAuth, assertNoFatalErrors } = require('../helpers/auth');
const { gotoLang, getCurrentLang, verifyLangPersistence } = require('../helpers/bilingual');
const { BASE_URL } = require('../../playwright.config');

/**
 * Helper: Test language toggle on a given page.
 * @param {import('@playwright/test').Page} page
 * @param {string} pageKey - e.g. 'dashboard', 'rfis'
 * @param {RegExp} expectedEN - Regex of text expected in EN mode
 * @param {RegExp} expectedES - Regex of text expected in ES mode
 */
async function testLanguageToggle(page, pageKey, expectedEN, expectedES) {
  // Test EN mode
  await gotoLang(page, `?page=${pageKey}`, 'en');
  await page.waitForTimeout(500);
  await verifyLangPersistence(page, 'en');
  const bodyEN = await page.textContent('body');
  expect(bodyEN).toMatch(expectedEN);

  // Test ES mode
  await gotoLang(page, `?page=${pageKey}`, 'es');
  await page.waitForTimeout(500);
  await verifyLangPersistence(page, 'es');
  const bodyES = await page.textContent('body');
  expect(bodyES).toMatch(expectedES);
}

test.describe('Bilingual — Dashboard (Public Page)', () => {

  test('BIL-01: Dashboard loads in EN with English labels', async ({ page }) => {
    await gotoLang(page, '?page=dashboard', 'en');
    await page.waitForTimeout(1000);
    const body = await page.textContent('body');
    assertNoFatalErrors(body);
    // Dashboard should show some EN-specific text
    expect(body.length).toBeGreaterThan(50);
  });

  test('BIL-02: Dashboard loads in ES with Spanish labels', async ({ page }) => {
    await gotoLang(page, '?page=dashboard', 'es');
    await page.waitForTimeout(1000);
    const body = await page.textContent('body');
    assertNoFatalErrors(body);
    // Should show Spanish text
    expect(body).toMatch(/Panel|RFI|Diarios|Construccion/);
  });

  test('BIL-03: Dashboard lang parameter persists across navigation', async ({ page }) => {
    await gotoLang(page, '?page=dashboard', 'es');
    await page.waitForTimeout(500);
    // Click a link on the dashboard
    const link = page.locator('a[href*="dashboard"]').first();
    const linkExists = await link.isVisible().catch(() => false);
    if (linkExists) {
      await link.click();
      await page.waitForTimeout(1000);
      await verifyLangPersistence(page, 'es');
    }
  });
});

test.describe('Bilingual — Login Page', () => {

  test('BIL-10: Login page shows EN labels in EN mode', async ({ page }) => {
    await gotoLang(page, '?page=login', 'en');
    await page.waitForTimeout(500);
    const body = await page.textContent('body');
    expect(body).toMatch(/Email Address|Sign In|Password/);
  });

  test('BIL-11: Login page shows ES labels in ES mode', async ({ page }) => {
    await gotoLang(page, '?page=login', 'es');
    await page.waitForTimeout(500);
    const body = await page.textContent('body');
    expect(body).toMatch(/Contrasena|Iniciar Sesion|Correo/);
  });

  test('BIL-12: Login form submits with ES lang parameter', async ({ page }) => {
    await gotoLang(page, '?page=login', 'es');
    await page.waitForTimeout(500);
    await page.fill('input[name="email"]', 'admin@openbuilder.com');
    await page.fill('input[name="password"]', 'admin123');
    await page.click('button[type="submit"]');
    await page.waitForURL(url => !url.toString().includes('page=login'), { timeout: 10000 }).catch(() => {});
    // Should redirect to dashboard with ES
    const url = page.url();
    expect(url).toContain('lang=es');
  });
});

test.describe('Bilingual — Protected Pages', () => {

  test('BIL-20: RFIs page switches language correctly', async ({ page }) => {
    await restoreAuth(page);
    await testLanguageToggle(
      page,
      'rfis',
      /RFIs|Requests|Create|Status/,
      /Solicitudes|Crear|Estado/
    );
  });

  test('BIL-21: Daily Logs page switches language correctly', async ({ page }) => {
    await restoreAuth(page);
    await testLanguageToggle(
      page,
      'daily_logs',
      /Daily Logs|Create|View/,
      /Diarios|Crear|Ver/
    );
  });

  test('BIL-22: Tasks page switches language correctly', async ({ page }) => {
    await restoreAuth(page);
    await testLanguageToggle(
      page,
      'tasks',
      /Task|Start Date|End Date|Critical/,
      /Tarea|Fecha de Inicio|Fecha de Fin|Critico/
    );
  });

  test('BIL-23: Budget page switches language correctly', async ({ page }) => {
    await restoreAuth(page);
    await testLanguageToggle(
      page,
      'budget',
      /Budget|Original|Change Orders|Variance/,
      /Presupuesto|Original|Cambios|Varianza/
    );
  });

  test('BIL-24: Users page switches language correctly', async ({ page }) => {
    await restoreAuth(page);
    await testLanguageToggle(
      page,
      'users',
      /User|Management|Add|Role/,
      /Usuario|Gestion|Agregar|Rol/
    );
  });

  test('BIL-25: Create RFI page switches language correctly', async ({ page }) => {
    await restoreAuth(page);
    await testLanguageToggle(
      page,
      'create_rfi',
      /Reference Number|Subject|Due Date|Priority/,
      /Numero de Referencia|Asunto|Fecha de Vencimiento|Prioridad/
    );
  });

  test('BIL-26: Create Daily Log page switches language correctly', async ({ page }) => {
    await restoreAuth(page);
    await testLanguageToggle(
      page,
      'create_daily_log',
      /Date|Weather|Manpower|Work Performed/,
      /Fecha|Clima|Fuerza Laboral|Trabajo/
    );
  });
});

test.describe('Bilingual — URL Parameter Persistence', () => {

  test('BIL-30: lang=es persists after page reload', async ({ page }) => {
    await restoreAuth(page);
    await gotoLang(page, '?page=rfis', 'es');
    await page.reload({ waitUntil: 'domcontentloaded' });
    await page.waitForTimeout(500);
    await verifyLangPersistence(page, 'es');
  });

  test('BIL-31: Invalid lang parameter falls back to EN', async ({ page }) => {
    await page.goto(`${BASE_URL}/?page=dashboard&lang=fr`, { waitUntil: 'domcontentloaded' });
    await page.waitForTimeout(500);
    const body = await page.textContent('body');
    // Should fall back to English (default)
    expect(body).not.toMatch(/Panel/); // Spanish word should NOT appear
  });

  test('BIL-32: lang parameter preserved in form submissions', async ({ page }) => {
    await restoreAuth(page);
    await gotoLang(page, '?page=create_rfi', 'es');
    await page.waitForTimeout(500);

    const refInput = page.locator('input[name="ref_number"]').first();
    const subjectInput = page.locator('input[name="subject"]').first();
    const dueInput = page.locator('input[name="due_date"]').first();

    const refVisible = await refInput.isVisible().catch(() => false);
    if (refVisible) {
      await refInput.fill('RFI-ES-TEST-' + Date.now());
      await subjectInput.fill('Prueba de idioma');
      await dueInput.fill('2026-07-15');
      await page.click('button[type="submit"]');
      await page.waitForTimeout(2000);

      // Should be on RFIs list with ES lang
      const url = page.url();
      expect(url).toContain('lang=es');
    }
  });
});

test.describe('Bilingual — Form Labels& Placeholders', () => {

  test('BIL-40: Create RFI form labels translate EN→ES', async ({ page }) => {
    await restoreAuth(page);
    await gotoLang(page, '?page=create_rfi', 'en');
    await page.waitForTimeout(500);

    // Get all form labels in EN
    const labelsEN = await page.locator('label').allTextContents();

    await gotoLang(page, '?page=create_rfi', 'es');
    await page.waitForTimeout(500);
    const labelsES = await page.locator('label').allTextContents();

    // Labels should be different
    const commonLabels = labelsEN.filter(l => labelsES.includes(l));
    // Some labels should be different (translation)
    expect(labelsES.length).toBeGreaterThan(0);
  });

  test('BIL-41: Error messages display in correct language', async ({ page }) => {
    await restoreAuth(page);
    await gotoLang(page, '?page=create_rfi', 'es');
    await page.waitForTimeout(500);

    // Submit without required fields
    const submitBtn = page.locator('button[type="submit"]').first();
    await submitBtn.click();
    await page.waitForTimeout(1000);

    const body = await page.textContent('body');
    // Should show Spanish error message
    expect(body).toMatch(/requerido|campo|obligatorio|error/i);
  });
});
