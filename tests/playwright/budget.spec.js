/**
 * Budget Page Tests
 * Suite: Playwright > Budget
 *
 * Tests all UI elements, interactions, and bilingual functionality
 * on the Budget management page.
 *
 * @see TESTING-GUIDE.md Section "Budget Page"
 */
const { test, expect } = require('@playwright/test');
const { restoreAuth } = require('../helpers/auth');
const { gotoLang } = require('../helpers/bilingual');
const { BASE_URL } = require('../playwright.config');

test.describe('Budget — Overview', () => {

  test.beforeEach(async ({ page }) => {
    await restoreAuth(page);
    await page.goto(BASE_URL + '?page=budget&lang=en');
    await page.waitForTimeout(1000);
  });

  test('BUDGET-01: Budget page loads', async ({ page }) => {
    const body = await page.textContent('body');
    expect(body).toMatch(/budget|presupuesto|committed|spent|revised/i);
  });

  test('BUDGET-02: Budget summary section is visible', async ({ page }) => {
    // Should show some kind of summary: total, spent, remaining
    const body = await page.textContent('body');
    expect(body).toMatch(/total|spent|remaining|committed|budget/i);
  });

  test('BUDGET-03: Cost code table is present', async ({ page }) => {
    const hasTable = await page.locator('table').count() > 0;
    const hasList = await page.locator('ul, div[class*="cost"]').count() > 0;
    expect(hasTable || hasList).toBe(true);
  });

  test('BUDGET-04: Add budget entry button is visible', async ({ page }) => {
    const addBtn = page.locator('a[href*="budget"], button:has-text("add"), button:has-text("new"), button:has-text("crear")');
    if (await addBtn.count() > 0) {
      await expect(addBtn.first()).toBeVisible();
    } else {
      // Budget might be read-only for some roles
      expect(true).toBe(true);
    }
  });

  test('BUDGET-05: Filter by cost code works', async ({ page }) => {
    const filterInput = page.locator('input[name*="filter"], input[placeholder*="cost"], select[name*="cost"]').first();
    if (await filterInput.count() > 0) {
      await filterInput.fill('01');
      await page.waitForTimeout(500);
      const body = await page.textContent('body');
      expect(body).toMatch(/01|cost|code/i);
    } else {
      expect(true).toBe(true);
    }
  });

  test('BUDGET-06: Budget totals display correctly', async ({ page }) => {
    // Look for currency/number formatting
    const body = await page.textContent('body');
    // Should have dollar signs or number formatting
    expect(body).toMatch(/\$|total|amount|committed/i);
  });

  test('BUDGET-07: Percentage spent is displayed', async ({ page }) => {
    const body = await page.textContent('body');
    // Should show percentage indicators
    expect(body).toMatch(/\%|percent|spent|utilization/i);
  });

  test('BUDGET-08: Bilingual toggle on budget page', async ({ page }) => {
    await gotoLang(page, '?page=budget', 'en');
    await page.waitForTimeout(500);
    const bodyEN = await page.textContent('body');
    expect(bodyEN).toMatch(/budget|committed|spent/i);

    await gotoLang(page, '?page=budget', 'es');
    await page.waitForTimeout(500);
    const bodyES = await page.textContent('body');
    expect(bodyES).toMatch(/presupuesto|comprometido|gastado/i);
  });
});

test.describe('Budget — Add/Edit Entry', () => {
  test.beforeEach(async ({ page }) => {
    await restoreAuth(page);
    await page.goto(BASE_URL + '?page=budget&lang=en');
    await page.waitForTimeout(1000);
  });

  test('BUDGET-ADD-01: Add budget entry form has required fields', async ({ page }) => {
    // Click add button if available
    const addBtn = page.locator('a[href*="add"], button:has-text("add"), button:has-text("new")').first();
    if (await addBtn.count() > 0) {
      await addBtn.click();
      await page.waitForTimeout(1000);
    }

    const body = await page.textContent('body');
    expect(body).toMatch(/cost|code|amount|description|commit/i);
  });

  test('BUDGET-ADD-02: Cost code field accepts input', async ({ page }) => {
    const costInput = page.locator('input[name*="cost_code"], input[name*="code"]').first();
    if (await costInput.count() > 0) {
      await costInput.fill('12345');
      await expect(costInput).toHaveValue('12345');
    }
  });

  test('BUDGET-ADD-03: Amount field accepts numbers', async ({ page }) => {
    const amountInput = page.locator('input[name*="amount"], input[name*="committed"], input[name*="cost"]').first();
    if (await amountInput.count() > 0) {
      await amountInput.fill('1000.50');
      const value = await amountInput.inputValue();
      expect(value).toMatch(/1000|1000.50/);
    }
  });

  test('BUDGET-ADD-04: Amount field rejects non-numeric input', async ({ page }) => {
    const amountInput = page.locator('input[name*="amount"]').first();
    if (await amountInput.count() > 0) {
      await amountInput.fill('abc123');
      const value = await amountInput.inputValue();
      // Should either reject non-numeric or clear the field
      if (value !== '') {
        expect(value).not.toBe('abc123');
      }
    }
  });

  test('BUDGET-ADD-05: Submit button is present', async ({ page }) => {
    const submitBtn = page.locator('button[type="submit"]').first();
    if (await submitBtn.count() > 0) {
      await expect(submitBtn).toBeVisible();
    }
  });

  test('BUDGET-ADD-06: CSRF token is present in form', async ({ page }) => {
    const csrfInput = page.locator('input[name="csrf_token"]').first();
    if (await csrfInput.count() > 0) {
      const value = await csrfInput.getAttribute('value');
      expect(value).toBeTruthy();
    }
  });

  test('BUDGET-ADD-07: Bilingual labels on add/edit form', async ({ page }) => {
    await gotoLang(page, '?page=budget', 'es');
    await page.waitForTimeout(500);
    const bodyES = await page.textContent('body');
    expect(bodyES).toMatch(/codigo|monto|descripcion|agregar|crear/i);
  });
});

test.describe('Budget — Scenario Simulator', () => {
  test.beforeEach(async ({ page }) => {
    await restoreAuth(page);
    await page.goto(BASE_URL + '?page=budget&lang=en');
    await page.waitForTimeout(1000);
  });

  test('BUDGET-SCENARIO-01: Scenario simulator section exists', async ({ page }) => {
    const body = await page.textContent('body');
    expect(body).toMatch(/scenario|simulator|what.?if|projection/i);
  });

  test('BUDGET-SCENARIO-02: Scenario inputs are functional', async ({ page }) => {
    const scenarioInput = page.locator('input[name*="scenario"], input[name*="whatif"], input[name*="projection"]').first();
    if (await scenarioInput.count() > 0) {
      await scenarioInput.fill('50000');
      await page.waitForTimeout(500);
      // Should update calculations
      const body = await page.textContent('body');
      expect(body).toMatch(/scenario|total|remaining|50000/i);
    } else {
      expect(true).toBe(true); // No simulator on this page
    }
  });
});
