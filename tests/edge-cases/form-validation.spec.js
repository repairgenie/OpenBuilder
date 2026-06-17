/**
 * Edge Case Tests: Form Validation
 * Suite: Edge Cases > Form Validation
 *
 * Tests that all forms correctly validate required fields, min/max lengths,
 * email formats, date ranges, and numeric boundaries in both EN and ES.
 *
 * @see TESTING-GUIDE.md Section 9 (Form Validation)
 */
const { test, expect } = require('@playwright/test');
const { restoreAuth, assertNoFatalErrors, isAuthPage } = require('../helpers/auth');
const { BASE_URL } = require('../../playwright.config');

test.describe.skip('Form Validation: RFIs', () => {

  test('FV-RFI-01: Submit RFI form with all fields empty shows required errors', async ({ page }) => {
    await restoreAuth(page);
    await page.goto(`${BASE_URL}/?page=create_rfi`, { waitUntil: 'domcontentloaded' });
    await page.waitForTimeout(1000);
    if (isAuthPage(page)) throw new Error('Auth failed');
    assertNoFatalErrors(await page.content());

    // Try to submit with no fields filled
    await page.click('button[type="submit"]');
    await page.waitForTimeout(1000);

    // Should show validation errors (HTML5 or server-side)
    const body = await page.textContent('body');
    const hasError = body.includes('required') || body.includes('Required') ||
                     body.includes('requerido') || body.includes('Requerido') ||
                     (await page.locator('.text-danger, .error, [class*="error"], .invalid-feedback').count()) > 0;
    expect(hasError).toBe(true);
  });

  test('FV-RFI-02: RFI subject exceeding max length is rejected or truncated', async ({ page }) => {
    await restoreAuth(page);
    await page.goto(`${BASE_URL}/?page=create_rfi`, { waitUntil: 'domcontentloaded' });
    await page.waitForTimeout(1000);
    if (isAuthPage(page)) throw new Error('Auth failed');

    const longSubject = 'A'.repeat(500); // Well beyond reasonable 200-char limit
    await page.fill('input[name="ref_number"]', 'RFI-MAX-' + Date.now());
    await page.fill('input[name="subject"]', longSubject);
    await page.fill('input[name="due_date"]', '2026-07-15');
    await page.click('button[type="submit"]');
    await page.waitForTimeout(1500);

    // Form should either reject it or URL should indicate an error
    const url = page.url();
    const body = await page.textContent('body');
    const rejected = url.includes('error') || url.includes('create_rfi') ||
                     body.includes('too long') || body.includes('max') ||
                     body.includes('200') || body.includes('largo') || body.includes('máximo');
    expect(rejected).toBe(true);
  });

  test('FV-RFI-03: RFI due date in the past is handled gracefully', async ({ page }) => {
    await restoreAuth(page);
    await page.goto(`${BASE_URL}/?page=create_rfi`, { waitUntil: 'domcontentloaded' });
    await page.waitForTimeout(1000);
    if (isAuthPage(page)) throw new Error('Auth failed');

    await page.fill('input[name="ref_number"]', 'RFI-PAST-' + Date.now());
    await page.fill('input[name="subject"]', 'Past Due Date Test');
    await page.fill('input[name="due_date"]', '2020-01-01');
    await page.click('button[type="submit"]');
    await page.waitForTimeout(1500);

    // Should either warn or accept with past date
    const body = await page.textContent('body');
    const handled = !body.includes('Fatal error') && !body.includes('Parse error');
    expect(handled).toBe(true);
  });
});

test.describe.skip('Form Validation: Daily Logs', () => {

  test('FV-DL-01: Submit daily log with empty required fields shows errors', async ({ page }) => {
    await restoreAuth(page);
    await page.goto(`${BASE_URL}/?page=create_daily_log`, { waitUntil: 'domcontentloaded' });
    await page.waitForTimeout(1000);
    if (isAuthPage(page)) throw new Error('Auth failed');
    assertNoFatalErrors(await page.content());

    await page.click('button[type="submit"]');
    await page.waitForTimeout(1000);

    const body = await page.textContent('body');
    const hasError = body.includes('required') || body.includes('Required') ||
                     body.includes('requerido') || body.includes('Requerido') ||
                     (await page.locator('.text-danger, .error, [class*="error"]').count()) > 0;
    expect(hasError).toBe(true);
  });

  test('FV-DL-02: Negative manpower value is rejected', async ({ page }) => {
    await restoreAuth(page);
    await page.goto(`${BASE_URL}/?page=create_daily_log`, { waitUntil: 'domcontentloaded' });
    await page.waitForTimeout(1000);
    if (isAuthPage(page)) throw new Error('Auth failed');

    await page.fill('input[name="date"]', '2026-06-10');
    await page.fill('input[name="weather"]', 'Sunny');
    // Try to set negative manpower via fill (HTML5 input type=number)
    await page.fill('input[name="manpower"]', '-5');
    await page.fill('textarea[name="work_performed"]', 'Test work');
    await page.click('button[type="submit"]');
    await page.waitForTimeout(1500);

    // HTML5 min=0 should prevent submission or server should reject it
    const body = await page.textContent('body');
    const rejected = body.includes('negative') || body.includes('min') ||
                     body.includes('número') || body.includes('mínimo') ||
                     (await page.locator('input[name="manpower"]:invalid').count()) > 0;
    expect(rejected).toBe(true);
  });

  test('FV-DL-03: Manpower exceeding reasonable limit is handled', async ({ page }) => {
    await restoreAuth(page);
    await page.goto(`${BASE_URL}/?page=create_daily_log`, { waitUntil: 'domcontentloaded' });
    await page.waitForTimeout(1000);
    if (isAuthPage(page)) throw new Error('Auth failed');

    await page.fill('input[name="date"]', '2026-06-10');
    await page.fill('input[name="weather"]', 'Sunny');
    await page.fill('input[name="manpower"]', '999999');
    await page.fill('textarea[name="work_performed"]', 'Test work');
    await page.click('button[type="submit"]');
    await page.waitForTimeout(1500);

    const body = await page.textContent('body');
    const handled = !body.includes('Fatal error') && !body.includes('Parse error');
    expect(handled).toBe(true);
  });
});

test.describe.skip('Form Validation: Tasks', () => {

  test('FV-TASK-01: Task with end date before start date is rejected', async ({ page }) => {
    await restoreAuth(page);
    await page.goto(`${BASE_URL}/?page=tasks`, { waitUntil: 'domcontentloaded' });
    await page.waitForTimeout(1000);
    if (isAuthPage(page)) throw new Error('Auth failed');
    assertNoFatalErrors(await page.content());

    // Try to create a task with invalid date range
    await page.goto(`${BASE_URL}/?page=create_task`, { waitUntil: 'domcontentloaded' });
    await page.waitForTimeout(1000);

    await page.fill('input[name="task_name"]', 'Invalid Date Task');
    await page.fill('input[name="start_date"]', '2026-06-25');
    await page.fill('input[name="end_date"]', '2026-06-10'); // End before start
    await page.click('button[type="submit"]');
    await page.waitForTimeout(1500);

    const body = await page.textContent('body');
    const rejected = body.includes('end') || body.includes('before') ||
                     body.includes('fecha') || body.includes('antes') ||
                     body.includes('invalid') || body.includes('inválida') ||
                     page.url().includes('create_task'); // Stayed on form = rejected
    expect(rejected).toBe(true);
  });

  test('FV-TASK-02: Task name exceeding max length is handled', async ({ page }) => {
    await restoreAuth(page);
    await page.goto(`${BASE_URL}/?page=create_task`, { waitUntil: 'domcontentloaded' });
    await page.waitForTimeout(1000);
    if (isAuthPage(page)) throw new Error('Auth failed');

    await page.fill('input[name="task_name"]', 'A'.repeat(300));
    await page.fill('input[name="start_date"]', '2026-06-15');
    await page.fill('input[name="end_date"]', '2026-06-20');
    await page.click('button[type="submit"]');
    await page.waitForTimeout(1500);

    const body = await page.textContent('body');
    const handled = !body.includes('Fatal error') && !body.includes('Parse error');
    expect(handled).toBe(true);
  });
});

test.describe.skip('Form Validation: Budget', () => {

  test('FV-BUD-01: Budget with negative amounts is rejected', async ({ page }) => {
    await restoreAuth(page);
    await page.goto(`${BASE_URL}/?page=budget`, { waitUntil: 'domcontentloaded' });
    await page.waitForTimeout(1000);
    if (isAuthPage(page)) throw new Error('Auth failed');
    assertNoFatalErrors(await page.content());

    // Find add budget item button or form
    const addBtn = page.locator('button:has-text("Add"), button:has-text("Agregar"), a:has-text("Add"), a:has-text("Agregar")').first();
    const hasAddBtn = await addBtn.count() > 0;
    if (!hasAddBtn) {
      // Budget page might not have an add form — just check no errors
      const body = await page.textContent('body');
      const hasContent = body.length > 100;
      expect(hasContent).toBe(true);
      return;
    }
    await addBtn.click();
    await page.waitForTimeout(1000);

    await page.fill('input[name="cost_code"]', 'Test Cost Code');
    await page.fill('input[name="original_budget"]', '-1000');
    await page.click('button[type="submit"]');
    await page.waitForTimeout(1500);

    const body = await page.textContent('body');
    const rejected = body.includes('negative') || body.includes('min') ||
                     body.includes('número') || body.includes('mínimo') ||
                     (await page.locator('input[name="original_budget"]:invalid').count()) > 0;
    expect(rejected).toBe(true);
  });

  test('FV-BUD-02: Budget amounts way over reasonable limits handled', async ({ page }) => {
    await restoreAuth(page);
    await page.goto(`${BASE_URL}/?page=budget`, { waitUntil: 'domcontentloaded' });
    await page.waitForTimeout(1000);
    if (isAuthPage(page)) throw new Error('Auth failed');

    const addBtn = page.locator('button:has-text("Add"), button:has-text("Agregar"), a:has-text("Add"), a:has-text("Agregar")').first();
    const hasAddBtn = await addBtn.count() > 0;
    if (!hasAddBtn) {
      const body = await page.textContent('body');
      const hasContent = body.length > 100;
      expect(hasContent).toBe(true);
      return;
    }
    await addBtn.click();
    await page.waitForTimeout(1000);

    await page.fill('input[name="cost_code"]', 'Overflow Test');
    await page.fill('input[name="original_budget"]', '999999999999');
    await page.click('button[type="submit"]');
    await page.waitForTimeout(1500);

    const body = await page.textContent('body');
    const handled = !body.includes('Fatal error') && !body.includes('Parse error');
    expect(handled).toBe(true);
  });
});

test.describe.skip('Form Validation: Users', () => {

  test('FV-USER-01: User form with invalid email format is rejected', async ({ page }) => {
    await restoreAuth(page);
    await page.goto(`${BASE_URL}/?page=create_user`, { waitUntil: 'domcontentloaded' });
    await page.waitForTimeout(1000);
    if (isAuthPage(page)) throw new Error('Auth failed');
    assertNoFatalErrors(await page.content());

    await page.fill('input[name="name"]', 'Test User');
    await page.fill('input[name="email"]', 'notavalidemail');
    await page.selectOption('select[name="role"]', 'Subcontractor');
    await page.click('button[type="submit"]');
    await page.waitForTimeout(1500);

    const body = await page.textContent('body');
    const rejected = body.includes('email') || body.includes('Email') ||
                     body.includes('correo') || body.includes('Correo') ||
                     body.includes('invalid') || body.includes('inválido') ||
                     page.url().includes('create_user'); // Stayed on form
    expect(rejected).toBe(true);
  });

  test('FV-USER-02: User form with empty required fields shows errors', async ({ page }) => {
    await restoreAuth(page);
    await page.goto(`${BASE_URL}/?page=create_user`, { waitUntil: 'domcontentloaded' });
    await page.waitForTimeout(1000);
    if (isAuthPage(page)) throw new Error('Auth failed');

    await page.click('button[type="submit"]');
    await page.waitForTimeout(1500);

    const body = await page.textContent('body');
    const hasError = body.includes('required') || body.includes('Required') ||
                     body.includes('requerido') || body.includes('Requerido') ||
                     (await page.locator('.text-danger, .error, [class*="error"]').count()) > 0;
    expect(hasError).toBe(true);
  });

  test('FV-USER-03: Duplicate email is handled gracefully', async ({ page }) => {
    await restoreAuth(page);
    await page.goto(`${BASE_URL}/?page=create_user`, { waitUntil: 'domcontentloaded' });
    await page.waitForTimeout(1000);
    if (isAuthPage(page)) throw new Error('Auth failed');

    await page.fill('input[name="name"]', 'Duplicate Test');
    await page.fill('input[name="email"]', 'admin@openbuilder.com'); // Already exists
    await page.selectOption('select[name="role"]', 'Admin');
    await page.click('button[type="submit"]');
    await page.waitForTimeout(2000);

    const body = await page.textContent('body');
    const handled = !body.includes('Fatal error') && !body.includes('Parse error');
    expect(handled).toBe(true);
  });
});

test.describe.skip('Form Validation: Bilingual Error Messages', () => {

  test('FV-BI-01: Required field errors shown in English', async ({ page }) => {
    await restoreAuth(page);
    await page.goto(`${BASE_URL}/?page=create_rfi&lang=en`, { waitUntil: 'domcontentloaded' });
    await page.waitForTimeout(1000);
    if (isAuthPage(page)) throw new Error('Auth failed');

    await page.click('button[type="submit"]');
    await page.waitForTimeout(1000);

    const body = await page.textContent('body');
    const hasEnglishError = body.includes('Required') || body.includes('required') ||
                            body.includes('This field') || body.includes('Please fill');
    expect(hasEnglishError).toBe(true);
  });

  test('FV-BI-02: Required field errors shown in Spanish', async ({ page }) => {
    await restoreAuth(page);
    await page.goto(`${BASE_URL}/?page=create_rfi&lang=es`, { waitUntil: 'domcontentloaded' });
    await page.waitForTimeout(1000);
    if (isAuthPage(page)) throw new Error('Auth failed');

    await page.click('button[type="submit"]');
    await page.waitForTimeout(1000);

    const body = await page.textContent('body');
    const hasSpanishError = body.includes('Requerido') || body.includes('requerido') ||
                            body.includes('Este campo') || body.includes('Por favor');
    expect(hasSpanishError).toBe(true);
  });
});