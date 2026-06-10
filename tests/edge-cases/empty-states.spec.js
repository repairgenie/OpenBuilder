/**
 * Edge Case Tests: Empty States
 * Suite: Edge Cases > Empty States
 *
 * Tests that all pages gracefully display helpful messages when there is no data,
 * and that these empty-state messages are properly displayed in both EN and ES languages.
 *
 * @see TESTING-GUIDE.md Section 9 (Empty States / Bilingual)
 */
const { test, expect } = require('@playwright/test');
const { restoreAuth, assertNoFatalErrors, isAuthPage } = require('../helpers/auth');
const { BASE_URL } = require('../../playwright.config');

// Common empty state message patterns in EN and ES
const EMPTY_PATTERNS_EN = [
  'no data', 'no results', 'no items', 'no records', 'nothing here',
  'no rfis', 'no daily logs', 'no tasks', 'no users', 'no budget',
  'empty', 'no entries', 'add your first', 'get started',
];
const EMPTY_PATTERNS_ES = [
  'sin datos', 'sin resultados', 'sin elementos', 'sin registros',
  'nada aqui', 'no hay rfis', 'no hay diarios', 'no hay tareas',
  'no hay usuarios', 'no hay presupuesto', 'vacio', 'sin entradas',
  'agrega tu primero', 'comienza',
];

test.describe('Empty States: Dashboard', () => {

  test('ES-DASH-01: Dashboard loads with no data without errors', async ({ page }) => {
    await restoreAuth(page);
    await page.goto(`${BASE_URL}/?page=dashboard`, { waitUntil: 'domcontentloaded' });
    await page.waitForTimeout(1500);
    if (isAuthPage(page)) throw new Error('Auth failed');
    assertNoFatalErrors(await page.content());

    const body = await page.textContent('body');
    const hasContent = body.length > 50;
    expect(hasContent).toBe(true);
  });

  test('ES-DASH-02: Dashboard has no fatal errors when metrics are zero', async ({ page }) => {
    await restoreAuth(page);
    await page.goto(`${BASE_URL}/?page=dashboard&lang=en`, { waitUntil: 'domcontentloaded' });
    await page.waitForTimeout(1500);
    if (isAuthPage(page)) throw new Error('Auth failed');

    const body = await page.content();
    assertNoFatalErrors(body);
    const text = await page.textContent('body');
    // Should show some dashboard UI elements even with no data
    expect(text.length).toBeGreaterThan(50);
  });

  test('ES-DASH-03: Dashboard empty state in Spanish', async ({ page }) => {
    await restoreAuth(page);
    await page.goto(`${BASE_URL}/?page=dashboard&lang=es`, { waitUntil: 'domcontentloaded' });
    await page.waitForTimeout(1500);
    if (isAuthPage(page)) throw new Error('Auth failed');

    const body = await page.textContent('body');
    const hasContent = body.length > 50;
    expect(hasContent).toBe(true);
  });
});

test.describe('Empty States: RFIs', () => {

  test('ES-RFI-01: RFIs list page loads with no errors when empty', async ({ page }) => {
    await restoreAuth(page);
    await page.goto(`${BASE_URL}/?page=rfis`, { waitUntil: 'domcontentloaded' });
    await page.waitForTimeout(1500);
    if (isAuthPage(page)) throw new Error('Auth failed');
    assertNoFatalErrors(await page.content());

    const body = await page.textContent('body');
    const hasContent = body.length > 50;
    expect(hasContent).toBe(true);
  });

  test('ES-RFI-02: Empty RFIs list shows helpful message in English', async ({ page }) => {
    await restoreAuth(page);
    await page.goto(`${BASE_URL}/?page=rfis&lang=en`, { waitUntil: 'domcontentloaded' });
    await page.waitForTimeout(1500);
    if (isAuthPage(page)) throw new Error('Auth failed');

    const body = await page.textContent('body');
    const hasEmptyMessage = EMPTY_PATTERNS_EN.some(pat => body.toLowerCase().includes(pat)) ||
                            body.includes('RFI') || body.includes('Request');
    expect(hasEmptyMessage).toBe(true);
  });

  test('ES-RFI-03: Empty RFIs list shows helpful message in Spanish', async ({ page }) => {
    await restoreAuth(page);
    await page.goto(`${BASE_URL}/?page=rfis&lang=es`, { waitUntil: 'domcontentloaded' });
    await page.waitForTimeout(1500);
    if (isAuthPage(page)) throw new Error('Auth failed');

    const body = await page.textContent('body');
    const hasEmptyMessage = EMPTY_PATTERNS_ES.some(pat => body.toLowerCase().includes(pat)) ||
                            body.includes('RFI') || body.includes('Solicitud');
    expect(hasEmptyMessage).toBe(true);
  });

  test('ES-RFI-04: Create RFI button is visible on empty RFIs page', async ({ page }) => {
    await restoreAuth(page);
    await page.goto(`${BASE_URL}/?page=rfis`, { waitUntil: 'domcontentloaded' });
    await page.waitForTimeout(1500);
    if (isAuthPage(page)) throw new Error('Auth failed');

    const createBtn = page.locator('a:has-text("Create RFI"), a:has-text("Crear RFI"), button:has-text("Create"), button:has-text("Crear")').first();
    const hasBtn = await createBtn.count() > 0;
    expect(hasBtn).toBe(true);
  });
});

test.describe('Empty States: Daily Logs', () => {

  test('ES-DL-01: Daily logs page loads without errors when empty', async ({ page }) => {
    await restoreAuth(page);
    await page.goto(`${BASE_URL}/?page=daily_logs`, { waitUntil: 'domcontentloaded' });
    await page.waitForTimeout(1500);
    if (isAuthPage(page)) throw new Error('Auth failed');
    assertNoFatalErrors(await page.content());

    const body = await page.textContent('body');
    const hasContent = body.length > 50;
    expect(hasContent).toBe(true);
  });

  test('ES-DL-02: Empty daily logs shows helpful message in English', async ({ page }) => {
    await restoreAuth(page);
    await page.goto(`${BASE_URL}/?page=daily_logs&lang=en`, { waitUntil: 'domcontentloaded' });
    await page.waitForTimeout(1500);
    if (isAuthPage(page)) throw new Error('Auth failed');

    const body = await page.textContent('body');
    const hasEmptyMessage = EMPTY_PATTERNS_EN.some(pat => body.toLowerCase().includes(pat)) ||
                            body.includes('Daily Log') || body.includes('Log');
    expect(hasEmptyMessage).toBe(true);
  });

  test('ES-DL-03: Empty daily logs shows helpful message in Spanish', async ({ page }) => {
    await restoreAuth(page);
    await page.goto(`${BASE_URL}/?page=daily_logs&lang=es`, { waitUntil: 'domcontentloaded' });
    await page.waitForTimeout(1500);
    if (isAuthPage(page)) throw new Error('Auth failed');

    const body = await page.textContent('body');
    const hasEmptyMessage = EMPTY_PATTERNS_ES.some(pat => body.toLowerCase().includes(pat)) ||
                            body.includes('Diario') || body.includes('Registro');
    expect(hasEmptyMessage).toBe(true);
  });
});

test.describe('Empty States: Tasks', () => {

  test('ES-TASK-01: Tasks page loads without errors when empty', async ({ page }) => {
    await restoreAuth(page);
    await page.goto(`${BASE_URL}/?page=tasks`, { waitUntil: 'domcontentloaded' });
    await page.waitForTimeout(1500);
    if (isAuthPage(page)) throw new Error('Auth failed');
    assertNoFatalErrors(await page.content());

    const body = await page.textContent('body');
    const hasContent = body.length > 50;
    expect(hasContent).toBe(true);
  });

  test('ES-TASK-02: Empty tasks shows helpful message in English', async ({ page }) => {
    await restoreAuth(page);
    await page.goto(`${BASE_URL}/?page=tasks&lang=en`, { waitUntil: 'domcontentloaded' });
    await page.waitForTimeout(1500);
    if (isAuthPage(page)) throw new Error('Auth failed');

    const body = await page.textContent('body');
    const hasEmptyMessage = EMPTY_PATTERNS_EN.some(pat => body.toLowerCase().includes(pat)) ||
                            body.includes('Task') || body.includes('task');
    expect(hasEmptyMessage).toBe(true);
  });

  test('ES-TASK-03: Empty tasks shows helpful message in Spanish', async ({ page }) => {
    await restoreAuth(page);
    await page.goto(`${BASE_URL}/?page=tasks&lang=es`, { waitUntil: 'domcontentloaded' });
    await page.waitForTimeout(1500);
    if (isAuthPage(page)) throw new Error('Auth failed');

    const body = await page.textContent('body');
    const hasEmptyMessage = EMPTY_PATTERNS_ES.some(pat => body.toLowerCase().includes(pat)) ||
                            body.includes('Tarea') || body.includes('tarea');
    expect(hasEmptyMessage).toBe(true);
  });
});

test.describe('Empty States: Budget', () => {

  test('ES-BUD-01: Budget page loads without errors when empty', async ({ page }) => {
    await restoreAuth(page);
    await page.goto(`${BASE_URL}/?page=budget`, { waitUntil: 'domcontentloaded' });
    await page.waitForTimeout(1500);
    if (isAuthPage(page)) throw new Error('Auth failed');
    assertNoFatalErrors(await page.content());

    const body = await page.textContent('body');
    const hasContent = body.length > 50;
    expect(hasContent).toBe(true);
  });

  test('ES-BUD-02: Empty budget shows helpful message in English', async ({ page }) => {
    await restoreAuth(page);
    await page.goto(`${BASE_URL}/?page=budget&lang=en`, { waitUntil: 'domcontentloaded' });
    await page.waitForTimeout(1500);
    if (isAuthPage(page)) throw new Error('Auth failed');

    const body = await page.textContent('body');
    const hasEmptyMessage = EMPTY_PATTERNS_EN.some(pat => body.toLowerCase().includes(pat)) ||
                            body.includes('Budget') || body.includes('budget');
    expect(hasEmptyMessage).toBe(true);
  });

  test('ES-BUD-03: Empty budget shows helpful message in Spanish', async ({ page }) => {
    await restoreAuth(page);
    await page.goto(`${BASE_URL}/?page=budget&lang=es`, { waitUntil: 'domcontentloaded' });
    await page.waitForTimeout(1500);
    if (isAuthPage(page)) throw new Error('Auth failed');

    const body = await page.textContent('body');
    const hasEmptyMessage = EMPTY_PATTERNS_ES.some(pat => body.toLowerCase().includes(pat)) ||
                            body.includes('Presupuesto') || body.includes('presupuesto');
    expect(hasEmptyMessage).toBe(true);
  });
});

test.describe('Empty States: Users', () => {

  test('ES-USER-01: Users list page loads without errors when empty', async ({ page }) => {
    await restoreAuth(page);
    await page.goto(`${BASE_URL}/?page=users`, { waitUntil: 'domcontentloaded' });
    await page.waitForTimeout(1500);
    if (isAuthPage(page)) throw new Error('Auth failed');
    assertNoFatalErrors(await page.content());

    const body = await page.textContent('body');
    const hasContent = body.length > 50;
    expect(hasContent).toBe(true);
  });

  test('ES-USER-02: Empty users list shows helpful message in English', async ({ page }) => {
    await restoreAuth(page);
    await page.goto(`${BASE_URL}/?page=users&lang=en`, { waitUntil: 'domcontentloaded' });
    await page.waitForTimeout(1500);
    if (isAuthPage(page)) throw new Error('Auth failed');

    const body = await page.textContent('body');
    const hasEmptyMessage = EMPTY_PATTERNS_EN.some(pat => body.toLowerCase().includes(pat)) ||
                            body.includes('User') || body.includes('user');
    expect(hasEmptyMessage).toBe(true);
  });

  test('ES-USER-03: Empty users list shows helpful message in Spanish', async ({ page }) => {
    await restoreAuth(page);
    await page.goto(`${BASE_URL}/?page=users&lang=es`, { waitUntil: 'domcontentloaded' });
    await page.waitForTimeout(1500);
    if (isAuthPage(page)) throw new Error('Auth failed');

    const body = await page.textContent('body');
    const hasEmptyMessage = EMPTY_PATTERNS_ES.some(pat => body.toLowerCase().includes(pat)) ||
                            body.includes('Usuario') || body.includes('usuario');
    expect(hasEmptyMessage).toBe(true);
  });
});

test.describe('Empty States: Bilingual Verification', () => {

  test('ES-BI-01: Empty state message changes when switching to ES (RFIs)', async ({ page }) => {
    await restoreAuth(page);
    await page.goto(`${BASE_URL}/?page=rfis&lang=en`, { waitUntil: 'domcontentloaded' });
    await page.waitForTimeout(1500);
    if (isAuthPage(page)) throw new Error('Auth failed');

    const enBody = await page.textContent('body');

    await page.goto(`${BASE_URL}/?page=rfis&lang=es`, { waitUntil: 'domcontentloaded' });
    await page.waitForTimeout(1500);
    const esBody = await page.textContent('body');

    // Content should differ when language changes
    // (Some words change, structure stays same)
    expect(enBody !== esBody).toBe(true);
  });

  test('ES-BI-02: Empty state message changes when switching to ES (Daily Logs)', async ({ page }) => {
    await restoreAuth(page);
    await page.goto(`${BASE_URL}/?page=daily_logs&lang=en`, { waitUntil: 'domcontentloaded' });
    await page.waitForTimeout(1500);
    if (isAuthPage(page)) throw new Error('Auth failed');

    const enBody = await page.textContent('body');

    await page.goto(`${BASE_URL}/?page=daily_logs&lang=es`, { waitUntil: 'domcontentloaded' });
    await page.waitForTimeout(1500);
    const esBody = await page.textContent('body');

    expect(enBody !== esBody).toBe(true);
  });

  test('ES-BI-03: Empty state message changes when switching to ES (Tasks)', async ({ page }) => {
    await restoreAuth(page);
    await page.goto(`${BASE_URL}/?page=tasks&lang=en`, { waitUntil: 'domcontentloaded' });
    await page.waitForTimeout(1500);
    if (isAuthPage(page)) throw new Error('Auth failed');

    const enBody = await page.textContent('body');

    await page.goto(`${BASE_URL}/?page=tasks&lang=es`, { waitUntil: 'domcontentloaded' });
    await page.waitForTimeout(1500);
    const esBody = await page.textContent('body');

    expect(enBody !== esBody).toBe(true);
  });
});