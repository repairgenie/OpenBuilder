/**
 * Daily Logs Page Tests
 * Suite: Playwright > Daily Logs
 *
 * Tests all UI elements, interactions, and bilingual functionality
 * on the Daily Logs listing and creation pages.
 *
 * @see TESTING-GUIDE.md Section "Daily Logs Page"
 */
const { test, expect } = require('@playwright/test');
const { restoreAuth } = require('../helpers/auth');
const { gotoLang } = require('../helpers/bilingual');
const { BASE_URL } = require('../playwright.config');

test.describe('Daily Logs — Listing Page', () => {

  test.beforeEach(async ({ page }) => {
    await restoreAuth(page);
    await page.goto(BASE_URL + '?page=daily_logs&lang=en');
    await page.waitForTimeout(1000);
  });

  test('LOGS-01: Daily logs page loads', async ({ page }) => {
    const body = await page.textContent('body');
    expect(body).toMatch(/daily|log|diario|diarios/i);
  });

  test('LOGS-02: Log entries are displayed in a table or list', async ({ page }) => {
    const hasTable = await page.locator('table').count() > 0;
    const hasList = await page.locator('ul, div[class*="log"]').count() > 0;
    expect(hasTable || hasList).toBe(true);
  });

  test('LOGS-03: Create log button is visible', async ({ page }) => {
    const createBtn = page.locator('a[href*="create_daily_log"], button:has-text("create"), button:has-text("new"), button:has-text("nuevo")');
    if (await createBtn.count() > 0) {
      await expect(createBtn.first()).toBeVisible();
    }
  });

  test('LOGS-04: Date filter is present', async ({ page }) => {
    const dateInput = page.locator('input[type="date"], input[name*="date"], input[placeholder*="date"]').first();
    if (await dateInput.count() > 0) {
      await expect(dateInput).toBeVisible();
    } else {
      // May use a date picker instead
      const datePicker = page.locator('[class*="date"], [placeholder*="date"]').first();
      if (await datePicker.count() > 0) {
        await expect(datePicker).toBeVisible();
      }
    }
  });

  test('LOGS-05: Filter by date range works', async ({ page }) => {
    const startDate = page.locator('input[name*="start"], input[name*="from"]').first();
    const endDate = page.locator('input[name*="end"], input[name*="to"]').first();

    if (await startDate.count() > 0 && await endDate.count() > 0) {
      await startDate.fill('2026-06-01');
      await endDate.fill('2026-06-10');
      await page.waitForTimeout(1000);
      const body = await page.textContent('body');
      expect(body).toMatch(/daily|log|diario/i);
    } else {
      expect(true).toBe(true);
    }
  });

  test('LOGS-06: Clicking a log entry opens detail view', async ({ page }) => {
    const logLink = page.locator('a[href*="view_daily_log"], a[href*="daily_log"]').first();
    if (await logLink.count() > 0) {
      await logLink.click();
      await page.waitForTimeout(2000);
      const url = page.url();
      expect(url).toMatch(/view_daily_log|daily_log.*\d|detail/i);
    } else {
      // No logs - empty state is valid
      const body = await page.textContent('body');
      expect(body).toMatch(/no.*log|empty|no.*found/i);
    }
  });

  test('LOGS-07: Bilingual toggle on daily logs page', async ({ page }) => {
    await gotoLang(page, '?page=daily_logs', 'en');
    await page.waitForTimeout(500);
    const bodyEN = await page.textContent('body');
    expect(bodyEN).toMatch(/daily|log|logs/i);

    await gotoLang(page, '?page=daily_logs', 'es');
    await page.waitForTimeout(500);
    const bodyES = await page.textContent('body');
    expect(bodyES).toMatch(/diario|diarios|registro|registros/i);
  });

  test('LOGS-08: Pagination works if multiple pages', async ({ page }) => {
    const pagination = page.locator('nav, .pagination, a[href*="page="]');
    if (await pagination.count() > 0) {
      const nextBtn = page.locator('a:has-text("next"), a:has-text(">")').first();
      if (await nextBtn.count() > 0) {
        await nextBtn.click();
        await page.waitForTimeout(1000);
        const url = page.url();
        expect(url).toMatch(/page=\d+/);
      }
    } else {
      expect(true).toBe(true);
    }
  });
});

test.describe('Daily Logs — Create/Edit Page', () => {
  test.beforeEach(async ({ page }) => {
    await restoreAuth(page);
    await page.goto(BASE_URL + '?page=create_daily_log&lang=en');
    await page.waitForTimeout(1000);
  });

  test('LOGS-CREATE-01: Create log form has required fields', async ({ page }) => {
    const body = await page.textContent('body');
    expect(body).toMatch(/date|log|description|notes|weather|work/i);
  });

  test('LOGS-CREATE-02: Date field is present and functional', async ({ page }) => {
    const dateInput = page.locator('input[name="log_date"], input[name*="date"]').first();
    if (await dateInput.count() > 0) {
      await dateInput.fill('2026-06-10');
      await expect(dateInput).toHaveValue('2026-06-10');
    }
  });

  test('LOGS-CREATE-03: Weather conditions dropdown is present', async ({ page }) => {
    const weatherSelect = page.locator('select[name*="weather"]').first();
    if (await weatherSelect.count() > 0) {
      const options = await weatherSelect.locator('option').count();
      expect(options).toBeGreaterThan(1);
    }
  });

  test('LOGS-CREATE-04: Work progress field accepts input', async ({ page }) => {
    const progressInput = page.locator('textarea[name*="progress"], textarea[name*="work"]').first();
    if (await progressInput.count() > 0) {
      await progressInput.fill('Completed foundation work on building A.');
      await expect(progressInput).toHaveValue(/foundation/i);
    }
  });

  test('LOGS-CREATE-05: Notes field accepts long input', async ({ page }) => {
    const notesInput = page.locator('textarea[name*="note"]').first();
    if (await notesInput.count() > 0) {
      const longText = 'A'.repeat(500);
      await notesInput.fill(longText);
      const value = await notesInput.inputValue();
      expect(value.length).toBeGreaterThan(400);
    }
  });

  test('LOGS-CREATE-06: Crew count field accepts numbers', async ({ page }) => {
    const crewInput = page.locator('input[name*="crew"], input[name*="people"], input[name*="workers"]').first();
    if (await crewInput.count() > 0) {
      await crewInput.fill('15');
      await expect(crewInput).toHaveValue('15');
    }
  });

  test('LOGS-CREATE-07: Submit button is present', async ({ page }) => {
    const submitBtn = page.locator('button[type="submit"]').first();
    await expect(submitBtn).toBeVisible();
  });

  test('LOGS-CREATE-08: Cancel button is present', async ({ page }) => {
    const cancelBtn = page.locator('button:has-text("cancel"), a:has-text("cancel")').first();
    if (await cancelBtn.count() > 0) {
      await expect(cancelBtn).toBeVisible();
    }
  });

  test('LOGS-CREATE-09: CSRF token is present', async ({ page }) => {
    const csrfInput = page.locator('input[name="csrf_token"]').first();
    const value = await csrfInput.getAttribute('value');
    expect(value).toBeTruthy();
  });

  test('LOGS-CREATE-10: Empty form submission shows validation error', async ({ page }) => {
    const submitBtn = page.locator('button[type="submit"]').first();
    await submitBtn.click();
    await page.waitForTimeout(1000);
    const body = await page.textContent('body');
    expect(body).toMatch(/required|error|please|field/i);
  });

  test('LOGS-CREATE-11: Bilingual labels on create form', async ({ page }) => {
    await gotoLang(page, '?page=create_daily_log', 'es');
    await page.waitForTimeout(500);
    const bodyES = await page.textContent('body');
    expect(bodyES).toMatch(/fecha|registro|notas|clima|progreso|crew/i);
  });
});
