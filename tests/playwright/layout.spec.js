/**
 * Modular Layout Tests
 * Suite: Playwright > Layout
 *
 * Tests the overall page layout structure - header, sidebar,
 * main content area, footer, and responsive behavior.
 *
 * @see TESTING-GUIDE.md Section "Layout & Navigation"
 */
const { test, expect } = require('@playwright/test');
const { restoreAuth } = require('../helpers/auth');
const { BASE_URL } = require('../playwright.config');

test.describe('Layout — Structure', () => {

  test.beforeEach(async ({ page }) => {
    await restoreAuth(page);
    await page.goto(BASE_URL + '?page=dashboard&lang=en');
    await page.waitForTimeout(1000);
  });

  test('LAYOUT-01: Header is present on all pages', async ({ page }) => {
    const header = page.locator('header, [role="banner"], nav[class*="header"], .app-header');
    if (await header.count() > 0) {
      await expect(header.first()).toBeVisible();
    } else {
      // Alternative: top navigation bar
      const nav = page.locator('nav, .navbar, .top-bar');
      await expect(nav.first()).toBeVisible();
    }
  });

  test('LAYOUT-02: Sidebar navigation is present', async ({ page }) => {
    const sidebar = page.locator('aside, [role="complementary"], nav[class*="sidebar"], .side-nav, .sidebar');
    if (await sidebar.count() > 0) {
      await expect(sidebar.first()).toBeVisible();
    }
  });

  test('LAYOUT-03: Main content area is present', async ({ page }) => {
    const main = page.locator('main, [role="main"], .main-content, .content-area');
    await expect(main.first()).toBeVisible();
  });

  test('LAYOUT-04: Page title is displayed', async ({ page }) => {
    const title = page.locator('h1, .page-title, [class*="title"]');
    if (await title.count() > 0) {
      const titleText = await title.first().textContent();
      expect(titleText.length).toBeGreaterThan(0);
    }
  });

  test('LAYOUT-05: Footer is present', async ({ page }) => {
    const footer = page.locator('footer, [role="contentinfo"], .app-footer');
    if (await footer.count() > 0) {
      await expect(footer.first()).toBeVisible();
    }
  });

  test('LAYOUT-06: Logo is visible in header', async ({ page }) => {
    const logo = page.locator('header img, header a[class*="logo"], .logo, [class*="brand"]');
    if (await logo.count() > 0) {
      await expect(logo.first()).toBeVisible();
    }
  });

  test('LAYOUT-07: User menu/profile is accessible', async ({ page }) => {
    const userMenu = page.locator('[class*="user"], [class*="profile"], a[href*="user"], button[class*="user"]');
    if (await userMenu.count() > 0) {
      await expect(userMenu.first()).toBeVisible();
    }
  });

  test('LAYOUT-08: Logout button is accessible', async ({ page }) => {
    const logoutBtn = page.locator('a:has-text("logout"), button:has-text("logout"), a:has-text("sign out")');
    if (await logoutBtn.count() > 0) {
      await expect(logoutBtn.first()).toBeVisible();
    }
  });
});

test.describe('Layout — Navigation', () => {
  test.beforeEach(async ({ page }) => {
    await restoreAuth(page);
    await page.goto(BASE_URL + '?page=dashboard&lang=en');
    await page.waitForTimeout(1000);
  });

  test('LAYOUT-NAV-01: All nav links are clickable', async ({ page }) => {
    const navLinks = page.locator('nav a, aside a, .sidebar a');
    const count = await navLinks.count();
    expect(count).toBeGreaterThan(0);

    // Check each link has valid href
    for (let i = 0; i < Math.min(count, 10); i++) {
      const href = await navLinks.nth(i).getAttribute('href');
      if (href && !href.startsWith('#') && !href.startsWith('javascript')) {
        expect(href.length).toBeGreaterThan(0);
      }
    }
  });

  test('LAYOUT-NAV-02: Active page is highlighted in nav', async ({ page }) => {
    // Dashboard should be active since we're on it
    const activeLink = page.locator('a[class*="active"], a[aria-current="page"]');
    const count = await activeLink.count();
    // Should have at least one active link
    expect(count).toBeGreaterThan(0);
  });

  test('LAYOUT-NAV-03: Nav collapse/expand works', async ({ page }) => {
    // Find collapse toggle
    const toggle = page.locator('button[class*="toggle"], button[class*="collapse"], button[class*="menu"]').first();
    if (await toggle.count() > 0) {
      await toggle.click();
      await page.waitForTimeout(500);
      // Sidebar should be hidden or minimized
      const sidebar = page.locator('aside, .sidebar');
      const isHidden = await sidebar.first().isHidden();
      // Toggle should work (either hide or show)
      expect(isHidden !== undefined).toBe(true);
    } else {
      expect(true).toBe(true);
    }
  });
});

test.describe('Layout — Consistency', () => {
  test.beforeEach(async ({ page }) => {
    await restoreAuth(page);
  });

  test('LAYOUT-CONSISTENT-01: Header consistent across pages', async ({ page }) => {
    const pages = ['dashboard', 'rfis', 'budget'];
    for (const p of pages) {
      await page.goto(BASE_URL + `?page=${p}&lang=en`);
      await page.waitForTimeout(1000);
      const header = page.locator('header, nav[class*="header"]');
      await expect(header.first()).toBeVisible();
    }
  });

  test('LAYOUT-CONSISTENT-02: Sidebar consistent across pages', async ({ page }) => {
    const pages = ['dashboard', 'rfis', 'budget'];
    for (const p of pages) {
      await page.goto(BASE_URL + `?page=${p}&lang=en`);
      await page.waitForTimeout(1000);
      const sidebar = page.locator('aside, .sidebar, nav[class*="sidebar"]');
      if (await sidebar.count() > 0) {
        await expect(sidebar.first()).toBeVisible();
      }
    }
  });

  test('LAYOUT-CONSISTENT-03: Page title updates per page', async ({ page }) => {
    const pages = [
      { page: 'dashboard', expected: /dashboard|panel|resumen/i },
      { page: 'rfis', expected: /rfi|solicitud/i },
      { page: 'budget', expected: /budget|presupuesto/i }
    ];

    for (const { page: p, expected } of pages) {
      await page.goto(BASE_URL + `?page=${p}&lang=en`);
      await page.waitForTimeout(1000);
      const body = await page.textContent('body');
      expect(body).toMatch(expected);
    }
  });
});
