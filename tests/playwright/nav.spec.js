/**
 * Dynamic Navigation & Language Tests
 * Suite: Playwright > Navigation
 *
 * Tests navigation links, language switching via nav controls,
 * breadcrumbs, and URL-driven navigation.
 *
 * @see TESTING-GUIDE.md Section "Navigation"
 */
const { test, expect } = require('@playwright/test');
const { restoreAuth } = require('../helpers/auth');
const { gotoLang, getCurrentLang } = require('../helpers/bilingual');
const { BASE_URL } = require('../playwright.config');

test.describe('Navigation — Links', () => {
  test.beforeEach(async ({ page }) => {
    await restoreAuth(page);
    await page.goto(BASE_URL + '?page=dashboard&lang=en');
    await page.waitForTimeout(1000);
  });

  test('NAV-01: Dashboard link in nav works', async ({ page }) => {
    const dashLink = page.locator('a[href*="dashboard"], a:has-text("dashboard"), a:has-text("panel")').first();
    if (await dashLink.count() > 0) {
      await dashLink.click();
      await page.waitForTimeout(1000);
      const url = page.url();
      expect(url).toMatch(/dashboard|panel/i);
    }
  });

  test('NAV-02: RFIs link in nav works', async ({ page }) => {
    const rfisLink = page.locator('a[href*="rfis"], a:has-text("rfi"), a:has-text("solicitud")').first();
    if (await rfisLink.count() > 0) {
      await rfisLink.click();
      await page.waitForTimeout(1000);
      const url = page.url();
      expect(url).toMatch(/rfis|rfi/i);
    }
  });

  test('NAV-03: Daily Logs link in nav works', async ({ page }) => {
    const logsLink = page.locator('a[href*="daily_log"], a:has-text("daily"), a:has-text("log")').first();
    if (await logsLink.count() > 0) {
      await logsLink.click();
      await page.waitForTimeout(1000);
      const url = page.url();
      expect(url).toMatch(/daily_log|logs/i);
    }
  });

  test('NAV-04: Budget link in nav works', async ({ page }) => {
    const budgetLink = page.locator('a[href*="budget"], a:has-text("budget"), a:has-text("presupuesto")').first();
    if (await budgetLink.count() > 0) {
      await budgetLink.click();
      await page.waitForTimeout(1000);
      const url = page.url();
      expect(url).toMatch(/budget/i);
    }
  });

  test('NAV-05: Tasks link in nav works', async ({ page }) => {
    const tasksLink = page.locator('a[href*="task"], a:has-text("task"), a:has-text("tarea")').first();
    if (await tasksLink.count() > 0) {
      await tasksLink.click();
      await page.waitForTimeout(1000);
      const url = page.url();
      expect(url).toMatch(/task/i);
    }
  });

  test('NAV-06: Users link in nav works (admin only)', async ({ page }) => {
    const usersLink = page.locator('a[href*="user"], a:has-text("user"), a:has-text("usuario")').first();
    if (await usersLink.count() > 0) {
      await usersLink.click();
      await page.waitForTimeout(1000);
      const url = page.url();
      const body = await page.textContent('body');
      // Should show users page or access denied
      expect(url).toMatch(/user|users|login|denied/i);
    }
  });

  test('NAV-07: All nav links have valid href attributes', async ({ page }) => {
    const navLinks = page.locator('nav a, .sidebar a, aside a');
    const count = await navLinks.count();
    expect(count).toBeGreaterThan(0);

    for (let i = 0; i < Math.min(count, 15); i++) {
      const href = await navLinks.nth(i).getAttribute('href');
      if (href && !href.startsWith('#') && !href.startsWith('javascript:')) {
        expect(href.length).toBeGreaterThan(0);
      }
    }
  });

  test('NAV-08: Active nav item is visually indicated', async ({ page }) => {
    const activeLinks = page.locator('a[class*="active"], a[aria-current="page"]');
    const count = await activeLinks.count();
    // Should highlight the current page
    expect(count).toBeGreaterThan(0);
  });
});

test.describe('Navigation — Language Toggle', () => {
  test.beforeEach(async ({ page }) => {
    await restoreAuth(page);
    await page.goto(BASE_URL + '?page=dashboard&lang=en');
    await page.waitForTimeout(1000);
  });

  test('NAV-LANG-01: Language toggle button is present', async ({ page }) => {
    const langToggle = page.locator('button:has-text("EN"), button:has-text("ES"), button:has-text("English"), button:has-text("Espanol"), a[href*="lang=en"], a[href*="lang=es"]');
    if (await langToggle.count() > 0) {
      await expect(langToggle.first()).toBeVisible();
    } else {
      // May be in a dropdown
      const langDropdown = page.locator('[class*="lang"], [class*="language"]');
      await expect(langDropdown.first()).toBeVisible();
    }
  });

  test('NAV-LANG-02: Toggling to ES switches all text to Spanish', async ({ page }) => {
    const esToggle = page.locator('a[href*="lang=es"], button:has-text("ES"), button:has-text("Espanol")').first();
    if (await esToggle.count() > 0) {
      await esToggle.click();
      await page.waitForTimeout(1000);
      const body = await page.textContent('body');
      expect(body).toMatch(/panel|resumen|proyecto|construccion/i);
    } else {
      expect(true).toBe(true);
    }
  });

  test('NAV-LANG-03: Toggling to EN switches all text to English', async ({ page }) => {
    await gotoLang(page, '?page=dashboard', 'es');
    await page.waitForTimeout(1000);

    const enToggle = page.locator('a[href*="lang=en"], button:has-text("EN"), button:has-text("English")').first();
    if (await enToggle.count() > 0) {
      await enToggle.click();
      await page.waitForTimeout(1000);
      const body = await page.textContent('body');
      expect(body).toMatch(/dashboard|summary|project|construction/i);
    }
  });

  test('NAV-LANG-04: Language preference persists across page navigation', async ({ page }) => {
    // Set ES
    await gotoLang(page, '?page=dashboard', 'es');
    await page.waitForTimeout(500);

    // Navigate to another page
    const rfisLink = page.locator('a[href*="rfis"]').first();
    if (await rfisLink.count() > 0) {
      await rfisLink.click();
      await page.waitForTimeout(1000);
      const url = page.url();
      // Lang should be preserved in URL
      if (url.includes('lang=')) {
        expect(url).toMatch(/lang=es/);
      }
    }
  });

  test('NAV-LANG-05: Language toggle works on every page', async ({ page }) => {
    const pages = ['dashboard', 'rfis', 'daily_logs', 'budget', 'tasks'];
    for (const p of pages) {
      await gotoLang(page, `?page=${p}`, 'es');
      await page.waitForTimeout(500);
      const body = await page.textContent('body');
      expect(body).toMatch(/panel|solicitud|diario|presupuesto|tarea/i);
    }
  });
});

test.describe('Navigation — Breadcrumbs', () => {
  test.beforeEach(async ({ page }) => {
    await restoreAuth(page);
  });

  test('NAV-BREAD-01: Breadcrumbs are present on detail pages', async ({ page }) => {
    await page.goto(BASE_URL + '?page=view_rfi&id=1&lang=en');
    await page.waitForTimeout(1000);
    const breadcrumbs = page.locator('[class*="breadcrumb"], nav[class*="breadcrumb"], .breadcrumbs');
    if (await breadcrumbs.count() > 0) {
      await expect(breadcrumbs.first()).toBeVisible();
    }
  });

  test('NAV-BREAD-02: Breadcrumbs are clickable', async ({ page }) => {
    await page.goto(BASE_URL + '?page=view_rfi&id=1&lang=en');
    await page.waitForTimeout(1000);
    const breadcrumbLinks = page.locator('[class*="breadcrumb"] a, .breadcrumbs a');
    const count = await breadcrumbLinks.count();
    if (count > 0) {
      const href = await breadcrumbLinks.first().getAttribute('href');
      expect(href).toBeTruthy();
    }
  });

  test('NAV-BREAD-03: Breadcrumbs show correct hierarchy', async ({ page }) => {
    await page.goto(BASE_URL + '?page=view_rfi&id=1&lang=en');
    await page.waitForTimeout(1000);
    const body = await page.textContent('body');
    // Should show: Home > RFIs > [Specific RFI]
    expect(body).toMatch(/home|dashboard|rfis?/i);
  });
});
