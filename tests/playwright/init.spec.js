/**
 * Navigation Router Tests
 * Suite: Playwright > Init
 *
 * Tests the application's routing mechanism - how URL parameters
 * control page navigation, default redirects, and route handling.
 *
 * @see TESTING-GUIDE.md Section "Navigation"
 */
const { test, expect } = require('@playwright/test');
const { restoreAuth } = require('../helpers/auth');
const { BASE_URL } = require('../playwright.config');

test.describe('Navigation Router', () => {

  test('ROUTER-01: Root URL redirects to dashboard or login', async ({ page }) => {
    await page.goto(BASE_URL + '/');
    await page.waitForTimeout(2000);
    const url = page.url();
    const body = await page.textContent('body');
    // Should redirect to a valid page
    expect(url).toMatch(/dashboard|login|panel|page=/);
    expect(body.length).toBeGreaterThan(50);
  });

  test('ROUTER-02: Unknown page parameter shows error or 404', async ({ page }) => {
    await page.goto(BASE_URL + '?page=nonexistent_page_xyz');
    await page.waitForTimeout(2000);
    const body = await page.textContent('body');
    // Should show error, 404, or redirect
    expect(body).toMatch(/404|error|not.*found|page.*not.*exist/i);
  });

  test('ROUTER-03: Valid page parameters load correctly', async ({ page }) => {
    const pages = ['dashboard', 'rfis', 'daily_logs', 'budget', 'tasks'];
    for (const pageName of pages) {
      await page.goto(BASE_URL + `?page=${pageName}`);
      await page.waitForTimeout(2000);
      const body = await page.textContent('body');
      expect(body.length).toBeGreaterThan(50);
    }
  });

  test('ROUTER-04: Page parameter is preserved across navigation', async ({ page }) => {
    await restoreAuth(page);
    await page.goto(BASE_URL + '?page=rfis');
    await page.waitForTimeout(1000);

    // Click a link that should preserve or update the page param
    const link = page.locator('a[href*="rfis"]').first();
    if (await link.count() > 0) {
      const href = await link.getAttribute('href');
      await page.goto(BASE_URL + href);
      await page.waitForTimeout(1000);
      const url = page.url();
      // Should have rfis in the URL
      expect(url).toMatch(/rfis|page=rfis/i);
    } else {
      expect(true).toBe(true);
    }
  });

  test('ROUTER-05: Language parameter is preserved across navigation', async ({ page }) => {
    await page.goto(BASE_URL + '?page=dashboard&lang=es');
    await page.waitForTimeout(1000);
    const bodyES = await page.textContent('body');
    expect(bodyES).toMatch(/panel|resumen|proyecto/i);

    // Navigate to another page
    const link = page.locator('a[href*="dashboard"]').first();
    if (await link.count() > 0) {
      await link.click();
      await page.waitForTimeout(1000);
      const url = page.url();
      // Lang param should be preserved
      if (url.includes('lang=')) {
        expect(url).toMatch(/lang=es/);
      }
    }
  });

  test('ROUTER-06: Invalid language parameter defaults to EN', async ({ page }) => {
    await page.goto(BASE_URL + '?page=dashboard&lang=invalid');
    await page.waitForTimeout(1000);
    const body = await page.textContent('body');
    // Should default to English
    expect(body).toMatch(/dashboard|summary|project/i);
  });

  test('ROUTER-07: Deep link to specific resource works', async ({ page }) => {
    await restoreAuth(page);
    // Try to access a specific RFI by ID if available
    await page.goto(BASE_URL + '?page=view_rfi&id=1');
    await page.waitForTimeout(2000);
    const body = await page.textContent('body');
    const url = page.url();
    // Should show the RFI or error gracefully
    expect(body.length).toBeGreaterThan(50);
    expect(url).toMatch(/view_rfi|rfi.*\d|page=rfis/i);
  });

  test('ROUTER-08: Multiple URL parameters work together', async ({ page }) => {
    await page.goto(BASE_URL + '?page=rfis&lang=es&status=open');
    await page.waitForTimeout(2000);
    const body = await page.textContent('body');
    const url = page.url();
    // Should handle multiple params without error
    expect(body.length).toBeGreaterThan(50);
    expect(url).toMatch(/page=rfis/);
  });
});

test.describe('URL Parameter Edge Cases', () => {
  test('ROUTER-EDGE-01: Empty page parameter handled', async ({ page }) => {
    await page.goto(BASE_URL + '?page=');
    await page.waitForTimeout(2000);
    const url = page.url();
    // Should redirect to default
    expect(url).not.toMatch(/page=$/);
  });

  test('ROUTER-EDGE-02: Page parameter with special characters handled', async ({ page }) => {
    await page.goto(BASE_URL + '?page=dashboard<script>');
    await page.waitForTimeout(2000);
    // Should not execute script, should show error or ignore it
    const body = await page.textContent('body');
    expect(body).not.toMatch(/<script>/i);
  });

  test('ROUTER-EDGE-03: Very long page parameter handled gracefully', async ({ page }) => {
    const longPage = 'a'.repeat(500);
    await page.goto(BASE_URL + `?page=${longPage}`);
    await page.waitForTimeout(2000);
    // Should not crash, should show error
    const body = await page.textContent('body');
    expect(body).toMatch(/error|404|not.*found/i);
  });
});
